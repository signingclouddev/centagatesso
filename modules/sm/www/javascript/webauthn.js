
// success retrive cert, display information 
function fetchCredentials(centoken) {
JSON.stringify({"username": regUsername,"cenToken": centoken})
	$.post( baseUrl + '/webauthn/registered/keys/' + loginUsername, 
            JSON.stringify({"username": regUsername,"cenToken": centoken}
        ), null, 'json').done(function(data) {
                
                codeValue = JSON.parse(data.code);
                if(codeValue === 0) {
                    preRegisterFidoToken(JSON.parse(data.object));   
                }
                else {
                    document.getElementById('spinner').style.display='none';
                    document.getElementById('fade').style.display='none';
                    showNotification('1',returnInfo.errorMsg);
                }
	});
}

function deleteCredential(centoken) {

    $.post( baseUrl + '/webauthn/delete/credential/' + loginUsername, JSON.stringify(
            {
                "credentialId": unregSerialNumber,
                "cenToken": centoken,
                "username" : regUsername
            }) 
    , null, 'json').done(function(data) {
                codeValue = JSON.parse(data.code);
                if(codeValue === 0) {
                    console.log(data.object);
                    console.log(JSON.parse(data.object));
                    response = JSON.parse(data.object);  
                    console.log(response.status);
                    if ( response.status !== "ok" ) {
                        document.getElementById('spinner').style.display='none';
                        document.getElementById('fade').style.display='none';
                        showNotification('1',response.errorMsg);
                    } else {
                        alert("unregisterToken");
                        generateAuthToken(unregisterToken);
                    }
                }
                else {
                    document.getElementById('spinner').style.display='none';
                    document.getElementById('fade').style.display='none';
                    showNotification('1',response.errorMsg);
                }
    });
}

//fido registration
function sendRegistration(centoken) {

	//发送注册请求，浏览器与fido交互，返回注册结果
        //send registration request, browser and fido token exchange data and return result
        
	makeCredentialOptions(regUsername, regUsername, centoken).then(function(returnInfo) {

		if (returnInfo.successMsg != undefined) {
			//fido registration sucessful
                        generateAuthToken ( fetchCredentials );
		} else {
			//fido failed return error message
                        document.getElementById('spinner').style.display='none';
                        document.getElementById('fade').style.display='none';
                        showNotification('1',returnInfo.errorMsg);
		}   
	});
}

//fido authentication
function verifyRegistration(fidoReturnObject) {

        var username = $( "#loginform\\:username" ).val() ;
        parameters = JSON.parse(fidoReturnObject);   
	getAssertion(parameters,username).then(function(returnInfo) {
		if (returnInfo.successMsg !== undefined) {
			//fido认证成功
//                        var fidoLink = $( "#loginform\\:fidoLinkHidden" ) ;
//                        if ( fidoLink )
//                            fidoLink.click ( ) ;
                        
		} else {
			//fido认证失败,后期记录日志
                        console.log(returnInfo.errorMsg);
			//window.location = "loginuserpass.php?err=37000" ;
		}
	});

}

//发送客户端返回异常信息到后台
function clientExceptionMesg(errMesg) {
	var username = $('#userId').val();
	$.post({
		url : 'client/exceptionmesg',
		type : 'POST',
		data : JSON.stringify({
			errorMessage : errMesg,
			username : username
		}),
		dataType : 'json'
	});
}


//function assignButtons() {
//	$("#credential-button").click(function() {
//		sendRegistration();
//	});
//
//	$("#authenticate-button").click(function() {
//		verifyRegistration();
//	});
//}

