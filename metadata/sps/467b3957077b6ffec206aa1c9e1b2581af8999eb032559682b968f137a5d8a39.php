<?php

$metadata['sp.test'] = array (
  'entityid' => 'sp.test',
  'contacts' => 
  array (
    0 => 
    array (
      'contactType' => 'support',
    ),
  ),
  'metadata-set' => 'saml20-sp-remote',
  'AssertionConsumerService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://adfs.centagate.lan/adfs/ls/',
      'index' => 0,
      'isDefault' => true,
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
      'Location' => 'https://adfs.centagate.lan/adfs/ls/',
      'index' => 1,
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://adfs.centagate.lan/adfs/ls/',
      'index' => 2,
    ),
  ),
  'SingleLogoutService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://adfs.centagate.lan/adfs/ls/',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://adfs.centagate.lan/adfs/ls/',
    ),
  ),
  'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => true,
      'signing' => false,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIIFOjCCBCKgAwIBAgITeAAAAAXk3hXBq4aG9QAAAAAABTANBgkqhkiG9w0BAQsFADA/MRMwEQYKCZImiZPyLGQBGRYDbGFuMRkwFwYKCZImiZPyLGQBGRYJY2VudGFnYXRlMQ0wCwYDVQQDEwRNU0NBMB4XDTE4MDgwNjA5NDQxOVoXDTIwMDgwNTA5NDQxOVowWzELMAkGA1UEBhMCTVkxCzAJBgNVBAgTAklUMQswCQYDVQQHEwJJVDELMAkGA1UEChMCSVQxCzAJBgNVBAsTAklUMRgwFgYDVQQDDA8qLmNlbnRhZ2F0ZS5sYW4wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCQvJ9pEMhwbl0C7cWkL001+KB+ps7klW/nCiDaWlEv558trGLBRf4zApf/QygRTUesQ9d2wLc8x0JN7oKnOw/w/9MNE47XBONERRHbePlSJJCpE5OnXbYAHcASMOKDFxbAFJQr9rYrtJbOhkwVkNtJlgvzVmOxcZ3jW7rtkZyBc5MZAlJHLXkGFn2NyK6fOWDoUCutRLpzB3IM0+dE2t1up25SRxGL2M8+0zteU6qryVL+URuuuZFw9wJmcvCghn2Yc9P8b3zX5CCUX4IBsO9/kf1hclesayNXD61Qa9NhDQJKXngy8ALjeXwG1KTmN16n1a9nbY0emMa2/NBgE/CrAgMBAAGjggIRMIICDTAdBgNVHQ4EFgQUDug6AJORUMEnEm+tQqvLrdyWm+MwHwYDVR0jBBgwFoAUJ+TypP3ESwOfE4SGwNE0KjtvJkswgccGA1UdHwSBvzCBvDCBuaCBtqCBs4aBsGxkYXA6Ly8vQ049TVNDQSxDTj1jc20tcG9jLWFkLENOPUNEUCxDTj1QdWJsaWMlMjBLZXklMjBTZXJ2aWNlcyxDTj1TZXJ2aWNlcyxDTj1Db25maWd1cmF0aW9uLERDPWNlbnRhZ2F0ZSxEQz1sYW4/Y2VydGlmaWNhdGVSZXZvY2F0aW9uTGlzdD9iYXNlP29iamVjdENsYXNzPWNSTERpc3RyaWJ1dGlvblBvaW50MIG4BggrBgEFBQcBAQSBqzCBqDCBpQYIKwYBBQUHMAKGgZhsZGFwOi8vL0NOPU1TQ0EsQ049QUlBLENOPVB1YmxpYyUyMEtleSUyMFNlcnZpY2VzLENOPVNlcnZpY2VzLENOPUNvbmZpZ3VyYXRpb24sREM9Y2VudGFnYXRlLERDPWxhbj9jQUNlcnRpZmljYXRlP2Jhc2U/b2JqZWN0Q2xhc3M9Y2VydGlmaWNhdGlvbkF1dGhvcml0eTAhBgkrBgEEAYI3FAIEFB4SAFcAZQBiAFMAZQByAHYAZQByMA4GA1UdDwEB/wQEAwIFoDATBgNVHSUEDDAKBggrBgEFBQcDATANBgkqhkiG9w0BAQsFAAOCAQEANJs94li/lmQVHXpMmzgVZz1JdBTgDgEHmSzpcyTgjODwImi2E5R8kj3RhW/FKD6BLMVrrJs27JO5THKoSrM1ACON68J7u9fiDiNHp9O6I7ajJRuYnNOIJHjRj5M1RrnIq4N4UKhIWzNRwKS5yJxS888X/8eCsuLOEp0dEABcI76qw0mZ20rhkqPTBm6OgN6hnZow6SpOVbT06eMc+ipyFnuUZKY6KDFMBIOPMYg3a2c1XXH5tbot5FPGrdFZvJuliEFVYdp1fv9SMjMW/LnN8Jjs5Z7I0ZXwUZEJZlO/fiUOZlwYLJkRat7Cwpfd+cAlSE1zWBJFOiWvfVNjDjrf5Q==',
    ),
    1 => 
    array (
      'encryption' => false,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIIFOjCCBCKgAwIBAgITeAAAAAXk3hXBq4aG9QAAAAAABTANBgkqhkiG9w0BAQsFADA/MRMwEQYKCZImiZPyLGQBGRYDbGFuMRkwFwYKCZImiZPyLGQBGRYJY2VudGFnYXRlMQ0wCwYDVQQDEwRNU0NBMB4XDTE4MDgwNjA5NDQxOVoXDTIwMDgwNTA5NDQxOVowWzELMAkGA1UEBhMCTVkxCzAJBgNVBAgTAklUMQswCQYDVQQHEwJJVDELMAkGA1UEChMCSVQxCzAJBgNVBAsTAklUMRgwFgYDVQQDDA8qLmNlbnRhZ2F0ZS5sYW4wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCQvJ9pEMhwbl0C7cWkL001+KB+ps7klW/nCiDaWlEv558trGLBRf4zApf/QygRTUesQ9d2wLc8x0JN7oKnOw/w/9MNE47XBONERRHbePlSJJCpE5OnXbYAHcASMOKDFxbAFJQr9rYrtJbOhkwVkNtJlgvzVmOxcZ3jW7rtkZyBc5MZAlJHLXkGFn2NyK6fOWDoUCutRLpzB3IM0+dE2t1up25SRxGL2M8+0zteU6qryVL+URuuuZFw9wJmcvCghn2Yc9P8b3zX5CCUX4IBsO9/kf1hclesayNXD61Qa9NhDQJKXngy8ALjeXwG1KTmN16n1a9nbY0emMa2/NBgE/CrAgMBAAGjggIRMIICDTAdBgNVHQ4EFgQUDug6AJORUMEnEm+tQqvLrdyWm+MwHwYDVR0jBBgwFoAUJ+TypP3ESwOfE4SGwNE0KjtvJkswgccGA1UdHwSBvzCBvDCBuaCBtqCBs4aBsGxkYXA6Ly8vQ049TVNDQSxDTj1jc20tcG9jLWFkLENOPUNEUCxDTj1QdWJsaWMlMjBLZXklMjBTZXJ2aWNlcyxDTj1TZXJ2aWNlcyxDTj1Db25maWd1cmF0aW9uLERDPWNlbnRhZ2F0ZSxEQz1sYW4/Y2VydGlmaWNhdGVSZXZvY2F0aW9uTGlzdD9iYXNlP29iamVjdENsYXNzPWNSTERpc3RyaWJ1dGlvblBvaW50MIG4BggrBgEFBQcBAQSBqzCBqDCBpQYIKwYBBQUHMAKGgZhsZGFwOi8vL0NOPU1TQ0EsQ049QUlBLENOPVB1YmxpYyUyMEtleSUyMFNlcnZpY2VzLENOPVNlcnZpY2VzLENOPUNvbmZpZ3VyYXRpb24sREM9Y2VudGFnYXRlLERDPWxhbj9jQUNlcnRpZmljYXRlP2Jhc2U/b2JqZWN0Q2xhc3M9Y2VydGlmaWNhdGlvbkF1dGhvcml0eTAhBgkrBgEEAYI3FAIEFB4SAFcAZQBiAFMAZQByAHYAZQByMA4GA1UdDwEB/wQEAwIFoDATBgNVHSUEDDAKBggrBgEFBQcDATANBgkqhkiG9w0BAQsFAAOCAQEANJs94li/lmQVHXpMmzgVZz1JdBTgDgEHmSzpcyTgjODwImi2E5R8kj3RhW/FKD6BLMVrrJs27JO5THKoSrM1ACON68J7u9fiDiNHp9O6I7ajJRuYnNOIJHjRj5M1RrnIq4N4UKhIWzNRwKS5yJxS888X/8eCsuLOEp0dEABcI76qw0mZ20rhkqPTBm6OgN6hnZow6SpOVbT06eMc+ipyFnuUZKY6KDFMBIOPMYg3a2c1XXH5tbot5FPGrdFZvJuliEFVYdp1fv9SMjMW/LnN8Jjs5Z7I0ZXwUZEJZlO/fiUOZlwYLJkRat7Cwpfd+cAlSE1zWBJFOiWvfVNjDjrf5Q==',
    ),
  ),
  'saml20.sign.assertion' => true,
);

?>
