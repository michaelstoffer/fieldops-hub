<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\StripeClient;

class SetupStripeProducts extends Command
{
    protected $signature = 'stripe:setup-products {--force : Overwrite existing env values}';

    protected $description = 'Create FieldOps Hub products and prices in Stripe and write price IDs to .env';

    // Plan definitions — single source of truth
    const PLANS = [
        'starter' => [
            'name'        => 'FieldOps Hub — Starter',
            'description' => 'Up to 3 technicians',
            'monthly'     => 7900,   // cents
            'annual'      => 75600,  // cents ($63/mo × 12)
        ],
        'growth' => [
            'name'        => 'FieldOps Hub — Growth',
            'description' => 'Up to 10 technicians',
            'monthly'     => 14900,
            'annual'      => 142800, // $119/mo × 12
        ],
        'pro' => [
            'name'        => 'FieldOps Hub — Pro',
            'description' => 'Unlimited technicians',
            'monthly'     => 24900,
            'annual'      => 238800, // $199/mo × 12
        ],
    ];

    public function handle(): int
    {
        $secret = config('services.stripe.secret');

        if (! $secret) {
            $this->error('STRIPE_SECRET is not set in your .env file.');
            return self::FAILURE;
        }

        $stripe = new StripeClient($secret);

        $this->info('Creating Stripe products and prices...');

        $envLines = [];

        foreach (self::PLANS as $key => $plan) {
            $this->line("  → {$plan['name']}");

            // Create or retrieve product
            $products = $stripe->products->search([
                'query' => "name:'{$plan['name']}' AND active:'true'",
            ]);

            if ($products->data && count($products->data) > 0) {
                $product = $products->data[0];
                $this->line("    Product already exists: {$product->id}");
            } else {
                $product = $stripe->products->create([
                    'name'        => $plan['name'],
                    'description' => $plan['description'],
                    'metadata'    => ['plan' => $key],
                ]);
                $this->line("    Created product: {$product->id}");
            }

            // Monthly price
            $monthlyPrices = $stripe->prices->search([
                'query' => "product:'{$product->id}' AND metadata['interval']:'monthly' AND active:'true'",
            ]);

            if ($monthlyPrices->data && count($monthlyPrices->data) > 0) {
                $monthlyPrice = $monthlyPrices->data[0];
                $this->line("    Monthly price already exists: {$monthlyPrice->id}");
            } else {
                $monthlyPrice = $stripe->prices->create([
                    'product'        => $product->id,
                    'unit_amount'    => $plan['monthly'],
                    'currency'       => 'usd',
                    'recurring'      => ['interval' => 'month'],
                    'nickname'       => ucfirst($key).' Monthly',
                    'metadata'       => ['plan' => $key, 'interval' => 'monthly'],
                ]);
                $this->line("    Created monthly price: {$monthlyPrice->id}");
            }

            // Annual price
            $annualPrices = $stripe->prices->search([
                'query' => "product:'{$product->id}' AND metadata['interval']:'annual' AND active:'true'",
            ]);

            if ($annualPrices->data && count($annualPrices->data) > 0) {
                $annualPrice = $annualPrices->data[0];
                $this->line("    Annual price already exists: {$annualPrice->id}");
            } else {
                $annualPrice = $stripe->prices->create([
                    'product'        => $product->id,
                    'unit_amount'    => $plan['annual'],
                    'currency'       => 'usd',
                    'recurring'      => ['interval' => 'year'],
                    'nickname'       => ucfirst($key).' Annual',
                    'metadata'       => ['plan' => $key, 'interval' => 'annual'],
                ]);
                $this->line("    Created annual price: {$annualPrice->id}");
            }

            $envKey = strtoupper($key);
            $envLines[] = "STRIPE_PRICE_{$envKey}_MONTHLY={$monthlyPrice->id}";
            $envLines[] = "STRIPE_PRICE_{$envKey}_ANNUAL={$annualPrice->id}";
        }

        $this->newLine();
        $this->info('Add these lines to your .env file:');
        $this->newLine();
        foreach ($envLines as $line) {
            $this->line("  {$line}");
        }
        $this->newLine();

        // Auto-write to .env if possible
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $env = file_get_contents($envPath);
            $changed = false;

            foreach ($envLines as $line) {
                [$envKey, $envValue] = explode('=', $line, 2);

                if (str_contains($env, "{$envKey}=")) {
                    if ($this->option('force')) {
                        $env = preg_replace("/^{$envKey}=.*/m", $line, $env);
                        $changed = true;
                    }
                } else {
                    $env .= "\n{$line}";
                    $changed = true;
                }
            }

            if ($changed) {
                file_put_contents($envPath, $env);
                $this->info('.env updated automatically.');
            } else {
                $this->warn('.env already contains these keys. Run with --force to overwrite.');
            }
        }

        $this->info('Done. Run php artisan config:clear after updating .env.');

        return self::SUCCESS;
    }
}
