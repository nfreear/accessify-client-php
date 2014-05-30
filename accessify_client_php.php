<?php
/**
 *
 * @author Nick Freear, 29 May 2014.
 */

class Accessify_Client_Php {

  const API_URL  = '//accessifywiki.appspot.com/';  //No "http:"
  const WIKI_URL = 'http://accessify.wikia.com/wiki/WordPress';
  const CACHE_JS = 'cache/accessify-site-fixes.js';
  const APP_ID   = 'accessify-client-php';
  const FIX_OPT  = '&min=1&callback=__accessify_IPG&app=';
  const SITE_ID_REGEX = '/^Fix:[\w\_]+$/';

  protected $site_id = '';
  protected $mode_cache = FALSE;


  public function __construct() {
  
  }


  /**
  * @link http://accessify.wikia.com/wiki/Build_fix_js?q=Fix:Example_fixes
  */
  protected function print_glue_javascript() {
    ?>

  function __accessify_IPG(fixes) {
    "use strict";

    var res,
      pat = /debug/,
      L = document.location;

    function log(s) {
      if (typeof console !== "undefined" && (L.search.match(pat) || L.hash.match(pat))) {
        console.log(arguments.length > 1 ? arguments : s);
      }
    }

    log("AccessifyHTML5");

    res = AccessifyHTML5(false, fixes);

    log(res);
  }
<?php
  }

  public function is_valid_site_id( $site_id ) {
    return $site_id && preg_match( '/^Fix:[\w\_]+$/', $site_id );
  } 

  protected function lib_url() {
    return self::API_URL .'browser/js/accessifyhtml5.js';
  }

  protected function fix_url() {
    return self::API_URL .'fix?q='. $this->site_id . self::FIX_OPT . self::APP_ID;
  }

}

