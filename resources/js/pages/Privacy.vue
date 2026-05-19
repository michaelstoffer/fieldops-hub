<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
</script>

<template>
    <Head title="Privacy Policy — FieldOps Hub" />

    <div class="min-h-screen bg-slate-50 text-slate-900 antialiased">

        <!-- Nav -->
        <header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-sm border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <Link href="/" class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center shadow-md shadow-blue-500/30">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-white font-semibold tracking-tight">FieldOps Hub</span>
                    </Link>
                    <Link :href="login().url" class="text-sm text-slate-400 hover:text-white transition-colors font-medium">Sign in</Link>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="mb-10">
                <Link href="/" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors mb-6">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to home
                </Link>
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">Privacy Policy</h1>
                <p class="mt-3 text-sm text-slate-500">Last updated: May 18, 2026</p>
            </div>

            <div class="prose prose-slate max-w-none prose-headings:font-semibold prose-headings:text-slate-800 prose-p:text-slate-600 prose-p:leading-relaxed prose-li:text-slate-600 prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline">

                <p>FieldOps Hub ("we", "us", or "our") operates the FieldOps Hub platform (the "Service"). This page informs you of our policies regarding the collection, use, and disclosure of personal information when you use our Service.</p>

                <h2>Information We Collect</h2>
                <p>We collect information you provide directly when you create an account, use the Service, or contact us:</p>
                <ul>
                    <li><strong>Account information</strong> — your name, email address, and password when you register</li>
                    <li><strong>Organization data</strong> — company name, contact details, and settings you configure</li>
                    <li><strong>Business data</strong> — customer records, job details, invoices, and estimates you create within the platform</li>
                    <li><strong>Payment information</strong> — billing details are processed by Stripe and not stored on our servers</li>
                    <li><strong>Location data</strong> — if you enable GPS tracking for technicians, location updates are collected and stored while sharing is active</li>
                    <li><strong>Usage data</strong> — log data such as IP addresses, browser type, pages visited, and timestamps</li>
                </ul>

                <h2>How We Use Your Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Provide, maintain, and improve the Service</li>
                    <li>Process transactions and send related information, including confirmations and invoices</li>
                    <li>Send transactional notifications (job confirmations, status updates) on your behalf to your customers</li>
                    <li>Respond to comments and questions and provide customer support</li>
                    <li>Monitor and analyze usage patterns to improve the Service</li>
                    <li>Detect, investigate, and prevent fraudulent or unauthorized activity</li>
                </ul>

                <h2>Data Isolation and Multi-Tenancy</h2>
                <p>Every record in FieldOps Hub — customers, jobs, invoices, technicians — is scoped to your organization. No user from one organization can access data belonging to another organization. This isolation is enforced at the database query level on every request.</p>

                <h2>Third-Party Services</h2>
                <p>We integrate with the following third-party services, each governed by their own privacy policies:</p>
                <ul>
                    <li><strong>Stripe</strong> — payment processing. Stripe stores and processes your payment method information.</li>
                    <li><strong>Twilio</strong> — SMS notifications sent to your customers. Message content is logged within your organization.</li>
                    <li><strong>SendGrid</strong> — transactional email delivery.</li>
                    <li><strong>Google Maps</strong> — address lookup and dispatch map functionality.</li>
                    <li><strong>Sentry</strong> — error monitoring. Error reports may include request metadata but are not used for advertising.</li>
                </ul>

                <h2>Data Retention</h2>
                <p>We retain your data for as long as your account is active. If you cancel your subscription, your data remains accessible for 30 days, after which it may be deleted. You may request deletion of your data at any time by contacting us.</p>

                <h2>Security</h2>
                <p>We implement industry-standard security measures including encrypted data transmission (HTTPS/TLS), bcrypt password hashing, encrypted storage of third-party API keys, HTTP security headers, and rate limiting on all authentication endpoints. No method of transmission over the Internet is 100% secure, however, and we cannot guarantee absolute security.</p>

                <h2>Cookies and Sessions</h2>
                <p>We use session cookies to keep you logged in. We do not use tracking cookies or third-party advertising cookies. The only non-essential cookie is an appearance preference (light/dark mode), which contains no personal information.</p>

                <h2>Your Rights</h2>
                <p>You have the right to access, correct, or delete your personal information at any time. To exercise these rights, contact us at <a href="mailto:privacy@fieldops-hub.com">privacy@fieldops-hub.com</a>.</p>

                <h2>Children's Privacy</h2>
                <p>The Service is not directed to individuals under the age of 16. We do not knowingly collect personal information from children.</p>

                <h2>Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the new policy on this page with an updated date. Continued use of the Service after changes constitutes acceptance of the revised policy.</p>

                <h2>Contact</h2>
                <p>If you have questions about this Privacy Policy, please contact us at <a href="mailto:privacy@fieldops-hub.com">privacy@fieldops-hub.com</a>.</p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 border-t border-white/5 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-600">&copy; {{ new Date().getFullYear() }} FieldOps Hub. All rights reserved.</p>
                    <nav class="flex items-center gap-6">
                        <Link href="/" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">Home</Link>
                        <a href="/privacy" class="text-xs text-slate-400 font-medium">Privacy Policy</a>
                        <a href="/terms" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">Terms of Service</a>
                    </nav>
                </div>
            </div>
        </footer>
    </div>
</template>
