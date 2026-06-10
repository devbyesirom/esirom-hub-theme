<?php
/**
 * Template Name: Email Test (Admin)
 * Template Post Type: page
 *
 * Admin-only page to send test emails via Resend / SMTP.
 * Create a WordPress page with slug "email-test" and assign this template.
 *
 * @package Esirom_Client_Hub
 */

if (!defined('ABSPATH')) {
    exit;
}

show_admin_bar(false);

$login_page = get_page_by_path('login');
$login_url = $login_page ? get_permalink($login_page) : home_url('/login/');
$workflow_page = get_page_by_path('workflow');
$workflow_url = $workflow_page ? get_permalink($workflow_page) : home_url('/workflow/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="h-full">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test — <?php bloginfo('name'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/img/agencyhub-icon.png">
    <script>window.tailwindCDNWarning = false;</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <?php wp_head(); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">
    <div class="min-h-screen flex items-center justify-center py-10 px-4">
        <div class="w-full max-w-lg">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 px-8 py-6 text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/agencyhub-by-esirom.png" alt="Agency Hub" class="h-16 w-auto mx-auto mb-3">
                    <h1 class="text-lg font-bold text-white">Email Test</h1>
                    <p class="text-indigo-100 text-sm mt-1">Verify Resend / SMTP delivery</p>
                </div>

                <div id="loading" class="px-8 py-10 text-center text-gray-500 text-sm">Checking access…</div>

                <div id="denied" class="hidden px-8 py-10 text-center space-y-4">
                    <p class="text-gray-600 text-sm" id="deniedMessage">Admin access required.</p>
                    <a href="<?php echo esc_url($login_url); ?>" class="inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700">Sign in</a>
                </div>

                <div id="panel" class="hidden px-8 py-8 space-y-5">
                    <div id="alert" class="hidden px-4 py-3 rounded-xl text-sm" role="alert"></div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Send to</label>
                        <input id="email" type="email" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="you@example.com">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button type="button" id="btnTest" class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors">
                            Send basic test
                        </button>
                        <button type="button" id="btnContentBank" class="px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold transition-colors">
                            Send Content Bank preview
                        </button>
                    </div>

                    <p class="text-xs text-gray-400 leading-relaxed">
                        Uses your Hub login token. Check inbox and the
                        <a href="https://resend.com/emails" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Resend dashboard</a>
                        for delivery status.
                    </p>

                    <div class="pt-2 border-t border-gray-100 flex flex-wrap gap-3 text-sm">
                        <a href="<?php echo esc_url($workflow_url); ?>" class="text-indigo-600 hover:underline">← Back to Workflow</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = typeof ESIROM_API_URL !== 'undefined'
            ? ESIROM_API_URL
            : '<?php echo esc_js(get_option('esirom_api_url', 'https://esirom-hub-backend-production.up.railway.app/api')); ?>';
        const LOGIN_URL = <?php echo json_encode($login_url); ?>;

        const $ = (id) => document.getElementById(id);

        const showAlert = (message, type) => {
            const el = $('alert');
            el.textContent = message;
            el.className = 'px-4 py-3 rounded-xl text-sm ' + (
                type === 'success'
                    ? 'bg-green-50 border border-green-200 text-green-800'
                    : type === 'error'
                        ? 'bg-red-50 border border-red-200 text-red-800'
                        : 'bg-indigo-50 border border-indigo-200 text-indigo-800'
            );
            el.classList.remove('hidden');
        };

        const setLoading = (busy) => {
            ['btnTest', 'btnContentBank'].forEach((id) => {
                const btn = $(id);
                if (btn) btn.disabled = busy;
            });
        };

        async function sendTest(type) {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.href);
                return;
            }

            const email = ($('email').value || '').trim();
            if (!email) {
                showAlert('Enter an email address.', 'error');
                return;
            }

            setLoading(true);
            showAlert('Sending…', 'info');

            try {
                const res = await fetch(API_URL + '/admin/test-email', {
                    method: 'POST',
                    headers: {
                        Authorization: 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, type })
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    showAlert(data.message || 'Email sent successfully.', 'success');
                } else {
                    showAlert(data.message || 'Failed to send email.', 'error');
                }
            } catch (err) {
                showAlert(err.message || 'Network error.', 'error');
            } finally {
                setLoading(false);
            }
        }

        async function init() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.href);
                return;
            }

            try {
                const res = await fetch(API_URL + '/auth/me', {
                    headers: { Authorization: 'Bearer ' + token }
                });
                const data = await res.json();

                if (!res.ok || !data.success || !data.user) {
                    localStorage.removeItem('token');
                    window.location.href = LOGIN_URL + '?redirect=' + encodeURIComponent(window.location.href);
                    return;
                }

                if (data.user.role !== 'admin') {
                    $('loading').classList.add('hidden');
                    $('denied').classList.remove('hidden');
                    $('deniedMessage').textContent = 'This page is for admin accounts only.';
                    return;
                }

                $('loading').classList.add('hidden');
                $('panel').classList.remove('hidden');
                $('email').value = data.user.email || '';

                $('btnTest').addEventListener('click', () => sendTest('test'));
                $('btnContentBank').addEventListener('click', () => sendTest('content_bank'));

                const params = new URLSearchParams(window.location.search);
                const autoType = params.get('type') || (params.get('send') ? 'test' : null);
                if (params.get('auto') === '1' && autoType) {
                    if (params.get('email')) $('email').value = params.get('email');
                    sendTest(autoType === 'content_bank' ? 'content_bank' : 'test');
                }
            } catch (err) {
                $('loading').classList.add('hidden');
                $('denied').classList.remove('hidden');
                $('deniedMessage').textContent = 'Could not verify access: ' + err.message;
            }
        }

        init();
    </script>
    <?php wp_footer(); ?>
</body>
</html>
