<?xml version='1.0' encoding='UTF-8' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:h="http://java.sun.com/jsf/html"
      xmlns:ui="http://java.sun.com/jsf/facelets"
      xmlns:f="http://java.sun.com/jsf/core"
      xmlns:c="http://java.sun.com/jsp/jstl/core">

        <head>
            <title><?php echo $this -> t ( '{app:title}' ) . " " . $this -> t ( '{app:version}' ) ?></title>

            <link href="styles/ui.css" rel="stylesheet" media="all" />
            <link href="styles/base/jquery.ui.all.css" rel="stylesheet" />

            <script type="text/javascript" src="javascript/jquery-1.11.1.js"></script>
            <script type="text/javascript" src="javascript/jquery-ui-1.10.4.js"></script>
            <script type="text/javascript" src="javascript/ui/jquery.ui.dialog.min.js"></script>
            <script type="text/javascript" src="javascript/ui.js"></script>
            <script type="text/javascript" src="javascript/login.js"></script>
            <script type="text/javascript">
				function requestPki ( )
				{
					var loginForm = $( "#loginform" ) ;
					
					if ( loginForm )
					{
						var queryString = window.location.search ;
						
						<?php
							$globalConfig = SimpleSAML_Configuration::getInstance ( ) ;
							$secureUrl = $globalConfig -> getValue ( "login-url-with-client-auth" ) ;
						?>
						
						var secureSite = "<?php echo $secureUrl ; ?>" + queryString ;
						
						loginForm.attr ( "action" , secureSite ) ;
						loginForm.submit ( ) ;
					}
				}
				
                $( document ).ready ( function ( )
                {
                    $( "#email" ).focus ( ) ;
					
					<?php
						if ( isset ( $_SESSION [ "auth_static" ] ) && $_SESSION [ "auth_static" ] == true )
						{
							?>
								setTimeout ( function ( )
								{
									$( "#reset_login_button" ).click ( ) ;
								} , 120000 ) ;
							<?php
						}
					?>
					
					var smsOtpField = $( "#sms_otp_field" ) ;
					
					if ( smsOtpField )
						smsOtpField.focus ( ) ;
						
					var otpField = $( "#otp_field" ) ;
					
					if ( otpField )
						otpField.focus ( ) ;
					
					<?php
						if ( isset ( $_SESSION [ "num_of_2fa" ] ) && $_SESSION [ "num_of_2fa" ] < 4 )
						{
							?>
								$('#scroll-pane').css({
									overflow: 'hidden'
								});
								$('#scroll-content').css({
									width: '550px'
								});
							<?php
						}
						else if ( isset ( $_SESSION [ "num_of_2fa" ] ) && $_SESSION [ "num_of_2fa" ] >= 4 )
						{
							?>
								$('#scroll-content').css({
									width: '<?=($_SESSION [ "num_of_2fa" ] + 1) * 118?>px'
								});
							<?php
						}
					?>
                } ) ;
            </script>
        </head>

        <body>
            <div class="fixed" id="page_wrapper">            
                <div id="page-header" >
                    <div class="fixed" id="page-header-wrapper">
                        <div id="top">
                            <a href="#" class="logo" title="<?php echo $this -> t ( '{app:title}' ) . " " . $this -> t ( '{app:version}' ) ?>"><?php echo $this -> t ( '{app:title}' ) . " " . $this -> t ( '{app:version}' ) ?></a>                        
                        </div>                                        
                    </div>
                </div>

				<div id="error_div" class="page-notification success" style="position: relative;margin: 0 auto;top:0;left:0;width:545px;">
					<div id="error_div" class="page-notification" style="position: relative;margin: 0 auto;top:0;left:0;width:350px;">
						<div id="error_resultbox" class="response-msg ui-corner-all">
							<span id="error_message_text" style="margin-left: -30px; margin-top: 8px; color: black">You have been logged out.</span>
						</div>
					</div>
				</div>

                <div id="footer">
                    <div id="footer-wrapper" class="fixed">
                        <a href="#" title="<?php echo htmlentities ( $this -> t ( '{login:home}' ) ) ; ?><"><?php echo htmlentities ( $this -> t ( '{login:home}' ) ) ; ?></a> | 
                        <a href="https://cloud.centagate.com/centagate/companyselfregister.xhtml" title="<?php echo htmlentities ( $this -> t ( '{login:register}' ) ) ; ?>"><?php echo htmlentities ( $this -> t ( '{login:register}' ) ) ; ?></a> | 
                        <a href="#" title="<?php echo htmlentities ( $this -> t ( '{login:terms_condition}' ) ) ; ?>"><?php echo htmlentities ( $this -> t ( '{login:terms_condition}' ) ) ; ?></a> | 
                        <a href="https://www.securemetric.com" title="<?php echo htmlentities ( $this -> t ( '{login:about_us}' ) ) ; ?>"><?php echo htmlentities ( $this -> t ( '{login:about_us}' ) ) ; ?></a>
                    </div>
                </div>

                <div id="copyright">
                    <div id="copyright-wrapper" class="fixed">
                        Powered by <a href="<?php echo htmlentities ( $this -> t ( '{login:company_url}' ) ) ; ?>" title="<?php echo htmlentities ( $this -> t ( '{login:company_title}' ) ) ; ?>"><?php echo htmlentities ( $this -> t ( '{login:company_name_powered_by}' ) ) ; ?></a>
                    </div>
                </div>
            </div>
            <!-- dialogue panel -->
            <div id="input_empty_dialog" title="Validation">
                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">&nbsp;</span></p>
            </div>
        </body>
</html>
