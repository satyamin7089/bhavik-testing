<?php
/** Static product navigation: seven polymers, each with four separate supply pages. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="sub-menu ab-mega-menu" aria-label="Product categories">
    <svg class="ab-mega-sprite" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="ab-mega-blue" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#55dcff"/><stop offset=".5" stop-color="#149cf7"/><stop offset="1" stop-color="#1761d3"/></linearGradient>
            <linearGradient id="ab-mega-teal" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#60e5da"/><stop offset=".5" stop-color="#0caba7"/><stop offset="1" stop-color="#00767d"/></linearGradient>
            <radialGradient id="ab-mega-pearl" cx=".3" cy=".25" r=".8"><stop stop-color="#efffff"/><stop offset=".3" stop-color="#8ae3e4"/><stop offset=".7" stop-color="#25b7c1"/><stop offset="1" stop-color="#05808e"/></radialGradient>
            <symbol id="ab-mega-arrow" viewBox="0 0 24 24"><path d="M4 12h15m-6-6 6 6-6 6"/></symbol>
            <symbol id="ab-mega-chevron" viewBox="0 0 16 16"><path d="m6 4 4 4-4 4"/></symbol>
            <symbol id="ab-mega-layers" viewBox="0 0 40 40"><path d="m3 14 17-9 17 9-17 9Z"/><path d="m3 20 17 9 17-9M3 26l17 9 17-9" fill="none" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/><path d="m5 14 15 8 15-8" fill="none" stroke="#b9f7ff" stroke-width="1.5"/></symbol>
            <symbol id="ab-mega-cube" viewBox="0 0 40 40"><path d="m20 3 16 9v17l-16 9-16-9V12Z"/><path d="m4 12 16 9 16-9M20 21v17" fill="none" stroke="#a6f8ed" stroke-width="1.6"/><path d="m20 3 16 9-16 9L4 12Z" fill="#a6f8ed" opacity=".24"/></symbol>
            <symbol id="ab-mega-recycle" viewBox="0 0 40 40"><g fill="none" stroke="currentColor" stroke-width="4" stroke-linejoin="round"><path d="m13 13 5-8a3 3 0 0 1 5 0l5 9m-1-8 1 8-8-1M30 19l5 9a3 3 0 0 1-3 5H21m7 4-7-4 4-7M15 33H6a3 3 0 0 1-2-5l6-10m-8 4 8-4 4 7"/></g></symbol>
            <symbol id="ab-mega-molecule" viewBox="0 0 40 40"><g fill="none" stroke="currentColor" stroke-width="2.5"><path d="m10 10 17 3 4 17-18 2-3-22 20-5M13 32l-8 5"/></g><g><circle cx="10" cy="10" r="5"/><circle cx="27" cy="13" r="4"/><circle cx="31" cy="30" r="5"/><circle cx="13" cy="32" r="4"/><circle cx="30" cy="5" r="3"/><circle cx="5" cy="37" r="2"/></g></symbol>
            <symbol id="ab-mega-pellets" viewBox="0 0 40 40"><circle cx="11" cy="7" r="3"/><circle cx="27" cy="8" r="3.5"/><circle cx="5" cy="19" r="3"/><circle cx="19" cy="18" r="4"/><circle cx="33" cy="21" r="3"/><circle cx="11" cy="31" r="3.5"/><circle cx="26" cy="33" r="3"/></symbol>
            <symbol id="ab-mega-flask" viewBox="0 0 40 40"><path d="M14 3h12M16 4v12L6 32a3 3 0 0 0 3 5h22a3 3 0 0 0 3-5L24 16V4" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/><path d="m12 25-5 8a2 2 0 0 0 2 3h22a2 2 0 0 0 2-3l-5-8Z"/><circle cx="20" cy="25" r="2" fill="#c1f6ff"/><circle cx="24" cy="30" r="1.5" fill="#c1f6ff"/></symbol>
        </defs>
    </svg>
    <svg class="ab-mega-molecule-art" viewBox="0 0 180 120" aria-hidden="true" focusable="false"><g fill="none" stroke="#4fb8c0" stroke-width="4"><path d="m25 54 49-21 43 29 42-39M117 62l12 40"/></g><g fill="url(#ab-mega-pearl)"><circle cx="25" cy="54" r="17"/><circle cx="74" cy="33" r="12"/><circle cx="117" cy="62" r="13"/><circle cx="159" cy="23" r="19"/><circle cx="129" cy="102" r="11"/></g></svg>
    <svg class="ab-mega-ribbon-art" viewBox="0 0 600 360" aria-hidden="true" focusable="false"><path d="M600 15C435 72 528 168 306 215S101 257 0 360h600Z" fill="#8de2e3" opacity=".25"/><path d="M600 66c-131 42-95 121-262 172S153 303 77 360h523Z" fill="#c7f5f3" opacity=".65"/><path d="M600 102c-100 53-108 130-240 169S211 320 169 360" fill="none" stroke="#fff" stroke-width="3" opacity=".7"/><g fill="url(#ab-mega-pearl)" opacity=".65"><circle cx="557" cy="187" r="19"/><circle cx="587" cy="215" r="14"/><circle cx="550" cy="230" r="13"/><circle cx="520" cy="217" r="11"/><circle cx="574" cy="258" r="18"/><circle cx="527" cy="262" r="16"/><circle cx="496" cy="248" r="9"/></g></svg>
    <!--<div class="ab-mega-heading"><div><span class="ab-mega-eyebrow">Materials &amp; supply types</span><p>Find the <em>right</em> starting point</p></div><p class="ab-mega-intro">Premium polymer materials for<br> advanced applications.</p></div>-->
    <div class="ab-mega-grid">
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--blue"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-layers" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/ldpe/' ) ); ?>">LDPE</a><p>Low-Density Polyethylene</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ldpe/ldpe-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--teal"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-cube" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/hdpe/' ) ); ?>">HDPE</a><p>High-Density Polyethylene</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/hdpe/hdpe-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--blue"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-recycle" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/pp/' ) ); ?>">PP</a><p>Polypropylene</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/pp/pp-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pp/pp-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pp/pp-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pp/pp-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--teal"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-molecule" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">PVC</a><p>Polyvinyl Chloride</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--blue"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-pellets" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/ps/' ) ); ?>">PS</a><p>Polystyrene</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/ps/ps-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ps/ps-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ps/ps-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/ps/ps-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--teal"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-layers" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/abs/' ) ); ?>">ABS</a><p>Acrylonitrile Butadiene Styrene</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/abs/abs-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/abs/abs-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/abs/abs-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/abs/abs-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-material">
            <div class="ab-mega-material-head">
                <span class="ab-mega-material-icon ab-mega-material-icon--blue"><svg viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-flask" /></svg></span>
                <div><a class="ab-mega-material-title" href="<?php echo esc_url( home_url( '/products/eva/' ) ); ?>">EVA</a><p>Ethylene Vinyl Acetate</p></div>
            </div>
            <div class="ab-mega-grade-links">
                <a href="<?php echo esc_url( home_url( '/products/eva/eva-prime/' ) ); ?>">Prime / Virgin <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/eva/eva-offgrade/' ) ); ?>">Off-grade <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/eva/eva-recycled/' ) ); ?>">Recycled <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
                <a href="<?php echo esc_url( home_url( '/products/eva/eva-scrap-regrind/' ) ); ?>">Scrap / Regrind <svg class="ab-mega-chevron" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><use href="#ab-mega-chevron" /></svg></a>
            </div>
        </div>
        <div class="ab-mega-help"><span class="ab-mega-eyebrow">Not sure which grade?</span><p>Start with your application.</p><span>Tell us your process, material and quantity. We’ll help you frame the requirement.</span><a class="ab-mega-directory" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Discuss your requirement <svg class="ab-icon" aria-hidden="true"><use href="#ab-mega-arrow" /></svg></a></div>
    </div>
    <div class="ab-mega-footer"><span><svg class="ab-mega-footer-icon" viewBox="0 0 40 40" aria-hidden="true" focusable="false"><use href="#ab-mega-layers" /></svg>Browse the polymer first, then compare its supply types.</span><div class="ab-mega-directories"><a class="ab-mega-directory" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Product range <svg class="ab-icon" aria-hidden="true"><use href="#ab-mega-arrow" /></svg></a></div></div>
    <?php if ( ! empty( $extra_links ) ) : ?>
    <div class="ab-mega-extra"><span class="ab-mega-eyebrow">More product links</span><div><?php echo $extra_links; // Links are escaped when collected from the WordPress menu. ?></div></div>
    <?php endif; ?>
</div>
