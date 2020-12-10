<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
	include_once ('/var/www/html/simplesamlphp/modules/sm/lib/Auth/Source/Config.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php
			$isBelowIE8 = preg_match('/msie [2-7]/i',$_SERVER['HTTP_USER_AGENT']) ;
		?>
		<title><?php echo $this -> t ( '{app:title}' ) ?></title>		
		<link href="styles/ui.css" rel="stylesheet" media="all" />
		<link href="styles/base/jquery.ui.all.css" rel="stylesheet" />

                <meta charset="utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/>


  	<!-- Custom Theme files -->
	<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" media="all" />
	<!-- //Custom Theme files -->

	<!-- web font -->
	<link href="//fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet"/>
	<!-- //web font -->

		<script type="text/javascript" src="javascript/jquery-1.11.1.js"></script>
		<script type="text/javascript" src="javascript/jquery-ui-1.10.4.js"></script>
		<script type="text/javascript" src="javascript/ui/jquery.ui.dialog.min.js"></script>
		<?php
			if ($isBelowIE8 == 1)
			{
				echo "<script type=\"text/javascript\" src=\"javascript/ui_ie6.js\"></script>";
			}
			else
			{
				echo "<script type=\"text/javascript\" src=\"javascript/ui.js\"></script>";
			}
		?>
	 <script type="text/javascript" src="javascript/base64url-arraybuffer.js"></script>	
<script type="text/javascript" src="javascript/webauthn.js"></script>
<script type="text/javascript" src="javascript/fidoapi.js"></script>
		<script type="text/javascript" src="javascript/login.js"></script>
		<script type="text/javascript" src="javascript/json2.js"></script>
		<script type="text/javascript">

                
			function requestPki ( )
			{
				var loginForm = $( "#loginform" ) ;
                            	
				if ( loginForm )
				{
					var queryString = window.location.search ;
				        var qrcode = $( "#request_qr_code" ).val;
 	                               if (qrcode != 0){ 
					<?php
				 	
						$authMethod = isset($_SESSION["auth_method"]) ? $_SESSION["auth_method"]:"";
						$login_mode = isset($_SESSION["login_mode"]) ? $_SESSION["login_mode"]:"";
                                                //error_log("____________________PKI______________________".$authMethod." ___ ".$login_mode);

                                                //$globalConfig = SimpleSAML_Configuration::getInstance ( ) ;
						$config = SimpleSAML_Configuration::getInstance ();
						$email = isset($_SESSION['email']) ? $_SESSION['email']:"";
						$authToken = isset($_SESSION['authToken']) ? $_SESSION['authToken']:"";
						$secureUrl = $config->getString('ws.baseurl', 'http://localhost:8080')."/centagate/PKILoginServlet?username=".$email."&authToken=".$authToken."&saml_call=saml_call&err=no" ;
                                                if (empty($authMethod)){                                                
                                                        //error_log("____________ set auth method to PKI__________");
                                                       // $_SESSION["auth_method"]="PKI";
                                                       // $_SESSION["login_mode"]=1;
                                                 }

					?>
			                console.log("PKI LOGIN");		
      			    		var secureSite = "<?php echo $secureUrl ; ?>" + queryString ;
					//document.location=secureSite;
				        //	loginForm.attr ( "action" , secureSite ) ;
					//loginForm.submit ( ) ;
                                       window.open(secureSite);

                                       var requestPki = $( "#request_pki_code" ) ;
                                       requestPki.val("1");
                                       console.log("PKI REQUEST VALUE ="+$( "#request_pki_code" ).val());

                                       var loginForm = $( "#loginform" ) ;
                                       loginForm.submit();

                              }else{
                                              console.log("ELSE"); 
}
                                      
				}
			}
                        

			<?php
			if ( $isBelowIE8 == 1)
			{
				//due to img component unable to display base64 data in ie6 and 7, so cache image is not availabe
				?>				
				function requestImage ( )
				{
					$( '#secImage' ).html ( "<img src='security_image.php?email=" + $("#email").val() + "' style='width:185px;height:125px;'></img>" ) ;
					$( "#password" ).removeAttr ( "disabled" ) ;
					$( "#password" ).focus ( ) ;
				}
				
				function requestCacheImage( )
				{
					$( '#secImage' ).html ( "<img src='security_image.php?email=" + $("#email").val() + "' style='width:185px;height:125px;'></img>" ) ;
				}
				<?php
			}else{
				//only ie8 and above have cache image feature
				?>
				function requestImage ( )
				{
					jQuery.ajax ( {
						type: "POST",
						url: "security_image.php",           
						data: $( "#loginform" ).serialize ( ),
						statusCode:
						{
							200: function ( response ) { },
							400: function ( response ) { },
							401: function ( response ) { },
							403: function ( response ) { },
							404: function ( response ) { }
						},
						success: function ( data , status , jqXHR )
						{
							if ( data === "" )
							{
								$( '#secImage' ).html ( "<img src='images/qmark.png'></img>" ) ;
							}
							else
							{
								$( '#secImage' ).html ( "<img src='data:image/jpeg;base64," + data + "'' style='width:185px;height:125px;'></img>" ) ;
							}
						  
							$( "#password" ).removeAttr ( "disabled" ) ;
							$( "#password" ).focus ( ) ;

							if ( jqXHR && jqXHR.readystate != 4 )
							{
								jqXHR.abort ( ) ;
							}
						},
						error: function ( jqXHR , status )
						{
							// error handler
							var statusCode = jqXHR.status ; //200
							var head = jqXHR.getAllResponseHeaders ( ) ; //Detail header info

							//alert("The following error occured: " + statusCode + "\n" + head)
							console.error ( "The following error occured: " + status , jqXHR ) ;
						}
					} ) ;
				}
				
				function requestCacheImage( )
				{
					jQuery.ajax ( {
						type: "POST",
						url: "get_session_image.php",           
						contentType: "application/json; charset=utf-8",   
						statusCode:
						{
							200: function ( response ) { },
							400: function ( response ) { },
							401: function ( response ) { },
							403: function ( response ) { },
							404: function ( response ) { }
						},
						success: function ( data , status , jqXHR )
						{
							if ( data === "" )
							{
								$( '#secImage' ).html ( "<img src='images/qmark.png'></img>" ) ;
								
								return false ;
							}
							else
							{
								$( '#secImage' ).html ( "<img src='data:image/jpeg;base64," + data + "' style='width:185px;height:125px;' />" ) ;
							}
							
							if ( jqXHR && jqXHR.readystate != 4 )
							{
								jqXHR.abort ( ) ;
							}
						},
						error: function ( jqXHR , status )
						{
							// error handler
							var statusCode = jqXHR.status ; //200
							var head = jqXHR.getAllResponseHeaders ( ) ; //Detail header info

							//alert("The following error occured: " + statusCode + "\n" + head)
							console.error ( "The following error occured: " + status , jqXHR ) ;
						}
					} ) ;
				}

				<?php
			}
			?>
								
			function enablePass ( )
			{
				var email = $.trim ( $( '#email' ).val ( ) ) ;
				
				if ( email.length > 0 )
				{
					$( '#secImage' ).html ( "<img style='margin-left:50px;margin-top:25px;' src='images/spinner-small.gif' />" ) ;
					
					requestImage ( ) ;
				}
				else
				{
					$( "#password" ).attr ( "disabled" , "disabled" ) ;
				}
			}
			  
			function disablePass ( )
			{
				$( "#password" ).val ( "" ) ;
				$( "#password" ).attr ( "disabled" , "disabled" ) ;

				$( '#secImage' ).html ( "<img src='images/qmark.png'></img>" ) ;
			}
			
			$( document ).ready ( function ( )
			{


                        console.log("onready doc");


                       <?php 
                          if ($_SESSION["stepupAuth"]=="On" && isset($_SESSION["email"])){
$_SESSION["stepupAuth"]="Off";
?>

  window.onload = function(){

  var form=document.getElementById('loginform');

 var input = document.createElement('input');
    input.setAttribute('name', 'login_button');
    input.setAttribute('value', 'Log In');
    input.setAttribute('type', 'hidden');

    form.appendChild(input);//append the input to the form

 form.submit();
}
<?php
}
?>
                        <?php if ( isset($_SESSION['email']) && isset($_SESSION['auth_method']) && isset($_SESSION['authToken'])) {
				echo "const interval = setInterval(function() {refreshAuthState();},1000);";}?>


                                                                               function refreshAuthState ( )
												{
                                                                                 var url="./checkstate.php?"+"<?php $email = isset($_SESSION['email']) ? $_SESSION['email']:"";$auth_method = isset($_SESSION['auth_method']) ? $_SESSION['auth_method']:"";$authToken = isset($_SESSION['authToken']) ? $_SESSION['authToken']:"";echo "email=". $email. "&auth_method=". $auth_method."&authToken=". $authToken;?>";

										 console.log(url);
			
                                                                                 var dt = new Date ( ) ;
													var ajax = dt.getTime ( ) ;
													  jQuery.ajax ( {
                                                                                                           type: "POST",
                                                                                                          url: url,
                                                                                                         data: $( "#loginform" ).serialize ( ),
                                                statusCode:
                                                {
                                                        200: function ( response ) { },
                                                        400: function ( response ) { },
                                                        401: function ( response ) { },
                                                        403: function ( response ) { },
                                                        404: function ( response ) { }
                                                },
                                                success: function ( data , status , jqXHR )
                                                {
                                                       
                                                           console.log("data====="+data);

                                                           var response = data.split ( "|" ) ;

                                                           // console.log("response="+response);


                                                                                                                if ( response [ 0 ] == "1" )
                                                                                                                {
                                                                                                                       console.log("SUCCESS LOGIN_____");
                                                                                                                        /* Login successful */
                                                                                                                        window.location = "loginuserpass.php?m=1&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
                                                                                                                }
                                                },
                                                error: function ( jqXHR , status )
                                                {
                                                        // error handler
                                                        var statusCode = jqXHR.status ; //200
                                                        var head = jqXHR.getAllResponseHeaders ( ) ; //Detail header info

                                                        //alert("The following error occured: " + statusCode + "\n" + head)
                                                        console.error ( "The following error occured: " + status , jqXHR ) ;
                                                }
                                        } ) ;

		 	}										
										

	
				<?php
			
                                       //error_log("_____________________email".$_SESSION["authToken"]."____________");                	
                                      	if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
					{
						?>
							setTimeout ( function ( )
							{
								$( "#reset_login_button" ).click ( ) ;
							} , 120000 ) ;
							
							var smsOtpField = $( "#sms_otp_field" ) ;
							
							if ( smsOtpField )
								smsOtpField.focus ( ) ;
								
							var otpField = $( "#otp_field" ) ;
							
							if ( otpField )
								otpField.focus ( ) ;
						<?php
					}
					else
					{
						?>
							var emailField = $( "#email" ) ;
							
							emailField.focus ( ) ;
						<?php
					}
				?>
				
				<?php if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true ) echo "requestCacheImage( );"; ?>
				
				<?php
					if ( isset ( $_SESSION [ "num_of_2fa" ] ) && $_SESSION [ "num_of_2fa" ] < 4 )
					{
						?>
							$( '#scroll-pane' ).css ( {
								overflow: 'hidden'
							} ) ;
							$( '#scroll-content' ).css ( {
								width: '550px'
							} ) ;
						<?php
					}
					else if ( isset ( $_SESSION [ "num_of_2fa" ] ) && $_SESSION [ "num_of_2fa" ] >= 4 )
					{
						?>
							$( '#scroll-content' ).css ( {
								width: '<?=($_SESSION [ "num_of_2fa" ] + 1) * 118?>px'
							} ) ;
						<?php
					}
				?>
			} ) ;
		</script>
	</head>

	<body   >				
		<div class="bg-layer"  style=" <?php echo $_SESSION["body_skin"]; ?>" > 
                        <img style="width:300px; height:20px; padding:40px;display: block;margin-left: auto;margin-right: auto;" src="images/centagate-logo-ori.png" /> 
			
		
			<?php				
				if ( isset ( $_REQUEST [ "err" ] ) && $_REQUEST [ "err" ] !== "" )
				{
					$ignore_error = false ;

					if ( isset ( $_SESSION [ "ignore_error" ] ) && $_SESSION [ "ignore_error" ] === true )
						$ignore_error = true ;
					
					if ( ! $ignore_error )
					{
					    $error_list = [
					        "centagateDown" => "Unable to connect to CENTAGATE",
					        "defaultPassword" => "You are using default password. Please change your password at CENTAGATE self service before proceed to log in",
					        "expiredPassword" => "Your password is expired. Please change your password at CENTAGATE self service before proceed to log in",
					        "riskDetected" => "Login is not allowed. Risk is detected on your login",
					        "serverError" => "Internal server error. Please contact administrator",
					        "spNotRegistered" => "The service provider is not registered under the company",
                            "10001" => "Permission not allowed",
                            "10002" => "Invalid input",
                            "10003" => "DB protection error",
                            "10004" => "DB error",
                            "10007" => "Send SMS failed",
                            "10011" => "Cryptographic error",
                            "20002" => "Company not found",
                            "20007" => "Company is not active",
                            "22002" => "User not found",
                            "22003" => "User password not found",
                            "22004" => "User is not active",
                            "22005" => "User mobile not found",
                            "22023" => "Please wait before requesting another SMS code",
                            "23001" => "Invalid credentials",
                            "23002" => "Please wait before requesting for another SMS OTP",
                            "23004" => "Generate SMS OTP failed",
                            "23005" => "Generate OTP challenge failed",
                            "23006" => "Push mobile soft certificate failed",
                            "23007" => "Authentication is pending",
                            "23008" => "Invalid SMS OTP",
                            "23009" => "SMS OTP is expired",
                            "23010" => "SMS OTP has not been generated",
                            "23012" => "Certificate is revoked",
                            "23015" => "Push mobile audio pass failed",
                            "23016" => "Authentication request not found",
                            "23017" => "Authentication method is not enabled",
                            "23018" => "Invalid user session",
                            "23019" => "Authentication request owner not match",
                            "23020" => "Authentication request rejected. API is not come from trusted IP address list",
                            "23021" => "Authentication request rejected. User's group is not allowed to access this API",
                            "23022" => "Authentication request rejected. Invalid API signature",
                            "23023" => "Authentication request rejected. Request expired",
                            "23024" => "API not found",
                            "23025" => "Authentication API error",
                            "23026" => "Authentication request rejected",
                            "23027" => "Authentication request rejected. Country code not found",
                            "23028" => "Authentication request rejected. Timezone not match with requester",
                            "23029" => "Authentication request rejected. Integration key not belong to LDAP app",
                            "23030" => "Authentication request rejected. AD proxy invalid setting",
                            "23031" => "Authentication request rejected. AD proxy invalid credentials",
                            "23032" => "Authentication request rejected. AD proxy connection failed",
                            "23033" => "Authentication request rejected. AD proxy user require change password on next logon",
                            "23034" => "Push mobile CR OTP failed",
                            "23035" => "OTP token out of sync",
                            "230001" => "Company reached the maximum allowed transaction",
                            "25009" => "Insufficient SMS credit",
                            "29018" => "SMS has been requested",
                            "36004" => "User device not active",
                            "37000" => "FIDO Login Error",
                            "23039" => "User has not enabled any 2FA option , please login to centagate and enable at least one 2FA.",
					    ] ;
					    
						$error_code = $_REQUEST [ "err" ] ;
						$error_message = $error_list [ $error_code ] ;
						
						if ( isset ( $error_message ) && strlen ( $error_message ) > 0 )
						{
						    if ( $isBelowIE8 == 1 )
						    {							
						    ?>							
							    <div style="text-align: center;">
								    <div id="error_div" class="page-notification error" style="text-align:left; position: relative;margin: 0 auto;top:0;left:0;width:545px;">
									    <div id="error_div" class="page-notification" style="position: relative;margin: 0 auto;top:0;left:0;width:350px;">
										    <div id="error_resultbox" class="response-msg ui-corner-all">
											    <span id="error_message_text" style="margin-left: -30px; margin-top: 8px" ><?php echo htmlentities ( $error_message ) ; ?></span>
										    </div>
									    </div>
								    </div>
							    </div>
						    <?php
						    }
						    else
						    {							
						    ?>
							    <div id="error_div" class="page-notification error" style="position: relative;margin: 0 auto;top:0;left:0;width:545px;">
								    <div id="error_div" class="page-notification" style="position: relative;margin: 0 auto;top:0;left:0;width:350px;">
									    <div id="error_resultbox" class="response-msg ui-corner-all">
										    <span id="error_message_text" style="margin-left: -30px; margin-top: 8px" ><?php echo htmlentities ( $error_message ) ; ?></span>
									    </div>
								    </div>
							    </div>
						    <?php
						    }
						}
					}
				}
			?>

   	
	
			<div  >
                                                                                                        
                                                                                                         <div class="main-icon" >
                                                                                                            <img src="<?php echo $_SESSION["logo_url"]; ?>" alt="Avatar" style="margin-bottom:10px; border-radius: 50%;width:80px;height:80px;" />
                                                                                                         </div>
                                                                                               

				<div class="loginpanel"> 
				
						<form id="loginform" method="post" style="padding-top:2px; padding-bottom:5px; box-shadow: 0px 24px 25px rgba(36, 43 ,82, 0.2); padding: 13px;" novalidate>
							
							<?php
							if ( ! isset ( $_SESSION [ "multi_step_auth" ] ) || $_SESSION [ "multi_step_auth" ] === false )
								{
                                                                  if ( isset ( $_SESSION [ "pwd" ] ) && $_SESSION [ "pwd" ] === "true" ) {  
							?>
							      <div class="main-icon" >

                                                              <div id="secImage" style="margin-top:5px; ">

                                                                   <img style="margin-top:15px; width:150px; height:120px; "  src="images/qmark.png"/>

                                                              </div>
                                                             </div>

							<?php
							}
          	                                    }
							?>
							
							<?php
								if ( ! isset ( $_SESSION [ "multi_step_auth" ] ) || $_SESSION [ "multi_step_auth" ] === false )
								{
									?>
										
								
                                                                                      
						                                                         <div  style="aligment:center; margin-top:20px; margin-left:7px;" align="center">
									                                       
														<input  style="background-color: #EFEFEF; width:224px;"  type="email" id="email" name="email" placeholder="Username"  onfocus="disablePass();" onblur="enablePass();" <?php if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true ) echo "disabled=\"disabled\"" ; ?> <?php if ( isset ( $_SESSION [ "email" ] ) ) echo "value=\"" . $_SESSION [ "email" ] . "\"" ; ?> />
													
												
                                                                                                </div>

                                                                                               <?php
                                                                                  if (  isset ( $_SESSION [ "pwd" ] ) && $_SESSION [ "pwd" ] === "true" )
                                                                                       { ?> 
                                                                                                <div style="aligment:center; margin-top:10px;  margin-left:7px;" align="center" >
											                        
														<input style="background-color: #EFEFEF; width:224px;" type="password" id="password" placeholder="Password" name="password" <?php if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true ) echo "disabled=\"disabled\" value=\"********\"" ; ?>   />
                                                                                               </div>	
                                                                      <script type="text/javascript">
                                                                               document.getElementById("password").focus();
                                                                      </script>
										<?php } ?>
                                                                              <input type="hidden" id="passwordless" name="passwordless" value="1" />

	        									<table cellspacing="0" cellpadding="0" style="margin-left: 25px">
											<tr>
												<td>
													<?php
														if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
														{
															?>
																<input style="width:240px;margin-left:0px;"  name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_reset}' ) ) ; ?>" />
															<?php
														}
														else
														{
                                                                                                                 
															?>
																<input style="width:240px;margin-left:0px;"   id="login_button" name="login_button" type="submit" onclick="return validateLogin();" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_submit}' ) ) ; ?>" />
															<?php

											}
													?>
												</td>
											</tr>
										</table>
									<?php
								}
							?>
							
							<?php
								foreach ( $this -> data [ 'stateparams' ] as $name => $value )
								{
									echo ( '<input type="hidden" name="' . htmlspecialchars ( $name ) . '" value="' . htmlspecialchars ( $value ) . '" />' ) ;
								}
							?>

							<div id="inputs">
								<?php
									if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true && ( ! isset ( $_SESSION [ "multi_step_auth" ] ) || $_SESSION [ "multi_step_auth" ] === false ) )
									{
										?>
											<div style="height:30px;">&nbsp;</div>
											<label><?php echo htmlentities ( $this -> t ( '{login:subtitle2}' ) ) ; ?></label>
										<?php
									}
									else if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] === true )
									{
										if ( isset ( $_SESSION [ "num_of_2fa" ] ) && $_SESSION [ "num_of_2fa" ] > 0 )
										{
											?>
												<label style="margin-left: 103px; width: 350px"><?php echo htmlentities ( $this -> t ( '{login:subtitle3}' ) ) ; ?></label>
											<?php
										}
										else if ( isset ( $_SESSION [ "num_of_2fa" ] ) )
										{
											?>
												<label style="margin-left: 103px; width: 350px"><?php echo htmlentities ( $this -> t ( '{login:subtitle3_2}' ) ) ; ?></label>
											<?php
										}
									}
								?>
								
								<?php
									if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
									{
										$pki_enabled = isset ( $_SESSION [ "pki_enabled" ] ) && $_SESSION [ "pki_enabled" ] == true ;
										$otp_enabled = isset ( $_SESSION [ "otp_enabled" ] ) && $_SESSION [ "otp_enabled" ] == true ;
										$sms_otp_enabled = isset ( $_SESSION [ "sms_otp_enabled" ] ) && $_SESSION [ "sms_otp_enabled" ] == true ;
										$cr_otp_enabled = isset ( $_SESSION [ "cr_otp_enabled" ] ) && $_SESSION [ "cr_otp_enabled" ] == true ;
										$advanced_otp_enabled = isset ( $_SESSION [ "advanced_otp_enabled" ] ) && $_SESSION [ "advanced_otp_enabled" ] == true ;
										$mobile_softcert_enabled = isset ( $_SESSION [ "mobile_softcert_enabled" ] ) && $_SESSION [ "mobile_softcert_enabled" ] == true ;
										$mobile_push_enabled = isset ( $_SESSION [ "mobile_push_enabled" ] ) && $_SESSION [ "mobile_push_enabled" ] == true ;
										$qrcode_enabled = isset ( $_SESSION [ "qrcode_enabled" ] ) && $_SESSION [ "qrcode_enabled" ] == true ;
									        $fido_enabled = isset ( $_SESSION [ "fido_enabled" ] ) && $_SESSION [ "fido_enabled" ] == true ;	
										?>
											<center>
												<div id="scroll-pane1" class="scroll-pane1">
													<div id="scroll-content1" style="background-color:#FFF; height: 120px; width: 100%; white-space: nowrap; overflow-x: auto; overflow-y: hidden;"  class="scroll-content1">
														<input type="hidden" id="request_sms_otp" name="request_sms_otp" value="0" />
														<input type="hidden" id="request_otp" name="request_otp" value="0" />
														<input type="hidden" id="request_otp_challenge" name="request_otp_challenge" value="0" />
														<input type="hidden" id="request_mobile_soft_cert" name="request_mobile_soft_cert" value="0" />
														<input type="hidden" id="request_mobile_push" name="request_mobile_push" value="0" />
														<input type="hidden" id="request_qr_code" name="request_qr_code" value="0" />
              													<input type="hidden" id="request_pki_code" name="request_pki_code" value="0" />
	                                                                                                        <input type="hidden" id="request_fido" name="request_fido" value="0" />
                                                                                                                <input type="hidden" id="fidoPublicKeyCredential" name="fidoPublicKeyCredential"  />
                                                                                                                
                                                                                                                <?php
                                                                                                                        if ( $fido_enabled )
                                                                                                                        {
                                                                                                                                ?>
                                                                                                                                        <a href="#" title="FIDO Login" class="twofa2" <?php if ( ! $fido_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestFIDO( )\"" ; ?>>
                                                                                                                                                <?php
                                                                                                                                                        if ( $fido_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
                                                                                                                                                        {
                                                                                                                                                                ?>
                                                                                                                                                                       <img src="images/fido-login-s.png" id="fido" style="margin-bottom:29px; height: 60px; width: 60px;display: inline-block; margin-right:20px;" />
                                                                                                                                                                <?php
                                                                                                                                                        }
                                                                                                                                                        else
                                                                                                                                                        {
                                                                                                                                                                ?>
                                                                                                                                                               <img  src="images/fido-login-hover-s.png" id="fido" style="margin-bottom:29px; height: 60px; width: 60px;display: inline-block; margin-right:20px; "   />
                                                                                                                                                                  <?php
                                                                                                                                                        }
                                                                                                                                                ?>
                                                                                                                                        </a>
                                                                                                                                <?php
                                                                                                                        }
                                                                                                                ?>


														<?php
															if ( $pki_enabled )
															{
																?>
																	<a href="#" title="PKI Login" class="twofa2" <?php if ( ! $pki_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestPki( )\"" ; ?>>
																		<?php
																			if ( $pki_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																			  <img id="pki"  src="images/pki-login-s.png"  style="margin-top:20px; display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																			else
																			{
																				?>
																			    <img id="pki"  src="images/pki-login-s-disabled.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
			
														<?php
															if ( $otp_enabled )
															{
																?>
																	<a href="#" title="OTP Login" class="twofa" <?php if ( ! $otp_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true )  ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestOtp( )\""  ?>>
																		<?php
																			if ( $otp_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																				<img id="otp"  src="images/otp-token-s.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																			else
																			{
																				?>
																					<img id="otp"  src="images/otp-token-s-disabled.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $sms_otp_enabled )
															{
																?>
																	<a href="#" title="SMS OTP Login" <?php if ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == false ) ) echo "onclick='requestSmsOtp( ); return false;'" ?> class="twofa" <?php if ( ! $sms_otp_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; ?>>
																		<?php
																			if ( $sms_otp_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																					<img id="sms"  src="images/mobile-otp-s.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																			else
																			{
																				?>
																					<img id="sms"  src="images/mobile-otp-s-disabled.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $cr_otp_enabled )
															{
																?>
																	<a href="#" title="CR OTP Login" <?php if ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == false ) ) echo "onclick='requestOtpChallenge( ); return false;'" ?> class="twofa" <?php if ( ! $cr_otp_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; ?>>
																		<?php
																			if ( $cr_otp_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																				 <img id="crotp"  src="images/crotp-token-s.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																			else
																			{
																				?>
																					<img id="crotp"  src="images/crotp-token-s-disabled.png"  style="display: inline-block; width:100px;high:100px;" />
																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $advanced_otp_enabled )
															{
																?>
																	<a href="#" title="Advanced OTP Login" <?php if ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == false ) ) echo "onclick='requestOtpChallenge( ); return false;'" ?> class="twofa" <?php if ( ! $cr_otp_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; ?>>
																		<?php
																			if ( $advanced_otp_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																					<div id="advancedotp"><br/>Advanced OTP</div>
																				<?php
																			}
																			else
																			{
																				?>
																					<div id="advancedotpdisabled"><br/>Advanced OTP</div>
																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $mobile_softcert_enabled )
															{
																?>
																	<a href="#" title="Mobile Cert Login" class="twofa" <?php if ( ! $mobile_softcert_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestMobileSoftCert( )\"" ; ?>>
																		<?php
																			if ( $mobile_softcert_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																				 <img id="mobilecert"  src="images/msoftcert-s.png"  style="display: inline-block; width:100px;high:100px;" />

																				<?php
																			}
																			else
																			{
																				?>
																																										                              <img id="mobilecert"  src="images/msoftcert-s-disabled.png"  style="display: inline-block; width:100px;high:100px;" />

																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $mobile_push_enabled )
															{
																?>
																	<a href="#" title="Mobile Push Notification Login" class="twofa" <?php if ( ! $mobile_push_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestMobilePush( )\"" ; ?>>
																		<?php
																			if ( $mobile_push_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																					<img id="mobilepush"  src="images/push_notifi_s.png"  style="margin-bottom:30px; height: 70px; width: 70px;display: inline-block;" />

																				<?php
																			}
																			else
																			{
																				?>
																					 <img id="mobilepush"  src="images/push_notifi_disable.png"  style="margin-bottom:30px; height: 70px; width: 70px;display: inline-block;" />

																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
	
														<?php
															if ( $qrcode_enabled )
															{
																?>
																	<a href="#" title="QR Code Login" class="twofa" <?php if ( ! $qrcode_enabled || ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true ) ) echo "disabled=\"disabled\"" ; else echo "onclick=\"requestQrCode( )\"" ; ?>>
																		<?php
																			if ( $qrcode_enabled && ( ! isset ( $_SESSION [ "show_2fa_input" ] ) || $_SESSION [ "show_2fa_input" ] == false ) )
																			{
																				?>
																															 <img id="qrcode"  src="images/qr-code-s.png"  style="margin-bottom:36px; height: 60px; width: 60px;display: inline-block;margin-left:20px;" />

																				<?php
																			}
																			else
																			{
																				?>
																				  															 <img id="qrcode"  src="images/qr-code-s-disabled.png"  style="margin-bottom:36px; height: 60px; width: 60px;display: inline-block;margin-left:20px;" />

																				<?php
																			}
																		?>
																	</a>
																<?php
															}
														?>
													</div>
												</div>
											</center>
										<?php
									}
								?>
							</div>
							
							<?php
								if ( isset ( $_SESSION [ "show_2fa_input" ] ) && $_SESSION [ "show_2fa_input" ] == true )
								{
									$login_mode = $_SESSION [ "login_mode" ] ;

	                                                                //error_log("LOGIN MODE========".$login_mode);
								
                                                                        if ($login_mode == 15 ){
                                                                             echo "<script> verifyRegistration('".$_SESSION['fido_challenge']."'); </script>";
                                                                         }

	       								if ( $login_mode == 2 )
									{
										/* OTP login */
										?>
											<table style="margin-left: 10px; max-width: 300px">
												<tr>
													<td align="left">
                                                                                                                 <label><?php 
$devName=$_SESSION["devName"];
if (empty($devName)){
   echo "Default device : offline \n ";
}else{
  echo "Default device : ".$devName." \n";
}
 ?></label><br/> 
														<label><?php echo htmlentities ( $this -> t ( '{login:enter_otp}' ) ) ; ?></label>
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<input style="margin-bottom: 0px; width: 100px" maxlength="8" type="text" id="otp_field" name="otp_field" onkeypress="if ( event.keyCode === 13 ) { document.getElementById ( 'submit_otp_button' ).click ( ) ; return false }" />
													</td>
												</tr>
												
												<tr>
													<td>
														<?php
															if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] === true )
															{
																?>
																	<input style="margin-top:10px;" id="submit_otp_button" type="submit" onclick="return validateOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_continue}' ) ; ?>" />
																	<input name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_cancel}' ) ) ; ?>" />
																<?php
															}
															else
															{
																?>
																	<input style="margin-top:10px;"  id="submit_otp_button" type="submit" onclick="return validateOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_submit}' ) ; ?>" />
																<?php																	
															}
														?>
													</td>
												</tr>
											</table>
										<?php
									}
									else if ( $login_mode == 3 )
									{
										/* SMS OTP login */
										?>
											<table style="margin-left: 10px; max-width: 300px">
												<tr>
													<td align="left">
														<label>
															<?php
																$phone = $_SESSION [ "phone" ] ;
																$timeout = $_SESSION [ "timeout" ] ;
																
																$message = $this -> t ( '{login:sms_otp_send_to}' ) ;
																$message = str_replace ( "destphone" , $phone , $message ) ;
																$message = str_replace ( "timeout" , $timeout , $message ) ;
																
																unset ( $_SESSION [ "phone" ] ) ;
																unset ( $_SESSION [ "timeout" ] ) ;

																echo $message ;
															?>
														</label>
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<label><?php echo htmlentities ( $this -> t ( '{login:enter_sms_otp}' ) ) ; ?></label>			
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<input style="margin-bottom: 0px; width: 100px" type="text" id="sms_otp_field" name="sms_otp_field" onkeypress="if ( event.keyCode === 13 ) { document.getElementById ( 'submit_sms_otp_button' ).click ( ) ; return false }" //>			
													</td>
												</tr>
												
												<tr>
													<td>
														<?php
															if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] === true )
															{
																?>
																	<input style="margin-top:10px;"  id="submit_sms_otp_button" type="submit" onclick="return validateSmsOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_continue}' ) ; ?>" />
																	<input name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_cancel}' ) ) ; ?>" />
																<?php
															}
															else
															{
																?>
																	<input style="margin-top:10px;"  id="submit_sms_otp_button" type="submit" onclick="return validateSmsOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_submit}' ) ; ?>" />
																<?php
															}
														?>
													</td>
												</tr>
											</table>
										<?php
									}
									else if ( $login_mode == 5 )
									{
										/* CR OTP login */
										?>
											<table style="margin-left: 10px; max-width: 300px">
												<tr>
													<td align="left">
														
                                                                                                                 <label><?php
$devName=$_SESSION["devName"];
if (empty($devName)){
   echo "Default device : offline \n ";
}else{
  echo "Default device : ".$devName." \n";
}
 ?></label><br/>


                 <label><?php echo htmlentities ( $this -> t ( '{login:otp_challenge}' ) ) ; ?></label>
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<input type="text" value="<?php echo $_SESSION [ "otp_challenge" ] ; ?>" readonly="readonly" class="challenge" />
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<label><?php echo htmlentities ( $this -> t ( '{login:enter_otp}' ) ) ; ?></label>
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<input style="margin-bottom: 0px;  gidth: 100px" type="text" id="otp_field" name="otp_field"  onkeypress="if ( event.keyCode === 13 ) { document.getElementById ( 'submit_cr_otp_button' ).click ( ) ; return false }" //>
													</td>
												</tr>
												
												<tr>
													<td>
														<?php
															if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] === true )
															{
																?>
																	<input  style="margin-top:10px;" id="submit_cr_otp_button" type="submit" onclick="return validateOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_continue}' ) ; ?>" />
																	<input name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_cancel}' ) ) ; ?>" />
																<?php
															}
															else
															{
																?>
																	<input style="margin-top:10px;"  id="submit_cr_otp_button" type="submit" onclick="return validateOtp();" class="btn btn-primary" value="<?php echo $this -> t ( '{login:btn_submit}' ) ; ?>" />
																<?php
															}
														?>
													</td>
												</tr>
											</table>
										<?php
									}
									else if ( $login_mode == 6 || $login_mode == 7 )
									{
										/* Mobile soft cert or mobile push login */
										?>
											<!-- Show the progress bar -->
											<label style="margin-left: 10px">

												Please wait...<br />
                                                                                                                 <label  style="margin-left: 10px"><?php
$devName=$_SESSION["devName"];
if (empty($devName)){
   echo "Default device : offline \n ";
}else{
  echo "Default device : ".$devName." \n";
}
 ?></label><br/>

												<img style="margin-left: 103px" src="./images/prog_wait.gif" /><br />
											</label>
											<div>&nbsp;</div>
											<?php
												if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
												{
													?>
														<input style="margin-left: 10px" name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_cancel}' ) ) ; ?>" />
													<?php
												}
												else
												{
													?>
														<label>&nbsp;</label>
													<?php
												}
											?>
											
											<script type="text/javascript">
												var invalidLogin = "Invalid credentials." ;
												
												function refreshAuthState ( )
												{
													var dt = new Date ( ) ;
													var ajax = dt.getTime ( ) ;
													var url = "./checkstate.php?ajax=" + ajax ;
													
													$.ajaxSetup ( {
														cache: false
													} ) ;
													
													$.ajax ( {
														url: url,
														type: "POST",
														timeout: 5000
													} ).done ( function ( output )
													{                            
														var response = output.explode ( "|" ) ;
														
														if ( response [ 0 ] == "1" )
														{
															/* Login successful */
															window.location = "loginuserpass.php?m=1&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
														}
														else if ( response [ 0 ] == "2" )
														{
															/* Pending. do nothing */
														}
														else if ( response [ 0 ] == "0" )
														{
															if ( response.length > 1 )
															{
																window.location = "loginuserpass.php?m=0&err=" + response [ 1 ] + "&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
															else
															{
																window.location = "loginuserpass.php?m=0&err=Invalid%20credentials&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
														}
														else if ( response [ 0 ] == "-1" )
														{
															if ( response.length > 1 )
															{
																window.location = "loginuserpass.php?m=0&err=" + response [ 1 ] + "&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
															else
															{
																window.location = "loginuserpass.php?m=0&err=Invalid%20credentials&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
														}
														else if ( response [ 0 ] == "-2" )
														{
															window.location = "loginuserpass.php?m=2&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
														}
														else
														{
															document.getElementById ( "divStatus2" ).innerHTML = "<span class='error'>" + response [ 1 ].replace (/\n/g , "" ) + "</span>" ;
														}
													} ).fail ( function ( jqXHR , textStatus , errorThrown )
													{
														// log the error to the console
														console.error ( "The following error occured: " + textStatus , errorThrown ) ;
													} ) ;
												}

												var interval = 3 ;
												
												<?php
													if ( $login_mode == 6 )
													{
														?>
															interval = <?=$globalConfig -> getValue ( "mobile-softcert-status-check-interval" )?> ;
														<?php
													}
													else if ( $login_mode == 7 )
													{
														?>
															interval = <?=$globalConfig -> getValue ( "mobile-push-status-check-interval" )?> ;
														<?php
													}
												?>
												
												setInterval ( function ( )
												{
													refreshAuthState ( )
												}, interval * 1000 ) ;
											</script>
										<?php
									}
									else if ( $login_mode == 8 )
									{
										/* QR Code login */
										?>

											<table style="margin-left: 20px; max-width: 300px">
												<tr>


													<td align="left">

                                                                                                                 <label><?php
$devName=$_SESSION["devName"];
if (empty($devName)){
   echo "Default device : offline \n ";
}else{
  echo "Default device : ".$devName." \n";
}
 ?></label><br/>

														<label><?php echo   htmlentities ( $this -> t ( '{login:qr_code_scan_here}' ) ) ; ?></label>
													</td>
												</tr>
												
												<tr>
													<td align="left">
														<img src="qrcode.php?qr=<?php echo $_SESSION [ "qrCode" ] ?>" width="250px" height="250px" />
													</td>
												</tr>
												
												
												
		
												<tr>
											
												</tr>
											</table>
											
											<script type="text/javascript">



												$( document ).ready ( function ( )
		
										{



 $('#loginform"').on('submit',function(e) {
            console.log("submit_______________");
          
      });

                                                                                                   const interval = setInterval(function() {
                                                                                                        console.log("call AuthState");
                                                                                                         refreshAuthState(); 
                                                                                                          // method to be executed;
                                                                                                         },1000);
													var qrEnterOtpRow = $( "#qr_enter_otp_row" ) ;
													var qrOtpInputRow = $( "#qr_otp_input_row" ) ;
													var crSubmitButton = $( "#submit_cr_otp_button" ) ;

													if ( qrEnterOtpRow )
														qrEnterOtpRow.hide ( ) ;

													if ( qrOtpInputRow )
														qrOtpInputRow.hide ( ) ;
													
													if ( crSubmitButton )
														crSubmitButton.hide ( ) ;
												} ) ;
												
												var invalidLogin = "Invalid credentials." ;
												
												function refreshAuthState ( )
												{
													var dt = new Date ( ) ;
													var ajax = dt.getTime ( ) ;
													var url = "./checkstate.php?ajax=" + ajax ;
													
													$.ajaxSetup ( {
														cache: false
													} ) ;
													
													$.ajax ( {
														url: url,
														type: "POST",
														timeout: 5000
													} ).done ( function ( output )
													{
														var response = output.split ( "|" ) ;
														
														if ( response [ 0 ] == "1" )
														{
															/* Login successful */
															window.location = "loginuserpass.php?m=1&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
														}
														else if ( response [ 0 ] == "2" )
														{
															/* Pending. do nothing */
														}
														else if ( response [ 0 ] == "0" )
														{
															if ( response.length > 1 )
															{
																window.location = "loginuserpass.php?m=0&err=" + response [ 1 ] + "&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
															else
															{
																window.location = "loginuserpass.php?m=0&err=Invalid%20credentials&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
														}
														else if ( response [ 0 ] == "-1" )
														{
															if ( response.length > 1 )
															{
																window.location = "loginuserpass.php?m=0&err=" + response [ 1 ] + "&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
															else
															{
																window.location = "loginuserpass.php?m=0&err=Invalid%20credentials&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
															}
														}
														else if ( response [ 0 ] == "-2" )
														{
															window.location = "loginuserpass.php?m=2&AuthState=<?php echo $_REQUEST [ 'AuthState' ] ; ?>" ;
														}
														else
														{
															document.getElementById ( "divStatus2" ).innerHTML = "<span class='error'>" + response [ 1 ].replace (/\n/g , "" ) + "</span>" ;
														}
													} ).fail ( function ( jqXHR , textStatus , errorThrown )
													{
														// log the error to the console
														console.error ( "The following error occured: " + textStatus , errorThrown ) ;
													} ) ;
												}

												var interval = <?php $globalConfig -> getValue ( "qrcode-status-check-interval" )?> ;
												
												loginStateCheckService = setInterval ( function ( )
												{
													refreshAuthState ( )
												}, interval * 1000 ) ;
											</script>
										<?php
									}
								}
								else
								{
									if ( isset ( $_SESSION [ "multi_step_auth" ] ) && $_SESSION [ "multi_step_auth" ] == true )
									{
										?>
											<div>&nbsp;</div>
											<input style="margin-left: 103px" name="reset_login_button" id="reset_login_button" type="submit" class="btn btn-primary" value="<?php echo htmlentities ( $this -> t ( '{login:btn_cancel}' ) ) ; ?>" />
										<?php
									}
								}
							?>
						</form>
					</div>
				</div>

                                    <div style="margin-top:20px; text-align: center;" class="fixed" id="copyright-wrapper" >


                                        <p style="font-size:13px; color: #000"> Powered by  <a  style="color:#000" href="<?php echo htmlentities ( $this -> t ( '{login:company_url}' ) ) ; ?>" title="<?php echo htmlentities ( $this -> t ( '{login:company_title}' ) ) ; ?>"><?php echo htmlentities ( $this -> t ( '{login:company_name_powered_by}' ) ) ; ?></a> </p>

                              </div>

                                <!-- dialogue panel -->


			</div>

			
                
	</body>
</html>
