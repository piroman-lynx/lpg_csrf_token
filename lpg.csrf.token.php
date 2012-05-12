<?php


function lpg_csrf_token($token_key, $loading_text = "Loading...") {
    $headers = lpg_getallheaders();
    $source = null;
    $method = 'classic';

    if (isset($headers['X-CSRF-TOKEN'])) {
        $token = $headers['X-CSRF-TOKEN'];
        $source = 'header';
    } else
    if (isset($_GET['csrf_token'])) {
        $token = $_GET['csrf_token'];
        $source = 'get';
    } else {
        $token = '';
    }

    if (isset($headers['X-REQUESTED-WITH']) && ($headers['X-REQUESTED-WITH'] == 'XMLHttpRequest')) {
        $method = 'ajax';
    }

    if (($token == '') && ($method == 'ajax')) {
        error_log("CSRF Attempt! Ajax atack form site: " . $_SERVER['HTTP_REFERER']);
        return false;
    } else
    if (($source == 'header') && ($method == 'ajax')) {
        if ($_SESSION['token_' . $token] !== true) {
            error_log("CSRF Attempt! broken token from header/ajax");
            return false;
        }
    } else
    if (($source == 'get') && ($method == 'classic')) {
        if ($_SESSION['token'] == $token) {
            $_SESSION['token_' . $token] = true;
        } else {
            error_log("CSRF Attempt! broken token from get/classic");
            return false;
        }
    } else
    if ($token == '') {
        $token = md5(time() . $token_key);
        $_SESSION['token'] = $token;
        if (preg_match('/\?/i', $_SERVER['REQUEST_URI'])) {
            $token = "&csrf_token=" . $token;
        } else {
            $token = "?csrf_token=" . $token;
        }
        $url = $_SERVER['REQUEST_URI'] . $token;
        include dirname(__FILE__)."/newtoken.view.php";
        die;
    }
    return true;
}
