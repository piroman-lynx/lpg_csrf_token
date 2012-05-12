<?php

function getallheaders() {
    $headers = array();
    foreach($_SERVER as $h=>$v)
	if(ereg('HTTP_(.+)',$h,$hp))
	    $headers[$hp[1]]=$v;
    return $headers;
}
