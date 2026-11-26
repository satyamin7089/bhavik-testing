<?php
/**
 * Template Name: Homepage
 * Template Post Type: page
 *
 * Home page of the Bhavik Enterprises website.
 *
 * The layout and all copy come from the Bhavik Enterprises template; only the
 * shared site chrome is used (get_header() / get_footer()), so there is exactly
 * one header and one footer across the whole website.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

/*
 * Enquiry status after the shared handler redirects back here.
 * The handler appends `ab_enquiry`; `enquiry` is kept for older links.
 */
$ab_contact_result = '';
foreach ( array( 'ab_enquiry', 'enquiry' ) as $ab_result_key ) {
    if ( isset( $_GET[ $ab_result_key ] ) && is_scalar( $_GET[ $ab_result_key ] ) ) {
        $ab_contact_result = sanitize_key( wp_unslash( $_GET[ $ab_result_key ] ) );
        break;
    }
}

$bv_phone     = '+91 7007049478';
$bv_phone_raw = '+917007049478';
$bv_whatsapp  = '917007049478';
$bv_email     = 'info@bhavikenterprises.in';
$bv_address   = 'Village Barhat Bangar, Mandhana Bithoor Road, Kanpur – 209217, UP, India';

$bv_industries = array(
    array(
        'title' => 'Packaging',
        'tag'   => 'LDPE · HDPE',
        'icon'  => '#ab-icon-package',
        'img'   => home_url( '/wp-content/uploads/2026/11/Packaging.webp' ),
        'alt'   => 'Flexible LDPE packaging film rolls and rigid HDPE containers in a modern plant',
        'desc'  => 'Flexible shrink and liner films, heavy-duty industrial bags and rigid blow-moulded containers.',
    ),
    array(
        'title' => 'Pipe Manufacturing',
        'tag'   => 'HDPE · PVC',
        'icon'  => '#ab-icon-pipe',
        'img'   => home_url( '/wp-content/uploads/2026/11/Pipe-Manufacturing.webp' ),
        'alt'   => 'Extruded black HDPE pipes with teal stripes and grey PVC pipes stacked in a facility',
        'desc'  => 'Water supply pipes, agricultural irrigation lines, cable ducting and rigid drainage extrusion.',
    ),
    array(
        'title' => 'Injection Moulding',
        'tag'   => 'PP · HDPE',
        'icon'  => '#ab-icon-mould',
        'img'   => home_url( '/wp-content/uploads/2026/11/Injection-Mouldin.webp' ),
        'alt'   => 'Precision injection-moulded PP and HDPE closures and components in a steel mould press',
        'desc'  => 'Caps, closures, industrial fittings and high-precision multi-cavity moulded components.',
    ),
    array(
        'title' => 'Industrial Products',
        'tag'   => 'HDPE · PP',
        'icon'  => '#ab-icon-cube',
        'img'   => home_url( '/wp-content/uploads/2026/11/Industrial-Products.webp' ),
        'alt'   => 'Heavy-duty moulded HDPE logistics crates, pallets and technical polymer parts',
        'desc'  => 'Material-handling crates, warehouse pallets, chemical drums and rugged technical housings.',
    ),
    array(
        'title' => 'Consumer Goods',
        'tag'   => 'PP · LDPE',
        'icon'  => '#ab-icon-home-goods',
        'img'   => home_url( '/wp-content/uploads/2026/11/Consumer-Goods.webp' ),
        'alt'   => 'Clean moulded household storage containers and durable consumer plastic ware',
        'desc'  => 'Household storage containers, kitchenware, moulded furniture and everyday lifestyle products.',
    ),
    array(
        'title' => 'Recycling Units',
        'tag'   => 'Near virgin · Reprocessed',
        'icon'  => '#ab-icon-recycle',
        'img'   => home_url( '/wp-content/uploads/2026/11/Recycling-Units.webp' ),
        'alt'   => 'High-purity virgin and reprocessed polymer granules flowing in a compounding line',
        'desc'  => 'Consistent reprocessed granules and prime blending resins for compounding and reprocessing lines.',
    ),
);

$bv_faqs = array(
    array(
        'cat' => 'Materials',
        'q'   => 'Which polymer families does Bhavik Enterprises supply?',
        'a'   => 'We supply LDPE, HDPE, PP (Polypropylene), PVC and Polystyrene (PS). Compare prime / virgin, off-grade, recycled and scrap / regrind routes against your processing and end-use specification.',
    ),
    array(
        'cat' => 'Partnership',
        'q'   => 'How is Bhavik Enterprises connected with Abiroverseas LLC?',
        'a'   => 'Bhavik Enterprises is the authorised distributor of Abiroverseas LLC in India — combining global polymer sourcing standards with dedicated local stock and dispatch support.',
    ),
    array(
        'cat' => 'Coverage',
        'q'   => 'Where do you deliver across India?',
        'a'   => 'We dispatch Pan-India through our head office in Kanpur (Uttar Pradesh) and active branch hubs in Mumbai and Mundra for rapid port-to-plant and inland movement.',
    ),
    array(
        'cat' => 'Orders',
        'q'   => 'Do you handle both trial batches and recurring bulk truckloads?',
        'a'   => 'Yes. Share your required polymer grade, monthly or per-order tonnage and delivery pin code — we confirm batch availability, packaging and dispatch timelines upfront.',
    ),
    array(
        'cat' => 'Enquiries',
        'q'   => 'What details help us quote the right grade fastest?',
        'a'   => 'Mention the polymer family (LDPE, HDPE, PP, PVC or granules), near virgin or reprocessed preference, processing method (film, pipe, blow or injection moulding), quantity and destination city.',
    ),
);
?>
<main id="main-content" class="ab-bhavik-page">

    <!-- 1. Hero -->
    <section class="ab-about-hero" id="bv-hero" aria-labelledby="bv-hero-title">
        <div class="ab-wrap">
            <div class="ab-about-hero-grid">
                <div class="ab-about-hero-copy ab-reveal">
                    <span class="ab-eyebrow">Authorised Distributor of Abiroverseas LLC</span>
                    <h1 id="bv-hero-title">Trusted Plastic Raw Material Supplier in India</h1>
                    <p class="lead ab-lead">Bhavik Enterprises supplies near virgin and reprocessed LDPE, HDPE, PP and PVC raw materials to manufacturers across India — backed by verified grade consistency, competitive pricing and dependable dispatch from Kanpur, Mumbai and Mundra.</p>
                    <div class="ab-about-hero-actions ab-hero-actions">
                        <a class="ab-btn" href="<?php echo esc_url( ab_home_anchor_url( 'quick-enquiry' ) ); ?>">REQUEST A QUOTE
                            <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                        </a>
                        <a class="ab-btn ab-btn--outline" href="<?php echo esc_url( home_url( '/products' ) ); ?>">EXPLORE MATERIALS</a>
                    </div>
                </div>
                <div class="ab-about-hero-art ab-reveal ab-reveal-delay-1" aria-label="Polymer resin materials supplied across India">
                    <img class="ab-hero-img" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/hero-banner.png' ) ); ?>" width="960" height="720" alt="Virgin and reprocessed polymer resin granules" fetchpriority="high" decoding="async">
                    <span class="art-tag ab-art-tag">LDPE · HDPE · PP · PVC</span>
                    <div class="glass-card ab-hero-glass-card">
                        <small>Pan-India Dispatch</small>
                        <strong>Kanpur · Mumbai · Mundra</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    
      <!-- tab gird supply -->
    <section class="ab-section ab-product-categories" id="home-product-categories" aria-labelledby="home-product-categories-title" data-category-tabs data-initial-material="hdpe">
        <div class="ab-wrap">
            <svg class="ab-category-sprite" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><defs>
                <symbol id="home-category-icon-arrow" viewBox="0 0 24 24"><path d="M4 12h16m-6-6 6 6-6 6"/></symbol>
                <symbol id="home-category-icon-open" viewBox="0 0 24 24"><path d="M14 4h6v6m0-6L10 14m0-10H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-5"/></symbol>
                <symbol id="home-category-icon-cubes" viewBox="0 0 32 32"><path d="m16 2 8 5-8 5-8-5Zm-8 5v9l8 5 8-5V7M16 12v9M8 16l-7 4 8 5 7-4 7 4 8-5-7-4M1 20v9l8 5v-9m22-5v9l-8 5v-9" transform="translate(1 0) scale(.9)"/></symbol>
                <symbol id="home-category-icon-recycle" viewBox="0 0 32 32"><path d="m10 10 4-7a2 2 0 0 1 4 0l5 8m-1-7 1 7-7-1m9 5 4 8a2 2 0 0 1-2 4h-9m6 4-6-4 4-6M12 27H5a2 2 0 0 1-2-4l5-8m-6 4 6-4 4 6"/></symbol>
                <symbol id="home-category-icon-gear" viewBox="0 0 32 32"><path d="m13 3-1 4-4 2-4-1-2 5 3 3v3l-2 3 3 4 4-1 3 2 1 3h5l1-4 4-2 4 1 2-5-3-3v-3l2-3-3-4-4 1-3-2-1-3Z"/><circle cx="16" cy="16" r="5"/></symbol>
                <symbol id="home-category-icon-globe" viewBox="0 0 32 32"><circle cx="16" cy="16" r="13"/><ellipse cx="16" cy="16" rx="6" ry="13"/><path d="M3 16h26M6 8h20M6 24h20"/></symbol>
                <symbol id="home-category-icon-shield" viewBox="0 0 32 32"><path d="m16 2 12 5v8c0 7-6 12-12 15C10 27 4 22 4 15V7Zm-7 14 5 5 10-11"/></symbol>
                <symbol id="home-category-icon-truck" viewBox="0 0 32 32"><path d="M2 6h18v18H2Zm18 7h6l5 6v5H20M25 13v6h6"/><circle cx="8" cy="25" r="4"/><circle cx="25" cy="25" r="4"/></symbol>
                <symbol id="home-category-icon-factory" viewBox="0 0 32 32"><path d="M3 29V14l9 4v-9l10 5V3h7v26Zm4-6h2m6 0h2m6 0h2"/></symbol>
            </defs></svg>
            <div class="ab-category-heading"><div><span class="ab-eyebrow">Explore by category</span><h2 id="home-product-categories-title">Your material. Your supply type.</h2></div><p>Select a polymer to view its supply types, material guidance and dedicated pages.</p></div>
            <div class="ab-category-toolbar" hidden>
                <span class="ab-category-position" aria-live="polite" aria-atomic="true">2 of 7 · HDPE</span>
                <div class="ab-category-controls"><button class="ab-category-prev" type="button" aria-label="Previous material"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></button><button class="ab-category-next" type="button" aria-label="Next material"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></button></div>
            </div>
            <!-- Grain image sources are independent per material: replace each tab's src separately. -->
            <div class="ab-category-polymers" aria-label="Choose a polymer">
                <a class="ab-category-tab ab-category-tab--ldpe" id="home-category-tab-ldpe" href="#home-category-panel-ldpe" data-material="ldpe"><strong>LDPE</strong><span>Low-Density Polyethylene</span><img data-category-grains="ldpe" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-two.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--hdpe" id="home-category-tab-hdpe" href="#home-category-panel-hdpe" data-material="hdpe"><strong>HDPE</strong><span>High-Density Polyethylene</span><img data-category-grains="hdpe" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-one.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--pp" id="home-category-tab-pp" href="#home-category-panel-pp" data-material="pp"><strong>PP</strong><span>Polypropylene</span><img data-category-grains="pp" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-four.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--pvc" id="home-category-tab-pvc" href="#home-category-panel-pvc" data-material="pvc"><strong>PVC</strong><span>Polyvinyl Chloride</span><img data-category-grains="pvc" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-ten.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--ps" id="home-category-tab-ps" href="#home-category-panel-ps" data-material="ps"><strong>PS</strong><span>Polystyrene</span><img data-category-grains="ps" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-five.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--abs" id="home-category-tab-abs" href="#home-category-panel-abs" data-material="abs"><strong>ABS</strong><span>Acrylonitrile Butadiene Styrene</span><img data-category-grains="abs" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-six.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
                <a class="ab-category-tab ab-category-tab--eva" id="home-category-tab-eva" href="#home-category-panel-eva" data-material="eva"><strong>EVA</strong><span>Ethylene Vinyl Acetate</span><img data-category-grains="eva" src="https://bhavikenterprises.in/wp-content/uploads/2026/11/grain-seven.png" alt="" width="560" height="420" loading="lazy" decoding="async"></a>
            </div>
            <div class="ab-category-panels">
                <div class="ab-category-panel" id="home-category-panel-ldpe" data-material="ldpe" aria-labelledby="home-category-tab-ldpe">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>LDPE</h3><p>Low-Density Polyethylene</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/ldpe/' ) ); ?>">View LDPE material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in LDPE</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/LDPE_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>LDPE – Prime / Virgin</h5><p>Fresh LDPE resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-prime/' ) ); ?>">Explore LDPE Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/LDPE_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>LDPE – Off-grade</h5><p>Lot-specific LDPE materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-offgrade/' ) ); ?>">Explore LDPE Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/LDPE_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>LDPE – Recycled</h5><p>Recovered and reprocessed LDPE. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-recycled/' ) ); ?>">Explore LDPE Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/LDPE_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>LDPE – Scrap / Regrind</h5><p>Sorted LDPE scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-scrap-regrind/' ) ); ?>">Explore LDPE Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-hdpe" data-material="hdpe" aria-labelledby="home-category-tab-hdpe">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>HDPE</h3><p>High-Density Polyethylene</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/hdpe/' ) ); ?>">View HDPE material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in HDPE</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/HDPE_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>HDPE – Prime / Virgin</h5><p>Fresh HDPE resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-prime/' ) ); ?>">Explore HDPE Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/HDPE_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>HDPE – Off-grade</h5><p>Lot-specific HDPE materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-offgrade/' ) ); ?>">Explore HDPE Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/HDPE_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>HDPE – Recycled</h5><p>Recovered and reprocessed HDPE. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-recycled/' ) ); ?>">Explore HDPE Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/HDPE_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>HDPE – Scrap / Regrind</h5><p>Sorted HDPE scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-scrap-regrind/' ) ); ?>">Explore HDPE Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-pp" data-material="pp" aria-labelledby="home-category-tab-pp">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>PP</h3><p>Polypropylene</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/pp/' ) ); ?>">View PP material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in PP</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PP_01_Prime_Virgin.jpg' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>PP – Prime / Virgin</h5><p>Fresh PP resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/pp/pp-prime/' ) ); ?>">Explore PP Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PP_02_Off_Grade.jpg' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>PP – Off-grade</h5><p>Lot-specific PP materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/pp/pp-offgrade/' ) ); ?>">Explore PP Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PP_03_Recycled.jpg' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>PP – Recycled</h5><p>Recovered and reprocessed PP. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/pp/pp-recycled/' ) ); ?>">Explore PP Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PP_04_Scrap_Regrind.jpg' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>PP – Scrap / Regrind</h5><p>Sorted PP scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/pp/pp-scrap-regrind/' ) ); ?>">Explore PP Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-pvc" data-material="pvc" aria-labelledby="home-category-tab-pvc">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>PVC</h3><p>Polyvinyl Chloride</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">View PVC material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in PVC</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PVC_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>PVC – Prime / Virgin</h5><p>Fresh PVC resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/pvc/pvc-prime/' ) ); ?>">Explore PVC Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PVC_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>PVC – Off-grade</h5><p>Lot-specific PVC materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/pvc/pvc-offgrade/' ) ); ?>">Explore PVC Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PVC_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>PVC – Recycled</h5><p>Recovered and reprocessed PVC. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/pvc/pvc-recycled/' ) ); ?>">Explore PVC Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PVC_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>PVC – Scrap / Regrind</h5><p>Sorted PVC scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/pvc/pvc-scrap-regrind/' ) ); ?>">Explore PVC Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-ps" data-material="ps" aria-labelledby="home-category-tab-ps">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>PS</h3><p>Polystyrene</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/ps/' ) ); ?>">View PS material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in PS</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PS_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>PS – Prime / Virgin</h5><p>Fresh PS resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/ps/ps-prime/' ) ); ?>">Explore PS Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PS_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>PS – Off-grade</h5><p>Lot-specific PS materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/ps/ps-offgrade/' ) ); ?>">Explore PS Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PS_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>PS – Recycled</h5><p>Recovered and reprocessed PS. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/ps/ps-recycled/' ) ); ?>">Explore PS Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/PS_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>PS – Scrap / Regrind</h5><p>Sorted PS scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/ps/ps-scrap-regrind/' ) ); ?>">Explore PS Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-abs" data-material="abs" aria-labelledby="home-category-tab-abs">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>ABS</h3><p>Acrylonitrile Butadiene Styrene</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/abs/' ) ); ?>">View ABS material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in ABS</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/ABS_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>ABS – Prime / Virgin</h5><p>Fresh ABS resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/abs/abs-prime/' ) ); ?>">Explore ABS Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/ABS_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>ABS – Off-grade</h5><p>Lot-specific ABS materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/abs/abs-offgrade/' ) ); ?>">Explore ABS Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/ABS_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>ABS – Recycled</h5><p>Recovered and reprocessed ABS. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/abs/abs-recycled/' ) ); ?>">Explore ABS Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/ABS_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>ABS – Scrap / Regrind</h5><p>Sorted ABS scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/abs/abs-scrap-regrind/' ) ); ?>">Explore ABS Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
                <div class="ab-category-panel" id="home-category-panel-eva" data-material="eva" aria-labelledby="home-category-tab-eva">
                    <div class="ab-category-panel-head"><div class="ab-category-material-name"><h3>EVA</h3><p>Ethylene Vinyl Acetate</p></div><div class="ab-category-panel-actions"><a class="ab-category-details" href="<?php echo esc_url( home_url( '/products/eva/' ) ); ?>">View EVA material details <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-open" /></svg></a></div></div>
                    <div class="ab-category-panel-body"><h4 class="ab-category-supply-title">Available supply types in EVA</h4>
                        <div class="ab-category-grid">
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/EVA_01_Prime_Virgin.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><h5>EVA – Prime / Virgin</h5><p>Fresh EVA resin selected by manufacturer grade, technical specification and application.</p><a href="<?php echo esc_url( home_url( '/products/eva/eva-prime/' ) ); ?>">Explore EVA Prime / Virgin <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/EVA_02_Off_Grade.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-cubes" /></svg><h5>EVA – Off-grade</h5><p>Lot-specific EVA materials reviewed against declared variation and your process needs.</p><a href="<?php echo esc_url( home_url( '/products/eva/eva-offgrade/' ) ); ?>">Explore EVA Off-grade <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/EVA_03_Recycled.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-recycle" /></svg><h5>EVA – Recycled</h5><p>Recovered and reprocessed EVA. Review feedstock, consistency and application suitability.</p><a href="<?php echo esc_url( home_url( '/products/eva/eva-recycled/' ) ); ?>">Explore EVA Recycled <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                            <article class="ab-category-card">
                                <img class="ab-category-card-art" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/EVA_04_Scrap_Regrind.png' ) ); ?>" alt="" width="560" height="420" loading="lazy" decoding="async">
                                <div class="ab-category-card-copy"><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-gear" /></svg><h5>EVA – Scrap / Regrind</h5><p>Sorted EVA scrap and size-reduced feedstock for recovery or conversion.</p><a href="<?php echo esc_url( home_url( '/products/eva/eva-scrap-regrind/' ) ); ?>">Explore EVA Scrap / Regrind <svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-arrow" /></svg></a></div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="ab-category-benefits" aria-label="Our supply approach"><li><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-globe" /></svg><span>Global sourcing</span></li><li><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-shield" /></svg><span>Grade-specific checks</span></li><li><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-truck" /></svg><span>Supply coordination</span></li><li><svg class="ab-category-icon" aria-hidden="true" focusable="false"><use href="#home-category-icon-factory" /></svg><span>Industrial applications</span></li></ul>
        </div>
    </section>
    
    
    

    <!-- 2. Why Choose Bhavik Enterprises -->
    <section class="ab-section ab-section--white" id="why-bhavik" aria-labelledby="bv-why-title">
        <div class="ab-wrap">
            <div class="ab-section-head ab-reveal">
                <span class="ab-eyebrow">Why buyers work with us</span>
                <h2 id="bv-why-title">Four commitments behind every supply</h2>
                <p>What industrial processors and procurement teams rely on when sourcing polymer raw materials through Bhavik Enterprises.</p>
            </div>
            <div class="ab-pillars">
                <article class="ab-pillar ab-reveal">
                    <div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-shield" /></svg></div>
                    <h3>Authorised Supply</h3>
                    <p>Official India distribution partner of Abiroverseas LLC with direct, traceable polymer sourcing.</p>
                    <ul>
                        <li>Verified global supply chain</li>
                        <li>Clear grade &amp; batch identity</li>
                    </ul>
                </article>
                <article class="ab-pillar ab-reveal ab-reveal-delay-1">
                    <div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-globe" /></svg></div>
                    <h3>Pan-India Reach</h3>
                    <p>Strategically positioned stock and port connectivity for reliable inland and coastal dispatch.</p>
                    <ul>
                        <li>Head office in Kanpur (UP)</li>
                        <li>Branches in Mumbai &amp; Mundra</li>
                    </ul>
                </article>
                <article class="ab-pillar ab-reveal ab-reveal-delay-2">
                    <div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-layers" /></svg></div>
                    <h3>Consistent Quality</h3>
                    <p>Every lot is matched to your melt-flow, density and processing requirements before dispatch.</p>
                    <ul>
                        <li>Near virgin &amp; reprocessed options</li>
                        <li>Repeatable lot-to-lot behaviour</li>
                    </ul>
                </article>
                <article class="ab-pillar ab-reveal ab-reveal-delay-3">
                    <div class="ab-icon-box" aria-hidden="true"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720 720"
width="18"
height="18">
  <defs>
    <style>
      .cls-1 {
        fill: #00ac55;
      }
    </style>
  </defs>
  <path class="cls-1" d="M360,0C161.18,0,0,161.18,0,360c0,65.41,17.45,126.75,47.94,179.61L0,720l187.02-44.21c51.34,28.18,110.28,44.21,172.98,44.21,198.82,0,360-161.18,360-360S558.82,0,360,0ZM360,655.52c-60.17,0-116.13-17.98-162.82-48.87l-110.49,28.14,30.99-105.61c-33.53-47.93-53.2-106.26-53.2-169.19,0-163.21,132.31-295.52,295.52-295.52s295.52,132.31,295.52,295.52-132.31,295.52-295.52,295.52Z"/>
  <path class="cls-1" d="M444.35,407.52l87.1,41.06c4,1.88,6.56,5.94,6.2,10.34-.94,11.46-5.54,34.43-26.13,55.02-58.12,58.12-162.49-7.64-166.74-10.18-25.67-13.79-50.06-32.24-73.19-55.36-23.12-23.12-41.58-47.52-55.37-73.19-2.55-4.24-68.31-108.61-10.18-166.74,20.59-20.59,43.56-25.19,55.02-26.13,4.41-.36,8.46,2.2,10.34,6.2l41.07,87.1c1.94,4.12,1.09,9.02-2.13,12.24l-30.61,30.61c-6.62,6.62-8.56,16.93-4,25.11,11.17,20.03,26.19,39.32,43.59,57.07,17.75,17.4,37.04,32.43,57.07,43.59,8.18,4.56,18.48,2.62,25.11-4l30.61-30.61c3.22-3.22,8.12-4.08,12.24-2.13Z"/>
</svg></div>
                    <h3>Bulk &amp; Timely Delivery</h3>
                    <p>Structured scheduling and transparent commercial quotes tailored to production timelines.</p>
                    <ul>
                        <li>Full-load &amp; recurring supply</li>
                        <li>Direct WhatsApp &amp; phone desk</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    

    <!-- 4. Our Products 
    <section class="ab-section ab-section--white" id="products" aria-labelledby="bv-products-title">
        <div class="ab-wrap">
            <div class="ab-head-row">
                <div class="ab-section-head ab-reveal">
                    <span class="ab-eyebrow">Our products</span>
                    <h2 id="bv-products-title">High-quality polymers for diverse industrial uses</h2>
                    <p>Compare prime / virgin, off-grade, recycled and scrap / regrind supply on dedicated pages. Share your processing method, target properties and tonnage for an exact match.</p>
                </div>
                <a class="ab-btn" href="<?php echo esc_url( ab_home_anchor_url( 'quick-enquiry' ) ); ?>">ENQUIRE BY GRADE
                    <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                </a>
            </div>
            <div class="ab-why-grid">
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-layers" /></svg></div><span class="ab-why-tag">LDPE · Film &amp; Liner Grades</span></div>
                    <h3>LDPE</h3><p>Low-Density Polyethylene for films, liners and general extrusion. Compare prime, off-grade, recycled and scrap / regrind supply.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/ldpe/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-pipe" /></svg></div><span class="ab-why-tag">HDPE · Pipe &amp; Blow Moulding</span></div>
                    <h3>HDPE</h3><p>High-Density Polyethylene for containers, pipes and moulded parts, subject to grade and application requirements.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/hdpe/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-mould" /></svg></div><span class="ab-why-tag">PP · Moulding &amp; Extrusion</span></div>
                    <h3>PP (Polypropylene)</h3><p>Homopolymer and copolymer materials for packaging, household ware and industrial parts. Match flow, rigidity and impact needs.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/pp/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-cube" /></svg></div><span class="ab-why-tag">PVC · Rigid &amp; Flexible</span></div>
                    <h3>PVC</h3><p>Polyvinyl Chloride resins and compounds for profiles, conduits and cable applications. Specify rigid or flexible formulations.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/pvc/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-cube" /></svg></div><span class="ab-why-tag">PS · GPPS &amp; HIPS</span></div>
                    <h3>Polystyrene (PS)</h3><p>Choose clarity-led GPPS or impact-modified HIPS, then compare the four supply types on their own pages.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/ps/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-recycle" /></svg></div><span class="ab-why-tag">Granules · Reprocessed Materials</span></div>
                    <h3>Plastic Granules</h3><p>Choose a polymer family to compare recovered and reprocessed feedstock against your source, colour and batch-consistency needs.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/product-range/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-why-card ab-reveal">
                    <div class="ab-bv-product-top"><div class="ab-icon-box" aria-hidden="true"><svg class="ab-icon"><use href="#ab-icon-shield" /></svg></div><span class="ab-why-tag">Prime · Fresh Polymer Resin</span></div>
                    <h3>Prime / Virgin Polymers</h3><p>Start with a polymer family, then open its Prime / Virgin page for fresh resin selection checks and documentation needs.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/product-range/' ) ); ?>">Explore materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
            </div>
        </div>
    </section>-->

    

    <!-- 5. Industries We Serve — Ultra-premium borderless 3D image cards -->
    <section class="ab-section ab-section--mist" id="industries" aria-labelledby="bv-industries-title">
        <div class="ab-wrap">
            <div class="ab-section-head ab-reveal">
                <span class="ab-eyebrow">Industries we serve</span>
                <h2 id="bv-industries-title">Serving diverse industries across India</h2>
                <p>From high-speed packaging lines to pipe extrusion and compounding units, every polymer grade is matched to how your product is actually manufactured.</p>
            </div>
            <div class="ab-bv-ind-grid">
                <?php foreach ( $bv_industries as $i => $ind ) : ?>
                    <article class="ab-bv-ind-card ab-reveal<?php echo $i % 3 ? ' ab-reveal-delay-' . ( $i % 3 ) : ''; ?>">
                        <div class="ab-bv-ind-media">
                            <img src="<?php echo esc_url( $ind['img'] ); ?>" width="960" height="600" alt="<?php echo esc_attr( $ind['alt'] ); ?>" loading="lazy" decoding="async">
                            <span class="ab-bv-ind-badge"><?php echo esc_html( $ind['tag'] ); ?></span>
                        </div>
                        <div class="ab-bv-ind-body">
                            <div class="ab-icon-box ab-bv-ind-icon" aria-hidden="true">
                                <svg class="ab-icon"><use href="<?php echo esc_attr( $ind['icon'] ); ?>" /></svg>
                            </div>
                            <h3><?php echo esc_html( $ind['title'] ); ?></h3>
                            <p><?php echo esc_html( $ind['desc'] ); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 6. How We Work -->
    <section class="ab-section ab-way-section" id="the-way" aria-labelledby="bv-way-title">
        <div class="ab-wrap">
            <div class="ab-way-intro">
                <div class="ab-section-head ab-reveal">
                    <span class="ab-eyebrow">How we work</span>
                    <h2 id="bv-way-title">From enquiry to dispatch, in four clear steps</h2>
                    <p>No guesswork on grade or pricing — each stage is confirmed clearly before your material is scheduled for dispatch.</p>
                </div>
                <a class="ab-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">SHARE YOUR REQUIREMENT
                    <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                </a>
            </div>
            <div class="ab-way-grid">
                <article class="ab-way-item ab-reveal">
                    <span class="ab-way-num">01</span>
                    <h3>Share Requirement</h3>
                    <p>Tell us the polymer family, near virgin or reprocessed grade, processing method and required tonnage.</p>
                </article>
                <article class="ab-way-item ab-reveal ab-reveal-delay-1">
                    <span class="ab-way-num">02</span>
                    <h3>Grade &amp; Stock Match</h3>
                    <p>We verify current batch availability, melt-flow suitability and port or warehouse stock position.</p>
                </article>
                <article class="ab-way-item ab-reveal ab-reveal-delay-2">
                    <span class="ab-way-num">03</span>
                    <h3>Commercial Quote</h3>
                    <p>Receive transparent landed or ex-hub pricing, packaging details and confirmed dispatch schedule.</p>
                </article>
                <article class="ab-way-item ab-reveal ab-reveal-delay-3">
                    <span class="ab-way-num">04</span>
                    <h3>Pan-India Dispatch</h3>
                    <p>Material moves promptly from Kanpur, Mumbai or Mundra to your plant with direct coordination.</p>
                </article>
            </div>
        </div>
    </section>



<!-- 3. Who We Are -->
    <section class="ab-section ab-section--mist" id="about" aria-labelledby="bv-about-title">
        <div class="ab-wrap">
            <div class="ab-story">
                <div class="ab-story-visual ab-reveal ab-reveal-delay-1">
                    <img class="ab-story-img" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/11/who-we-are.jpg' ) ); ?>" width="960" height="720" alt="Polymer material handling and quality inspection" loading="lazy" decoding="async">
                    <span class="ab-story-chip">India Distribution</span>
                    <div class="ab-story-badge">
                        <strong>Authorised Partner</strong>
                        Abiroverseas LLC · India Network
                    </div>
                </div>
                <div class="ab-story-copy ab-reveal">
                    <span class="ab-eyebrow">Who we are</span>
                    <h2 id="bv-about-title">Built on a trusted partnership with Abiroverseas LLC</h2>
                    <p>Bhavik Enterprises is a dedicated B2B supplier of thermoplastic raw materials serving extruders, moulders and compounders across India. As the authorised distributor of Abiroverseas LLC, we bridge international polymer sourcing with responsive domestic execution.</p>
                    <p>From our head office in Kanpur and port-linked branches in Mumbai and Mundra, we help manufacturers secure dependable grades of LDPE, HDPE, PP, PVC and reprocessed granules without supply bottlenecks.</p>
                    <div class="ab-story-pills" aria-label="Core supply strengths">
                        <span class="ab-story-pill"><svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-check" /></svg> Authorised Abiroverseas LLC distributor</span>
                        <span class="ab-story-pill"><svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-check" /></svg> Near Virgin &amp; reprocessed polymer grades</span>
                        <span class="ab-story-pill"><svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-check" /></svg> Kanpur · Mumbai · Mundra network</span>
                        <span class="ab-story-pill"><svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-check" /></svg> Specification-led grade matching</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 7. Common Questions (FAQs) -->
    <section class="ab-section ab-faq-section" id="faqs" aria-labelledby="bv-faq-title">
        <div class="ab-wrap">
            <div class="ab-faq-grid">
                <div class="ab-faq-side ab-reveal">
                    <span class="ab-eyebrow">Common questions</span>
                    <h2 id="bv-faq-title">Before you send an enquiry</h2>
                    <p>Clear answers on polymer grades, authorised sourcing, order volumes and Pan-India delivery hubs.</p>
                    <div class="ab-faq-help-card">
                        <div class="ab-icon-box" aria-hidden="true">
                            <svg class="ab-icon"><use href="#ab-icon-chat" /></svg>
                        </div>
                        <div class="ab-faq-help-copy">
                            <h3 class="ab-faq-help-title">Have a specific grade enquiry?</h3>
                            <p class="ab-faq-help-sub">Speak directly with our polymer supply team for current stock and pricing.</p>
                            <a class="ab-faq-help-link" href="<?php echo esc_url( ab_home_anchor_url( 'quick-enquiry' ) ); ?>">
                                Go to enquiry form
                                <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="ab-faq-accordion ab-reveal ab-reveal-delay-1">
                    <?php foreach ( $bv_faqs as $index => $faq ) : ?>
                        <details class="ab-faq-item" <?php echo 0 === $index ? 'open' : ''; ?>>
                            <summary class="ab-faq-summary">
                                <div class="ab-faq-q-wrap">
                                    <span class="ab-faq-category-tag"><?php echo esc_html( $faq['cat'] ); ?></span>
                                    <h3 class="ab-faq-q"><?php echo esc_html( $faq['q'] ); ?></h3>
                                </div>
                                <span class="ab-faq-trigger-icon" aria-hidden="true">
                                    <svg class="ab-icon"><use href="#ab-icon-plus" /></svg>
                                </span>
                            </summary>
                            <div class="ab-faq-body">
                                <p><?php echo esc_html( $faq['a'] ); ?></p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Call to Action -->
    <section class="ab-final-cta" aria-labelledby="bv-cta-title">
        <div class="ab-wrap ab-final-inner">
            <div class="ab-final-copy">
                <span class="ab-eyebrow">Pan-India Polymer Supply</span>
                <h2 id="bv-cta-title">Source verified LDPE, HDPE, PP &amp; PVC grades with dependable dispatch</h2>
                <p>Share your polymer family, processing method, required quantity and delivery location. Our Kanpur, Mumbai and Mundra dispatch teams will respond with confirmed availability and competitive pricing.</p>
            </div>
            <div class="ab-final-actions">
                <a class="ab-btn" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">REQUEST A QUOTE
                    <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                </a>
                <a class="ab-btn ab-btn--light" href="tel:<?php echo esc_attr( $bv_phone_raw ); ?>">CALL <?php echo esc_html( $bv_phone ); ?></a>
            </div>
        </div>
    </section>

    <!-- 9. Contact Us & Quick Enquiry -->
    <section class="ab-contact-main ab-section" id="quick-enquiry" aria-labelledby="bv-contact-title">
        <div class="ab-wrap">
            <div class="ab-section-head ab-contact-intro ab-reveal">
                <span class="ab-eyebrow">Contact us</span>
                <h2 id="bv-contact-title">Get in touch with Bhavik Enterprises</h2>
                <p>Share your material requirement below, or reach our head office and regional dispatch hubs directly by phone, email or WhatsApp.</p>
            </div>

            <?php if ( 'sent' === $ab_contact_result ) : ?>
                <div class="ab-contact-notice ab-contact-notice--success" role="status" aria-live="polite">
                    <strong>Thank you for contacting Bhavik Enterprises.</strong>
                    <span>Your material requirement has been received. Our team will review your specification and contact you shortly.</span>
                </div>
            <?php elseif ( 'error' === $ab_contact_result ) : ?>
                <div class="ab-contact-notice ab-contact-notice--error" role="alert">
                    <strong>We couldn’t submit your enquiry.</strong>
                    <span>Please check the required fields and try again, or call us directly on <?php echo esc_html( $bv_phone ); ?>.</span>
                </div>
            <?php endif; ?>

            <div class="ab-contact-layout">
                <aside class="ab-contact-aside" aria-label="Bhavik Enterprises contact details">
                    <div class="ab-contact-aside-heading">
                        <span class="ab-eyebrow">Direct channels</span>
                        <h3>Talk to our supply desk.</h3>
                        <p>Connect directly for grade availability, bulk pricing and dispatch timelines across India.</p>
                    </div>

                    <div class="ab-bv-contact-details">
                        <div class="ab-contact-detail ab-contact-detail--full">
                            <span class="ab-contact-detail-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.2 7-11.2A7 7 0 0 0 5 9.8C5 14.8 12 21 12 21Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><circle cx="12" cy="9.8" r="2.5" stroke="currentColor" stroke-width="1.75"/></svg>
                            </span>
                            <span>
                                <small>Registered address</small>
                                <strong><?php echo esc_html( $bv_address ); ?></strong>
                            </span>
                        </div>

                        <a class="ab-contact-detail" href="tel:<?php echo esc_attr( $bv_phone_raw ); ?>">
                            <span class="ab-contact-detail-icon" aria-hidden="true">
                                <svg class="ab-icon"><use href="#ab-icon-phone" /></svg>
                            </span>
                            <span>
                                <small>Call us</small>
                                <strong><?php echo esc_html( $bv_phone ); ?></strong>
                                <em>Direct commercial &amp; dispatch desk</em>
                            </span>
                        </a>

                        <a class="ab-contact-detail" href="mailto:<?php echo esc_attr( $bv_email ); ?>">
                            <span class="ab-contact-detail-icon" aria-hidden="true">
                                <svg class="ab-icon"><use href="#ab-icon-mail" /></svg>
                            </span>
                            <span>
                                <small>Email us</small>
                                <strong><?php echo esc_html( $bv_email ); ?></strong>
                                <em>For specifications &amp; quotations</em>
                            </span>
                        </a>

                        <a class="ab-contact-detail" href="<?php echo esc_url( 'https://wa.me/' . $bv_whatsapp ); ?>" target="_blank" rel="noopener noreferrer">
                            <span class="ab-contact-detail-icon" aria-hidden="true">
                               <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720 720"
width="18"
height="18">
  <defs>
    <style>
      .cls-1 {
        fill: #00ac55;
      }
    </style>
  </defs>
  <path class="cls-1" d="M360,0C161.18,0,0,161.18,0,360c0,65.41,17.45,126.75,47.94,179.61L0,720l187.02-44.21c51.34,28.18,110.28,44.21,172.98,44.21,198.82,0,360-161.18,360-360S558.82,0,360,0ZM360,655.52c-60.17,0-116.13-17.98-162.82-48.87l-110.49,28.14,30.99-105.61c-33.53-47.93-53.2-106.26-53.2-169.19,0-163.21,132.31-295.52,295.52-295.52s295.52,132.31,295.52,295.52-132.31,295.52-295.52,295.52Z"/>
  <path class="cls-1" d="M444.35,407.52l87.1,41.06c4,1.88,6.56,5.94,6.2,10.34-.94,11.46-5.54,34.43-26.13,55.02-58.12,58.12-162.49-7.64-166.74-10.18-25.67-13.79-50.06-32.24-73.19-55.36-23.12-23.12-41.58-47.52-55.37-73.19-2.55-4.24-68.31-108.61-10.18-166.74,20.59-20.59,43.56-25.19,55.02-26.13,4.41-.36,8.46,2.2,10.34,6.2l41.07,87.1c1.94,4.12,1.09,9.02-2.13,12.24l-30.61,30.61c-6.62,6.62-8.56,16.93-4,25.11,11.17,20.03,26.19,39.32,43.59,57.07,17.75,17.4,37.04,32.43,57.07,43.59,8.18,4.56,18.48,2.62,25.11-4l30.61-30.61c3.22-3.22,8.12-4.08,12.24-2.13Z"/>
</svg>
                            </span>
                            <span>
                                <small>WhatsApp</small>
                                <strong><?php echo esc_html( $bv_phone ); ?></strong>
                                <em>Quick grade &amp; stock confirmation</em>
                            </span>
                        </a>

                        <div class="ab-bv-presence-block" aria-label="Our Presence">
                            <span class="ab-bv-presence-title">Our Presence</span>
                            <div class="ab-bv-presence-grid">
                                <div class="ab-contact-detail">
                                    <span class="ab-contact-detail-icon" aria-hidden="true">
                                        <svg class="ab-icon"><use href="#ab-icon-industry" /></svg>
                                    </span>
                                    <span>
                                        <small>Head Office</small>
                                        <strong>Kanpur</strong>
                                        <em>Central North India hub</em>
                                    </span>
                                </div>

                                <div class="ab-contact-detail">
                                    <span class="ab-contact-detail-icon" aria-hidden="true">
                                        <svg class="ab-icon"><use href="#ab-icon-globe" /></svg>
                                    </span>
                                    <span>
                                        <small>Branches</small>
                                        <strong>Mumbai, Mundra</strong>
                                        <em>Port-connected Western hubs</em>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                </aside>

                <form class="ab-form-card ab-contact-form" id="bhavik-enquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="ab_material_enquiry">
                    <input type="hidden" name="enquiry_kind" value="bhavik">
                    <?php wp_nonce_field( 'ab_material_enquiry', 'ab_material_enquiry_nonce' ); ?>
                    <label class="ab-honeypot" aria-hidden="true">Leave this field empty<input type="text" name="ab_website" tabindex="-1" autocomplete="off"></label>

                    <div class="ab-contact-form-heading">
                        <span class="ab-eyebrow">Enquiry form</span>
                        <h3>Send us your requirement</h3>
                        <p>Fields marked with an asterisk (*) are required.</p>
                    </div>

                    <div class="ab-form-grid">
                        <div class="ab-field">
                            <label for="bv-name">Full Name <span aria-hidden="true">*</span></label>
                            <input id="bv-name" name="name" type="text" autocomplete="name" required maxlength="120" placeholder="Your full name">
                        </div>
                        <div class="ab-field">
                            <label for="bv-phone">Phone Number <span aria-hidden="true">*</span></label>
                            <input id="bv-phone" name="phone" type="tel" autocomplete="tel" required maxlength="60" placeholder="+91 XXXXX XXXXX">
                        </div>
                        <div class="ab-field">
                            <label for="bv-email">Email Address <span aria-hidden="true">*</span></label>
                            <input id="bv-email" name="email" type="email" autocomplete="email" required maxlength="160" placeholder="you@company.com">
                        </div>
                        <div class="ab-field">
                            <label for="bv-material">Product Required <span aria-hidden="true">*</span></label>
                            <select id="bv-material" name="material" required>
                                <option value="">Select polymer / material</option>
                                <option value="LDPE">LDPE</option>
                                <option value="HDPE">HDPE</option>
                                <option value="PP (Polypropylene)">PP (Polypropylene)</option>
                                <option value="PVC">PVC</option>
                                <option value="Plastic Granules">Plastic Granules (Reprocessed)</option>
                                <option value="Virgin Polymers">Virgin Polymers</option>
                                <option value="Multiple / Other">Multiple / Other</option>
                            </select>
                        </div>
                        <div class="ab-field ab-field--full">
                            <label for="bv-quantity">Approximate Quantity</label>
                            <input id="bv-quantity" name="quantity" type="text" maxlength="120" placeholder="e.g., 10 MT, 1 truckload, or monthly requirement">
                        </div>
                        <div class="ab-field ab-field--full">
                            <label for="bv-message">Requirement Details <span aria-hidden="true">*</span></label>
                            <textarea id="bv-message" name="application" required maxlength="3000" placeholder="Share grade preference, processing method (film, pipe, blow or injection moulding), colour and delivery city."></textarea>
                        </div>
                    </div>

                    <div class="ab-form-foot">
                        <button class="ab-btn ab-contact-submit" type="submit">SEND ENQUIRY
                            <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>
<?php
get_footer();