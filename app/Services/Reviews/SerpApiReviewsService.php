<?php

namespace App\Services\Reviews;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SerpApiReviewsService
 *
 * Servicio reutilizable para obtener TODAS las reseñas de Google Maps
 * a través de SerpAPI (https://serpapi.com).
 *
 * Plan gratuito: 100 búsquedas/mes — más que suficiente para sync mensual.
 * Sin OAuth, sin aprobaciones de Google. Solo una API Key de SerpAPI.
 *
 * Reutilizable: copiar a app/Services/Reviews/ en otros proyectos.
 */
class SerpApiReviewsService
{
    protected const BASE_URL = 'https://serpapi.com/search.json';

    public function __construct(
        protected string $apiKey
    ) {}

    // -------------------------------------------------------------------------
    // Fetch todas las reseñas con paginación
    // -------------------------------------------------------------------------

    /**
     * Obtiene TODAS las reseñas de un negocio en Google Maps.
     *
     * @param  string  $placeId    El Google Maps Place ID (ej. ChIJ...)
     * @param  string  $language   Idioma de las reseñas (ej. 'es', 'en')
     * @return array{
     *     success: bool,
     *     reviews: array,
     *     total: int,
     *     average_rating: float|null,
     *     error: string|null
     * }
     */
    public function fetchReviews(string $placeId, string $language = 'es'): array
    {
        $reviews   = [];
        $pageToken = null;
        $total     = 0;
        $avgRating = null;

        do {
            $params = [
                'engine'    => 'google_maps_reviews',
                'place_id'  => $placeId,
                'api_key'   => $this->apiKey,
                'hl'        => $language,
                'sort_by'   => 'newestFirst',
            ];

            if ($pageToken) {
                $params['next_page_token'] = $pageToken;
            }

            $response = Http::timeout(30)->get(self::BASE_URL, $params);

            if ($response->failed()) {
                $error = $response->json('error') ?? "HTTP {$response->status()}";

                Log::warning('SerpApiReviewsService: API error', [
                    'place_id' => $placeId,
                    'status'   => $response->status(),
                    'error'    => $error,
                ]);

                return [
                    'success'        => false,
                    'reviews'        => [],
                    'total'          => 0,
                    'average_rating' => null,
                    'error'          => $error,
                ];
            }

            $data = $response->json();

            // Metadatos del negocio (solo en la primera página)
            if ($avgRating === null) {
                $avgRating = isset($data['reviews_results']['rating'])
                    ? (float) $data['reviews_results']['rating']
                    : null;
                $total = $data['reviews_results']['reviews'] ?? 0;
            }

            // Parsear reseñas de esta página
            foreach ($data['reviews'] ?? [] as $raw) {
                $reviews[] = $this->parseReview($placeId, $raw);
            }

            // Obtener token para la siguiente página
            $pageToken = $data['serpapi_pagination']['next_page_token'] ?? null;

        } while ($pageToken);

        return [
            'success'        => true,
            'reviews'        => $reviews,
            'total'          => $total ?: count($reviews),
            'average_rating' => $avgRating,
            'error'          => null,
        ];
    }

    // -------------------------------------------------------------------------
    // Parseo
    // -------------------------------------------------------------------------

    protected function parseReview(string $placeId, array $raw): array
    {
        $user       = $raw['user'] ?? [];
        $reviewTime = isset($raw['iso_date'])
            ? \Carbon\Carbon::parse($raw['iso_date'])
            : null;

        return [
            'place_id'                  => $placeId,
            'author_name'               => $user['name'] ?? 'Anónimo',
            'author_url'                => $user['link'] ?? null,
            'profile_photo_url'         => $user['thumbnail'] ?? null,
            'rating'                    => (int) ($raw['rating'] ?? 0),
            'text'                      => $raw['snippet'] ?? null,
            'relative_time_description' => $raw['date'] ?? null,
            'review_time'               => $reviewTime?->toDateTimeString(),
        ];
    }
}
