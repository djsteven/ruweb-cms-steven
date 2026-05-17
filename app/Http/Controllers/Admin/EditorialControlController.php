<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Editorial\EditorialInsightsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorialControlController extends Controller
{
    public function __construct(
        protected EditorialInsightsService $insights
    ) {
    }

    public function index(Request $request): View
    {
        $summary = $this->insights->summary();
        $activeTab = $request->query('tab', 'content');
        $selectedIssue = $request->query('issue');
        $selectedType = $request->query('type', 'all');
        $translationState = $request->query('translation_state', 'all');

        if (! in_array($activeTab, ['content', 'translations'], true)) {
            $activeTab = 'content';
        }

        $completionIssues = collect($summary['completionIssues'])
            ->map(function (array $issue) use ($selectedIssue, $selectedType): array {
                $items = collect($issue['items']);

                if ($selectedType !== 'all') {
                    $items = $items->where('type', $selectedType)->values();
                }

                return $issue + [
                    'selected' => $issue['key'] === $selectedIssue,
                    'count' => $items->count(),
                    'items' => $items,
                ];
            });

        $editorialRows = collect($summary['editorialRows']);

        if ($selectedType !== 'all') {
            $editorialRows = $editorialRows->where('type', $selectedType)->values();
        }

        if (in_array($selectedIssue, ['featured_image', 'seo_title', 'seo_description'], true)) {
            $editorialRows = $editorialRows
                ->filter(fn (array $record): bool => (bool) ($record['issues'][$selectedIssue] ?? false))
                ->values();
        }

        $pendingTranslations = collect($summary['pendingTranslations']);
        $outdatedTranslations = collect($summary['outdatedTranslations']);

        if ($selectedType !== 'all') {
            $pendingTranslations = $pendingTranslations->where('type', $selectedType)->values();
            $outdatedTranslations = $outdatedTranslations->where('type', $selectedType)->values();
        }

        if ($translationState === 'pending') {
            $outdatedTranslations = collect();
        } elseif ($translationState === 'outdated') {
            $pendingTranslations = collect();
        }

        return view('admin.editorial-control.index', [
            'activeTab' => $activeTab,
            'completionIssues' => $completionIssues,
            'editorialRows' => $editorialRows,
            'hasSecondaryPublicLocales' => $summary['hasSecondaryPublicLocales'],
            'pendingTranslations' => $pendingTranslations,
            'outdatedTranslations' => $outdatedTranslations,
            'selectedIssue' => $selectedIssue,
            'selectedType' => $selectedType,
            'translationState' => $translationState,
        ]);
    }
}
