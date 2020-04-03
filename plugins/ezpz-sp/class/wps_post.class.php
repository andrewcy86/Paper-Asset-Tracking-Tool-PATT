<?php
/**
 *  Ezpz SP
 *
 * @package E-Resource auth Plugin
 * @author Overt Software Solutions LTD
 * @license GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */


class EZPZPost extends EZPZCommon {
  function __construct() {
    add_action('post_submitbox_misc_actions', array($this, 'post_submitbox_misc_actions'));
    add_action('save_post', array($this, 'save_post'));
    add_filter('the_content', array($this, 'the_content'));
  }

  function post_submitbox_misc_actions() {
    $post_id = get_the_ID();

    wp_nonce_field("post_permissions_nonce", "post_permissions_nonce");

    $redirect_pages = $this->get_setting("redirect_pages");

    if (!$redirect_pages) {
      return;
    }

    $value = get_post_meta($post_id, '_post_require_auth', true);
?>
    <div class="misc-pub-section misc-pub-section-last">
      <label><input type="checkbox" name="post_require_auth" value="1" <?php checked($value, true, true); ?>/> <?php echo __('Require authentication to access this article', 'ezpzsp'); ?></label>
    </div>
<?php
  }

  function save_post($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['post_permissions_nonce']) || !wp_verify_nonce($_POST['post_permissions_nonce'], 'post_permissions_nonce')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $redirect_pages = $this->get_setting("redirect_pages");

    if (!$redirect_pages) {
      return;
    }

    if(isset($_POST['post_require_auth'])) {
      update_post_meta($post_id, '_post_require_auth', sanitize_text_field($_POST['post_require_auth']));
    } else {
      delete_post_meta($post_id, '_post_require_auth');
    }
  }

  function the_content($content) {
    if (is_admin()) {
      return $content;
    }

    $redirect_pages = $this->get_setting("redirect_pages");

    if (!$redirect_pages) {
      return;
    }


    $post_id = get_the_ID();


    if (!$post_id) {
      return null;
    }

    $restricted = get_post_meta($post_id, '_post_require_auth', true);

    if ($restricted != 1) {
      return $content;
    }

    if (!is_user_logged_in()) {
      return "";
    }


    return $content;
  }
}
