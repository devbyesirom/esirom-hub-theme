<?php
/**
 * Template Name: Privacy Policy & Terms
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title>Privacy Policy &amp; Terms of Use | <?php bloginfo('name'); ?></title>
    <?php esirom_hub_head_assets(); ?>
    <?php wp_head(); ?>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <!-- Header -->
    <header class="hub-sticky-header">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 gap-4">
                <a href="<?php echo esc_url(esirom_hub_page_url('login')); ?>" class="hub-sidebar-brand">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="" class="h-8 w-8">
                    <span class="flex flex-col">
                        <span class="hub-sidebar-brand__name">Agency Hub</span>
                        <span class="hub-sidebar-brand__sub">by esirom</span>
                    </span>
                </a>
                <nav class="flex items-center gap-1 sm:gap-2" aria-label="Page sections">
                    <a href="#privacy" class="hub-btn hub-btn--ghost hub-btn--sm hidden sm:inline-flex">Privacy Policy</a>
                    <a href="#terms" class="hub-btn hub-btn--ghost hub-btn--sm hidden sm:inline-flex">Terms of Use</a>
                    <a href="<?php echo esc_url(esirom_hub_page_url('login')); ?>" class="hub-btn hub-btn--primary hub-btn--sm">Sign in</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 space-y-8">
        <!-- Privacy Policy Section -->
        <section id="privacy" class="hub-card p-6 sm:p-10">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Last Updated: January 23, 2026</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">1. Information We Collect</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">Agency Hub collects and processes the following information when you connect your Facebook or Instagram account:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5">
                <li>Facebook Page information (name, ID)</li>
                <li>Instagram Business Account information (username, ID)</li>
                <li>Public posts and their associated metrics (likes, comments, reach, impressions)</li>
                <li>Access tokens to retrieve this data from Facebook's Graph API</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">2. How We Use Your Information</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We use the collected information to:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5">
                <li>Display your social media posts in your Agency Hub dashboard</li>
                <li>Show analytics and performance metrics for your content</li>
                <li>Sync new posts automatically when requested</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">3. Data Storage and Security</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">Your data is stored securely in our database. Access tokens are encrypted and used only to fetch data from Facebook and Instagram APIs on your behalf.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">4. Data Sharing</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We do not share, sell, or distribute your data to third parties. Your social media data is only accessible to you and authorized users of your Agency Hub account.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">5. Your Rights</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">You can:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5">
                <li>Disconnect your social media accounts at any time</li>
                <li>Request deletion of your data</li>
                <li>View what data we have collected</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">6. Facebook and Instagram Data</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">This application uses Facebook and Instagram Graph APIs to retrieve your public posts and metrics. We comply with Facebook's Platform Terms and Policies. You can revoke our access at any time through your Facebook settings.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">7. Contact</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">For privacy-related questions or to request data deletion, please contact us at: <a href="mailto:support@esirom.com" class="text-brand-600 hover:text-brand-700 dark:text-brand-300 underline underline-offset-2">support@esirom.com</a></p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">8. Changes to This Policy</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We may update this privacy policy from time to time. The "Last Updated" date at the top indicates when the policy was last revised.</p>
        </section>

        <!-- Terms of Use Section -->
        <section id="terms" class="hub-card p-6 sm:p-10">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">Terms of Use</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Last Updated: January 27, 2026</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">1. Acceptance of Terms</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">By accessing and using Agency Hub ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use the Service.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">2. Description of Service</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">Agency Hub is a social media management platform that allows agencies and brands to:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>Connect and manage Facebook and Instagram business accounts</li>
                <li>View and analyse social media posts and performance metrics</li>
                <li>Manage workflow and content creation processes</li>
                <li>Track key performance indicators (KPIs) and analytics</li>
                <li>Collaborate with team members on content strategy</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">3. User Accounts</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">To use the Service, you must:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>Create an account with accurate and complete information</li>
                <li>Maintain the security of your password and account</li>
                <li>Notify us immediately of any unauthorized use of your account</li>
                <li>Be responsible for all activities that occur under your account</li>
            </ul>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">You must be at least 18 years old to use this Service.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">4. User Responsibilities</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">You agree to:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>Use the Service only for lawful purposes</li>
                <li>Not violate any applicable laws or regulations</li>
                <li>Not infringe upon the rights of others</li>
                <li>Not attempt to gain unauthorized access to the Service or related systems</li>
                <li>Not interfere with or disrupt the Service or servers</li>
                <li>Comply with Facebook and Instagram's Platform Terms and Policies</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">5. Social Media Integration</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">When connecting your Facebook or Instagram accounts:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>You grant us permission to access your public posts and metrics via official APIs</li>
                <li>You must have the necessary rights and permissions for the accounts you connect</li>
                <li>You remain responsible for your social media content and activities</li>
                <li>You can revoke access at any time through your account settings</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">6. Intellectual Property</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">The Service and its original content, features, and functionality are owned by Esirom and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">You retain all rights to the content you upload or create through the Service. By using the Service, you grant us a license to use, store, and display your content solely for the purpose of providing the Service to you.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">7. Service Availability</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We strive to provide reliable service, but we do not guarantee that:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>The Service will be uninterrupted, timely, secure, or error-free</li>
                <li>The results obtained from using the Service will be accurate or reliable</li>
                <li>Any errors in the Service will be corrected</li>
            </ul>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We reserve the right to modify, suspend, or discontinue the Service at any time without notice.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">8. Limitation of Liability</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">To the maximum extent permitted by law, Esirom shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses resulting from:</p>
            <ul class="list-disc list-outside space-y-2 text-gray-700 dark:text-gray-300 leading-relaxed ml-5 mb-4">
                <li>Your use or inability to use the Service</li>
                <li>Any unauthorized access to or use of our servers and/or any personal information stored therein</li>
                <li>Any interruption or cessation of transmission to or from the Service</li>
                <li>Any bugs, viruses, or similar that may be transmitted through the Service by any third party</li>
            </ul>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">9. Termination</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We may terminate or suspend your account and access to the Service immediately, without prior notice or liability, for any reason, including if you breach these Terms.</p>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">Upon termination, your right to use the Service will immediately cease. If you wish to terminate your account, you may contact us at <a href="mailto:support@esirom.com" class="text-brand-600 hover:text-brand-700 dark:text-brand-300 underline underline-offset-2">support@esirom.com</a>.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">10. Changes to Terms</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">11. Governing Law</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">These Terms shall be governed and construed in accordance with the laws of your jurisdiction, without regard to its conflict of law provisions.</p>

            <h2 class="text-xl font-semibold text-brand-700 dark:text-brand-300 mt-8 mb-3">12. Contact Information</h2>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">If you have any questions about these Terms, please contact us at:</p>
            <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-4 rounded-xl">
                <p class="text-gray-700 dark:text-gray-300"><strong>Email:</strong> <a href="mailto:support@esirom.com" class="text-brand-600 hover:text-brand-700 dark:text-brand-300 underline underline-offset-2">support@esirom.com</a></p>
                <p class="text-gray-700 dark:text-gray-300 mt-2"><strong>Website:</strong> <a href="https://hub.esirom.com" class="text-brand-600 hover:text-brand-700 dark:text-brand-300 underline underline-offset-2">hub.esirom.com</a></p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-gray-800 mt-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/agencyhub-icon.png" alt="" class="h-8 w-8">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Agency Hub by Esirom</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">&copy; <?php echo esc_html(gmdate('Y')); ?> All rights reserved.</p>
                    </div>
                </div>
                <div class="flex gap-6">
                    <a href="#privacy" class="text-sm text-gray-600 dark:text-gray-300 hover:text-brand-600">Privacy Policy</a>
                    <a href="#terms" class="text-sm text-gray-600 dark:text-gray-300 hover:text-brand-600">Terms of Use</a>
                    <a href="mailto:support@esirom.com" class="text-sm text-gray-600 dark:text-gray-300 hover:text-brand-600">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
