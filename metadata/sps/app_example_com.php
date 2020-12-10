<?php
$metadata['https://app.example.com/shibboleth'] = array (
  'entityid' => 'https://app.example.com/shibboleth',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-sp-remote',
  'AssertionConsumerService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML2/POST',
      'index' => 1,
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML2/POST-SimpleSign',
      'index' => 2,
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML2/Artifact',
      'index' => 3,
    ),
    3 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML2/ECP',
      'index' => 4,
    ),
    4 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML/POST',
      'index' => 5,
    ),
    5 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01',
      'Location' => 'https://app.example.com/Shibboleth.sso/SAML/Artifact',
      'index' => 6,
    ),
  ),
  'SingleLogoutService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://app.example.com/Shibboleth.sso/SLO/SOAP',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://app.example.com/Shibboleth.sso/SLO/Redirect',
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://app.example.com/Shibboleth.sso/SLO/POST',
    ),
    3 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
      'Location' => 'https://app.example.com/Shibboleth.sso/SLO/Artifact',
    ),
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => false,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIID1jCCAj6gAwIBAgIJAPKmwpjvY1AfMA0GCSqGSIb3DQEBCwUAMBAxDjAMBgNV
BAMTBWZpZG8yMB4XDTE5MTAxNDA3MzI0MVoXDTI5MTAxMTA3MzI0MVowEDEOMAwG
A1UEAxMFZmlkbzIwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQCanIsa
CaA/pRIR4QyEnCNjXcLQgtJ7cg2j5pM4vFlGrfVtvH1HySGw4t9nsG86vc6vWflf
EJ7AActXJya0ni7KWkVw5JJ6mEPSLlNV83YLQyECp+HjyZnaygJ1sMwaTjH9bdwF
B/lHnoUzN8Jx8k04frXHFw4gO5m4S6930xrnQVmbB9c7LAxNQU2uaPprlzb/g8XO
hyGdJ/cAFNMBBwcqRqWqClSN8Pzjpr3ln4dWR8WOIcn95UdmGFi+79yiluO8yqjO
bsiNMx6Ju5v68ViyVGkNJRGYDeTKecMfky0+va1RvCiHVGbhaBszSTeItD741OzN
awXGb7OgNzx1WAcyHlD+41Cb7TGHjrnbJvMMVwui+0neZFmniodFiywvS8XCyxSd
TcsH/91ebBngmDG7dkpmCZJIEG8GO4WO+7dpT8ppGqAZ3F0bu416WSyxYW1KbUAL
S3XLg9yfiq1/qePcMfvKtdcYXvdon2ZHLagGDop945v9EEBOzOAseobDnMUCAwEA
AaMzMDEwEAYDVR0RBAkwB4IFZmlkbzIwHQYDVR0OBBYEFDdfh2zb4MjbcE0NSLAN
AkvXJlIzMA0GCSqGSIb3DQEBCwUAA4IBgQBM+ZLQ0L98fVGHM1pjRW+HzZ3SrJgm
D0++WduEbndNqvbi6SI0Qab3SdKPQ/su42oey1fPVMg0GaXWqDoZ2b9389zL3YyI
4Ie6P75AW6qJeVC+oON0Jq/UnoHTuBmq4eES55Aby5eWg1DoLUDEUHZ13JTY6ek2
Ychm2p2NicB8wtkPLAxUUgSnTESSg9hRwH3Hc5VZb9bI5aeHY6WncqFD9lSJNR1h
MocvEnDIKlxBLlY0VmTBIZD6UeQTaYhtJWXipm2JWvwbrGZ7lxWaVqG92voDd+C/
OcYj8GlWaueFR9P1YqqRiTWXLyrQGUd4hcJiov5m/t7NIb/GGbHSfBfcbvSt8OPm
qPqIvyBrpF+f/3YgUrc09kwpudDLxRetH6G7coEKXRM7CJjvLaxKd/3E4uqFI+mi
x+UhcS3K1hb8Im++GZ4aprHRnsBXvkvo+hVETTZ8Yf1wAUeEWq9Clni20MS0ibRH
BAvxja4+3+3j7WVinbXD7dTMl6QX8GiuvHc=
',
    ),
    1 => 
    array (
      'encryption' => true,
      'signing' => false,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIID1jCCAj6gAwIBAgIJALNlvGjAQIazMA0GCSqGSIb3DQEBCwUAMBAxDjAMBgNV
BAMTBWZpZG8yMB4XDTE5MTAxNDA3MzI0MVoXDTI5MTAxMTA3MzI0MVowEDEOMAwG
A1UEAxMFZmlkbzIwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQC/k8wi
Pl+QBaWeH8FUC32pfsBli4PawZ4u3qUkPkDBQoraayN+VgHpJ/EHUNMGxccUzx9i
fI3hI/VFEPMu2Io4MA5prp0FjA1F2WQCt2pH6loj7jjtL1l8d0u8lhHP6D+wNcp8
qvwkHpdHNdq/iM2BkyENbYpgeAKInnfuG9oyryTg3uWdcSq+nZna4Gqf7NFxxlXg
GBM6OBPNQDhVbH/ckbtgXoU3vyAKiwgembhfSD/F99P5EvvHdSMEklv6Q+SdvTjh
bMNNgE/Qg3PnLjRJdEAmvo4trRnrXDAPr1N7p7dSJV+kt6GNYRDOX+erN3PqZ2rX
EXDcoy5R5ue7wg4Ub4dgJCtLvmSzjK1ctBsmrE4R7sV4vsTrSK+0LLrdrsUpdOio
7BstPvF8VDWQuupVmQx5YSJeSQ1tgNBC0unXMOFjSH26kmWMm1eFb2WfUdCWGZ+r
amY+NFV8azozcqSyk8qAwfGyF/OwPY8w7NyAmK44uUJy4NxrjkX96tGTfesCAwEA
AaMzMDEwEAYDVR0RBAkwB4IFZmlkbzIwHQYDVR0OBBYEFAWzRTripUId2gQfceqp
+YXDRa41MA0GCSqGSIb3DQEBCwUAA4IBgQCDTm0wmXGp5TiTBsOacXgpLKtsBza7
H9gLQ31MxKUNQS8UeZ1en9/UmtQhpLpMxChmExnBmUxm5AQNIwOD72rCnylPTTo3
S3mwS8pQriBjA+L7Jp1rea92aqoP+hRnbDRd0mXxzr6E+/A2TRNS5J7kQSKQ4sD/
ymFl6Fy7iRpy8Z2CKvjAZXiWvMnT7DV/VfMrhfT4VKbvDlLek01eMOplTVhNBv9k
o9xO9zS2crNiEqFvtcUYJHZjJpcgyM4SaNOJrMVnmhjKgi8HGYS5WCEZskHXg452
FaJm94EAKL8kHForxmHCorm9gHfskBFDnO1UnncFeVPWc99yw71rpsnU5eejjStX
gn93/akIU0Co49/kkpyTFPvTtsJIUKn4KBt2mgfEjdCxByH2EK/CabpZcy71NhgL
ZRLnbH9FNuchGg5WDStqUDwxrbN1ikY5fVOIlGH8ABvZ9BEN518JZlbzscrcHK0p
/vgD0Nap6gGdc+KgwLMAXh4YpW04ZRXRs8I=
',
    ),
  ),
);
?>
