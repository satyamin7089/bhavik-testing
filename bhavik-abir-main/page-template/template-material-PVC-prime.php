<?php
/**
 * Template Name: PVC — Prime / Virgin
 * Template Post Type: page
 * Standalone static supply-type page; related links navigate to real WordPress pages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main id="main-content" class="ab-material-grade-page">
    <nav class="ab-material-switch" aria-label="PVC material and supply types">
        <div class="ab-wrap ab-material-switch-inner">
            <span class="ab-material-switch-label">PVC supply options</span>
            <div class="ab-material-switch-links">
                <a href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">All PVC</a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-prime/' ) ); ?>" aria-current="page">Prime / Virgin</a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-offgrade/' ) ); ?>">Off-grade</a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-recycled/' ) ); ?>">Recycled</a>
                <a href="<?php echo esc_url( home_url( '/products/pvc/pvc-scrap-regrind/' ) ); ?>">Scrap / Regrind</a>
            </div>
        </div>
    </nav>
    <section class="ab-prod-hero" aria-labelledby="material-title">
        <div class="ab-wrap ab-prod-hero-grid">
            <div class="ab-prod-copy  is-visible">
                <span class="ab-eyebrow">Polyvinyl Chloride · Fresh resin</span>
                <h1 id="material-title">Prime / Virgin PVC Resin</h1>
                <p>Fresh PVC resin selected by manufacturer grade, datasheet and documented properties. Prime is not the same as near-virgin or recycled material.</p>
                <p>Clarify whether you need base resin or a ready-formulated compound. Specify rigid or flexible use, stabiliser requirements and the processing route.</p>
                <div class="ab-prod-actions"><a class="ab-btn" href="#primary-enquiry">Request a PVC quote <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a><a class="ab-btn ab-btn--outline" href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">All PVC supply types</a></div>
            </div>
                <div class="ab-product-gallery " data-hdpe-gallery role="region"
                    aria-roledescription="carousel" aria-label="PVC product image gallery">
                    <div class="ab-prod-art">
                        <div class="ab-gallery-stage">
                            <div class="ab-gallery-slide is-active" id="hdpe-gallery-slide-1" role="group"
                                aria-roledescription="slide" aria-label="1 of 4: PVC polymer pellets"
                                aria-hidden="false"><img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/pellets-macro.jpg' ) ); ?>" alt="" width="1536"
                                    height="1024" decoding="async" loading="eager" fetchpriority="high"></div>
                            <div class="ab-gallery-slide" id="hdpe-gallery-slide-2" role="group"
                                aria-roledescription="slide" aria-label="2 of 4: virgin and recycled polymer material"
                                aria-hidden="true"><img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/virgin-vs-recycled.jpg' ) ); ?>" alt="" width="1536"
                                    height="1024" decoding="async" loading="eager"></div>
                            <div class="ab-gallery-slide ab-gallery-slide--pellets-detail" id="hdpe-gallery-slide-3"
                                role="group" aria-roledescription="slide"
                                aria-label="3 of 4: close detail of PVC polymer pellets" aria-hidden="true"><img
                                    src="<?php echo esc_url( get_theme_file_uri( '/assets/img/pellets-macro.jpg' ) ); ?>" alt="" width="1536" height="1024"
                                    decoding="async" loading="eager"></div>
                            <div class="ab-gallery-slide ab-gallery-slide--recycled-detail" id="hdpe-gallery-slide-4"
                                role="group" aria-roledescription="slide"
                                aria-label="4 of 4: close detail of recycled polymer material" aria-hidden="true"><img
                                    src="<?php echo esc_url( get_theme_file_uri( '/assets/img/virgin-vs-recycled.jpg' ) ); ?>" alt="" width="1536" height="1024"
                                    decoding="async" loading="eager"></div>
                        </div><button class="ab-gallery-arrow ab-gallery-arrow--prev" type="button"
                            aria-label="Previous product image"><svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m15 18-6-6 6-6" />
                            </svg></button><button class="ab-gallery-arrow ab-gallery-arrow--next" type="button"
                            aria-label="Next product image"><svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="m9 18 6-6-6-6" />
                            </svg></button>
                    </div>
                    <div class="ab-gallery-thumbs" role="group" aria-label="Choose a product image"><button
                            class="ab-gallery-thumb is-active" type="button"
                            aria-label="Show image 1 of 4: PVC polymer pellets" aria-controls="hdpe-gallery-slide-1"
                            aria-pressed="true"><img src="<?php echo esc_url( get_theme_file_uri( '/assets/img/pellets-macro.jpg' ) ); ?>" alt="" width="1536"
                                height="1024" loading="eager" decoding="async"></button><button class="ab-gallery-thumb"
                            type="button" aria-label="Show image 2 of 4: virgin and recycled polymer material"
                            aria-controls="hdpe-gallery-slide-2" aria-pressed="false"><img
                                src="<?php echo esc_url( get_theme_file_uri( '/assets/img/virgin-vs-recycled.jpg' ) ); ?>" alt="" width="1536" height="1024"
                                loading="eager" decoding="async"></button><button
                            class="ab-gallery-thumb ab-gallery-thumb--crop" type="button"
                            aria-label="Show image 3 of 4: close detail of PVC polymer pellets"
                            aria-controls="hdpe-gallery-slide-3" aria-pressed="false"><img
                                src="<?php echo esc_url( get_theme_file_uri( '/assets/img/pellets-macro.jpg' ) ); ?>" alt="" width="1536" height="1024" loading="eager"
                                decoding="async"></button><button class="ab-gallery-thumb ab-gallery-thumb--crop"
                            type="button" aria-label="Show image 4 of 4: close detail of recycled polymer material"
                            aria-controls="hdpe-gallery-slide-4" aria-pressed="false"><img
                                src="<?php echo esc_url( get_theme_file_uri( '/assets/img/virgin-vs-recycled.jpg' ) ); ?>" alt="" width="1536" height="1024"
                                loading="eager" decoding="async"></button></div>
                </div>
        </div>
    </section>
    <nav class="ab-prod-sticky-wrap" aria-label="Prime / Virgin PVC page sections"><div class="ab-prod-sticky" id="product-section-nav"><span class="ab-prod-sticky-label">On this page</span><a href="#overview">Overview</a><a href="#selection-guide">Selection checks</a><a href="#applications">Application fit</a><a href="#primary-enquiry">Enquiry</a><a href="#faqs">FAQs</a><a href="#supply-types">Other supply types</a></div></nav>
    <section class="ab-section" id="overview" aria-labelledby="overview-title">
        <div class="ab-wrap ab-grade-intro"><div><span class="ab-eyebrow">Understand the material</span><h2 id="overview-title">Choosing prime / virgin PVC</h2><p>PVC is supplied as resin and formulated compounds; rigid and flexible systems have different processing and end-use requirements.</p><p>Fresh PVC resin selected by manufacturer grade, datasheet and documented properties. Prime is not the same as near-virgin or recycled material. The supply classification and polymer formulation are different decisions: review both before placing an order.</p></div><aside class="ab-grade-brief" aria-labelledby="brief-title"><span class="ab-supply-route-kicker">Your specification brief</span><h3 id="brief-title">What to include</h3><p>Specify resin K-value or compound specification, hardness, plasticiser, filler and stabiliser system.</p><p>Add the required form, colour, quantity, delivery location and any documentation or sample needs.</p></aside></div>
    </section>
    <section class="ab-section ab-section--white" id="selection-guide" aria-labelledby="selection-title">
        <div class="ab-wrap"><div class="ab-section-head"><span class="ab-eyebrow">Specification-led supply</span><h2 id="selection-title">Before selecting this PVC supply route</h2><p>Use these checks to make the enquiry specific to your process. General material guidance is not guaranteed batch data.</p></div><div class="ab-supply-route-grid ab-supply-route-grid--three">
            <article class="ab-supply-route-card"><span class="ab-supply-route-kicker">01 · Identify</span><h3>Manufacturer grade &amp; traceability</h3><p>Ask for the producer grade name, current technical datasheet and lot identification. Where applicable, request the relevant certificate or declaration for the specific application.</p></article>
            <article class="ab-supply-route-card"><span class="ab-supply-route-kicker">02 · Evaluate</span><h3>Process &amp; application fit</h3><p>Clarify whether you need base resin or a ready-formulated compound. Specify rigid or flexible use, stabiliser requirements and the processing route.</p></article>
            <article class="ab-supply-route-card"><span class="ab-supply-route-kicker">03 · Confirm</span><h3>Acceptance before supply</h3><p>Agree the specification, quantity, pack size, destination and any sample or trial requirements. A resin being prime does not establish approval for every end use.</p></article>
        </div></div>
    </section>
    <section class="ab-section" id="applications" aria-labelledby="applications-title">
        <div class="ab-wrap"><div class="ab-section-head"><span class="ab-eyebrow">Match the end use</span><h2 id="applications-title">Application fit for prime / virgin PVC</h2><p>The PVC family is evaluated for profiles, conduits and application-specific flexible products. Those examples do not establish that every prime / virgin lot is suitable for those uses.</p><p>Clarify whether you need base resin or a ready-formulated compound. Specify rigid or flexible use, stabiliser requirements and the processing route.</p><p>For food-contact, medical, electrical, pressure-rated or other regulated uses, request evidence for the exact grade, source and intended application. Confirm processing conditions and finished-product performance before approval.</p></div><a class="ab-link" href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">Read the full PVC material overview <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a></div>
    </section>
        <section class="ab-primary-enquiry" id="primary-enquiry" aria-labelledby="primary-enquiry-title">
            <div class="ab-wrap ab-primary-layout">
                <div class="ab-primary-copy "><span class="ab-eyebrow">PVC enquiry</span>
                    <h2 id="primary-enquiry-title">Request prime / virgin PVC pricing</h2>
                    <p>Provide the available technical and commercial details so the product team can review your
                        requirement
                        efficiently. Share the application, processing method, grade or specification, required form,
                        quantity, pack
                        size, destination, and required documents.</p>
                    <div class="ab-primary-prompt"><span>Grade / specification</span><span>Application &amp;
                            process</span><span>Form &amp; quantity</span><span>Pack size &amp;
                            destination</span><span>Datasheet
                            &amp; documents</span></div>
                </div>
                <form class="ab-product-form " id="hdpe-enquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="action" value="ab_material_enquiry"><input type="hidden" name="enquiry_kind" value="pvc"><input type="hidden" name="enquiry_page" value="pvc-prime"><?php wp_nonce_field( 'ab_material_enquiry', 'ab_material_enquiry_nonce' ); ?><label class="ab-honeypot" aria-hidden="true">Leave this field empty<input type="text" name="ab_website" tabindex="-1" autocomplete="off"></label>
                    <h3>Submit your prime / virgin PVC requirement</h3>
                    <p>Complete what you know. Your enquiry is emailed to the site administrator address configured in WordPress. Optional attachments are checked before sending.</p>
                    <div class="ab-product-form-grid">
                        <div class="ab-product-field"><label for="hdpe-name">Name *</label><input id="hdpe-name"
                                name="name" autocomplete="name" required placeholder="Your name"></div>
                        <div class="ab-product-field"><label for="hdpe-email">Business email *</label><input
                                id="hdpe-email" name="email" type="email" autocomplete="email" required
                                placeholder="name@company.com"></div>
                        <div class="ab-product-field"><label for="hdpe-material-type">Supply type</label><select
                                id="hdpe-material-type" name="material_type">
                                <option value="">Select supply type</option>
                                <option selected>Prime / Virgin</option>
                                <option>Off-grade</option>
                                <option>Recycled</option>
                                <option>Scrap / Regrind</option>
                                <option>Not sure</option>
                            </select></div>
                        <div class="ab-product-field"><label for="hdpe-process">Processing method</label><select
                                id="hdpe-process" name="processing_method"><option value="">Select if known</option><option>Injection moulding</option><option>Extrusion</option><option>Blow moulding</option><option>Film extrusion</option><option>Thermoforming</option><option>Compounding / recovery</option><option>Other</option></select></div>
                        <div class="ab-product-field"><label for="hdpe-manufacturer">Manufacturer (if
                                known)</label><input id="hdpe-manufacturer" name="manufacturer"
                                placeholder="Manufacturer or source"></div>
                        <div class="ab-product-field"><label for="hdpe-grade">Grade or specification</label><input
                                id="hdpe-grade" name="grade" placeholder="Grade name or target specification"></div>
                        <div class="ab-product-field"><label for="hdpe-form">Required form</label><select id="hdpe-form"
                                name="form">
                                <option value="">Select if known</option>
                                <option>Granules</option>
                                <option>Powder</option>
                                <option>Compound</option>
                                <option>Film-grade</option>
                                <option>Extrusion-grade</option>
                                <option>Other</option>
                                <option>Not sure</option>
                            </select></div>
                        <div class="ab-product-field"><label for="hdpe-color">Color</label><input id="hdpe-color"
                                name="color" placeholder="Color requirement"></div>
                        <div class="ab-product-field"><label for="hdpe-quantity">Quantity and unit</label><input
                                id="hdpe-quantity" name="quantity" placeholder="e.g. 5 metric tonnes"></div>
                        <div class="ab-product-field"><label for="hdpe-destination">Destination</label><input
                                id="hdpe-destination" name="destination" autocomplete="shipping country"
                                placeholder="Country and port / city"></div>
                        <div class="ab-product-field ab-product-field--full"><label for="hdpe-requirement">Application
                                and required
                                properties</label><textarea id="hdpe-requirement" name="requirement" required
                                placeholder="Describe the end application, technical requirements, and performance needs"></textarea>
                        </div>
                        <div class="ab-product-field ab-product-field--full" id="datasheet-upload"><label
                                for="hdpe-datasheet">Upload a technical datasheet (optional)</label><input
                                id="hdpe-datasheet" name="datasheet" type="file"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png"><span
                                class="ab-field-help">Choose a current datasheet or target specification. It is attached to the enquiry email after file-type and size checks.</span></div>
                    </div>
                    <details class="ab-enquiry-details">
                        <summary>More technical &amp; commercial details (optional)</summary>
                        <div class="ab-product-form-grid ab-details-grid">
                            <div class="ab-product-field"><label for="hdpe-pack-size">Pack size /
                                    packaging</label><input id="hdpe-pack-size" name="pack_size"
                                    placeholder="e.g. bag or bulk requirement"></div>
                            <div class="ab-product-field"><label for="hdpe-frequency">Requirement
                                    frequency</label><select id="hdpe-frequency" name="frequency">
                                    <option value="">Select if known</option>
                                    <option>One-time</option>
                                    <option>Monthly</option>
                                    <option>Regular</option>
                                </select></div>
                            <div class="ab-product-field"><label for="hdpe-delivery-date">Required delivery
                                    date</label><input id="hdpe-delivery-date" name="delivery_date" type="date"></div>
                            <div class="ab-product-field"><label for="hdpe-documents">Required
                                    documentation</label><input id="hdpe-documents" name="documents"
                                    placeholder="List any required documents"></div>
                            <div class="ab-product-field ab-product-field--full"><label for="hdpe-notes">Additional
                                    notes</label><textarea id="hdpe-notes" name="notes"
                                    placeholder="Anything else to help review the requirement"></textarea></div>
                        </div>
                    </details>
                    <div class="ab-product-form-foot"><button class="ab-btn ab-btn--blue" type="submit">Review PVC
                            requirement
                            <svg class="ab-icon" aria-hidden="true">
                                <use href="#ab-icon-arrow" />
                            </svg></button><span class="ab-product-status" id="hdpe-form-status" role="status"
                            aria-live="polite"></span></div>
                    <p class="ab-form-small">Required fields are checked before submission. Confirm source, grade, documentation, pack size, minimum order quantity, current availability and delivery timing for each request.</p>
                </form>
            </div>
        </section>
    <section class="ab-section" id="faqs" aria-labelledby="faqs-title">
        <div class="ab-wrap"><div class="ab-section-head"><span class="ab-eyebrow">Common questions</span><h2 id="faqs-title">Prime / Virgin PVC: useful answers</h2></div>
            <details class="ab-enquiry-details"><summary>Is prime PVC the same as near-virgin material?</summary><p>No. Prime / virgin describes fresh resin supplied to its declared grade specification. Near-virgin is not a substitute for a clear source and classification statement. Confirm the exact material identity before purchase.</p></details>
            <details class="ab-enquiry-details"><summary>Which details matter most for this PVC enquiry?</summary><p>Include resin K-value or compound specification, hardness, plasticiser, filler and stabiliser system. Clarify whether you need base resin or a ready-formulated compound. Specify rigid or flexible use, stabiliser requirements and the processing route. Also include quantity, delivery location and the intended process.</p></details>
            <details class="ab-enquiry-details"><summary>How can I compare the other supply types?</summary><p>Use the supply navigation above or the related pages below to compare prime, off-grade, recycled and scrap / regrind PVC. Each page describes a different supply route; exact availability and suitability still need confirmation.</p></details>
        </div>
    </section>
    <section class="ab-section ab-section--white ab-supply-routes" id="supply-types" aria-labelledby="supply-types-title">
        <div class="ab-wrap">
            <div class="ab-section-head"><span class="ab-eyebrow">Same polymer. Different supply routes.</span><h2 id="supply-types-title">Compare other PVC supply types</h2><p>Choose a supply type to read its own material guide, selection checks and enquiry details. These links open separate pages.</p></div>
            <div class="ab-supply-route-grid ab-supply-route-grid--three">
                <article class="ab-supply-route-card">
                    <span class="ab-supply-route-kicker">Lot-specific supply</span>
                    <h3>Off-grade PVC</h3>
                    <p>PVC supplied outside its original prime specification or as a declared off-grade lot. Review the actual deviation and batch results before deciding suitability.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/products/pvc/pvc-offgrade/' ) ); ?>">Explore off-grade PVC <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-supply-route-card">
                    <span class="ab-supply-route-kicker">Recovered &amp; reprocessed</span>
                    <h3>Recycled PVC</h3>
                    <p>Recovered PVC reprocessed for further use. Source, segregation and lot consistency matter as much as the polymer name.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/products/pvc/pvc-recycled/' ) ); ?>">Explore recycled PVC <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
                <article class="ab-supply-route-card">
                    <span class="ab-supply-route-kicker">Sorted recovery feedstock</span>
                    <h3>Scrap / Regrind PVC</h3>
                    <p>Sorted PVC scrap or mechanically reduced regrind for an agreed recovery or conversion process. Feedstock is not automatically equivalent to ready-to-use resin.</p>
                    <a class="ab-link" href="<?php echo esc_url( home_url( '/products/pvc/pvc-scrap-regrind/' ) ); ?>">Explore scrap / regrind PVC <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
                </article>
            </div>
            <a class="ab-link" href="<?php echo esc_url( home_url( '/products/pvc/' ) ); ?>">Back to all PVC materials <svg class="ab-icon" aria-hidden="true"><use href="#ab-icon-arrow" /></svg></a>
        </div>
    </section>
</main>
<?php get_footer(); ?>
