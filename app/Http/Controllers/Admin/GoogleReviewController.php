<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleReview;
use App\Models\Setting;
use App\Services\Reviews\SerpApiReviewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoogleReviewController extends Controller
{
    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $this->authorize('viewAny', GoogleReview::class);

        $hasApiKey  = ! empty(Setting::get('serpapi_key'));
        $hasPlaceId = ! empty(Setting::get('google_place_id'));

        $query = GoogleReview::query()
            ->orderBy('sort_order')
            ->orderByDesc('rating')
            ->orderByDesc('review_time');

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        if ($request->filled('visibility')) {
            $query->where('is_visible', $request->input('visibility') === 'visible');
        }

        $reviews = $query->paginate(25)->withQueryString();

        $stats = [
            'total'    => GoogleReview::count(),
            'visible'  => GoogleReview::visible()->count(),
            'featured' => GoogleReview::featured()->count(),
            'avg'      => round((float) GoogleReview::avg('rating'), 1),
        ];

        return view('admin.google-reviews.index', [
            'reviews'           => $reviews,
            'stats'             => $stats,
            'hasApiKey'         => $hasApiKey,
            'hasPlaceId'        => $hasPlaceId,
            'currentRating'     => $request->input('rating'),
            'currentVisibility' => $request->input('visibility'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Sincronizar desde SerpAPI
    // -------------------------------------------------------------------------

    public function sync(Request $request): RedirectResponse
    {
        $this->authorize('create', GoogleReview::class);

        $apiKey  = Setting::get('serpapi_key');
        $placeId = Setting::get('google_place_id');

        if (! $apiKey || ! $placeId) {
            return redirect()
                ->route('admin.google-reviews.index')
                ->with('error', __('admin.google_reviews_missing_config'));
        }

        $service = new SerpApiReviewsService($apiKey);
        $result  = $service->fetchReviews($placeId);

        if (! $result['success']) {
            return redirect()
                ->route('admin.google-reviews.index')
                ->with('error', __('admin.google_reviews_sync_error') . ': ' . $result['error']);
        }

        $imported = 0;
        $now      = now();

        foreach ($result['reviews'] as $reviewData) {
            $uniqueKey = [
                'place_id'    => $reviewData['place_id'],
                'author_name' => $reviewData['author_name'],
                'review_time' => $reviewData['review_time'],
            ];

            $updateValues = [
                'text'                      => $reviewData['text'],
                'profile_photo_url'         => $reviewData['profile_photo_url'],
                'relative_time_description' => $reviewData['relative_time_description'],
                'imported_at'               => $now,
            ];

            $existing = GoogleReview::where($uniqueKey)->first();

            if ($existing) {
                $existing->update($updateValues);

                continue;
            }

            try {
                GoogleReview::create(array_merge($reviewData, $updateValues, [
                    'is_visible'  => false,
                    'is_featured' => false,
                    'sort_order'  => 0,
                ]));
                $imported++;
            } catch (\Illuminate\Database\UniqueConstraintViolationException) {
                // Otra petición concurrente (doble clic, o solapamiento de páginas
                // de SerpAPI) ya insertó esta reseña entre el SELECT y el INSERT.
                GoogleReview::where($uniqueKey)->first()?->update($updateValues);
            }
        }

        $total = count($result['reviews']);

        return redirect()
            ->route('admin.google-reviews.index')
            ->with('success', __('admin.google_reviews_synced', [
                'imported' => $imported,
                'total'    => $total,
            ]));
    }

    // -------------------------------------------------------------------------
    // Toggle visible
    // -------------------------------------------------------------------------

    public function toggle(Request $request, GoogleReview $googleReview): JsonResponse
    {
        $this->authorize('update', $googleReview);

        $googleReview->update(['is_visible' => ! $googleReview->is_visible]);

        return response()->json(['is_visible' => $googleReview->is_visible]);
    }

    // -------------------------------------------------------------------------
    // Toggle featured
    // -------------------------------------------------------------------------

    public function feature(Request $request, GoogleReview $googleReview): JsonResponse
    {
        $this->authorize('update', $googleReview);

        $googleReview->update(['is_featured' => ! $googleReview->is_featured]);

        return response()->json(['is_featured' => $googleReview->is_featured]);
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(GoogleReview $googleReview): RedirectResponse
    {
        $this->authorize('delete', $googleReview);

        $googleReview->delete();

        return redirect()
            ->route('admin.google-reviews.index')
            ->with('success', __('admin.google_review_deleted'));
    }
}
