<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $appName }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:32px 32px 8px 32px;">
                            <h1 style="margin:0 0 16px 0;font-size:20px;color:#111;">
                                {{ __('admin.welcome_email_greeting', ['name' => $user->name]) }}
                            </h1>
                            <p style="margin:0 0 16px 0;font-size:14px;color:#444;line-height:1.6;">
                                {!! __('admin.welcome_email_intro', ['app' => e($appName)]) !!}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 16px 32px;">
                            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:16px 20px;">
                                <p style="margin:0 0 12px 0;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;">
                                    {{ __('admin.welcome_email_credentials') }}
                                </p>
                                <p style="margin:0 0 6px 0;font-size:13px;color:#444;">
                                    <strong>{{ __('admin.email') }}:</strong>
                                    <span style="font-family:monospace;">{{ $user->email }}</span>
                                </p>
                                <p style="margin:0;font-size:13px;color:#444;">
                                    <strong>{{ __('admin.password') }}:</strong>
                                    <span style="font-family:monospace;">{{ $plainPassword }}</span>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 24px 32px;">
                            <p style="margin:0 0 20px 0;font-size:13px;color:#6b7280;line-height:1.6;">
                                {{ __('admin.welcome_email_security') }}
                            </p>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background:#059669;border-radius:6px;">
                                        <a href="{{ $loginUrl }}" style="display:inline-block;padding:10px 20px;color:#ffffff;font-size:14px;font-weight:500;text-decoration:none;">
                                            {{ __('admin.welcome_email_cta') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;border-top:1px solid #e5e7eb;background:#fafafa;">
                            <p style="margin:0;font-size:11px;color:#9ca3af;text-align:center;">
                                {{ __('admin.welcome_email_footer', ['app' => $appName]) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
