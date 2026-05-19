<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
</script>

<template>
    <Head title="Terms of Service — FieldOps Hub" />

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
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">Terms of Service</h1>
                <p class="mt-3 text-sm text-slate-500">Last updated: May 18, 2026</p>
            </div>

            <div class="prose prose-slate max-w-none prose-headings:font-semibold prose-headings:text-slate-800 prose-p:text-slate-600 prose-p:leading-relaxed prose-li:text-slate-600 prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline">

                <p>Please read these Terms of Service ("Terms") carefully before using FieldOps Hub (the "Service") operated by FieldOps Hub ("we", "us", or "our"). By accessing or using the Service, you agree to be bound by these Terms.</p>

                <h2>1. Use of the Service</h2>
                <p>You may use the Service only for lawful purposes and in accordance with these Terms. You agree not to:</p>
                <ul>
                    <li>Use the Service in any way that violates applicable law or regulation</li>
                    <li>Attempt to gain unauthorized access to any part of the Service or its related systems</li>
                    <li>Transmit any unsolicited commercial communication through the Service</li>
                    <li>Reverse-engineer, decompile, or disassemble the Service</li>
                    <li>Use the Service to store or transmit malicious code</li>
                    <li>Interfere with or disrupt the integrity or performance of the Service</li>
                </ul>

                <h2>2. Accounts</h2>
                <p>You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. You must notify us immediately of any unauthorized use of your account. We reserve the right to terminate accounts that violate these Terms.</p>

                <h2>3. Subscriptions and Billing</h2>
                <p>Access to the Service requires a paid subscription after the 14-day free trial. Subscriptions are billed in advance on a monthly or annual basis. All fees are non-refundable except as required by law or as expressly stated in these Terms.</p>
                <p>You authorize us to charge your payment method on file at the start of each billing period. If payment fails, we may suspend your account until payment is resolved. You may cancel your subscription at any time; cancellation takes effect at the end of the current billing period.</p>

                <h2>4. Free Trial</h2>
                <p>We offer a 14-day free trial with no credit card required. At the end of the trial period, continued use of the Service requires a paid subscription. Trial accounts that are not converted may be deleted after 30 days of inactivity.</p>

                <h2>5. Your Data</h2>
                <p>You retain ownership of all data you input into the Service. We do not claim any intellectual property rights over your data. You grant us a limited license to store, process, and display your data solely to provide the Service to you.</p>
                <p>You are responsible for the accuracy and legality of data you enter, including customer information. You represent that you have obtained any necessary consents to store and process your customers' personal information.</p>

                <h2>6. Data Security and Backups</h2>
                <p>We implement reasonable technical and organizational measures to protect your data. However, we recommend maintaining your own backups of critical business data. We are not liable for data loss resulting from events beyond our reasonable control.</p>

                <h2>7. Third-Party Integrations</h2>
                <p>The Service integrates with third-party providers including Stripe, Twilio, SendGrid, and Google Maps. Your use of these integrations is subject to those providers' own terms and policies. We are not responsible for the availability or conduct of third-party services.</p>

                <h2>8. Acceptable Use of SMS and Email Features</h2>
                <p>When using the Service's SMS and email notification features, you agree to comply with all applicable laws, including the CAN-SPAM Act, TCPA, and GDPR where applicable. You are solely responsible for ensuring you have the proper consents from your customers to contact them via SMS or email.</p>

                <h2>9. Intellectual Property</h2>
                <p>The Service and its original content, features, and functionality are and will remain the exclusive property of FieldOps Hub. Our trademarks and trade dress may not be used in connection with any product or service without our prior written consent.</p>

                <h2>10. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, FieldOps Hub shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, data, or goodwill, arising out of or in connection with your use of the Service. Our total liability to you for any claim arising under these Terms shall not exceed the amounts you paid us in the three months preceding the claim.</p>

                <h2>11. Disclaimer of Warranties</h2>
                <p>The Service is provided "as is" and "as available" without warranties of any kind, either express or implied, including but not limited to implied warranties of merchantability, fitness for a particular purpose, or non-infringement. We do not warrant that the Service will be uninterrupted, error-free, or completely secure.</p>

                <h2>12. Termination</h2>
                <p>We may suspend or terminate your access to the Service immediately, without prior notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties. Upon termination, your right to use the Service will immediately cease.</p>

                <h2>13. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms at any time. We will provide notice of material changes by posting the updated Terms on this page with a revised date. Your continued use of the Service after changes are posted constitutes your acceptance of the revised Terms.</p>

                <h2>14. Governing Law</h2>
                <p>These Terms are governed by and construed in accordance with the laws of the United States, without regard to its conflict of law provisions.</p>

                <h2>15. Contact</h2>
                <p>If you have questions about these Terms, please contact us at <a href="mailto:legal@fieldops-hub.com">legal@fieldops-hub.com</a>.</p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-950 border-t border-white/5 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-600">&copy; {{ new Date().getFullYear() }} FieldOps Hub. All rights reserved.</p>
                    <nav class="flex items-center gap-6">
                        <Link href="/" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">Home</Link>
                        <a href="/privacy" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">Privacy Policy</a>
                        <a href="/terms" class="text-xs text-slate-400 font-medium">Terms of Service</a>
                    </nav>
                </div>
            </div>
        </footer>
    </div>
</template>
