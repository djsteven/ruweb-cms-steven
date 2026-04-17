<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMail extends Command
{
    protected $signature = 'mail:test {email : Destination email address}';

    protected $description = 'Send a test email via the configured Brevo mailer.';

    public function handle(): int
    {
        $to = $this->argument('email');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email: {$to}");

            return self::FAILURE;
        }

        $apiKey = Setting::get('brevo_api_key') ?: env('BREVO_API_KEY');
        if (! $apiKey) {
            $this->error('No Brevo API key configured (DB setting or BREVO_API_KEY env).');

            return self::FAILURE;
        }

        $fromAddress = Setting::get('mail_from_address') ?: config('mail.from.address');
        $fromName = Setting::get('mail_from_name') ?: config('mail.from.name');

        $this->info("Sending test email via Brevo...");
        $this->line("  From: {$fromName} <{$fromAddress}>");
        $this->line("  To:   {$to}");

        try {
            Mail::mailer('brevo')->raw(
                "This is a test email sent from " . config('app.name') . " via Brevo.\n\nIf you received this, your setup is working.",
                function ($message) use ($to, $fromAddress, $fromName) {
                    $message->to($to)
                        ->from($fromAddress, $fromName)
                        ->subject('[' . config('app.name') . '] Test email');
                }
            );

            $this->info('✓ Email sent successfully.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('✗ Failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
