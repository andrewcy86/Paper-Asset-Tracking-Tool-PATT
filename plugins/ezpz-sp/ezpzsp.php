<?php
/**
 * Plugin Name: Ezpz SP
 * Depends: OneLogin PHP-SAML
 * Plugin URI: http://www.overtsoftware.com
 * Description: SAML based user authentication
 * Author: Overt Software Solutions LTD
 * Version: 1.2
 * Author URI: http://www.overtsoftware.com
 */


/**
 *  Ezpz SP
 *
 * @package E-Resource auth Plugin
 * @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}



$ezpzsp_db_version = '1.0';

function ezpzsp_install () {
  global $wpdb;
  global $ezpzsp_db_version;


  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

  $charset_collate = $wpdb->get_charset_collate();

  $institutions_table_name = $wpdb->prefix . "institutions";
  $institutions_meta_table_name = $wpdb->prefix . "institution_meta";
  $institution_settings_table_name = $wpdb->prefix . "institution_settings";

  dbDelta(
    "CREATE TABLE ".$institutions_table_name." (
      id int(11) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      idp_entityid VARCHAR(255),
      idp_metadataurl VARCHAR(255),
      idp_usernameattribute VARCHAR(255),
      idp_emailattribute VARCHAR(255),
      PRIMARY KEY (`id`)
    )".$charset_collate.";"
  );

  dbDelta(
    "CREATE TABLE ".$institutions_meta_table_name." (
	     id int(11) NOT NULL AUTO_INCREMENT,
       institution_id int(11),
       metadata MEDIUMTEXT,
	     expire_at DATETIME,
       created_at DATETIME,
       manual int(1) NOT NULL DEFAULT 0,
	     PRIMARY KEY (`id`)
    )".$charset_collate.";"
  );

  dbDelta(
    "CREATE TABLE ".$institution_settings_table_name." (
	     id int(11) NOT NULL AUTO_INCREMENT,
       option_name VARCHAR(255),
	     option_value TEXT,
	     PRIMARY KEY (`id`)
    )".$charset_collate.";"
  );


  $certificate = null;
  if (isset($wpdb)) {
    $certificate = $wpdb->get_var("SELECT option_value FROM ".$wpdb->prefix."institution_settings WHERE option_name = 'certificate_cert'");
  }

  if (!$certificate && isset($wpdb)) {
    $privkey = openssl_pkey_new(array(
        "private_key_bits" => 4096,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ));

    $gen_commonname = bin2hex(random_bytes(5));

    $csr = openssl_csr_new(array(
      "countryName" => "GB",
      "stateOrProvinceName" => "Worcestershire",
      "localityName" => "Worcester",
      "organizationName" => "EzpzSP",
      "organizationalUnitName" => "Automaticly generated certificate",
      "commonName" => $gen_commonname,
      "emailAddress" => "ezpzsp@".$gen_commonname
    ), $privkey, array('digest_alg' => 'sha256'));

    $x509 = openssl_csr_sign($csr, null, $privkey, $days=7300, array('digest_alg' => 'sha256'));
    openssl_x509_export($x509, $certout);
    openssl_pkey_export($privkey, $pkeyout);

    $wpdb->query($wpdb->prepare("
      INSERT INTO ".$wpdb->prefix."institution_settings
      (option_name, option_value) VALUES
      ('certificate_cert', '%s'),
      ('certificate_key', '%s')
    ", array($certout, $pkeyout)));
  }

  define("EZPZSP_REWRITE_CHANGED", true);
}

register_activation_hook( __FILE__, 'ezpzsp_install' );


function ezpzsp_db_update_check() {
  global $ezpzsp_db_version;
  if ( get_option( 'ezpzsp_db_version' ) != $ezpzsp_db_version ) {
    ezpzsp_install();
  }
}

add_action('plugins_loaded', 'ezpzsp_db_update_check');

function ezpzsp_my_plugin_action_links($links) {
	$links = array_merge(array('<a href="'.esc_url(admin_url('/admin.php?page=ezpzsp')).'">' . __('Configure', 'ezpzsp') . '</a>'), $links);
	return $links;
}

add_action('plugin_action_links_'.plugin_basename(__FILE__), 'ezpzsp_my_plugin_action_links');


function ezpzsp_my_plugin_query_vars($vars) {
  $vars[] = 'ezpzsp_metadata';
  $vars[] = 'ezpzsp_slo';
  return $vars;
}

add_filter('query_vars','ezpzsp_my_plugin_query_vars');

function ezpzsp_rewrite_rules() {
  global $wp_rewrite;

  add_rewrite_rule('^sso/?$', 'index.php?pagename=sso', 'top');
  add_rewrite_rule('^sso/metadata/?$', 'index.php?ezpzsp_metadata=1', 'top');
  add_rewrite_rule('^sso/slo/?$', 'index.php?ezpzsp_slo=1', 'top');

  # Hard refresh of rewrite rules only on upgrades
  if(defined('EZPZSP_REWRITE_CHANGED') && EZPZSP_REWRITE_CHANGED) {
    $wp_rewrite->flush_rules( true );
  }
}

add_action('init', 'ezpzsp_rewrite_rules');

$config = array();

require_once dirname( __FILE__ )."/config.php";

DEFINE("EZPZSP_TOOLKIT_PATH", $config['onelogin_path']);
require_once(EZPZSP_TOOLKIT_PATH."_toolkit_loader.php");

if (!class_exists('OneLogin_Saml2_Auth')) {
  die("Could not load OneLogin class"); // Exit if we cannot load the one login library
}

require_once dirname( __FILE__ )."/class/wps_common.class.php";
require_once dirname( __FILE__ )."/class/wps_saml.class.php";
require_once dirname( __FILE__ )."/class/wps_auth.class.php";
require_once dirname( __FILE__ )."/class/wps_institution.class.php";
require_once dirname( __FILE__ )."/class/wps_post.class.php";

new EZPZAuth($config);
new EZPZInstitution($config);
new EZPZPost();
