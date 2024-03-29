<md:EntityDescriptor entityID="https://sso.example.com/portal" validUntil="2017-08-30T19:10:29Z"
    xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata"
    xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
    xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi"
    xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute"
    xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui"
    xmlns:idpdisc="urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol"
    xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
    <!-- insert ds:Signature element (omitted) -->
    <md:Extensions>
      <mdrpi:RegistrationInfo registrationAuthority="https://registrar.example.net"/>
      <mdrpi:PublicationInfo creationInstant="2017-08-16T19:10:29Z" publisher="https://registrar.example.net"/>
      <mdattr:EntityAttributes>
        <saml:Attribute Name="http://registrar.example.net/entity-category" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
          <saml:AttributeValue>https://registrar.example.net/category/self-certified</saml:AttributeValue>
        </saml:Attribute>
      </mdattr:EntityAttributes>
    </md:Extensions>
    <md:SPSSODescriptor WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
      <md:Extensions>
        <mdui:UIInfo>
          <mdui:DisplayName xml:lang="en">Example.com Vendor Service</mdui:DisplayName>
          <mdui:InformationURL xml:lang="en">https://service.example.com/about.html</mdui:InformationURL>
          <mdui:PrivacyStatementURL xml:lang="en">https://service.example.com/privacy.html</mdui:PrivacyStatementURL>
          <mdui:Logo height="32" width="32" xml:lang="en">https://service.example.com/myicon.png</mdui:Logo>
        </mdui:UIInfo>
        <idpdisc:DiscoveryResponse index="0" Binding="urn:oasis:names:tc:SAML:profiles:SSO:idp-discovery-protocol" Location="https://service.example.com/SAML2/Login"/>
      </md:Extensions>
      <md:KeyDescriptor use="encryption">
        <ds:KeyInfo>...</ds:KeyInfo>
      </md:KeyDescriptor>
      <md:NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</md:NameIDFormat>
      <md:AssertionConsumerService index="0" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"  Location="https://service.example.com/SAML2/SSO/POST"/>
      <md:AttributeConsumingService index="0">
        <md:ServiceName xml:lang="en">Example.com Employee Portal</md:ServiceName>
        <md:RequestedAttribute isRequired="true"
          NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri"
          Name="urn:oid:1.3.6.1.4.1.5923.1.1.1.13" FriendlyName="eduPersonUniqueId"/>
        <md:RequestedAttribute
          NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri"
          Name="urn:oid:0.9.2342.19200300.100.1.3" FriendlyName="mail"/>
      </md:AttributeConsumingService>
    </md:SPSSODescriptor>
    <md:Organization>
      <md:OrganizationName xml:lang="en">Example.com Inc.</md:OrganizationName>
      <md:OrganizationDisplayName xml:lang="en">Example.com</md:OrganizationDisplayName>
      <md:OrganizationURL xml:lang="en">https://www.example.com/</md:OrganizationURL>
    </md:Organization>
    <md:ContactPerson contactType="technical">
      <md:SurName>SAML Technical Support</md:SurName>
      <md:EmailAddress>mailto:technical-support@example.com</md:EmailAddress>
    </md:ContactPerson>
  </md:EntityDescriptor>
