<?php
/** Provision static supply-type landing pages, without a runtime product-content renderer. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Keep existing editorial copy, publication status, custom templates and SEO fields.
 * Failures leave the migration incomplete so WordPress can retry on a later request.
 */
function ab_ensure_supply_landing_page( $slug, $title, $template, $description ) {
    $page = get_page_by_path( $slug, OBJECT, 'page' );
    if ( $page ) {
        $id = (int) $page->ID;
        $assigned = get_post_meta( $id, '_wp_page_template', true );
        if ( $assigned && 'default' !== $assigned && $template !== $assigned ) {
            // A site owner's custom route takes precedence. Review it in Pages.
            return true;
        }
    } else {
        $id = wp_insert_post( array(
            'post_type' => 'page', 'post_status' => 'publish',
            'post_name' => $slug, 'post_title' => $title,
        ), true );
        if ( is_wp_error( $id ) || ! $id ) { return false; }
    }
    update_post_meta( $id, '_wp_page_template', $template );
    if ( ! get_post_meta( $id, 'ab_meta_title', true ) ) {
        update_post_meta( $id, 'ab_meta_title', $title . ' | Bhavik Enterprises' );
    }
    if ( ! get_post_meta( $id, 'ab_meta_description', true ) ) {
        update_post_meta( $id, 'ab_meta_description', $description );
    }
    return true;
}

add_action( 'init', 'ab_install_material_supply_pages', 35 );
function ab_install_material_supply_pages() {
    if ( get_option( 'ab_static_supply_pages_v1_ready' ) ) { return; }
    // Prevent concurrent first visits from creating duplicate suffixed pages.
    $lock_key = 'ab_static_supply_pages_installing';
    $locked_at = (int) get_option( $lock_key );
    if ( $locked_at && time() - $locked_at > 300 ) { delete_option( $lock_key ); }
    if ( ! add_option( $lock_key, time(), '', false ) ) { return; }
    try {
        $ready = true;
        $ready = ab_ensure_supply_landing_page( 'ldpe', 'Low-Density Polyethylene (LDPE)', 'page-template/template-material-LDPE.php', 'Explore LDPE prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ldpe-prime', 'Prime / Virgin LDPE', 'page-template/template-material-LDPE-prime.php', 'Prime / Virgin LDPE from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ldpe-offgrade', 'Off-grade LDPE', 'page-template/template-material-LDPE-offgrade.php', 'Off-grade LDPE from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ldpe-recycled', 'Recycled LDPE', 'page-template/template-material-LDPE-recycled.php', 'Recycled LDPE from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ldpe-scrap-regrind', 'Scrap / Regrind LDPE', 'page-template/template-material-LDPE-scrap-regrind.php', 'Scrap / Regrind LDPE from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'hdpe', 'High-Density Polyethylene (HDPE)', 'page-template/template-material.php', 'Explore HDPE prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'hdpe-prime', 'Prime / Virgin HDPE', 'page-template/template-material-HDPE-prime.php', 'Prime / Virgin HDPE from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'hdpe-offgrade', 'Off-grade HDPE', 'page-template/template-material-HDPE-offgrade.php', 'Off-grade HDPE from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'hdpe-recycled', 'Recycled HDPE', 'page-template/template-material-HDPE-recycled.php', 'Recycled HDPE from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'hdpe-scrap-regrind', 'Scrap / Regrind HDPE', 'page-template/template-material-HDPE-scrap-regrind.php', 'Scrap / Regrind HDPE from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pp', 'Polypropylene (PP)', 'page-template/template-material-PP.php', 'Explore PP prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pp-prime', 'Prime / Virgin PP', 'page-template/template-material-PP-prime.php', 'Prime / Virgin PP from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pp-offgrade', 'Off-grade PP', 'page-template/template-material-PP-offgrade.php', 'Off-grade PP from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pp-recycled', 'Recycled PP', 'page-template/template-material-PP-recycled.php', 'Recycled PP from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pp-scrap-regrind', 'Scrap / Regrind PP', 'page-template/template-material-PP-scrap-regrind.php', 'Scrap / Regrind PP from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pvc', 'Polyvinyl Chloride (PVC)', 'page-template/template-material-PVC.php', 'Explore PVC prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pvc-prime', 'Prime / Virgin PVC', 'page-template/template-material-PVC-prime.php', 'Prime / Virgin PVC from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pvc-offgrade', 'Off-grade PVC', 'page-template/template-material-PVC-offgrade.php', 'Off-grade PVC from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pvc-recycled', 'Recycled PVC', 'page-template/template-material-PVC-recycled.php', 'Recycled PVC from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'pvc-scrap-regrind', 'Scrap / Regrind PVC', 'page-template/template-material-PVC-scrap-regrind.php', 'Scrap / Regrind PVC from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ps', 'Polystyrene (PS)', 'page-template/template-material-PS.php', 'Explore PS prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ps-prime', 'Prime / Virgin PS', 'page-template/template-material-PS-prime.php', 'Prime / Virgin PS from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ps-offgrade', 'Off-grade PS', 'page-template/template-material-PS-offgrade.php', 'Off-grade PS from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ps-recycled', 'Recycled PS', 'page-template/template-material-PS-recycled.php', 'Recycled PS from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'ps-scrap-regrind', 'Scrap / Regrind PS', 'page-template/template-material-PS-scrap-regrind.php', 'Scrap / Regrind PS from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'abs', 'Acrylonitrile Butadiene Styrene (ABS)', 'page-template/template-material-ABS.php', 'Explore ABS prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'abs-prime', 'Prime / Virgin ABS', 'page-template/template-material-ABS-prime.php', 'Prime / Virgin ABS from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'abs-offgrade', 'Off-grade ABS', 'page-template/template-material-ABS-offgrade.php', 'Off-grade ABS from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'abs-recycled', 'Recycled ABS', 'page-template/template-material-ABS-recycled.php', 'Recycled ABS from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'abs-scrap-regrind', 'Scrap / Regrind ABS', 'page-template/template-material-ABS-scrap-regrind.php', 'Scrap / Regrind ABS from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'eva', 'Ethylene Vinyl Acetate (EVA)', 'page-template/template-material-EVA.php', 'Explore EVA prime, off-grade, recycled and scrap / regrind supply types, material guidance and enquiry details from Bhavik Enterprises.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'eva-prime', 'Prime / Virgin EVA', 'page-template/template-material-EVA-prime.php', 'Prime / Virgin EVA from Bhavik Enterprises. Review manufacturer grade, technical documentation and application-specific selection checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'eva-offgrade', 'Off-grade EVA', 'page-template/template-material-EVA-offgrade.php', 'Off-grade EVA from Bhavik Enterprises. Review lot-specific deviations, actual batch results and acceptance checks for your process.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'eva-recycled', 'Recycled EVA', 'page-template/template-material-EVA-recycled.php', 'Recycled EVA from Bhavik Enterprises. Review feedstock source, reprocessing, contamination and batch-consistency checks.' ) && $ready;
        $ready = ab_ensure_supply_landing_page( 'eva-scrap-regrind', 'Scrap / Regrind EVA', 'page-template/template-material-EVA-scrap-regrind.php', 'Scrap / Regrind EVA from Bhavik Enterprises. Review source segregation, physical form, contamination limits and recovery requirements.' ) && $ready;
        if ( $ready ) { update_option( 'ab_static_supply_pages_v1_ready', 1, false ); }
    } finally {
        delete_option( $lock_key );
    }

}
