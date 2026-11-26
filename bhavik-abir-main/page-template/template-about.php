<?php
/**
 * Template Name: About Bhavik Enterprises
 * Template Post Type: page
 * Exact layout/content based on the supplied finalized HTML file.
 */
get_header();
?>
<main id="main-content">

    <!-- 1. ABOUT HERO -->
    <section class="ab-about-hero" aria-labelledby="about-title">
      <div class="ab-wrap">
        
        <div class="ab-about-hero-grid">
          <div class="ab-about-hero-copy ab-reveal is-visible">
            <span class="ab-eyebrow">Who We Are</span>
            <h1 id="about-title">Welcome to Bhavik Enterprises</h1>
            <p class="lead">We are dedicated to delivering high-quality plastic raw materials while driving innovation and sustainability across the industry. As the authorised distributor of Abiroverseas LLC, we supply manufacturers across India from our head office in Kanpur and branch hubs in Mumbai and Mundra. Our commitment to quality assurance, sustainable solutions, and customer satisfaction sets us apart.</p>
   <div class="ab-story-pills" style="margin-bottom:20px;">
            <span class="ab-story-pill" style="background-color:#FFFFFF">Requirements-first</span>
            <span class="ab-story-pill" style="background-color:#FFFFFF">Grade-aware</span>
            <span class="ab-story-pill" style="background-color:#FFFFFF">Documentation led</span>
            <span class="ab-story-pill" style="background-color:#FFFFFF">Process-fit</span>
            <span class="ab-story-pill" style="background-color:#FFFFFF">Clear confirmation</span>
          </div>            
<div class="ab-about-hero-actions">
              <a class="ab-btn" href="<?php echo home_url('/contact-us/'); ?>">Start a conversation
                <svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-arrow" />
                </svg>
              </a>
              <a class="ab-btn ab-btn--outline" href="#principles">How we work</a>
            </div>
            
          </div>

          <!-- Hero visual -->
          <div class="ab-about-hero-art ab-reveal ab-reveal-delay-1" role="img"
            aria-label="Polymer pellets - macro photograph of raw plastic material">
 <video class="ab-hero-img" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/10/video1.mp4' ) ); ?>"
              poster="<?php echo esc_url( home_url( '/wp-content/uploads/2026/10/default-video-image.png' ) ); ?>"
              width="700" height="520" autoplay loop muted playsinline preload="auto"></video>
        
          </div>
        </div>
      </div>
    </section>

    <!-- 2. WHO WE ARE / STORY -->
    <section class="ab-section ab-section--white" id="who-we-are" aria-labelledby="who-title">
      <div class="ab-wrap ab-story">
        <div class="ab-story-visual ab-reveal ab-reveal-delay-1">
          <img class="ab-story-img" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/10/r-p-18.png' ) ); ?>" width="560" height="500"
            alt="Virgin and recycled polymer pellets compared side by side" loading="lazy" decoding="async">
         
          <div class="ab-story-badge">
            <strong>Material conversations</strong>
            Built around your brief, documentation and process.
          </div>
        </div>

        <div class="ab-story-copy ab-reveal">
          <span class="ab-eyebrow">Our story</span>
          <h2>We help manufacturers choose material by requirement — not by catalogue</h2>
          <p>Bhavik Enterprises was built on a simple idea: polymer supply should start with the <strong>application and
              specification</strong>, not a shortlist of grades picked in isolation. We work with buyers, processors and
            technical teams who want a clear path from brief to confirmed material</p>
          <p>Our focus spans reprocessed and recycled polymers, mainstream resin families such as PP, HDPE, LDPE /
            LLDPE, PVC, PET and ABS, plus masterbatch and additives — always matched to the <strong>process, form and
              end-use</strong> you describe.</p>
          <div class="ab-story-pills">
            <span class="ab-story-pill">Requirements-first</span>
            <span class="ab-story-pill">Grade-aware</span>
            <span class="ab-story-pill">Documentation led</span>
            <span class="ab-story-pill">Process-fit</span>
            <span class="ab-story-pill">Clear confirmation</span>
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

    <!-- 3. MISSION / VISION / PROMISE -->
    <section class="ab-section ab-section--mist" id="mission" aria-labelledby="mission-title">
      <div class="ab-wrap">
        <div class="ab-section-head ab-reveal">
          <span class="ab-eyebrow">What we stand for</span>
          <h2 id="mission-title">Our mission, vision and promise</h2>
          <p>Three complementary ideas that shape how we work with every polymer buyer, from first enquiry to
            confirmed supply.</p>
        </div>

        <div class="ab-pillars">
          <article class="ab-pillar ab-reveal">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-target" />
              </svg></div>
            <h3>Our Mission</h3>
            <p>We are dedicated to empowering businesses worldwide with reliable and eco-conscious products that not only meet their needs but also contribute to a greener and more sustainable future.</p>
            <!--<ul>
              <li>Replace guesswork with structured specification.</li>
              <li>Match material to process and operating conditions.</li>
              <li>Keep conversations transparent and grade-aware.</li>
            </ul>-->
          </article>
          <article class="ab-pillar ab-reveal ab-reveal-delay-1">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-compass" />
              </svg></div>
            <h3>Our Vision</h3>
            <p>Our vision at Bhavik Enterprises is to be a trusted supply partner for plastics processors across India, setting new standards for excellence, sustainability, and client satisfaction.</p>
            <!-- <ul>
              <li>Clear portfolio navigation across polymer families.</li>
              <li>Supply conversations supported by real datasheets.</li>
              <li>Long-term relationships over one-off transactions.</li>
            </ul> -->
          </article>
          <article class="ab-pillar ab-reveal ab-reveal-delay-2">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-handshake" />
              </svg></div>
            <h3>Our Values</h3>
            <p>Our clients are at the heart of everything we do. We prioritize their needs, foster strong relationships, and tailor our solutions to ensure their success.</p>
           <!-- <ul>
              <li>Specifications verified against supplier data.</li>
              <li>Source, form and classification stated clearly.</li>
              <li>Terms and expectations confirmed in writing.</li>
            </ul>-->
          </article>
   <article class="ab-pillar ab-reveal">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-target" />
              </svg></div>
            <h3>Corporate Social Responsibility (CSR)</h3>
            <p>At Bhavik Enterprises, Corporate Social Responsibility (CSR) is ingrained in our core values. We are committed to making a positive impact on society and the environment. Our CSR initiatives include.</p>
            <!--<ul>
              <li>Replace guesswork with structured specification.</li>
              <li>Match material to process and operating conditions.</li>
              <li>Keep conversations transparent and grade-aware.</li>
            </ul>-->
          </article>
        </div>
      </div>
    </section>

    
    

    <!-- 5. PRINCIPLES -->
    <section class="ab-section" id="principles" aria-labelledby="principles-title">
      <div class="ab-wrap">
        <div class="ab-section-head ab-reveal">
          <span class="ab-eyebrow">Principles</span>
          <h2 id="principles-title">What we care about, in practice</h2>
          <p>These are the quiet rules that shape day-to-day conversations with buyers and processors.</p>
        </div>
        <div class="ab-principles">
          <article class="ab-principle ab-reveal">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-layers" />
              </svg></div>
            <h3>Specification first</h3>
            <p>Grade, process and application lead every discussion — not generic product descriptions.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-1">
            <div class="ab-icon-box"
              style="background:var(--ab-blue-mist);color:var(--ab-blue);box-shadow:0 2px 8px rgba(29,78,159,.08)">
              <svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-file" />
              </svg>
            </div>
            <h3>Documented clarity</h3>
            <p>Properties referenced against datasheets, with source and classification stated plainly.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-2">
            <div class="ab-icon-box" style="background:#fff0e2;color:#b65a09;box-shadow:0 2px 8px rgba(245,130,32,.08)">
              <svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-sparkle" />
              </svg>
            </div>
            <h3>Honest limits</h3>
            <p>If something needs verification or is grade-dependent, we say so instead of overclaiming.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-3">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-leaf" />
              </svg></div>
            <h3>Reprocessed with care</h3>
            <p>Recycled polymer options are discussed with clear source, form and suitability for the end use.</p>
          </article>
          <article class="ab-principle ab-reveal">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-users" />
              </svg></div>
            <h3>Long-term relationships</h3>
            <p>We prefer repeat conversations, not one-off transactions driven by shortcut pricing.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-1">
            <div class="ab-icon-box"
              style="background:var(--ab-blue-mist);color:var(--ab-blue);box-shadow:0 2px 8px rgba(29,78,159,.08)">
              <svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-shield" />
              </svg>
            </div>
            <h3>Confirm before commit</h3>
            <p>Specification, quantity, pack and terms are agreed in writing before anything ships.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-2">
            <div class="ab-icon-box" style="background:#fff0e2;color:#b65a09;box-shadow:0 2px 8px rgba(245,130,32,.08)">
              <svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-truck" />
              </svg>
            </div>
            <h3>Process-aware supply</h3>
            <p>Form and delivery considered against your actual conversion method and shop-floor reality.</p>
          </article>
          <article class="ab-principle ab-reveal ab-reveal-delay-3">
            <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                <use href="#ab-icon-chat" />
              </svg></div>
            <h3>Plain language</h3>
            <p>Technical topics explained simply, without unnecessary jargon or false certainty.</p>
          </article>
        </div>
      </div>
    </section>


<!-- 4. THE WAY WE WORK -->
<section class="ab-section ab-way-section" id="the-way" aria-labelledby="way-title">
      <div class="ab-wrap">
        <div class="ab-way-intro">
          <div class="ab-section-head ab-reveal">
            <span class="ab-eyebrow">The way we work</span>
            <h2 id="way-title">A clear path from brief to confirmed material.</h2>
            <p>Instead of leading with price lists, we lead with questions. The more precise the brief, the more
              useful the response.</p>
          </div>
          <a class="ab-btn " href="<?php echo home_url('/contact-us/'); ?>">Share your brief
            <svg class="ab-icon" aria-hidden="true">
              <use href="#ab-icon-arrow" />
            </svg>
          </a>
        </div>

        <div class="ab-way-grid">
          <article class="ab-way-item ab-reveal">
            <div class="ab-way-no">01</div>
            <h3>Listen</h3>
            <p>We start with your application, process route, grade preferences and operating conditions.</p>
          </article>
          <article class="ab-way-item ab-reveal ab-reveal-delay-1">
            <div class="ab-way-no">02</div>
            <h3>Frame</h3>
            <p>We translate the brief into a workable material specification, including form and documentation needs.
            </p>
          </article>
          <article class="ab-way-item ab-reveal ab-reveal-delay-2">
            <div class="ab-way-no">03</div>
            <h3>Match</h3>
            <p>We review suitable polymer families, grades and sources against your technical and commercial criteria.
            </p>
          </article>
          <article class="ab-way-item ab-reveal ab-reveal-delay-3">
            <div class="ab-way-no">04</div>
            <h3>Confirm</h3>
            <p>We confirm specification, source classification, quantity and delivery before anything is committed.</p>
          </article>
        </div>

       
      </div>
    </section>

    <!-- 6. REACH / FOOTPRINT -->
    <section class="ab-section ab-section--white" id="reach" aria-labelledby="reach-title">
      <div class="ab-wrap ab-reach-grid">
        <div class="ab-reach-copy ab-reveal">
          <span class="ab-eyebrow">Reach &amp; coverage</span>
          <h2 id="reach-title">A broad polymer footprint, discussed one brief at a time</h2>
          

          <div class="ab-reach-stats">
            <div class="ab-reach-stat">
              <strong>10<span>+</span></strong>
              <small>Polymer families</small>
              <em>PP, HDPE, LDPE/LLDPE, PVC, PET, ABS, PS/HIPS, engineering plastics, masterbatch, recycled.</em>
            </div>
            <div class="ab-reach-stat">
              <strong>4<span>+</span></strong>
              <small>Processing routes</small>
              <em>Injection, extrusion, blow moulding and selected rotational applications.</em>
            </div>
            <div class="ab-reach-stat">
              <strong>8<span>+</span></strong>
              <small>End-use markets</small>
              <em>Packaging, logistics, consumer, infrastructure, industrial, construction, agriculture, water.</em>
            </div>
            <div class="ab-reach-stat">
              <strong>1<span>→</span></strong>
              <small>Approach</small>
              <em>One method: specification first, confirmation before commit.</em>
            </div>
          </div>
        </div>

        <div class="ab-reach-visual ab-reveal ab-reveal-delay-1" aria-hidden="true">
          <img class="ab-reach-img" src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/10/Frame-3.jpg' ) ); ?>" width="500" height="460"
            alt="Polymer pellets - macro photograph" loading="lazy" decoding="async">
          <div class="ab-reach-note">
            <b>Scope, not promise</b>
            Figures describe conversation scope. Actual supply is confirmed per enquiry.
          </div>
        </div>
      </div>
    </section>

    <!-- 7. OUR PROMISE
    <section class="ab-section ab-promise" id="promise" aria-labelledby="promise-title">
      <div class="ab-wrap ab-promise-grid">
        <div class="ab-promise-copy ab-reveal">
          <span class="ab-eyebrow">What you can expect</span>
          <h2 id="promise-title">Working with Bhavik Enterprises, from first email onward.</h2>
          <p>Material supply doesn't need to feel opaque. Here's the kind of experience we aim for on every enquiry —
            whether it's a single trial consignment or a repeat industrial requirement.</p>

          <div class="ab-promise-list">
            <div class="ab-promise-item">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-chat" />
                </svg></div>
              <div>
                <strong>Clear first response</strong>
                <p>We acknowledge the brief, confirm what's understood and ask only the questions that genuinely matter.
                </p>
              </div>
            </div>
            <div class="ab-promise-item">
              <div class="ab-icon-box"
                style="background:var(--ab-blue-mist);color:var(--ab-blue);box-shadow:0 2px 8px rgba(29,78,159,.08)">
                <svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-file" />
                </svg>
              </div>
              <div>
                <strong>Documented options</strong>
                <p>Suggested grades are tied back to datasheets, source classification and intended processing route.
                </p>
              </div>
            </div>
            <div class="ab-promise-item">
              <div class="ab-icon-box"
                style="background:#fff0e2;color:#b65a09;box-shadow:0 2px 8px rgba(245,130,32,.08)">
                <svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-scale" />
                </svg>
              </div>
              <div>
                <strong>Transparent commercials</strong>
                <p>Pricing conversations include pack, quantity and delivery context — not a one-line number out of air.
                </p>
              </div>
            </div>
            <div class="ab-promise-item">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-shield" />
                </svg></div>
              <div>
                <strong>No surprise commitments</strong>
                <p>Specification, source and terms are confirmed in writing before anything is treated as an order.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="ab-promise-panel ab-reveal ab-reveal-delay-1">
          <div class="ab-promise-cards">
            <div class="ab-promise-card">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-layers" />
                </svg></div>
              <strong>Grade-aware brief</strong>
              <p>Share known grade names or ask us to help frame a workable specification.</p>
            </div>
            <div class="ab-promise-card">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-cube" />
                </svg></div>
              <strong>Form &amp; process fit</strong>
              <p>Pellets, powder or compound discussed against your actual conversion route.</p>
            </div>
            <div class="ab-promise-card">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-truck" />
                </svg></div>
              <strong>Quantity-led review</strong>
              <p>Indicative volume and destination shape the response, not generic pricing.</p>
            </div>
            <div class="ab-promise-card">
              <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                  <use href="#ab-icon-check" />
                </svg></div>
              <strong>Clear confirmation</strong>
              <p>Specification, source and terms confirmed in writing before any commitment.</p>
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- 8. WHY BHAVIK ENTERPRISES -->
    <section class="ab-section ab-why" id="why-bhavik" aria-labelledby="why-title">
      <div class="ab-wrap">
        <div class="ab-section-head ab-reveal">
          <span class="ab-eyebrow">Why buyers stay</span>
          <h2 id="why-title">Why teams choose Bhavik Enterprises for polymer briefs</h2>
          <p>Three themes come up most often from processors and procurement teams we work with.</p>
        </div>

        <div class="ab-why-grid">
          <article class="ab-why-card ab-reveal">
            <span class="ab-why-tag">Clarity</span>
            <h3>Specification over sales talk</h3>
            <p>We lead with the actual grade, process and documentation — not a generic "available ex-stock" line that
              doesn't survive a technical review.</p>
            <ul>
              <li>Grade and property discussion grounded in datasheets.</li>
              <li>Source and classification stated plainly.</li>
              <li>Honest about what still needs verification.</li>
            </ul>
          </article>
          <article class="ab-why-card ab-reveal ab-reveal-delay-1">
            <span class="ab-why-tag">Range</span>
            <h3>Broad polymer conversation</h3>
            <p>From everyday PP and HDPE through LDPE / LLDPE, PVC, PET, ABS, PS / HIPS, engineering plastics,
              masterbatch and recycled polymers.</p>
            <ul>
              <li>One partner for many material questions.</li>
              <li>Reprocessed and recycled options reviewed with care.</li>
              <li>Guidance on which family likely fits the application.</li>
            </ul>
          </article>
          <article class="ab-why-card ab-reveal ab-reveal-delay-2">
            <span class="ab-why-tag">Confidence</span>
            <h3>Confirm before commit</h3>
            <p>Nothing becomes an order until specification, source, form, pack and terms are agreed in writing — so
              both sides know exactly what's being supplied.</p>
            <ul>
              <li>Clear written confirmation of what's agreed.</li>
              <li>Terms discussed in context, not in isolation.</li>
              <li>Space for repeat conversations as needs evolve.</li>
            </ul>
          </article>
        </div>
      </div>
    </section>

    <!-- 9. FAQ -->
    <section class="ab-section ab-faq-section" id="about-faq" aria-labelledby="faq-title">
      <div class="ab-wrap">
        <div class="ab-faq-grid">

          <aside class="ab-faq-side">
            <div class="ab-faq-side-sticky ab-reveal">
              <span class="ab-eyebrow">Common questions</span>
              <h2 id="faq-title">About Bhavik Enterprises — in plain terms</h2>
              <p class="lead">A focused set of answers that often come up in first conversations with new buyers,
                procurement teams and processors.</p>

              <div class="ab-faq-filter-pills" role="tablist" aria-label="FAQ categories">
                <button class="ab-faq-tab is-active" data-faq-filter="all" role="tab" aria-selected="true"
                  type="button">
                  <span>All questions</span>
                  <span class="ab-tab-count">6</span>
                </button>
                <button class="ab-faq-tab" data-faq-filter="scope" role="tab" aria-selected="false" type="button">
                  <span>Scope &amp; portfolio</span>
                  <span class="ab-tab-count">2</span>
                </button>
                <button class="ab-faq-tab" data-faq-filter="process" role="tab" aria-selected="false" type="button">
                  <span>Enquiry process</span>
                  <span class="ab-tab-count">2</span>
                </button>
                <button class="ab-faq-tab" data-faq-filter="commercial" role="tab" aria-selected="false" type="button">
                  <span>Commercials &amp; contact</span>
                  <span class="ab-tab-count">2</span>
                </button>
              </div>

              <div class="ab-faq-help-card">
                <div class="ab-faq-help-head">
                  <div class="ab-icon-box"><svg class="ab-icon" aria-hidden="true">
                      <use href="#ab-icon-chat" />
                    </svg></div>
                  <div>
                    <strong class="ab-faq-help-title">Have a specific polymer brief?</strong>
                    <p class="ab-faq-help-sub">Share your grade, process and quantity for a structured response.</p>
                  </div>
                </div>
                <div class="ab-faq-help-actions">
                  <a href="<?php echo home_url('/contact-us/'); ?>" class="ab-btn">Start an enquiry
                    <svg class="ab-icon" aria-hidden="true">
                      <use href="#ab-icon-arrow" />
                    </svg>
                  </a>
                  <a href="mailto:info@bhavikenterprises.in" class="ab-faq-help-link">
                    <svg class="ab-icon" aria-hidden="true">
                      <use href="#ab-icon-mail" />
                    </svg>
                    info@bhavikenterprises.in
                  </a>
                </div>
              </div>
            </div>
          </aside>

          <div class="ab-faq-main ab-reveal ab-reveal-delay-1">
            <div class="ab-faq-list" id="ab-faq-container">

              <details class="ab-faq-card" data-category="scope" open>
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Scope &amp; portfolio</span>
                    <h3 class="ab-faq-q">What exactly does Bhavik Enterprises do?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>We work with buyers and processors on polymer material supply conversations — <strong>PP, HDPE,
                      LDPE / LLDPE, PVC, PET, ABS, PS / HIPS</strong>, engineering plastics, masterbatch, additives and
                    reprocessed / recycled polymers.</p>
                  <p>Every enquiry starts from the <strong>application and specification</strong>, not from a generic
                    catalogue. We aim to make the conversation grade-aware, documentation-led and clearly confirmed
                    before anything is committed.</p>
                </div>
              </details>

              <details class="ab-faq-card" data-category="scope">
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Scope &amp; portfolio</span>
                    <h3 class="ab-faq-q">Do you only supply recycled polymers, or virgin material too?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>Both are part of the conversation. Virgin resins, reprocessed material and recycled polymers each
                    have their own suitability window.</p>
                  <p>We discuss <strong>source, classification, form and documentation</strong> so the right option is
                    matched to the actual end use — rather than defaulting to one category by habit.</p>
                </div>
              </details>

              <details class="ab-faq-card" data-category="process">
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Enquiry process</span>
                    <h3 class="ab-faq-q">How is an enquiry handled from start to finish?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>We follow a simple four-step pattern:</p>
                  <ul>
                    <li><strong>Listen</strong> — the brief, application and operating conditions.</li>
                    <li><strong>Frame</strong> — translate it into a workable specification.</li>
                    <li><strong>Match</strong> — review suitable polymer families, grades and sources.</li>
                    <li><strong>Confirm</strong> — specification and terms in writing before commitment.</li>
                  </ul>
                </div>
              </details>

              <details class="ab-faq-card" data-category="process">
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Enquiry process</span>
                    <h3 class="ab-faq-q">What do you need from me to give a useful response?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>The more precise the brief, the more useful the reply. Helpful details include:</p>
                  <ul>
                    <li>Material family or current grade name.</li>
                    <li>Processing route — injection, extrusion, blow moulding, etc.</li>
                    <li>Form required — pellet, powder or compound.</li>
                    <li>Indicative quantity and destination.</li>
                    <li>Applicable standards or documentation needs.</li>
                  </ul>
                </div>
              </details>

              <details class="ab-faq-card" data-category="commercial">
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Commercials</span>
                    <h3 class="ab-faq-q">Will prices be shared upfront on the website?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>Polymer pricing is <strong>grade-, source-, pack- and quantity-dependent</strong>, so numbers
                    shared out of context are usually misleading.</p>
                  <p>We prefer to discuss commercials once the specification and brief are clear — that way, the price
                    actually reflects what will be supplied.</p>
                </div>
              </details>

              <details class="ab-faq-card" data-category="commercial">
                <summary class="ab-faq-summary">
                  <div class="ab-faq-summary-content">
                    <span class="ab-faq-category-tag">Getting in touch</span>
                    <h3 class="ab-faq-q">How do I actually start a conversation?</h3>
                  </div>
                  <div class="ab-faq-trigger-icon" aria-hidden="true">
                    <svg class="ab-icon">
                      <use href="#ab-icon-plus" />
                    </svg>
                  </div>
                </summary>
                <div class="ab-faq-body">
                  <p>Use the <a href="#quick-enquiry" style="color:var(--ab-blue);font-weight:700">quote request</a>
                    at the bottom of the page, or email the address shown in the top bar.</p>
                  <p>A short description of your <strong>application, preferred material and quantity</strong> is
                    usually enough for a useful first reply.</p>
                </div>
              </details>

            </div>

            <div class="ab-faq-empty-state" id="ab-faq-empty">
              No questions in this category yet. Click &ldquo;All questions&rdquo; to view the complete list.
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- 10. FINAL CTA 
    <section class="ab-final-cta" id="quick-enquiry" aria-labelledby="final-cta-title">
      <div class="ab-wrap ab-final-inner">
        <div class="ab-final-copy">
          <span class="ab-eyebrow">Ready when you are</span>
          <h2 id="final-cta-title">Have a polymer brief that needs a clear answer?</h2>
          <p>Share the application, process, grade or specification, required form, quantity and destination — we'll
            take it from there with a structured, requirements-first response.</p>
        </div>
        <div class="ab-final-actions">
          <a class="ab-btn" href="<?php echo esc_url( home_url( '/#quick-enquiry' ) ); ?>">Request a quote
            <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg>
          </a>
          <a class="ab-btn ab-btn--light" href="<?php echo esc_url( home_url( '/#product-families' ) ); ?>">Explore products</a>
        </div>
      </div>
    </section>-->
  </main>
<?php get_footer(); ?>