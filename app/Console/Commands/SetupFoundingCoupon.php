<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\StripeClient;

class SetupFoundingCoupon extends Command
{
    protected $signature = 'stripe:setup-founding-coupon {--force : Overwrite existing env value}';

    protected $description = 'Create the Founding Member 20%-off-forever coupon in Stripe and write its ID to .env';

    public function handle(): int
    {
        $secret = config('services.stripe.secret');

        if (! $secret) {
            $this->error('STRIPE_SECRET is not set in your .env file.');
            return self::FAILURE;
        }

        $stripe = new StripeClient($secret);

        // Check if we already have the coupon ID in env
        $existingId = env('STRIPE_COUPON_FOUNDING');
        if ($existingId && ! $this->option('force')) {
            $this->warn("STRIPE_COUPON_FOUNDING is already set to {$existingId}. Run with --force to recreate.");
            return self::SUCCESS;
        }

        $this->info('Creating Founding Member coupon in Stripe...');

        // Search for an existing coupon with our metadata marker
        $coupons = $stripe->coupons->all(['limit' => 100]);
        $existing = null;
        foreach ($coupons->data as $coupon) {
            if (($coupon->metadata['type'] ?? null) === 'founding_member') {
                $existing = $coupon;
                break;
            }
        }

        if ($existing && ! $this->option('force')) {
            $couponId = $existing->id;
            $this->line("  Coupon already exists: {$couponId}");
        } else {
            $coupon = $stripe->coupons->create([
                'percent_off' => 20,
                'duration'    => 'forever',
                'name'        => 'Founding Member — Price Lock',
                'metadata'    => ['type' => 'founding_member'],
            ]);
            $couponId = $coupon->id;
            $this->line("  Created coupon: {$couponId}");
        }

        $envLine = "STRIPE_COUPON_FOUNDING={$couponId}";

        $this->newLine();
        $this->info('Add this line to your .env file:');
        $this->line("  {$envLine}");
        $this->newLine();

        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);

            if (str_contains($env, 'STRIPE_COUPON_FOUNDING=')) {
                if ($this->option('force')) {
                    $env = preg_replace('/^STRIPE_COUPON_FOUNDING=.*/m', $envLine, $env);
                    file_put_contents($envPath, $env);
                    $this->info('.env updated.');
                } else {
                    $this->warn('.env already contains STRIPE_COUPON_FOUNDING. Run with --force to overwrite.');
                }
            } else {
                file_put_contents($envPath, $env."\n".$envLine);
                $this->info('.env updated.');
            }
        }

        $this->info('Done. Run php artisan config:clear after updating .env.');

        return self::SUCCESS;
    }
}
