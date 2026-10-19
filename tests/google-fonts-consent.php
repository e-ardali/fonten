<?php
/**
 * Standalone regression checks: php tests/google-fonts-consent.php
 * WordPress stubs isolate consent, network, and stylesheet behavior.
 */
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) { return false; }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
define('ABSPATH', '/tmp/');
define('FONTEN_TEST_ROOT', dirname(__DIR__));
define('MB_IN_BYTES', 1048576);
define('DAY_IN_SECONDS', 86400);
define('MINUTE_IN_SECONDS', 60);
$hooks = $options = $transients = $styles = [];
$requests = 0;
$remote_error = $upload_error = false;
$allowed = true;
$nonce_valid = true;
$upload_root = '/tmp/fonten-check-' . bin2hex(random_bytes(4));
$multisite = false;
$current_site = 1;
$site_states = $blog_stack = [];
$deactivation_hooks = [];
class WP_Error {
    private $code; private $message;
    function __construct($code, $message) { $this->code = $code; $this->message = $message; }
    function get_error_code() { return $this->code; }
    function get_error_message() { return $this->message; }
}
class Redirected extends Exception {}
class JsonResult extends Exception {
    public $success; public $data; public $status;
    function __construct($success, $data, $status) { $this->success = $success; $this->data = $data; $this->status = $status; }
}
function wp_send_json_error($data, $status) { throw new JsonResult(false, $data, $status); }
function wp_send_json_success($data) { throw new JsonResult(true, $data, 200); }
function check_ajax_referer($action, $key) { if (!$GLOBALS['nonce_valid']) { throw new RuntimeException('nonce rejected'); } }

function is_wp_error($v) { return $v instanceof WP_Error; }
function __($s, $domain = '') { return $s; }
function esc_html__($s, $domain = '') { return htmlspecialchars($s); }
function esc_html($s) { return htmlspecialchars($s); }
function esc_attr($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_url($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_html_e($s, $domain) { echo esc_html($s); }
function get_transient($key) { return $GLOBALS['transients'][$key] ?? false; }
function set_transient($key, $value, $ttl) { $GLOBALS['transients'][$key] = $value; }
function get_option($key, $default = false) { return $GLOBALS['options'][$key] ?? $default; }
function update_option($key, $value, $autoload = null) { $GLOBALS['options'][$key] = $value; return true; }
function wp_remote_get($url, $args) {
    $GLOBALS['requests']++;
    return $GLOBALS['remote_error'] ? new WP_Error('http', 'offline') : ['body' => json_encode(['familyMetadataList' => [['family' => 'Estedad', 'fonts' => ['400' => [], '700' => []]], ['family' => 'Roboto', 'fonts' => ['400' => [], '700' => []]]]])];
}
function wp_remote_retrieve_response_code($r) { return 200; }
function wp_remote_retrieve_body($r) { return $r['body']; }
function trailingslashit($s) { return rtrim($s, '/') . '/'; }
function wp_upload_dir($date = null, $create = true) { return ['basedir' => $GLOBALS['upload_root'], 'baseurl' => 'https://example.test/uploads', 'error' => $GLOBALS['upload_error']]; }
function wp_mkdir_p($p) { return is_dir($p) || @mkdir($p, 0777, true); }
function wp_generate_password($n, $special, $extra) { return bin2hex(random_bytes($n / 2)); }
function plugin_dir_path($p) { return dirname($p) . '/'; }
function plugin_dir_url($p) { return 'https://example.test/plugins/fonten/'; }
function add_action($hook, $callback) { $GLOBALS['hooks'][$hook][] = $callback; }
function wp_create_nonce($action) { return 'test-nonce'; }
function wp_localize_script($handle, $name, $data) { $GLOBALS['localized'][$name] = $data; }
function register_deactivation_hook($file, $callback) { $GLOBALS['deactivation_hooks'][$file] = $callback; }
function delete_option($key) { unset($GLOBALS['options'][$key]); return true; }
function delete_transient($key) { unset($GLOBALS['transients'][$key]); return true; }
function wp_delete_file($file) { @unlink($file); }
function is_multisite() { return $GLOBALS['multisite']; }
function get_current_network_id() { return 1; }
function get_sites($args) { return array_slice(array_keys($GLOBALS['site_states']), $args['offset'], $args['number']); }
function switch_to_blog($id) {
    $GLOBALS['site_states'][$GLOBALS['current_site']] = [$GLOBALS['options'], $GLOBALS['transients'], $GLOBALS['upload_root']];
    $GLOBALS['blog_stack'][] = $GLOBALS['current_site'];
    $GLOBALS['current_site'] = $id;
    [$GLOBALS['options'], $GLOBALS['transients'], $GLOBALS['upload_root']] = $GLOBALS['site_states'][$id];
}
function restore_current_blog() {
    $GLOBALS['site_states'][$GLOBALS['current_site']] = [$GLOBALS['options'], $GLOBALS['transients'], $GLOBALS['upload_root']];
    $GLOBALS['current_site'] = array_pop($GLOBALS['blog_stack']);
    [$GLOBALS['options'], $GLOBALS['transients'], $GLOBALS['upload_root']] = $GLOBALS['site_states'][$GLOBALS['current_site']];
}
function wp_enqueue_style($handle, $url, $deps, $version) { $GLOBALS['styles'][$handle] = compact('url', 'deps', 'version'); }
function current_user_can($cap) { return $GLOBALS['allowed']; }
function check_admin_referer($action) { if (!$GLOBALS['nonce_valid']) { throw new RuntimeException('nonce rejected'); } }
function wp_die($message, $title, $args) { throw new RuntimeException('access rejected'); }
function sanitize_text_field($s) { return strip_tags(trim($s)); }
function sanitize_key($s) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower($s)); }
function wp_unslash($s) { return stripslashes($s); }
function admin_url($s) { return 'https://example.test/wp-admin/' . $s; }
function add_query_arg($args, $url) { return $url . '?' . http_build_query($args); }
function wp_safe_redirect($url) { throw new Redirected($url); }
function wp_nonce_field($action) { echo '<input name="_wpnonce" value="test">'; }
function selected($a, $b = true) { if ($a === $b) { echo 'selected="selected"'; } }
function disabled($a, $b = true) { if ($a === $b) { echo 'disabled="disabled"'; } }
function wp_enqueue_script($handle, $url, $deps, $version, $footer) { $GLOBALS['scripts'][$handle] = compact('url', 'deps', 'version', 'footer'); }
function checked($a, $b = true) { if ($a === $b) { echo 'checked="checked"'; } }
function submit_button($label) { echo '<button>' . esc_html($label) . '</button>'; }
function get_admin_page_title() { return 'Fonten Settings'; }
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } echo "PASS: $message\n"; }
function save($family = '', $enabled = true) {
    $_POST = ['fonten_family' => $family];
    if ($enabled) { $_POST['fonten_google_fonts_enabled'] = '1'; }
    try { (new Fonten_Admin())->save_font(); } catch (Redirected $r) { return $r->getMessage(); }
    throw new RuntimeException('Missing redirect');
}
require FONTEN_TEST_ROOT . '/fonten.php';
$fonts = new Fonten_Fonts();
$admin = new Fonten_Admin();
function render_page() {
    ob_start();
    (new Fonten_Admin())->render_settings_page();
    return ob_get_clean();
}

try {
    $admin->enqueue_assets('settings_page_fonten');
    check(isset($GLOBALS['scripts']['fonten-settings']) && strpos($GLOBALS['scripts']['fonten-settings']['url'], 'assets/js/settings.js') !== false, 'Local checkbox controls load on settings page without consent');
    $html = render_page();
    check($requests === 0 && !$styles && !Fonten_Fonts::is_enabled(), 'Fresh installation makes no server requests and enqueues no browser styles');
    check(strpos($html, 'Enable Google Fonts') !== false && strpos($html, 'checked="checked"') === false && strpos($html, 'name="fonten_family" disabled="disabled"') !== false, 'Consent is unchecked and font selection is unavailable before saving');
    check(strpos($html, 'https://developers.google.com/fonts/terms') !== false && strpos($html, 'https://policies.google.com/privacy') !== false, 'Settings explain connections and link terms and privacy');
    check(is_wp_error($fonts->get_catalog()) && is_wp_error($fonts->generate_stylesheet('Estedad')) && $requests === 0, 'Service methods also block requests and CSS generation without consent');

    // Simulate a pre-consent installation with saved font, generated CSS, and cached catalog.
    wp_mkdir_p($upload_root . '/fonten');
    $legacy_file = 'admin-' . str_repeat('a', 64) . '.css';
    file_put_contents($upload_root . '/fonten/' . $legacy_file, '@import url("https://fonts.googleapis.com/css2?family=Estedad");');
    $options['fonten_settings'] = ['family' => 'Estedad', 'file' => $legacy_file];
    $transients['fonten_google_fonts'] = ['Estedad' => ['axis' => 'wght', 'weights' => [400, 700]]];
    $options['fonten_google_fonts_backup'] = $transients['fonten_google_fonts'];
    $admin->enqueue_assets();
    render_page();
    check(!$styles && $requests === 0 && !Fonten_Fonts::is_enabled(), 'Legacy selection, CSS, and catalog do not imply consent');
    check(is_wp_error($fonts->get_catalog()), 'Even cached catalogs require explicit consent');

    $old_settings = get_option('fonten_settings');
    check(strpos(save('Roboto', false), 'fonten_status=disabled') !== false && $requests === 0 && get_option('fonten_settings') === $old_settings, 'Saving disabled ignores submitted font without contacting Google');
    $transients = [];
    $before_ajax = $requests;
    $allowed = false;
    $_POST = ['consent' => '1'];
    try { $admin->get_fonts(); } catch (JsonResult $json) { check(!$json->success && $json->status === 403 && $requests === $before_ajax, 'Catalog endpoint rejects unauthorized requests'); }
    $allowed = true;
    $_POST = [];
    try { $admin->get_fonts(); } catch (JsonResult $json) { check(!$json->success && $json->status === 400 && $requests === $before_ajax, 'Catalog endpoint requires checkbox consent'); }
    $nonce_valid = false;
    $_POST = ['consent' => '1'];
    try { $admin->get_fonts(); throw new Exception('nonce allowed'); } catch (RuntimeException $e) { check($e->getMessage() === 'nonce rejected' && $requests === $before_ajax, 'Catalog endpoint rejects invalid nonces'); }
    $nonce_valid = true;
    try { $admin->get_fonts(); } catch (JsonResult $json) { check($json->success && in_array('Roboto', $json->data['fonts'], true) && !Fonten_Fonts::is_enabled(), 'Checking fetches the catalog without persisting dashboard consent'); }
    check($requests === $before_ajax + 1, 'Explicit checkbox request may contact Google before saving');
    $transients = [];
    $requests = 0;
    check(strpos(save(''), 'fonten_status=enabled') !== false && Fonten_Fonts::is_enabled() && $requests === 0 && get_option('fonten_settings') === $old_settings, 'Explicit opt-in saves consent before any catalog fetch');
    $html = render_page();
    check($requests === 1 && strpos($html, 'name="fonten_family"') !== false && strpos($html, 'checked="checked"') !== false, 'Catalog and font selection become available after saved opt-in');
    render_page();
    check($requests === 1, 'Catalog cache avoids repeat requests');

    $url = save('Roboto');
    $settings = get_option('fonten_settings');
    $path = $upload_root . '/fonten/' . $settings['file'];
    $css = file_get_contents($path);
    check(strpos($url, 'fonten_status=saved') !== false && strpos($css, '--fonten-admin-font: "Roboto"') !== false && strpos($css, 'fonts.googleapis.com') !== false, 'Opted-in font save generates the selected Google stylesheet');
    $styles = [];
    $admin->enqueue_assets();
    check(count($styles) === 1 && strpos($styles['fonten-admin']['url'], $settings['file']) !== false, 'Generated CSS is enqueued only with consent');
    check(strpos(save('Unknown Font'), 'invalid_font') !== false && get_option('fonten_settings') === $settings, 'Invalid font does not overwrite previous selection');
    check(strpos(save('x";}body{display:none}/*'), 'invalid_font') !== false, 'Reject CSS injection');
    $upload_error = 'unwritable';
    check(strpos(save('Estedad'), 'css_write_failed') !== false && get_option('fonten_settings') === $settings && file_get_contents($path) === $css, 'Write errors preserve previous settings and CSS');
    $upload_error = false;

    $allowed = false;
    try { save('', false); throw new Exception('access allowed'); } catch (RuntimeException $e) { check($e->getMessage() === 'access rejected' && Fonten_Fonts::is_enabled(), 'Unauthorized users cannot change consent'); }
    $allowed = true;
    $nonce_valid = false;
    try { save('', false); throw new Exception('nonce allowed'); } catch (RuntimeException $e) { check($e->getMessage() === 'nonce rejected' && Fonten_Fonts::is_enabled(), 'Invalid nonces cannot change consent'); }
    $nonce_valid = true;

    $remote_error = true;
    $transients = [];
    unset($options['fonten_google_fonts_backup']);
    $html = render_page();
    check(strpos($html, '<form') !== false && strpos($html, 'Enable Google Fonts') !== false && strpos($html, 'Google Fonts could not be loaded') !== false && strpos($html, 'required') === false, 'Catalog failure keeps opt-out form usable');
    $before_disable = $requests;
    check(strpos(save('', false), 'fonten_status=disabled') !== false && !Fonten_Fonts::is_enabled(), 'Opt-out saves even during Google outage');
    $styles = [];
    $admin->enqueue_assets();
    $html = render_page();
    $fonts->get_catalog();
    $fonts->generate_stylesheet('Roboto');
    check($requests === $before_disable && !$styles && strpos($html, 'checked="checked"') === false, 'After opt-out no catalog requests or font styles remain, restoring WordPress typography');
    check(get_option('fonten_settings') === $settings, 'Opt-out retains previous selection without loading it');

    unset($options['fonten_settings']);
    $remote_error = false;
    save();
    $styles = [];
    $admin->enqueue_assets();
    check(!$styles, 'No automatic Estedad fallback after enabling without a saved font');
    check(!isset($hooks['wp_enqueue_scripts']), 'Public website is unaffected');
    save('', false);
    check(strpos(save('Roboto'), 'fonten_status=saved') !== false && Fonten_Fonts::is_enabled() && get_option('fonten_settings')['family'] === 'Roboto', 'First save can enable Google Fonts and apply the selected font together');
    $cleanup = $deactivation_hooks[FONTEN_TEST_ROOT . '/fonten.php'] ?? null;
    check(is_callable($cleanup), 'Cleanup hook is registered against the main plugin file');
    $options['unrelated_option'] = 'keep';
    $options['fonten_settings'] = $settings;
    $options['fonten_google_fonts_backup'] = ['cached'];
    $options['_transient_fonten_google_fonts'] = ['stale database cache'];
    $options['_transient_timeout_fonten_google_fonts'] = 1;
    $transients['fonten_google_fonts'] = ['object cache'];
    $temporary = $path . '.Abc123Def456.tmp';
    file_put_contents($temporary, 'unfinished css');
    $unrelated = $upload_root . '/fonten/keep.txt';
    file_put_contents($unrelated, 'unrelated');
    $before_cleanup = $requests;
    $cleanup(false);
    check($options === ['unrelated_option' => 'keep'] && !$transients, 'Deactivation deletes consent, settings, backup, transient value and timeout, and object cache');
    check(!file_exists($path) && !file_exists($temporary) && !file_exists($upload_root . '/fonten/' . $legacy_file), 'Deactivation removes current, old, and unfinished generated CSS');
    check(file_exists($unrelated) && $requests === $before_cleanup, 'Cleanup preserves unrelated files and makes no external requests');
    unlink($unrelated);
    $cleanup(false);
    check(!is_dir($upload_root . '/fonten'), 'Repeated cleanup is safe and removes the empty Fonten directory');
    $styles = [];
    $admin->enqueue_assets();
    render_page();
    check(!Fonten_Fonts::is_enabled() && !$styles && $requests === $before_cleanup, 'Reactivation starts with no consent or Google requests');

    wp_mkdir_p($upload_root . '/outside');
    $outside = $upload_root . '/outside/' . $legacy_file;
    file_put_contents($outside, 'keep outside file');
    symlink($upload_root . '/outside', $upload_root . '/fonten');
    $cleanup(false);
    check(is_file($outside), 'Cleanup does not follow a symlinked Fonten directory');
    unlink($upload_root . '/fonten');
    unlink($outside);
    rmdir($upload_root . '/outside');

    $multisite = true;
    $options['fonten_google_fonts_enabled'] = '1';
    $site_states[1] = [$options, ['fonten_google_fonts' => ['cached']], $upload_root];
    $second_root = $upload_root . '-site2';
    wp_mkdir_p($second_root . '/fonten');
    file_put_contents($second_root . '/fonten/' . $legacy_file, 'site two css');
    $site_states[2] = [['fonten_settings' => $settings, 'other_site_option' => 'keep'], ['fonten_google_fonts' => ['cached']], $second_root];
    $cleanup(true);
    check($options === ['unrelated_option' => 'keep'] && $site_states[2][0] === ['other_site_option' => 'keep'] && !$site_states[2][1], 'Network deactivation removes each site data and preserves unrelated options');
    check(!is_dir($second_root . '/fonten') && $current_site === 1 && !$blog_stack, 'Network cleanup removes site files and restores the original site');
    rmdir($second_root);
    echo "All checks passed.\n";
} finally {
    foreach (glob($upload_root . '/fonten/*') ?: [] as $file) { unlink($file); }
    if (is_dir($upload_root . '/fonten')) { rmdir($upload_root . '/fonten'); }
    if (is_dir($upload_root)) { rmdir($upload_root); }
}
