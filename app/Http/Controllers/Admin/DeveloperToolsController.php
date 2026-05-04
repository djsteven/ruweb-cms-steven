<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Snapshots\EnvironmentReportService;
use App\Services\Snapshots\SnapshotException;
use App\Services\Snapshots\SnapshotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeveloperToolsController extends Controller
{
    public function __construct(
        protected EnvironmentReportService $environment,
        protected SnapshotService $snapshots
    ) {}

    public function index(Request $request): View
    {
        return view('admin.developer-tools.index', [
            'report' => $this->environment->report(),
            'activeTab' => $request->query('tab', 'system') === 'migration' ? 'migration' : 'system',
        ]);
    }

    public function download(): BinaryFileResponse|RedirectResponse
    {
        try {
            $siteName = Setting::get('site_name') ?: config('app.name');
            $archiveName = Str::slug($siteName).'-'.now()->format('Ymd-His');
            $path = $this->snapshots->create($archiveName, storage_path('app/private/snapshots/tmp'));
        } catch (SnapshotException $exception) {
            return redirect()
                ->route('admin.developer-tools.index', ['tab' => 'migration'])
                ->with('error', $exception->getMessage());
        }

        return response()
            ->download($path, basename($path), ['Content-Type' => 'application/octet-stream'])
            ->deleteFileAfterSend(true);
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'backup' => ['required', 'file', 'max:512000'],
            'force' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('backup');

        if (! $file || $file->getClientOriginalExtension() !== 'appbackup') {
            return redirect()
                ->route('admin.developer-tools.index', ['tab' => 'migration'])
                ->with('error', __('admin.snapshot_invalid_extension'));
        }

        $directory = storage_path('app/private/snapshots/uploads');
        File::ensureDirectoryExists($directory);
        $path = $file->move($directory, uniqid('upload-', true).'.appbackup')->getPathname();

        try {
            $result = $this->snapshots->restore($path, $request->boolean('force'));

            return redirect()
                ->route('admin.developer-tools.index', ['tab' => 'migration'])
                ->with('success', __('admin.snapshot_restored', [
                    'tables' => $result['tables'],
                    'files' => $result['uploads_files'],
                ]));
        } catch (SnapshotException $exception) {
            return redirect()
                ->route('admin.developer-tools.index', ['tab' => 'migration'])
                ->with('error', $exception->getMessage());
        } finally {
            File::delete($path);
        }
    }
}
