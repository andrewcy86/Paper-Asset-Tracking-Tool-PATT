<?php
/**
 *  E-Resource auth Plugin
 *
 * @package wpsaml
 * @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

class EZPZAuth extends EZPZCommon {
  private $SAML;
  private $config;

  public function __construct($config) {
    $this->config = $config;
    add_action('template_redirect', array($this, 'init'));
    add_action('login_init', array($this, 'login_init'));
    add_action('wp_logout', array($this, 'wp_logout'));

    add_action('login_footer', array($this, 'login_footer'), 1, 0);
    $this->SAML = new EZPZSAML($this->config);
   }


  public function init() {
    if (defined('WP_CLI') && WP_CLI) {
      return true;
    }
    

    if(intval(get_query_var('ezpzsp_metadata')) == 1) {
      $metadata = $this->SAML->metadata();

      if (!$metadata) {
        die("Unable to get SAML metadata.");
      } else {
        header('Content-Type: text/xml', true, 200);
        echo $metadata;
      }
      exit;
    }

    if(intval(get_query_var('ezpzsp_slo')) == 1) {
      $this->SAML->doSLS();
      wp_redirect(site_url());
      exit;
    }


    if (!$this->is_saml_auth()) {
      if (get_query_var('pagename') == 'sso' && is_user_logged_in()) {
        # redirect /sso back to the home page incase we some how end up here.
        wp_redirect(site_url());
        exit;
      } else if (get_query_var('pagename') == 'sso' && !is_user_logged_in()) {
        # If we hit /sso and do not have a SAML session then the login has failed
        echo "<h1>".__('An unexpected error occured when logging in.', 'ezpzsp')."</h1>";
        exit;
      }

      if ($this->get_setting('redirect_whole_site') === "1" && !is_user_logged_in()) {
        $this->auth_redirect();
      } else if (!is_front_page() && !is_search() && !is_feed() && $this->get_setting('redirect_pages') === "1" && !is_user_logged_in()) {

        $post_id = get_the_ID();
        if ($post_id) {
          $restricted = get_post_meta($post_id, '_post_require_auth', true);
          if ($restricted === "1") {
            $this->auth_redirect();
          }
        }
      }
    }
  }

  public function login_init() {
    if ($this->get_setting('redirect_login') === "1" && !is_user_logged_in()) {
      $this->auth_redirect();
    }

    if (isset($_GET['institution_login'])) {
      $this->auth_redirect();
    }
  }

  public function wp_logout() {
    if ($this->get_setting('enable_singlelogout') === "1") {
      $institution = $this->get_institution_free_plugin();
      if (!$institution) {
        return false;
      }

      $this->SAML->logout($institution->idp_metadataurl);
    }
  }

  public function login_footer() {
    $idp_login_url = site_url('wp-login.php', 'login');
    $idp_login_url = add_query_arg('institution_login', '1', $idp_login_url);
?>
  <div style="text-align: center;" id="institution_login"><a href="<?php echo $idp_login_url; ?>"><?php echo __("Login via your institution", "ezpzsp"); ?></a></div>
<?php
  }


  private function is_saml_auth() {
    return $this->SAML->isAuthenticated();
  }


  public function saml_login($institution_id, $redirect_to) {
    $institution = $this->get_institution_by_id($institution_id, true);

    if (!$institution) {
      return false;
    }

    return $this->SAML->login($institution->idp_metadataurl, $redirect_to);
  }


  private function auth_redirect() {
   global $wp;

   $institution = $this->get_institution_free_plugin();

    if (!$institution) {
      return false;
    }

    $link = home_url($wp->request);
    if (!$link) $link = home_url();

    $this->saml_login($institution->id, $link);
    exit;
  }
}
