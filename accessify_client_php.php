<?php
/**
 *
 * @author Nick Freear, 29 May 2014.
 */

class Accessify_Client_Php {

  const API_URL  = 'http://accessifywiki.appspot.com/';  //Was: No "http:"
  const WIKI_URL = 'http://accessify.wikia.com/wiki/';
  const COMPILER_URL = 'https://closure-compiler.appspot.com/compile';
  const CACHE_JS = 'cache/accessify-site-fixes.js';
  const APP_ID   = 'accessify-client-php';
  const FIX_OPT  = '&min=1&callback=__accessify_IPG&app=';
  const SITE_ID_REGEX = '/^Fix:[\w\_]+$/';

  const Q_SIMPLE = 'SIMPLE_OPTIMIZATIONS';
  const Q_WHITE  = 'WHITESPACE_ONLY';


  protected $site_id = '';
  protected $mode_cache = FALSE;
  protected $app_id;


  public function __construct($site_id = NULL, $app_id = NULL) {
    $this->site_id = $site_id;
    $this->app_id = $app_id ? $app_id : self::APP_ID;
  }


  /**
  * @link http://accessify.wikia.com/wiki/Build_fix_js?q=Fix:Example_fixes
  */
  protected function print_glue_javascript() {
    ?>

  AC5U = window.AC5U || {};

  function __accessify_IPG(fixes) {
    "use strict";

    var G = AC5U,
      L = document.location,
      pat = /debug/,
      debug = G.debug || L.search.match(pat) || L.hash.match(pat);

    function log(s) {
      window.console && debug &&
        console.log(arguments.length > 1 ? arguments : s);
    }

    if (G.result) {
      return log("AccessifyHTML5: already run");
    }

    log("AccessifyHTML5: running");

    G.result = AccessifyHTML5(false, fixes);

    log(G.result);
  }
<?php
  }


  public function print_fix_test_scripts() {
    ?>
    <script src="<?php echo $this->js_library_url() ?>" id="accessify-js"></script>
    <script><?php $this->print_glue_javascript() ?></script>
    <script src="<?php echo $this->jsonp_fix_url() ?>"></script>
<?php
  }


  public function debug_config_scripts() {
    ?><script>AC5U = { debug: true }</script>
<?php
  }

  public function wiki_url($page = NULL) {
    return self::WIKI_URL . str_replace(' ', '_', $page);
  }

  public function is_valid_site_id( $site_id ) {
    return $site_id && preg_match( self::SITE_ID_REGEX, $site_id );
  } 


  protected function js_library_url() {
    return self::API_URL .'browser/js/accessifyhtml5.js';
  }

  protected function js_inpage_callback_url() {
    return self::API_URL .'browser/js/accessify-inpage-callback.js';
  }

  public function jsonp_fix_url() {
    return self::API_URL .'fix?q='. $this->site_id . self::FIX_OPT . $this->app_id;
  }

  protected function build_cache_fix_javascript() {
    // TODO: Implement cached fix JS.
  }


  public function compiler_url() {
    return self::COMPILER_URL;
  }

  /** @link http://accessifywiki.appspot.com/site/build.html
  */
  public function compiler_query_params($site_id, $compilation_level = self::Q_SIMPLE) {
    $this->site_id = $site_id ? $site_id : $this->site_id;

    $params = array(
      'compilation_level' => $compilation_level,
      'code_url' => array(
        'http://dl.dropbox.com/u/3203144/wai-aria/inpage-header.js',
        $this->js_library_url(),
        $this->js_inpage_callback_url(),
        $this->jsonp_fix_url(),
      ),
      'js_code' => '',
      'output_format' => 'json',  //Or, 'text'
      'output_info' => array( 'compiled_code', 'statistics' ),
      'output_file_name' => 'accessify-my-site-fixes.min.js',
      'formatting' => 'print_input_delimiter',
    );
    return preg_replace(
      '/%5B[0-9]+%5D/simU',
      '',   #'%5B%5D',
      http_build_query( $params )
    );
  }

}

