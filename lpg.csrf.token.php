<?php

function create_token($token_key){
    //перемнные $_SERVER добавлены для повышения уникальности токена
    $token = md5(time() . $_SERVER['REMOTE_ADDR']. $_SERVER['HTTP_USER_AGENT'] . $token_key);
    return $token;
}

function lpg_csrf_token($token_key, $loading_text = "Loading...") {
    if (defined('TOKEN_DISABLED')){
	return true;
    }

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
        error_log("[LPG_CSRF_TOKEN] Warning: CSRF Attempt! Ajax atack form site: " . $_SERVER['HTTP_REFERER']);
        return false;
    } else
    if (($source == 'header') && ($method == 'ajax')) {
        if ($_SESSION['token_' . $token] === true) { //штатная ситуация - новый токен
    	    //убиваем старый токен
    	    foreach ($_SESSION as $name=>$value){
    		if (preg_match('/^oldtoken_/',$name)){
    		    unset($_SESSION[$name]);
    		}
    	    }

    	    //ставим текущий как старый
    	    $_SESSION['oldtoken_' . $token] = true;
    	    unset($_SESSION['token_' . $token]);

    	    //выбаем новый
    	    $newtoken = create_token($token_key);
    	    setcookie('XCSRFTOKEN',$newtoken,0,'/');
    	    $_SESSION['token_'.$newtoken] = true;

    	    return true;
        } else if ($_SESSION['oldtoken_' . $token] === true) { //нештатаная ситуация - старый токен
    	    error_log("[LPG_CSRF_TOKEN] Notice: old token");
    	    //выдаем новый токен
    	    $newtoken = create_token($token_key);
    	    setcookie('XCSRFTOKEN',$newtoken,0,'/');
    	    $_SERVER['token_'.$newtoken]=true;
    	    
    	    return true;
        } else {
    	    error_log("[LPG_CSRF_TOKEN] Warning: CSRF Attempt! broken token from header/ajax".var_export($_SESSION,true));
            return false;
        }
    } else
    if (($source == 'get') && ($method == 'classic')) {
        if ($_SESSION['token'] == $token) {
            $_SESSION['token_' . $token] = true;
        } else {
            error_log("[LPG_CSRF_TOKEN] Warning: CSRF Attempt! broken token from get/classic".var_export($_SESSION,true));
            return false;
        }
    } else
    if ($token == '') {
	$token = create_token($token_key);
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
