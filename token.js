
function csrf_token_init(){
    function get_token(){
	get = document.location.search.replace('?','&');
	params = get.split('&');
	for (i=0; i<params.length; i++){
    	    param_vars = params[i].split('=');
    	    if (param_vars[0]=='csrf_token'){
        	return param_vars[1];
    	    }
	}
	return null;
    }

    function setup(){
	$.ajaxSetup({
	    headers: {
    		'X-Csrf-Token':token
	    }
	});
    }

    var token = get_token();

    setup();

    $(document).ready(function(){
	$('body').ajaxSuccess(function(){
	    tok = get_token();
	    if (tok){
		token = tok;
		setup();
		return true;
	    }
	    cookies=document.cookie.split(';');
	    $.each(cookies, function(k,v){
		if (/XCSRFTOKEN/.test(v)){
		    s=v.split('=');
		    token = s[1];
		    setup();
		}
	    });
	});
    });
}
csrf_token_init();