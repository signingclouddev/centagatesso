var loginStateCheckService;

function validateLogin ( )
{
    var emailField = $( "#username" ) ;
    var passwordField = $( "#password" ) ;
    var passwordless = $( "#passwordless" ) ;
    var loginform = $( "#loginform" ) ;

    if ( emailField !== null && passwordField !== null )
    {
        var email = emailField.val ( ) ;
        var password = passwordField.val ( ) ;

        
        if ( email === "" )
        {
            /* Email is empty */
            emailField.focus ( ) ;
            msgbox ( 'input_empty_dialog' , "You have not entered your username" ) ;
        }
        else if ( !password )
        {
           console.log("passwordless == true");
         
           passwordless.val("1");

          var p=passwordless.val ();
          console.log("passwordless value =="+p) ;
        
          return true; 
       
            /* Password ie smpty */
           // passwordField.focus ( ) ;
           //   msgbox ( 'input_empty_dialog' , "You have not entered your password" ) ;
        }
        else
        {
          

           passwordless.val("0");

          var p=passwordless.val ();
          console.log("passwordless value =="+p) ;
               if ( passwordless )
            {
                        passwordless.val ( "0" ) ;
            }

    
    if ( loginForm )
    {
        console.log("submit login");
        loginForm.submit ( ) ;
    }

        return false ;

        }
    }

    return false ;
}

function validateSmsOtp ( )
{
    var smsOtpField = $( "#sms_otp_field" ) ;
    
    if ( smsOtpField )
    {
        var smsOtp = smsOtpField.val ( ) ;
        
        if ( smsOtp === "" )
        {
            smsOtpField.focus ( ) ;
            msgbox ( 'input_empty_dialog' , "You have not entered the passcode" ) ;
        }
        else
            return true ;
    }
    
    return false ;
}

function validateOtp ( )
{
    var otpField = $( "#otp_field" ) ;
    
    if ( otpField )
    {
        var otp = otpField.val ( ) ;
        
        if ( otp === "" )
        {
            otpField.focus ( ) ;
            msgbox ( 'input_empty_dialog' , "You have not entered the OTP" ) ;
        }
        else
            return true ;
    }
    
    return false ;
}

function requestSmsOtp ( )
{
    var requestSmsOtpField = $( "#request_sms_otp" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestSmsOtpField )
    {
        requestSmsOtpField.val ( "1" ) ;
        var reqSms=requestSmsOtpField.val ();
        console.log("SMS=="+reqSms);

    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
    return false ;
}

function requestOtpChallenge( )
{
    var requestOtpChallengeField = $( "#request_otp_challenge" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestOtpChallengeField )
    {
        requestOtpChallengeField.val ( "1" ) ;
    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
	return false ;
}


function requestFIDO( )
{
    var requestFIDO = $( "#request_fido" ) ;
    var loginForm = $( "#loginform" ) ;

    if ( requestFIDO )
    {
        requestFIDO.val ( "1" ) ;
    }

    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }

        return false ;
}


function requestOtp ( )
{
    var requestOtpField = $( "#request_otp" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestOtpField )
    {
        requestOtpField.val ( "1" ) ;
    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
	return false ;
}

function requestMobileSoftCert ( )
{
    var requestMobileSoftCertField = $( "#request_mobile_soft_cert" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestMobileSoftCertField )
    {
        requestMobileSoftCertField.val ( "1" ) ;
    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
	return false ;
}

function requestMobilePush ( )
{
    var requestMobilePushField = $( "#request_mobile_push" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestMobilePushField )
    {
        requestMobilePushField.val ( "1" ) ;
    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
	return false ;
}

function requestQrCode ( )
{
    var requestQrCodeField = $( "#request_qr_code" ) ;
    var loginForm = $( "#loginform" ) ;
    
    if ( requestQrCodeField )
    {
        requestQrCodeField.val ( "1" ) ;
    }
    
    if ( loginForm )
    {
        loginForm.submit ( ) ;
    }
	
	return false ;
}

function showQrOtpInput ( )
{
	var qrClickRow = $( "#qr_click_row" ) ;
	var qrEnterOtpRow = $( "#qr_enter_otp_row" ) ;
	var qrOtpInputRow = $( "#qr_otp_input_row" ) ;
	var crSubmitButton = $( "#submit_cr_otp_button" ) ;
	var otpField = $( "#otp_field" ) ;
	
	if ( qrClickRow )
		qrClickRow.hide ( ) ;
	
	if ( qrEnterOtpRow )
		qrEnterOtpRow.show ( ) ;
	
	if ( qrOtpInputRow )
	{
		qrOtpInputRow.show ( ) ;
		
		if ( otpField )
			otpField.focus ( ) ;
	}

	if ( crSubmitButton )
		crSubmitButton.show ( ) ;
	
	clearInterval ( loginStateCheckService ) ;
	
	return false ;
}
                   
