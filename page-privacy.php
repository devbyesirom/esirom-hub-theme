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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy & Terms of Use - <?php bloginfo('name'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .section-link:hover { background-color: #f3f4f6; }
    </style>
    <?php wp_head(); ?>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-10 w-10">
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-gray-900">Agency Hub</span>
                        <span class="text-xs text-gray-500">by esirom</span>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex items-center space-x-6">
                    <a href="#privacy" class="text-gray-600 hover:text-indigo-600 text-sm font-medium transition-colors">Privacy Policy</a>
                    <a href="#terms" class="text-gray-600 hover:text-indigo-600 text-sm font-medium transition-colors">Terms of Use</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('login'))); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">Sign In</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Privacy Policy Section -->
        <section id="privacy" class="bg-white rounded-lg shadow-sm p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-500 mb-8">Last Updated: January 23, 2026</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">1. Information We Collect</h2>
            <p class="text-gray-700 mb-4">Agency Hub collects and processes the following information when you connect your Facebook or Instagram account:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                <li>Facebook Page information (name, ID)</li>
                <li>Instagram Business Account information (username, ID)</li>
                <li>Public posts and their associated metrics (likes, comments, reach, impressions)</li>
                <li>Access tokens to retrieve this data from Facebook's Graph API</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">2. How We Use Your Information</h2>
            <p class="text-gray-700 mb-4">We use the collected information to:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                <li>Display your social media posts in your Agency Hub dashboard</li>
                <li>Show analytics and performance metrics for your content</li>
                <li>Sync new posts automatically when requested</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">3. Data Storage and Security</h2>
            <p class="text-gray-700 mb-4">Your data is stored securely in our database. Access tokens are encrypted and used only to fetch data from Facebook and Instagram APIs on your behalf.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">4. Data Sharing</h2>
            <p class="text-gray-700 mb-4">We do not share, sell, or distribute your data to third parties. Your social media data is only accessible to you and authorized users of your Agency Hub account.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">5. Your Rights</h2>
            <p class="text-gray-700 mb-4">You can:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                <li>Disconnect your social media accounts at any time</li>
                <li>Request deletion of your data</li>
                <li>View what data we have collected</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">6. Facebook and Instagram Data</h2>
            <p class="text-gray-700 mb-4">This application uses Facebook and Instagram Graph APIs to retrieve your public posts and metrics. We comply with Facebook's Platform Terms and Policies. You can revoke our access at any time through your Facebook settings.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">7. Contact</h2>
            <p class="text-gray-700 mb-4">For privacy-related questions or to request data deletion, please contact us at: <a href="mailto:support@esirom.com" class="text-indigo-600 hover:text-indigo-700 underline">support@esirom.com</a></p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">8. Changes to This Policy</h2>
            <p class="text-gray-700 mb-4">We may update this privacy policy from time to time. The "Last Updated" date at the top indicates when the policy was last revised.</p>
        </section>

        <!-- Terms of Use Section -->
        <section id="terms" class="bg-white rounded-lg shadow-sm p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Terms of Use</h1>
            <p class="text-sm text-gray-500 mb-8">Last Updated: January 27, 2026</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">1. Acceptance of Terms</h2>
            <p class="text-gray-700 mb-4">By accessing and using Agency Hub ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to these terms, please do not use the Service.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">2. Description of Service</h2>
            <p class="text-gray-700 mb-4">Agency Hub is a social media management platform that allows agencies and brands to:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>Connect and manage Facebook and Instagram business accounts</li>
                <li>View and analyze social media posts and performance metrics</li>
                <li>Manage workflow and content creation processes</li>
                <li>Track key performance indicators (KPIs) and analytics</li>
                <li>Collaborate with team members on content strategy</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">3. User Accounts</h2>
            <p class="text-gray-700 mb-4">To use the Service, you must:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>Create an account with accurate and complete information</li>
                <li>Maintain the security of your password and account</li>
                <li>Notify us immediately of any unauthorized use of your account</li>
                <li>Be responsible for all activities that occur under your account</li>
            </ul>
            <p class="text-gray-700 mb-4">You must be at least 18 years old to use this Service.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">4. User Responsibilities</h2>
            <p class="text-gray-700 mb-4">You agree to:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>Use the Service only for lawful purposes</li>
                <li>Not violate any applicable laws or regulations</li>
                <li>Not infringe upon the rights of others</li>
                <li>Not attempt to gain unauthorized access to the Service or related systems</li>
                <li>Not interfere with or disrupt the Service or servers</li>
                <li>Comply with Facebook and Instagram's Platform Terms and Policies</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">5. Social Media Integration</h2>
            <p class="text-gray-700 mb-4">When connecting your Facebook or Instagram accounts:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>You grant us permission to access your public posts and metrics via official APIs</li>
                <li>You must have the necessary rights and permissions for the accounts you connect</li>
                <li>You remain responsible for your social media content and activities</li>
                <li>You can revoke access at any time through your account settings</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">6. Intellectual Property</h2>
            <p class="text-gray-700 mb-4">The Service and its original content, features, and functionality are owned by Esirom and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
            <p class="text-gray-700 mb-4">You retain all rights to the content you upload or create through the Service. By using the Service, you grant us a license to use, store, and display your content solely for the purpose of providing the Service to you.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">7. Service Availability</h2>
            <p class="text-gray-700 mb-4">We strive to provide reliable service, but we do not guarantee that:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>The Service will be uninterrupted, timely, secure, or error-free</li>
                <li>The results obtained from using the Service will be accurate or reliable</li>
                <li>Any errors in the Service will be corrected</li>
            </ul>
            <p class="text-gray-700 mb-4">We reserve the right to modify, suspend, or discontinue the Service at any time without notice.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">8. Limitation of Liability</h2>
            <p class="text-gray-700 mb-4">To the maximum extent permitted by law, Esirom shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses resulting from:</p>
            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mb-4">
                <li>Your use or inability to use the Service</li>
                <li>Any unauthorized access to or use of our servers and/or any personal information stored therein</li>
                <li>Any interruption or cessation of transmission to or from the Service</li>
                <li>Any bugs, viruses, or similar that may be transmitted through the Service by any third party</li>
            </ul>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">9. Termination</h2>
            <p class="text-gray-700 mb-4">We may terminate or suspend your account and access to the Service immediately, without prior notice or liability, for any reason, including if you breach these Terms.</p>
            <p class="text-gray-700 mb-4">Upon termination, your right to use the Service will immediately cease. If you wish to terminate your account, you may contact us at <a href="mailto:support@esirom.com" class="text-indigo-600 hover:text-indigo-700 underline">support@esirom.com</a>.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">10. Changes to Terms</h2>
            <p class="text-gray-700 mb-4">We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">11. Governing Law</h2>
            <p class="text-gray-700 mb-4">These Terms shall be governed and construed in accordance with the laws of your jurisdiction, without regard to its conflict of law provisions.</p>

            <h2 class="text-2xl font-semibold text-indigo-600 mt-8 mb-4">12. Contact Information</h2>
            <p class="text-gray-700 mb-4">If you have any questions about these Terms, please contact us at:</p>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700"><strong>Email:</strong> <a href="mailto:support@esirom.com" class="text-indigo-600 hover:text-indigo-700 underline">support@esirom.com</a></p>
                <p class="text-gray-700 mt-2"><strong>Website:</strong> <a href="https://hub.esirom.com" class="text-indigo-600 hover:text-indigo-700 underline">hub.esirom.com</a></p>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png" alt="Agency Hub" class="h-8 w-8">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Agency Hub by Esirom</p>
                        <p class="text-xs text-gray-500">© <?php echo date('Y'); ?> All rights reserved.</p>
                    </div>
                </div>
                <div class="flex space-x-6">
                    <a href="#privacy" class="text-sm text-gray-600 hover:text-indigo-600">Privacy Policy</a>
                    <a href="#terms" class="text-sm text-gray-600 hover:text-indigo-600">Terms of Use</a>
                    <a href="mailto:support@esirom.com" class="text-sm text-gray-600 hover:text-indigo-600">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
