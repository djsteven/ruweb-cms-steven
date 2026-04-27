<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index(): View
    {
        $settings = Setting::allGrouped()->get('email', collect());

        return view('admin.email.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $existing = Setting::where('key', $key)->first();
            if ($existing && $existing->type === 'password' && ($value === null || $value === '')) {
                continue;
            }
            Setting::set($key, $value);
        }

        Setting::clearCache();

        return redirect()
            ->route('admin.email.index')
            ->with('success', __('admin.email_saved'));
    }

    public function sendTestEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $to = $request->input('test_email');
        $apiKey = Setting::get('brevo_api_key');
        $fromAddress = Setting::get('mail_from_address') ?: config('mail.from.address');
        $fromName = Setting::get('mail_from_name') ?: config('mail.from.name');

        if (! $apiKey) {
            return redirect()
                ->route('admin.email.index')
                ->with('error', __('admin.email_test_no_key'));
        }

        try {
            Mail::mailer('brevo')->raw(
                __('admin.email_test_body', ['app' => config('app.name')]),
                function ($message) use ($to, $fromAddress, $fromName) {
                    $message->to($to)
                        ->from($fromAddress, $fromName)
                        ->subject(__('admin.email_test_subject', ['app' => config('app.name')]));
                }
            );

            return redirect()
                ->route('admin.email.index')
                ->with('success', __('admin.email_test_success', ['email' => $to]));
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.email.index')
                ->with('error', __('admin.email_test_failed', ['error' => $e->getMessage()]));
        }
    }
}
