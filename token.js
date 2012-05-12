
$(document).ready(function(){
    
    function get_token(){
        get = document.location.search.replace('?','');
        params = get.split('&');
        for (i=0; i<params.length; i++){
            param_vars = params[i].split('=');
            if (param_vars[0]=='csrf_token'){
                return param_vars[1];
            }
        }
        if (console){
            console.log('can\'t find token!');
        }else{
            alert('can\'t find token!');
        }
        return null;
    }
    
    var token = get_token();
    
    $.ajaxSetup({
        headers: {
            'X-Csrf-Token':token
        }
    });

});
