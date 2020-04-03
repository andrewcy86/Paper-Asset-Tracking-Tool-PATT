<?php
/**
*  Ezpz SP
*
* @package E-Resource auth Plugin
* @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

class EZPZCommon {
  public function get_institutions() {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT wpi.* FROM ".$wpdb->prefix."institutions wpi" , array());

    $data = $wpdb->get_results($sql);


    return $data;
  }

  public function get_active_institutions() {
    global $wpdb;

    $current_date = date("Y-m-d");

    $sql = $wpdb->prepare("SELECT wpi.*
    FROM ".$wpdb->prefix."institutions wpi
    WHERE idp_entityid IS NOT NULL AND idp_entityid <> ''");

    $data = $wpdb->get_results($sql);

    return $data;
  }

  public function get_institution_free_plugin() {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT wpi.* FROM ".$wpdb->prefix."institutions wpi ORDER BY id DESC LIMIT 1", array());
    $data = $wpdb->get_row($sql);
    if (!$data) {
      return false;
    }

    return $data;
  }

  public function get_institution_by_id($id, $active=false) {
    global $wpdb;

    if (!$active) {
      $sql = $wpdb->prepare("SELECT wpi.*
        FROM ".$wpdb->prefix."institutions wpi
        WHERE wpi.id = %s", $id);
   } else {
     $sql = $wpdb->prepare("SELECT wpi.*
       FROM ".$wpdb->prefix."institutions wpi
       WHERE wpi.id = %s
       ", array($id,));
   }


    $data = $wpdb->get_row($sql);

    if (!$data) {
      return false;
    }

    return $data;
  }

  public function get_institution_by_user_id($id) {
    global $wpdb;

    $inst_id = get_user_meta($id, 'institution', true);

    if (!$inst_id || empty($inst_id)) {
      return false;
    }

    $sql = $wpdb->prepare("SELECT wpi.* FROM ".$wpdb->prefix."institutions wpi WHERE wpi.id = %s", $inst_id);

    $data = $wpdb->get_row($sql);

    return $data;
  }

  public function get_institution_id_from_user_id($user_id) {
    $inst = $this->get_institution_by_user_id($user_id);

    if ($inst) {
      return $inst->id;
    }

    return false;
  }

  public function get_users_by_instituion_id($id, $search=null) {

    if ($search) $search = "*".$search."*";
    return get_users(array('meta_key' => 'institution', 'meta_value' => $id, 'search' => $search));
  }

  public function get_setting($setting_name) {
    global $wpdb;
    $value = $wpdb->get_var($wpdb->prepare("SELECT option_value FROM ".$wpdb->prefix."institution_settings WHERE option_name = '%s'", array($setting_name)));

    return $value;
  }

  public function set_setting($setting_name, $value) {
    global $wpdb;

    if (empty($value)) {
      $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."institution_settings
        WHERE option_name = '%s'
      ", array( $setting_name)));

      return;
    }

    if (!$this->get_setting($setting_name)) {
      $wpdb->query($wpdb->prepare("
        INSERT INTO ".$wpdb->prefix."institution_settings
        (option_name, option_value) VALUES
        ('%s', '%s')
      ", array($setting_name, $value)));
    } else {
      $wpdb->query($wpdb->prepare("
        UPDATE ".$wpdb->prefix."institution_settings
        SET option_value = '%s' WHERE option_name = '%s'
      ", array($value, $setting_name)));
    }
  }

  public function get_metadata_url() {
    if (get_option('permalink_structure')) {
      return rtrim(home_url(), '/')."/sso/metadata";
    } else {
      return rtrim(home_url(), '/')."/?ezpzsp_metadata=1";
    }
  }

  public function get_slo_url() {
    if (get_option('permalink_structure')) {
      return rtrim(home_url(), '/')."/sso/slo";
    } else {
      return rtrim(home_url(), '/')."/?ezpzsp_slo=1";
    }
  }
}
