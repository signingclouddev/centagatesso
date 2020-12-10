/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function trim(str){
    if ((typeof (str) !== 'undefined') && (str !== null)) {
        return str.replace(/^\s*([\S\s]*?)\s*$/, '$1');
    } else{
        return "";
    }
}
function focusText(id){
    document.getElementById(id).focus();
    document.getElementById(id).select();
}
function focusCbo(id){
    document.getElementById(id).focus();
}

function allownumbers(e) {
    //var key = window.event ? e.keyCode : e.which;
    var key = (e.which) ? e.which : event.keyCode;
            var keychar = String.fromCharCode(key);
            var reg = new RegExp("[0-9]")
            if (key == 8 || key == 46 || key == 16) { //backspace, left, right, del, shift
        keychar = "8";
    }
    if (key == 46) {//46= "dot", not user why it will passed the regexp
        return false;
    }
    return reg.test(keychar);
}

function generateAuthToken ( fnCallBack ) {
    var dt = new Date () ;
    var ajax = dt.getTime () ;    
    var url = "./restfulUtil?ajax=" + ajax + "&mode=gentoken" ;
 //   alert(url);
    $.ajax({
        url: url,
        cache : false,
        type: "POST",
        timeout: 2000
    }).done(function ( output ){
    //    alert(output);
        var response = output.split ( "|" ) ;
        response[1] = trim ( response[1] ) ;
        
        if ( response[0] == "1" ) {
            //success
           // console.log("success. centoken="+ trim(response[1]));
            //"connectRestful(centoken)" is the implementation methods that should be write on each file that call the generateAuthToken
            fnCallBack(trim(response[1]));
        } else if ( trim ( response[0] ) == "-1" ) {
            window.location = "./logout?errMsg=error-invalid-session" ;
        } else if ( trim ( response[0] ) == "-2" ) {
            window.location = "./index.jsp?c=nopermission&m=" ;
        } else if ( trim ( response[0] ) == "-3" ) {
            window.location = "./logout?errMsg=possible-attack" ;        
        } else {
            window.location = "./logout?errMsg=error-invalid-session" ;
        }
    }).fail(function (jqXHR, textStatus, errorThrown){                
        // log the error to the console
        console.error(
            "The following error occured during generate auth token: "+
            textStatus, errorThrown
        );
    });
}