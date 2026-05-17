<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Services\Media\MediaHealthService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected MediaHealthService $health
    ) {
    }

    public function index(Request $request): View
    {
        $baseLocale = Locale::baseCode();
        $showCompleted = $request->boolean('show_completed');
        $setupTasks = collect($this->setupTasks($request))
            ->sortBy(fn (array $task): int => $task['completed'] ? 1 : 0)
            ->values();

        return view('admin.dashboard', [
            'mediaCount' => Media::count(),
            'pageCount' => Page::where('locale', $baseLocale)->count(),
            'postCount' => Post::where('locale', $baseLocale)->count(),
            'publishedPageCount' => Page::where('locale', $baseLocale)->where('status', 'published')->count(),
            'publishedPostCount' => Post::where('locale', $baseLocale)->where('status', 'published')->count(),
            'mediaHealth' => $this->health->summary(),
            'setupTasks' => $setupTasks,
            'pendingSetupTasks' => $setupTasks->where('completed', false)->values(),
            'completedSetupTasks' => $setupTasks->where('completed', true)->values(),
            'showCompleted' => $showCompleted,
        ]);
    }

    /**
     * @return array<int, array{key:string,title:string,description:string,completed:bool,href:?string,action:?string,status_label:string}>
     */
    private function setupTasks(Request $request): array
    {
        $googleTagId = trim((string) (Setting::get('google_tag_id') ?? ''));
        $metaPixelId = trim((string) (Setting::get('meta_pixel_id') ?? ''));
        $searchConsoleToken = trim((string) (Setting::get('search_console_verification_token') ?? ''));
        $mailEnabled = (bool) Setting::get('mail_enabled');
        $mailConfigured = $mailEnabled
            && filled(Setting::get('brevo_api_key'))
            && filled(Setting::get('mail_from_address'))
            && filled(Setting::get('mail_from_name'));
        $serverProtocol = strtoupper((string) $request->server('SERVER_PROTOCOL', ''));
        $http2Enabled = str_contains($serverProtocol, 'HTTP/2');

        return [
            [
                'key' => 'google_tag_id',
                'title' => __('admin.dashboard_task_google_tag_title'),
                'description' => __('admin.dashboard_task_google_tag_description'),
                'completed' => $googleTagId !== '',
                'href' => route('admin.analytics.index'),
                'action' => __('admin.dashboard_task_cta_configure'),
                'status_label' => $googleTagId !== '' ? __('admin.dashboard_task_done') : __('admin.dashboard_task_pending'),
            ],
            [
                'key' => 'meta_pixel_id',
                'title' => __('admin.dashboard_task_meta_pixel_title'),
                'description' => __('admin.dashboard_task_meta_pixel_description'),
                'completed' => $metaPixelId !== '',
                'href' => route('admin.analytics.index'),
                'action' => __('admin.dashboard_task_cta_configure'),
                'status_label' => $metaPixelId !== '' ? __('admin.dashboard_task_done') : __('admin.dashboard_task_pending'),
            ],
            [
                'key' => 'search_console_verification_token',
                'title' => __('admin.dashboard_task_search_console_title'),
                'description' => __('admin.dashboard_task_search_console_description'),
                'completed' => $searchConsoleToken !== '',
                'href' => route('admin.analytics.index'),
                'action' => __('admin.dashboard_task_cta_configure'),
                'status_label' => $searchConsoleToken !== '' ? __('admin.dashboard_task_done') : __('admin.dashboard_task_pending'),
            ],
            [
                'key' => 'mail_enabled',
                'title' => __('admin.dashboard_task_email_title'),
                'description' => __('admin.dashboard_task_email_description'),
                'completed' => $mailConfigured,
                'href' => route('admin.email.index'),
                'action' => __('admin.dashboard_task_cta_configure'),
                'status_label' => $mailConfigured ? __('admin.dashboard_task_done') : __('admin.dashboard_task_pending'),
            ],
            [
                'key' => 'http2',
                'title' => __('admin.dashboard_task_http2_title'),
                'description' => __('admin.dashboard_task_http2_description', ['protocol' => $serverProtocol !== '' ? $serverProtocol : 'unknown']),
                'completed' => $http2Enabled,
                'href' => null,
                'action' => null,
                'status_label' => $http2Enabled ? __('admin.dashboard_task_done') : __('admin.dashboard_task_pending'),
            ],
        ];
    }
}
