<?php
function somethingMissing() {
    $missing = false;
    foreach (func_get_args() as $param) {
        if ($param == null)
            $missing = true;
    }
    return $missing;
}

function sendVerifMail($user) {

    $lien = "http://sylvain-bourbousse.fr/api/mail.php?id=".strval($user->getId())."&verif=".md5($user->getCleMail());
    // Create the email and send the message
    $to = $user->getEmail();
    $subject = "Vérification de votre compte Alpes-Drive";
    $body = 'Ceci est un message automatique: pour vérifier votre compte cliquez sur ce lien : '.$lien;
    $header = 'MIME-Version: 1.0' . "\r\n";
    $header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    if(mail($to, $subject, $body, $header))
        return true;
    else
        return false;
}
