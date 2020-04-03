<?php
/**
*  E-Resource auth Plugin
*
* @package wpsaml
* @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

class EZPZSAML extends EZPZCommon {
  private $oneLogin;
  private $settings = array();

  public function __construct($config) {
    # Set default settings
    $this->settings = array(
      'strict' => $config['onelogin_strict'],
      'debug' => $config['onelogin_debug'],
      'baseurl' => home_url(),
      'sp' => array(
        'entityId' => rtrim(home_url(), '/')."/sso/metadata",
        'assertionConsumerService' => array(
          'url' => rtrim(home_url(), '/')."/sso",
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ),
        'singleLogoutService' => array (
           'url' => $this->get_slo_url(),
           'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        'x509cert' => $this->get_setting("certificate_cert"),
        'privateKey' => $this->get_setting('certificate_key'),
        'x509certNew' => $this->get_setting("certificate_rollover")
      ),
      'idp' => array(
        'entityId' => '',
        'singleSignOnService' => array(
          'url' => '',
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        ),
        'singleLogoutService' => array(
          'url' => '',
          'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        ),
      ),
    );

    $this->update_config();
    add_action('init', array($this, 'session_init'));
  }


  public function session_init() {
    if(!session_id()) {
      session_start();
    }
  }

  public function isAuthenticated() {
    if (!empty($_POST['SAMLResponse'])) {
      $issuer = $this->getIssuerFromResponse($_POST['SAMLResponse']);

      if (!$issuer) {
        return false;
      }

      $institution = $this->getInstitutionFromEntityID($issuer);

      if (!$institution) {
        return false;
      }

      if ($this->getIdpMetadata($institution->idp_metadataurl)) {

        $this->oneLogin = new OneLogin_Saml2_Auth($this->settings);

        $this->oneLogin->processResponse();


        if (!$this->oneLogin->isAuthenticated()) {
          echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
          exit;
        }


        $attributes_std = $this->oneLogin->getAttributes();
        $attributes_friend = $this->oneLogin->getAttributesWithFriendlyName();

        $attributes = array_merge($attributes_std, $attributes_friend);

        $email = null;

        if (!isset($attributes[$institution->idp_usernameattribute]) || count($attributes[$institution->idp_usernameattribute]) < 1) {
          echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
          exit;
        }

        if (isset($attributes[$institution->idp_emailattribute]) && count($attributes[$institution->idp_emailattribute]) > 0) {
          $email = $attributes[$institution->idp_emailattribute][0];
        }


        $username = $attributes[$institution->idp_usernameattribute][0];
        $redirect_to = filter_input( INPUT_POST, 'RelayState', FILTER_SANITIZE_URL );


        if (!username_exists($username)) {
          if (!email_exists($email)) {
            $user_id = wp_create_user($username, random_bytes(10), $email);
          } else {
            $user_id = wp_create_user($username, random_bytes(10));
          }
          $user = new WP_User($user_id);
          add_user_meta($user_id, 'institution', $institution->id);
          add_user_meta($user_id, 'wps_user_type', 'SAML');
        }

        $username = sanitize_user($username, true);
        $wp_user = get_user_by('login', $username);



        if ($wp_user && !is_wp_error($wp_user)) {
          if ($email && $email != $wp_user->user_email) {
            if (!email_exists($email)) {
              wp_update_user(array(
                "ID" => $wp_user->ID,
                'user_email' => esc_attr($email)
              ));
            }
          }

          wp_clear_auth_cookie();
          wp_set_current_user($wp_user->ID);
          wp_set_auth_cookie($wp_user->ID);

          $_SESSION['samlNameId'] = $this->oneLogin->getNameId();
          $_SESSION['samlNameIdFormat'] = $this->oneLogin->getNameIdFormat();
          $_SESSION['samlSessionIndex'] = $this->oneLogin->getSessionIndex();

          if (!$redirect_to) {
            wp_redirect(home_url());
            exit;
          }

          wp_redirect($redirect_to);
          exit;
       } else {
         echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
         exit;
       }
     } else {
       echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
       exit;
     }
   }

    return false;
  }

  public function doSLS() {
    $institution = $this->get_institution_free_plugin();
    if (!$institution) {
      return false;
    }

    if ($this->getIdpMetadata($institution->idp_metadataurl)) {
      $this->oneLogin = new OneLogin_Saml2_Auth($this->settings);

      $keepLocalSession = False;
      $callback = function () {
        wp_destroy_current_session();
        wp_clear_auth_cookie();
      };

      try {
        $this->oneLogin->processSLO($keepLocalSession, null, false, $callback);
      } catch(Exception $e) {
        return false;
      }
    }
  }

  public function login($metadata_url, $redirect_to) {
    if ($this->getIdpMetadata($metadata_url)) {
      $this->oneLogin = new OneLogin_Saml2_Auth($this->settings);
      $this->oneLogin->login($redirect_to);
      return true;
    }

    echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
    exit;
  }

  public function logout($metadata_url) {
    if ($this->getIdpMetadata($metadata_url)) {
      $this->oneLogin = new OneLogin_Saml2_Auth($this->settings);


      $nameId = null;
      $sessionIndex = null;
      $nameIdFormat = null;

      if (isset($_SESSION['samlNameId'])) {
          $nameId = $_SESSION['samlNameId'];
      }

      if (isset($_SESSION['samlSessionIndex'])) {
          $sessionIndex = $_SESSION['samlSessionIndex'];
      }

      if (isset($_SESSION['samlNameIdFormat'])) {
          $nameIdFormat = $_SESSION['samlNameIdFormat'];
      }

      $this->oneLogin->logout(site_url()."/wp-login.php", array(), $nameId, $sessionIndex, false, $nameIdFormat);
      return true;
    }

    return false;
  }

  public function metadata() {
    $settings = new OneLogin_Saml2_Settings($this->settings, true);

    try {
      $metadata = $settings->getSPMetadata();
      $errors = $settings->validateMetadata($metadata);
      if (empty($errors)) {
        return $metadata;
      } else {
        return false;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  private function getIdpMetadata($url) {

    $parser = new OneLogin_Saml2_IdPMetadataParser;

    if (preg_match("/MANUAL\_FILE\_([0-9]+)/", $url, $matches)) {
      $metadata = $this->get_manual_metadata($matches[1]);

      $data = $parser->parseXML($metadata->metadata);
    } else {
      $data = $parser->parseRemoteXML($url);
    }

    if ($data && isset($data['idp'])) {
      $this->settings['idp'] = $data['idp'];
      return true;
    }
    return false;
  }


  private function getRemoteXML($url) {
    try {
      $response = wp_remote_get($url);
      $xml = wp_remote_retrieve_body($response);
      if ($xml !== false) {
        return $xml;
      } else {
        throw new Exception('Unable to contact the authentication service');
      }
    } catch (Exception $e) {
      throw new Exception('Error parsing metadata. '.$e->getMessage());

    }

  }

  private function get_manual_metadata($id) {
    global $wpdb;

    return $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."institution_meta WHERE id = %d AND manual = 1", array($id)));
  }

  private function getIssuerFromResponse($response) {
    $res = base64_decode($response);

    $dom = new DOMDocument();
    $dom = OneLogin_Saml2_Utils::loadXML($dom, $res);

    if (!$dom) {
      throw new OneLogin_Saml2_ValidationError("SAML Response could not be processed", OneLogin_Saml2_ValidationError::INVALID_XML_FORMAT);
    }

    $responseIssuer = OneLogin_Saml2_Utils::query($dom, '/samlp:Response/saml:Issuer');
    if ($responseIssuer->length > 0) {
      if ($responseIssuer->length == 1) {
        return $responseIssuer->item(0)->textContent;
      } else {
        throw new OneLogin_Saml2_ValidationError("Issuer of the Response is multiple.", OneLogin_Saml2_ValidationError::ISSUER_MULTIPLE_IN_RESPONSE);
      }
    }

    return false;
  }

  private function getInstitutionFromEntityID($entity_id) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT wpi.*
    FROM ".$wpdb->prefix."institutions wpi
    WHERE wpi.idp_entityid = %s", $entity_id);

    $data = $wpdb->get_row($sql);

    if (!$data) {
      return false;
    }

    return $data;
  }

  private function update_config() {
    $security = array(
      /** signatures and encryptions offered */

      // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
      // will be encrypted.
      'nameIdEncrypted' => false,

      // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
      // will be signed.  [Metadata of the SP will offer this info]
      'authnRequestsSigned' => true,

      // Indicates whether the <samlp:logoutRequest> messages sent by this SP
      // will be signed.
      'logoutRequestSigned' => true,

      // Indicates whether the <samlp:logoutResponse> messages sent by this SP
      // will be signed.
      'logoutResponseSigned' => true,

      /* Sign the Metadata
       False || True (use sp certs) || array (
                                                  keyFileName => 'metadata.key',
                                                  certFileName => 'metadata.crt'
                                              )
      */
      'signMetadata' => false,

      /** signatures and encryptions required **/

      // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest>
      // and <samlp:LogoutResponse> elements received by this SP to be signed.
      'wantMessagesSigned' => true,

      // Indicates a requirement for the <saml:Assertion> elements received by
      // this SP to be encrypted.
      'wantAssertionsEncrypted' => true,

      // Indicates a requirement for the <saml:Assertion> elements received by
      // this SP to be signed. [Metadata of the SP will offer this info]
      'wantAssertionsSigned' => true,

      // Indicates a requirement for the NameID element on the SAMLResponse
      // received by this SP to be present.
      'wantNameId' => true,

      // Indicates a requirement for the NameID received by
      // this SP to be encrypted.
      'wantNameIdEncrypted' => false,

      // Authentication context.
      // Set to false and no AuthContext will be sent in the AuthNRequest.
      // Set true or don't present this parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'.
      // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509').
      'requestedAuthnContext' => true,

      // Indicates if the SP will validate all received xmls.
      // (In order to validate the xml, 'strict' and 'wantXMLValidation' must be true).
      'wantXMLValidation' => true,

      // If true, SAMLResponses with an empty value at its Destination
      // attribute will not be rejected for this fact.
      'relaxDestinationValidation' => false,

      // Algorithm that the toolkit will use on signing process. Options:
      //    'http://www.w3.org/2000/09/xmldsig#rsa-sha1'
      //    'http://www.w3.org/2000/09/xmldsig#dsa-sha1'
      //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256'
      //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384'
      //    'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512'
      // Notice that sha1 is a deprecated algorithm and should not be used
      'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

      // Algorithm that the toolkit will use on digest process. Options:
      //    'http://www.w3.org/2000/09/xmldsig#sha1'
      //    'http://www.w3.org/2001/04/xmlenc#sha256'
      //    'http://www.w3.org/2001/04/xmldsig-more#sha384'
      //    'http://www.w3.org/2001/04/xmlenc#sha512'
      // Notice that sha1 is a deprecated algorithm and should not be used
      'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',

      // ADFS URL-Encodes SAML data as lowercase, and the toolkit by default uses
      // uppercase. Turn it True for ADFS compatibility on signature verification
      'lowercaseUrlencoding' => false,
    );

    $nameid_format = $this->get_setting("nameid_format");
    $signature_algorithm = $this->get_setting("signature_algorithm");
    $digest_algorithm = $this->get_setting("digest_algorithm");

    # Disabled by default
    $enable_nameid_encrypted = $this->get_setting("enable_nameid_encrypted");
    $enable_want_name_id_encrypted = $this->get_setting("enable_want_name_id_encrypted");
    $enable_relax_destination_validation = $this->get_setting("enable_relax_destination_validation");
    $enable_lowercase_urlencoding = $this->get_setting("enable_lowercase_urlencoding");

    # Enabled by default
    $disable_authn_requests_signed = $this->get_setting("disable_authn_requests_signed");
    $disable_logout_request_signed = $this->get_setting("disable_logout_request_signed");
    $disable_logout_response_signed = $this->get_setting("disable_logout_response_signed");
    $disable_want_messages_signed = $this->get_setting("disable_want_messages_signed");
    $disable_want_assertions_encrypted = $this->get_setting("disable_want_assertions_encrypted");
    $disable_want_assertions_signed = $this->get_setting("disable_want_assertions_signed");
    $disable_want_name_id = $this->get_setting("disable_want_name_id");
    $disable_request_authn_context = $this->get_setting("disable_request_authn_context");
    $disable_want_xml_validation = $this->get_setting("disable_want_xml_validation");

    if ($enable_nameid_encrypted === "1") {
      $security['nameIdEncrypted'] = true;
    }

    if ($enable_want_name_id_encrypted === "1") {
      $security['wantNameIdEncrypted'] = true;
    }

    if ($enable_relax_destination_validation === "1") {
      $security['relaxDestinationValidation'] = true;
    }

    if ($enable_lowercase_urlencoding === "1") {
      $security['lowercaseUrlencoding'] = true;
    }

    if ($disable_authn_requests_signed === "1") {
      $security['authnRequestsSigned'] = false;
    }

    if ($disable_logout_request_signed === "1") {
      $security['logoutRequestSigned'] = false;
    }

    if ($disable_logout_response_signed === "1") {
      $security['logoutResponseSigned'] = false;
    }

    if ($disable_want_messages_signed === "1") {
      $security['wantMessagesSigned'] = false;
    }

    if ($disable_want_assertions_encrypted === "1") {
      $security['wantAssertionsEncrypted'] = false;
    }

    if ($disable_want_assertions_signed === "1") {
      $security['wantAssertionsSigned'] = false;
    }

    if ($disable_want_name_id === "1") {
      $security['wantNameId'] = false;
    }

    if ($disable_request_authn_context === "1") {
      $security['requestedAuthnContext'] = false;
    }

    if ($disable_want_xml_validation === "1") {
      $security['wantXMLValidation'] = false;
    }

    if ($signature_algorithm) {
      $security['signatureAlgorithm']= $signature_algorithm;
    }

    if ($digest_algorithm) {
      $security['digestAlgorithm']= $digest_algorithm;
    }

    $this->settings['security'] = $security;
    $this->settings['sp']['NameIDFormat'] = $nameid_format;

  }
}
