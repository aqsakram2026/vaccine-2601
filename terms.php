<?php
$sitePage = 'terms';
$pageTitle = 'Terms & Privacy | E-Vaccine';
$pageDescription = 'Read the E-Vaccine site terms, privacy information and customer feedback publication policy.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Terms & Privacy']
];
require __DIR__ . '/includes/site-header.php';
?>
<main>
    <section class="page-hero">
        <div class="container section-space">
            <span class="eyebrow mb-3"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Clear and respectful care</span>
            <h1 class="display-title mb-3">Terms &amp; <span class="text-gradient">Privacy.</span></h1>
            <p class="lead-copy mb-0">How to use this website and what happens to information submitted through its forms.</p>
            <p class="small text-secondary mt-3 mb-0">Last updated: November 2, 2026 · This summary is informational and is not legal advice.</p>
        </div>
    </section>

    <section class="container section-space pt-4">
        <div class="row g-4">
            <div class="col-lg-3" data-aos="fade-right">
                <nav class="surface-card terms-toc p-4" aria-label="Terms and privacy sections">
                    <h2 class="h6 fw-bold">On this page</h2>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                        <li><a href="#website-use">Website use</a></li>
                        <li><a href="#health-information">Health information</a></li>
                        <li><a href="#privacy">Information and privacy</a></li>
                        <li><a href="#feedback-policy">Feedback and reviews</a></li>
                        <li><a href="#third-parties">Third-party services</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-9">
                <div class="d-flex flex-column gap-3">
                    <article class="surface-card p-4 p-md-5" id="website-use" data-aos="fade-up">
                        <h2 class="h4 fw-bold">Using this website</h2>
                        <p class="text-secondary mb-0">Use the information pages and role-specific portals lawfully and only with accounts you are authorized to access. Keep account credentials private and provide accurate information when using a portal. Access may be unavailable during maintenance or service interruptions.</p>
                    </article>
                    <article class="surface-card p-4 p-md-5" id="health-information" data-aos="fade-up">
                        <h2 class="h4 fw-bold">Health information and schedule</h2>
                        <p class="text-secondary mb-0">The schedule on this website is a general project overview, not medical guidance. Vaccine recommendations depend on the person and current clinical guidance. Ask a qualified healthcare professional about individual care, vaccine timing, contraindications or urgent symptoms. Do not rely on this site for diagnosis or emergency care.</p>
                    </article>
                    <article class="surface-card p-4 p-md-5" id="privacy" data-aos="fade-up">
                        <h2 class="h4 fw-bold">Information and privacy</h2>
                        <p class="text-secondary">The contact form collects the details you enter so the team can respond. The feedback form stores a rating and review; a display name is optional, and it does not ask for a phone number or email address. Avoid submitting sensitive health information or details about a child in public feedback.</p>
                        <p class="text-secondary mb-0">Form submissions are stored in the project's application database and are accessible to authorized site operators. Reviews are kept unpublished until approved. This project does not define an automatic data-retention period or a self-service deletion tool; contact the team through the <a href="contact.php">Contact page</a> to ask about a submission.</p>
                    </article>
                    <article class="surface-card p-4 p-md-5" id="feedback-policy" data-aos="fade-up">
                        <h2 class="h4 fw-bold">Feedback and reviews</h2>
                        <p class="text-secondary mb-0">Submit only feedback that reflects your own experience and does not reveal personal or medical details. By checking the publication-consent box, you agree that the review may be shown publicly after moderation. Submissions are not published automatically, may be declined or removed, and are not represented as a verified clinical outcome or patient-satisfaction statistic.</p>
                    </article>
                    <article class="surface-card p-4 p-md-5" id="third-parties" data-aos="fade-up">
                        <h2 class="h4 fw-bold">Third-party services and location</h2>
                        <p class="text-secondary mb-0">This site loads libraries from public CDNs and embeds a Google Maps view for Jinnah Postgraduate Medical Centre in Karachi. Those services may receive standard connection data when your browser requests their content and are governed by their providers' terms and privacy notices. The map is a location reference and does not imply affiliation with JPMC.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
