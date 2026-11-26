<?php
/** Products mega menu for the shared primary navigation only. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function ab_products_mega_menu_markup( $extra_links = '' ) {
    ob_start();
    require get_theme_file_path( '/template-parts/products-mega-menu.php' );
    return ob_get_clean();
}

/** Recognise the Products branch without changing saved Appearance > Menus data. */
function ab_is_products_menu_item( $item ) {
    if ( ! empty( $item->menu_item_parent ) ) { return false; }
    if ( in_array( 'ab-menu-mega', (array) $item->classes, true ) ) { return true; }
    $path = untrailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) );
    return $path === untrailingslashit( (string) wp_parse_url( home_url( '/products/' ), PHP_URL_PATH ) )
        || $path === untrailingslashit( (string) wp_parse_url( home_url( '/product-range/' ), PHP_URL_PATH ) )
        || 'products' === sanitize_title( wp_strip_all_tags( $item->title ) );
}

/** Existing custom product links remain available beneath the curated material grid. */
function ab_is_curated_product_url( $url ) {
    $base = trailingslashit( home_url( '/' ) );
    if ( 0 !== strpos( $url, $base ) ) { return false; }
    $relative = trim( (string) wp_parse_url( substr( $url, strlen( $base ) ), PHP_URL_PATH ), '/' );
    return (bool) preg_match( '~^(products|product-range|product-families|(?:ldpe|hdpe|pp|pvc|ps|abs|eva)(?:-(?:prime|offgrade|recycled|scrap-regrind))?)$~', $relative )
        || ( '' === $relative && in_array( wp_parse_url( $url, PHP_URL_FRAGMENT ), array( 'products', 'materials', 'product-families' ), true ) );
}

add_filter( 'wp_nav_menu_objects', 'ab_prepare_products_mega_menu', 20, 2 );
function ab_prepare_products_mega_menu( $items, $args ) {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) { return $items; }
    $owners = array();
    $roots = array();
    foreach ( $items as $item ) {
        if ( ! ab_is_products_menu_item( $item ) ) { continue; }
        $roots[ $item->ID ] = $item;
        $owners[ $item->ID ] = $item->ID;
        $item->classes = array_unique( array_merge( (array) $item->classes, array( 'menu-item-has-children', 'ab-menu-mega' ) ) );
        $item->ab_extra_product_links = '';
    }
    if ( ! $roots ) { return $items; }
    // Follow all levels, including custom menus whose children are listed first.
    do {
        $changed = false;
        foreach ( $items as $item ) {
            if ( isset( $owners[ $item->ID ] ) || ! isset( $owners[ $item->menu_item_parent ] ) ) { continue; }
            $owners[ $item->ID ] = $owners[ $item->menu_item_parent ];
            $changed = true;
        }
    } while ( $changed );
    $visible = array();
    foreach ( $items as $item ) {
        if ( isset( $owners[ $item->ID ] ) && ! isset( $roots[ $item->ID ] ) ) {
            if ( ! ab_is_curated_product_url( $item->url ) ) {
                $roots[ $owners[ $item->ID ] ]->ab_extra_product_links .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( wp_strip_all_tags( $item->title ) ) . '</a>';
            }
            continue;
        }
        $visible[] = $item;
    }
    return $visible;
}

add_filter( 'walker_nav_menu_start_el', 'ab_append_products_mega_menu', 20, 4 );
function ab_append_products_mega_menu( $output, $item, $depth, $args ) {
    if ( 0 === $depth && ! empty( $args->theme_location ) && 'primary' === $args->theme_location && in_array( 'ab-menu-mega', (array) $item->classes, true ) ) {
        $output .= ab_products_mega_menu_markup( isset( $item->ab_extra_product_links ) ? $item->ab_extra_product_links : '' );
    }
    return $output;
}
