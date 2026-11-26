<?php
/**
 * Template Name: Bhavik Enterprises
 * Template Post Type: page
 *
 * Legacy alias of the site home page (/bhavik-enterprises/).
 *
 * The whole website is now the Bhavik Enterprises website, so this template no
 * longer renders a standalone page with its own header/footer. It renders the
 * exact same Bhavik Enterprises layout as the home page through the shared
 * header.php and footer.php — one header, one footer, no duplicated chrome.
 *
 * The canonical content lives in page-template/template-home.php.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

require get_theme_file_path( '/page-template/template-home.php' );
