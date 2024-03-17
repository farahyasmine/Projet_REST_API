<?php
//$_SERVER['PHP_AUTH_USER']=NULL;
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'L\'utilisateur a appuié sur annuler ' ;
    exit;
} else {
    echo "<p>Salut {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>Vous avez mis {$_SERVER['PHP_AUTH_PW']} comme votre mot de passe.</p>";
    //$_SERVER['PHP_AUTH_USER']=NULL;
}
