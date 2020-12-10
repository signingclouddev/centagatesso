
var consolePrint = false;

// send registration request
function makeCredentialOptions(username, displayname, centoken) {
	return new Promise(function(resolve, reject) {
		$.ajax({
			type : 'POST',
			url : baseUrl + '/webauthn/attestation/options/' +loginUsername,
			data : JSON.stringify({
				username : username,
				displayName : displayname,
                                cenToken : centoken        
			}),
                        contentType: "application/json; charset=utf-8",
			dataType : 'json',
			success : function(data) {
                                codeValue = JSON.parse(data.code);
                                if(codeValue === 0) {
                                    resolve(makeCredential( JSON.parse(data.object), centoken));
                                }
                                else {
                                    showNotification('1',data.message ); 
                                }
			},
			error : function(xhr) {
				reject("Error message: " + xhr.status + ", " + xhr.statusText);
			}
		});
	});
}

//handle response data and data exchange with fido token
function makeCredential(options, centoken) {
	return new Promise(function(resolve, reject) {
		var returnInfo = {};
		if (options.status != "ok") {
			returnInfo.errorMsg = options.errorMessage;
			resolve(returnInfo);
		}
		
		var makeCredentialOptions = {};
		// Required parameters
		makeCredentialOptions.rp = options.rp;
		makeCredentialOptions.user = options.user;
		makeCredentialOptions.user.id = base64url.decode(options.user.id);
		makeCredentialOptions.challenge = base64url.decode(options.challenge);
		makeCredentialOptions.pubKeyCredParams = options.pubKeyCredParams;

		// Optional parameters
		if ('timeout' in options) {
			makeCredentialOptions.timeout = options.timeout;
		}
		if ('excludeCredentials' in options) {
			makeCredentialOptions.excludeCredentials = credentialListConversion(options.excludeCredentials);
		}
		if ('authenticatorSelection' in options) {
			makeCredentialOptions.authenticatorSelection = options.authenticatorSelection;
		}
		if ('attestation' in options) {
			makeCredentialOptions.attestation = options.attestation;
		}
		if ('extensions' in options) {
			makeCredentialOptions.extensions = options.extensions;
		}
		if(consolePrint){ //console log
		    (makeCredentialOptions);
		}

		// check the browser support navigator.credentials.create
		if (typeof navigator.credentials.create !== "function") {
			returnInfo.errorMsg = "Browser does not support credential creation";
			resolve(returnInfo);
		}
		
		navigator.credentials.create({
			"publicKey" : makeCredentialOptions
		}).then(function(attestation) {
			var publicKeyCredential = {};
			if ('id' in attestation) {
				publicKeyCredential.id = attestation.id;
			}
			if ('type' in attestation) {
				publicKeyCredential.type = attestation.type;
			}
			if ('rawId' in attestation) {
				publicKeyCredential.rawId = attestation.rawId;
			}
			if ('response' in attestation) {
				var response = {};
				response.clientDataJSON = base64url.encode(attestation.response.clientDataJSON);
				response.attestationObject = base64url.encode(attestation.response.attestationObject);
				publicKeyCredential.response = response;
				publicKeyCredential.username = options.user.name;
				resolve(makCredentialResult(publicKeyCredential, centoken));
			} else {
				returnInfo.errorMsg = "Make Credential response lacking 'response' attribute";
				resolve(returnInfo);
			}
		}).catch(function(err) {
			var errMesg = err.message;
			if (errMesg != null && errMesg != "") {
				returnInfo.errorMsg = errMesg;
				resolve(returnInfo);
			} else {
			    returnInfo.errorMsg = "unknown error";
                resolve(returnInfo);
			}
		});
	});
}

// complete registration
function makCredentialResult(publicKeyCredential, centoken) {
	return new Promise(function(resolve, reject) {
		var returnInfo = {};
		$.post(baseUrl + '/webauthn/attestation/result/'+ loginUsername + "/" + centoken, JSON.stringify(publicKeyCredential)
			, null, 'json').done(function(data) {
                        
                        codeValue = JSON.parse(data.code);
                        if(codeValue === 0)  {
                            parameters = JSON.parse(data.object);    
                            if (parameters.status != "ok") {
                                    returnInfo.errorMsg = parameters.errorMessage;
                                    resolve(returnInfo);
                            }
                            if(consolePrint){ // console log
                                (parameters);
                            }
                            if ('success' in parameters && 'message' in parameters) {
                                    returnInfo.successMsg = parameters.message;
                                    resolve(returnInfo);
                            }
                        }
                        else {
                            showNotification('1',data.message ); 
                        }
                });
	});
}


//assertion request
function getAssertion(parameters,username) {
	return new Promise(function(resolve, reject) { 
//		$.ajax({
//			type : 'POST',
//			url : baseUrl + '/webauthn/assertion/options',
//			async : false,
//			data : JSON.stringify({
//				username : username
//			}),
//			dataType : 'json',
//			success : function(parameters) {
				resolve(analysisAssertion(parameters, username));
//			},
//			error : function() {
//				reject("The request failed");
//			}
//		});
	});
}


// analyse response data from the assertion
function analysisAssertion(parameters, username) {
	return new Promise(function(resolve, reject) {
		var returnInfo = {};
		if (parameters.status != "ok") {
			returnInfo.errorMsg = parameters.errorMessage;
			resolve(returnInfo);
		}
		var requestOptions = {};
		requestOptions.challenge = base64url.decode(parameters.challenge);
		if ('timeout' in parameters) {
			requestOptions.timeout = parameters.timeout;
		}
		if ('rpId' in parameters) {
			requestOptions.rpId = parameters.rpId;
		}
		if ('allowCredentials' in parameters) {
			requestOptions.allowCredentials = credentialListConversion(parameters.allowCredentials);
		}
		if(consolePrint){ // console log
		    (requestOptions);
		}

		if (typeof navigator.credentials.get !== "function") {
			returnInfo.errorMsg = "Browser does not support credential lookup";
			resolve(returnInfo);
		}

		navigator.credentials.get({
			"publicKey" : requestOptions
		}).then(function(assertion) {
			var publicKeyCredential = {};
			if ('id' in assertion) {
				publicKeyCredential.id = assertion.id;
			}
                        console.log("fido id success");

			if ('type' in assertion) {
				publicKeyCredential.type = assertion.type;
			}
 console.log("fido type success");

			if ('rawId' in assertion) {
				publicKeyCredential.rawId = assertion.rawId;
			}
 console.log("fido rawID success");

			if ('response' in assertion) {
				var response = {};
				response.clientDataJSON = base64url.encode(assertion.response.clientDataJSON);
				response.authenticatorData = base64url.encode(assertion.response.authenticatorData);
				response.signature = base64url.encode(assertion.response.signature);
				response.userHandle = base64url.encode(assertion.response.userHandle);
				publicKeyCredential.response = response;
				publicKeyCredential.username = username;
				//resolve(finishAssertion(publicKeyCredential));
                                
                                $( "#fidoPublicKeyCredential" ).val(JSON.stringify(publicKeyCredential));
                               // var fidoLink = $( "#loginform\\:fidoLinkHidden" ) ;
                                var fidoform = $("#loginform");
                                var fidocred = $("#fidoPublicKeyCredential").val();

                                console.log("fido cred = "+fidocred);
                     
                                 fidoform.submit();
          
                                if ( fidoform )
                                    fidoform.click ( ) ;
                                console.log("fido click");

			}
		}).catch(function(err) {
                        console.log("fido error");
			var errMesg = err.message;
			if (errMesg != null && errMesg != "") {
				returnInfo.errorMsg = errMesg;
				resolve(returnInfo);
			}
		});
	});
}

//complete assertion
function finishAssertion(publicKeyCredential) {
	return new Promise(function(resolve, reject) {
		var returnInfo = {};
		$.post('assertion/result', JSON.stringify(publicKeyCredential),
			null, 'json').done(function(resp) {
			if (resp.status != "ok") {
				returnInfo.errorMsg = resp.errorMessage;
				resolve(returnInfo);
			}
			if(consolePrint){ // console log
			    (resp);
			}
			if ('success' in resp && 'message' in resp) {
				returnInfo.successMsg = resp.message;
				returnInfo.handle = resp.handle;
				resolve(returnInfo);
			}
		});
	});
}

function credentialListConversion(list) {
	var result = [];
	for (var i = 0; i < list.length; i++) {
		var credential = {};
		credential.type = list[i].type;
		credential.id = base64url.decode(list[i].id);
		if ('transports' in list) {
			credential.transports = list.transports;
		}
		result.push(credential);
	}
	return result;
}
