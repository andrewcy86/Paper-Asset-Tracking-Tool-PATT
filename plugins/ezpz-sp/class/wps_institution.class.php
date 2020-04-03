<?php
/**
 *  Ezpz SP
 *
 * @package E-Resource auth Plugin
 * @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */


class EZPZInstitution extends EZPZCommon {
  private $config;
  public function __construct($config=array()) {
    $this->config = $config;
    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_head', array($this, 'admin_head'));

  }


  public function admin_menu() {
    global $submenu;
    add_menu_page(__('Ezpz SP', 'ezpzsp'), __('Ezpz SP', 'ezpzsp'), 'manage_options', 'ezpzsp', array($this, 'nav_switch'));
    add_submenu_page('ezpzsp', __('Ezpz SP', 'ezpzsp'), __('Configure', 'ezpzsp'), 'manage_options', 'ezpzsp', array($this, 'nav_switch'));
    $submenu['ezpzsp'][] = array(__('Upgrade to premium', 'ezpzsp'), 'manage_options', "https://ezpzsp.com/premium-upgrade/");
  }

  private function ezpzp_nav($tab='institution') {
?>
<h2 class="nav-tab-wrapper">
  <a href="<?php echo admin_url("admin.php?page=ezpzsp"); ?>" class="nav-tab <?php if ($tab == "institution") { ?>nav-tab-active<?php } ?>"><?php echo __('Institution/SP settings', 'ezpzsp'); ?></a>
  <a href="<?php echo admin_url("admin.php?page=ezpzsp&tab=login"); ?>" class="nav-tab <?php if ($tab == "login") { ?>nav-tab-active<?php } ?>"><?php echo __('Login options', 'ezpzsp'); ?></a>
  <a href="<?php echo admin_url("admin.php?page=ezpzsp&tab=certs"); ?>" class="nav-tab <?php if ($tab == "certs") { ?>nav-tab-active<?php } ?>"><?php echo __('Certificate options', 'ezpzsp'); ?></a>
  <a href="<?php echo admin_url("admin.php?page=ezpzsp&tab=advanced"); ?>" class="nav-tab <?php if ($tab == "advanced") { ?>nav-tab-active<?php } ?>"><?php echo __('Advanced options', 'ezpzsp'); ?></a>
  <a href="https://ezpzsp.com/premium-upgrade/" target="_blank" class="nav-tab nav-tab-yellow" style="background: #ffe150; color: #9c3939;"><?php echo __('Premium features', 'ezpzsp'); ?></a>
  <a href="https://ezpzsp.com/support/" target="_blank" class="nav-tab nav-tab-blue" style="background: #58aaff; color: #FFF;"><?php echo __('Support', 'ezpzsp'); ?></a>
</h2>
<?php
  }

  public function admin_head() {
  ?>
    <style type="text/css">
      li#toplevel_page_ezpzsp ul a[href="https://ezpzsp.com/premium-upgrade/"]{
        color: #ffe141 !important;
      }
    </style>
  <?php
  }


  public function nav_switch() {
    $switch = (isset($_GET['tab'])) ? $_GET['tab'] : "institution";

    switch ($switch) {
      case 'institution':
        $this->institution();
      break;

      case 'login':
        $this->login_options();
      break;

      case 'certs':
        $this->certs();
      break;

      case 'advanced':
        $this->advanced_options();
      break;

      default:
        $this->institution();
      break;
    }
  }

  public function institution() {
    global $wpdb;


    $institution_name = null;
    $institution_idp_entityid = null;
    $institution_idp_metadataurl = null;
    $institution_idp_usernameattribute = "eduPersonTargetedID";
    $institution_idp_emailattribute = "";
    $institution_idp_metadatafile = null;

    $inst = $this->get_institution_free_plugin();
    if ($inst) {
      $institution_name = $inst->name;
      $institution_idp_entityid = $inst->idp_entityid;
      $institution_idp_metadataurl = $inst->idp_metadataurl;
      $institution_idp_usernameattribute = $inst->idp_usernameattribute;
      $institution_idp_emailattribute = $inst->idp_emailattribute;
    }

    $errors = array();
    $success = false;

    if (isset($_POST['createinstitution'])) {
      $institution_name = sanitize_title($_POST['institution_name']);
      $institution_idp_entityid = sanitize_text_field($_POST['institution_idp_entityid']);
      $institution_idp_metadataurl = esc_url_raw($_POST['institution_idp_metadataurl']);
      $institution_idp_usernameattribute = sanitize_text_field($_POST['institution_idp_usernameattribute']);
      $institution_idp_emailattribute = sanitize_text_field($_POST['institution_idp_emailattribute']);

      if (isset($_FILES['institution_idp_metadatafile'])) {
        $institution_idp_metadatafile = $_FILES['institution_idp_metadatafile'];
      }


      if (!$institution_name) {
        $errors[] = __("An institution name is required", "ezpzsp");
      } else if (preg_match("/@/", $institution_name)) {
        $errors[] = __("The institution name cannot contain @.", "ezpzsp");
      }

      if (!$institution_idp_entityid || (!$institution_idp_metadataurl && empty($institution_idp_metadatafile['tmp_name'])) || !$institution_idp_usernameattribute) {
        $errors[] = __("A IDP entity ID, a metadata file/url and a username attribute are required.", "ezpzsp");
      }

      if (!$institution_idp_metadataurl && !empty($institution_idp_metadatafile['tmp_name'])) {
        try {
          $xml = @simplexml_load_file($institution_idp_metadatafile['tmp_name']);
          if (!$xml) {
            $errors[] = __("The supplied idp metadata file is invalid", "ezpzsp");
          }
        } catch (Exception $e) {
          $errors[] = __("The supplied idp metadata file is invalid", "ezpzsp");
        }

      }


      if (count($errors) < 1) {
        if (!$inst) {
          $wpdb->insert($wpdb->prefix."institutions", array(
            "name" => $institution_name,
            "idp_entityid" => $institution_idp_entityid,
            "idp_metadataurl" => $institution_idp_metadataurl,
            "idp_usernameattribute" => $institution_idp_usernameattribute,
            "idp_emailattribute" => $institution_idp_emailattribute,
          ));
          $institution_id = $wpdb->insert_id;

        } else {
          $institution_id = $inst->id;
          $wpdb->update($wpdb->prefix."institutions", array(
            "name" => $institution_name,
            "idp_entityid" => $institution_idp_entityid,
            "idp_metadataurl" => $institution_idp_metadataurl,
            "idp_usernameattribute" => $institution_idp_usernameattribute,
            "idp_emailattribute" => $institution_idp_emailattribute,
          ), array("id" => $institution_id));
        }


        if (!$institution_idp_metadataurl && !empty($institution_idp_metadatafile['tmp_name'])) {
          $id = $this->update_institution_metadata($institution_id, file_get_contents($institution_idp_metadatafile['tmp_name']));
          unlink($institution_idp_metadatafile['tmp_name']);

          if ($id) {
            $institution_idp_metadataurl = "MANUAL_FILE_".$id;
          }
        } else {
          $this->update_institution_metadata($institution_id);
        }



        $success = true;
      }
    }

    $institution_idp_metadataurl = preg_replace("/MANUAL\_FILE\_[0-9]+/", "", $institution_idp_metadataurl);

?>
<div class="wrap">

  <?php $this->ezpzp_nav('institution'); ?>
  <h3><?php echo __('Institution/SP settings', 'ezpzsp'); ?></h3>

  <?php
    if (count($errors)) {
      foreach($errors as $error) {
  ?>
  <div class="notice notice-error"><?php echo $error; ?></div>
  <?php
      }
    }

    if ($success) {
  ?>
  <div class="notice notice-success"><?php echo __("The institution has been updated.", "ezpzsp"); ?></div>
  <?php
    }
  ?>

  <a href="https://ezpzsp.com/getting-started/" target="_blank" class="nav-tab nav-tab-yellow" style="background: #ffe150; color: #9c3939; border:1px solid #ccc; padding: 5px 10px; font-size: 14px;"><?php echo __('Link to getting started guide', 'ezpzsp'); ?></a>

  <form method="post" enctype="multipart/form-data">

    <table class="form-table">
      <tr class="form-field form-required">
        <th scope="row">
          <label for="institution_name"><?php echo __("Instituion Name", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <input name="institution_name" id="institution_name" type="text" value="<?php echo $institution_name; ?>" aria-required="true" required />
        </td>
      </tr>

      <tr class="form-field">
        <th scope="row">
          <label for="institution_idp_entityid"><?php echo __("IDP entity id", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <input name="institution_idp_entityid" id="institution_idp_entityid" value="<?php echo $institution_idp_entityid; ?>" type="text" />
        </td>
      </tr>

      <tr class="form-field">
        <th scope="row">
          <label for="institution_idp_metadataurl"><?php echo __("IDP metadata file/url", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <input name="institution_idp_metadataurl" id="institution_idp_metadataurl" value="<?php echo $institution_idp_metadataurl; ?>" type="text" /> <br /><br />

          <input type="file" name="institution_idp_metadatafile" id="institution_idp_metadatafile">

        </td>
      </tr>

      <tr class="form-field">
        <th scope="row">
          <label for="institution_idp_usernameattribute"><?php echo __("Username Attribute", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <input name="institution_idp_usernameattribute" id="institution_idp_usernameattribute" value="<?php echo $institution_idp_usernameattribute; ?>" type="text" />
        </td>
      </tr>

      <tr class="form-field">
        <th scope="row">
          <label for="institution_idp_emailattribute"><?php echo __("Email Attribute", "ezpzsp"); ?> <span class="description">(<?php echo __("optional", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <input name="institution_idp_emailattribute" id="institution_idp_emailattribute" value="<?php echo $institution_idp_emailattribute; ?>" type="text" />
        </td>
      </tr>

    </table>
    <p class="submit">
      <input type="submit" name="createinstitution" id="createinstitution" class="button button-primary" value="<?php echo __("Update settings", "ezpzsp"); ?>"  />
      <a href="<?php echo $this->get_metadata_url(); ?>" class="button" target="_blank"><?php echo __("Download SP metadata", "ezpzsp"); ?></a>

    </p>

  </form>
</div>
<?php
  }

  public function certs() {

    $certificate_cert = $this->get_setting("certificate_cert");
    $certificate_key = $this->get_setting("certificate_key");
    $certificate_rollover = $this->get_setting("certificate_rollover");

    $errors = array();
    $success = false;

    if (isset($_POST['updatesettings'])) {
      $certificate_cert = sanitize_text_field($_POST['certificate_cert']);
      $certificate_key = sanitize_text_field($_POST['certificate_key']);
      $certificate_rollover = sanitize_text_field($_POST['certificate_rollover']);

      if (!$certificate_cert) {
        $errors[] = __("A certificate is required", "ezpzsp");
      }

      if (!$certificate_key) {
        $errors[] = __("A private key is required", "ezpzsp");
      }

      if (count($errors) < 1) {
        $success = true;
        $this->set_setting("certificate_cert", $certificate_cert);
        $this->set_setting("certificate_key", $certificate_key);
        $this->set_setting("certificate_rollover", $certificate_rollover);
      }
    }
?>
<div class="wrap">
  <?php $this->ezpzp_nav('certs'); ?>
  <h3><?php echo __('Certificate options', 'ezpzsp'); ?></h3>

  <?php
    if (count($errors)) {
      foreach($errors as $error) {
  ?>
  <div class="notice notice-error"><?php echo $error; ?></div>
  <?php
      }
    }

    if ($success) {
  ?>
  <div class="notice notice-success"><?php echo __("The certificate options have been updated.", "ezpzsp"); ?></div>
  <?php
    }
  ?>

  <form method="post" enctype="multipart/form-data">

    <table class="form-table">
      <tr class="form-field form-required">
        <th scope="row">
          <label for="certificate_cert"><?php echo __("Certificate", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <textarea name="certificate_cert" rows="15" aria-required="true" required><?php echo $certificate_cert; ?></textarea>
        </td>
      </tr>

      <tr class="form-field form-required">
        <th scope="row">
          <label for="certificate_key"><?php echo __("Private key", "ezpzsp"); ?> <span class="description">(<?php echo __("required", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <textarea name="certificate_key" rows="15" aria-required="true" required><?php echo $certificate_key; ?></textarea>
        </td>
      </tr>

      <tr class="form-field form-required">
        <th scope="row">
          <label for="certificate_rollover"><?php echo __("Certificate rollover", "ezpzsp"); ?> <span class="description">(<?php echo __("optional", "ezpzsp"); ?>)</span></label>
        </th>
        <td>
          <textarea name="certificate_rollover" rows="15"><?php echo $certificate_rollover; ?></textarea>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="<?php echo __("Update settings", "ezpzsp"); ?>"  />
    </p>

  </form>
</div>
<?php
  }

  public function login_options() {
    $redirect_whole_site = $this->get_setting("redirect_whole_site");
    $redirect_pages = $this->get_setting("redirect_pages");
    $redirect_login = $this->get_setting("redirect_login");
    $enable_singlelogout = $this->get_setting("enable_singlelogout");

    $errors = array();
    $success = false;

    if (isset($_POST['updatesettings'])) {
      $redirect_whole_site = sanitize_text_field($_POST['redirect_whole_site']);
      $redirect_pages = sanitize_text_field($_POST['redirect_pages']);
      $redirect_login = sanitize_text_field($_POST['redirect_login']);
      $enable_singlelogout = sanitize_text_field($_POST['enable_singlelogout']);

      if (count($errors) < 1) {
        $success = true;
        $this->set_setting("redirect_whole_site", $redirect_whole_site);
        $this->set_setting("redirect_pages", $redirect_pages);
        $this->set_setting("redirect_login", $redirect_login);
        $this->set_setting("enable_singlelogout", $enable_singlelogout);
      }
    }
?>
<div class="wrap">
  <?php $this->ezpzp_nav('login'); ?>
  <h3><?php echo __('Login options', 'ezpzsp'); ?></h3>

  <?php
    if (count($errors)) {
      foreach($errors as $error) {
  ?>
  <div class="notice notice-error"><?php echo $error; ?></div>
  <?php
      }
    }

    if ($success) {
  ?>
  <div class="notice notice-success"><?php echo __("The login options have been updated.", "ezpzsp"); ?></div>
  <?php
    }
  ?>

  <form method="post" enctype="multipart/form-data">

    <table class="form-table">
      <tr class="form-field form-required">
        <th scope="row">
          <label for="redirect_whole_site"><?php echo __("Protect whole site", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="redirect_whole_site" <?php if ($redirect_whole_site == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="redirect_pages"><?php echo __("Enable per page/post protection", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="redirect_pages" <?php if ($redirect_pages == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="redirect_login"><?php echo __("Redirect wordpress login page", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="redirect_login" <?php if ($redirect_login == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="enable_singlelogout"><?php echo __("Enable Single Logout", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="enable_singlelogout" <?php if ($enable_singlelogout == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="<?php echo __("Update settings", "ezpzsp"); ?>"  />
    </p>

  </form>
</div>
<?php
  }

  public function advanced_options() {

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

    $errors = array();
    $success = false;

    if (isset($_POST['updatesettings'])) {

      $nameid_format = sanitize_text_field($_POST['nameid_format']);
      $signature_algorithm = sanitize_text_field($_POST['signature_algorithm']);
      $digest_algorithm = sanitize_text_field($_POST['digest_algorithm']);

      # Disabled by default
      $enable_nameid_encrypted = sanitize_text_field($_POST['enable_nameid_encrypted']);
      $enable_want_name_id_encrypted = sanitize_text_field($_POST['enable_want_name_id_encrypted']);
      $enable_relax_destination_validation = sanitize_text_field($_POST['enable_relax_destination_validation']);
      $enable_lowercase_urlencoding = sanitize_text_field($_POST['enable_lowercase_urlencoding']);

      # Enabled by default
      $disable_authn_requests_signed = (($_POST['disable_authn_requests_signed'] === "1") ? false : "1");
      $disable_logout_request_signed = (($_POST['disable_logout_request_signed'] === "1") ? false : "1");
      $disable_logout_response_signed = (($_POST['disable_logout_response_signed'] === "1") ? false : "1");
      $disable_want_messages_signed = (($_POST['disable_want_messages_signed'] === "1") ? false : "1");
      $disable_want_assertions_encrypted = (($_POST['disable_want_assertions_encrypted'] === "1") ? false : "1");
      $disable_want_assertions_signed = (($_POST['disable_want_assertions_signed'] === "1") ? false : "1");
      $disable_want_name_id = (($_POST['disable_want_name_id'] === "1") ? false : "1");
      $disable_request_authn_context = (($_POST['disable_request_authn_context'] === "1") ? false : "1");
      $disable_want_xml_validation = (($_POST['disable_want_xml_validation'] === "1") ? false : "1");

      if (count($errors) < 1) {
        $success = true;

        $this->set_setting("nameid_format", $nameid_format);
        $this->set_setting("signature_algorithm", $signature_algorithm);
        $this->set_setting("digest_algorithm", $digest_algorithm);

        # Disabled by default
        $this->set_setting("enable_nameid_encrypted", $enable_nameid_encrypted);
        $this->set_setting("enable_want_name_id_encrypted", $enable_want_name_id_encrypted);
        $this->set_setting("enable_relax_destination_validation", $enable_relax_destination_validation);
        $this->set_setting("enable_lowercase_urlencoding", $enable_lowercase_urlencoding);

        # Enabled by default
        $this->set_setting("disable_authn_requests_signed", $disable_authn_requests_signed);
        $this->set_setting("disable_logout_request_signed", $disable_logout_request_signed);
        $this->set_setting("disable_logout_response_signed", $disable_logout_response_signed);
        $this->set_setting("disable_want_messages_signed", $disable_want_messages_signed);
        $this->set_setting("disable_want_assertions_encrypted", $disable_want_assertions_encrypted);
        $this->set_setting("disable_want_assertions_signed", $disable_want_assertions_signed);
        $this->set_setting("disable_want_name_id", $disable_want_name_id);
        $this->set_setting("disable_request_authn_context", $disable_request_authn_context);
        $this->set_setting("disable_want_xml_validation", $disable_want_xml_validation);
      }
    }
?>
<div class="wrap">
  <?php $this->ezpzp_nav('advanced'); ?>
  <h3><?php echo __('Advanced options', 'ezpzsp'); ?></h3>

  <?php
    if (count($errors)) {
      foreach($errors as $error) {
  ?>
  <div class="notice notice-error"><?php echo $error; ?></div>
  <?php
      }
    }

    if ($success) {
  ?>
  <div class="notice notice-success"><?php echo __("The advanced options have been updated.", "ezpzsp"); ?></div>
  <?php
    }
  ?>

  <form method="post" enctype="multipart/form-data">

    <table class="form-table">
      <tr class="form-field form-required">
        <th scope="row">
          <label for="nameid_format"><?php echo __("NameID Format", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="text" value="<?php echo ((!$nameid_format) ? "urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified" : $nameid_format); ?>" name="nameid_format"/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="enable_nameid_encrypted"><?php echo __("NameId Encrypted", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="enable_nameid_encrypted" <?php if ($enable_nameid_encrypted == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_authn_requests_signed"><?php echo __("AuthN Requests Signed", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_authn_requests_signed" <?php if ($disable_authn_requests_signed != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_logout_request_signed"><?php echo __("Logout Requests Signed", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_logout_request_signed" <?php if ($disable_logout_request_signed != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_logout_response_signed"><?php echo __("Logout Response Signed", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_logout_response_signed" <?php if ($disable_logout_response_signed != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_want_messages_signed"><?php echo __("Want Messages Signed", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_want_messages_signed" <?php if ($disable_want_messages_signed != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_want_assertions_encrypted"><?php echo __("Want Assertions Encrypted", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_want_assertions_encrypted" <?php if ($disable_want_assertions_encrypted != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_want_assertions_signed"><?php echo __("Want Assertions Signed", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_want_assertions_signed" <?php if ($disable_want_assertions_signed != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_want_name_id"><?php echo __("Want NameId", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_want_name_id" <?php if ($disable_want_name_id != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="enable_want_name_id_encrypted"><?php echo __("Want NameId Encrypted", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="enable_want_name_id_encrypted" <?php if ($enable_want_name_id_encrypted == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_request_authn_context"><?php echo __("Request AuthN Context", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_request_authn_context" <?php if ($disable_request_authn_context != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="disable_want_xml_validation"><?php echo __("Want XML Validation", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="disable_want_xml_validation" <?php if ($disable_want_xml_validation != "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="enable_relax_destination_validation"><?php echo __("Relax Destination Validation", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="enable_relax_destination_validation" <?php if ($enable_relax_destination_validation == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="signature_algorithm"><?php echo __("Signature Algorithm", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="text" value="<?php echo ((!$signature_algorithm) ? "http://www.w3.org/2001/04/xmldsig-more#rsa-sha256" : $signature_algorithm); ?>" name="signature_algorithm"/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="digest_algorithm"><?php echo __("Digest Algorithm", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="text" value="<?php echo ((!$digest_algorithm) ? "http://www.w3.org/2001/04/xmlenc#sha256" : $digest_algorithm); ?>" name="digest_algorithm"/>
        </td>
      </tr>
      <tr class="form-field form-required">
        <th scope="row">
          <label for="enable_lowercase_urlencoding"><?php echo __("Lowercase URL Encoding", "ezpzsp"); ?> </label>
        </th>
        <td>
          <input type="checkbox" value="1" name="enable_lowercase_urlencoding" <?php if ($enable_lowercase_urlencoding == "1") { echo "checked"; } ?>/>
        </td>
      </tr>
    </table>
    <p class="submit">
      <input type="submit" name="updatesettings" id="updatesettings" class="button button-primary" value="<?php echo __("Update settings", "ezpzsp"); ?>"  />
    </p>

  </form>
</div>
<?php
  }

  private function update_institution_metadata($inst, $data=false) {
    global $wpdb;

    $wpdb->delete($wpdb->prefix."institution_meta", array("institution_id" => $inst));

    if ($data) {
      $wpdb->insert($wpdb->prefix."institution_meta", array("institution_id" => $inst, "metadata" => $data, "manual" => 1));

      return $wpdb->insert_id;
    }

    return false;
  }
}
