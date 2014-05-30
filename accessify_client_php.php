<?php
/**
 *
 * @author Nick Freear, 29 May 2014.
 */

class Accessify_Client_Php {

  const API_URL  = '//accessifywiki.appspot.com/';  //No "http:"
  const WIKI_URL = 'http://accessify.wikia.com/wiki/';
  const CACHE_JS = 'cache/accessify-site-fixes.js';
  const APP_ID   = 'accessify-client-php';
  const FIX_OPT  = '&min=1&callback=__accessify_IPG&app=';
  const SITE_ID_REGEX = '/^Fix:[\w\_]+$/';

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

    log("AccessifyHTML5: run");

    G.result = AccessifyHTML5(false, fixes);

    log(G.result);
  }
<?php
  }


  public function print_fix_test_scripts() {
    ?>
    <script src="<?php echo $this->lib_url() ?>" id="accessify-js"></script>
    <script><?php $this->print_glue_javascript() ?></script>
    <script src="<?php echo $this->fix_url() ?>"></script>
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


  protected function lib_url() {
    return self::API_URL .'browser/js/accessifyhtml5.js';
  }

  public function fix_url() {
    return self::API_URL .'fix?q='. $this->site_id . self::FIX_OPT . $this->app_id;
  }

  protected function build_cache_fix_javascript() {
    // TODO: Implement cached fix JS.
  }

}

