<?php
$sitePage = 'about';
$pageTitle = 'About | E-Vaccine';
$pageDescription = "Learn how the E-Vaccine platform connects parents, hospitals and administrators around a child's vaccination journey.";
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'About']
];
require __DIR__ . '/includes/site-header.php';
?>
<main>
    <section class="page-hero">
        <div class="container section-space">
            <div class="row align-items-center g-5">
                <div class="col-lg-7" data-aos="fade-up">
                    <span class="eyebrow mb-3"><i class="bi bi-info-circle-fill" aria-hidden="true"></i> About the platform</span>
                    <h1 class="display-title mb-4">One record.<br><span class="text-gradient">A more connected journey.</span></h1>
                    <p class="lead-copy mb-0">E-Vaccine brings parents, hospitals and administrators into one coordinated workflow, helping keep vaccination details organized from registration through follow-up.</p>
                </div>
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="surface-card p-4 p-md-5 h-100 d-flex flex-column justify-content-center text-center">
                        <span class="icon-tile mx-auto mb-3"><i class="bi bi-shield-check" aria-hidden="true"></i></span>
                        <h2 class="h4 fw-bold">Designed for trusted care</h2>
                        <p class="text-secondary mb-4">Parents manage child details, administrators coordinate requests, and hospitals update appointment outcomes in their respective portals.</p>
                        <div class="row g-3">
                            <div class="col-6">
                                <span class="icon-tile mx-auto mb-2"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                                <span class="small fw-semibold">Health first</span>
                            </div>
                            <div class="col-6">
                                <span class="icon-tile mx-auto mb-2"><i class="bi bi-check-circle" aria-hidden="true"></i></span>
                                <span class="small fw-semibold">Care and protection</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container section-space">
        <div class="section-heading" data-aos="fade-up">
            <span class="eyebrow mb-3">How it works</span>
            <h2>A practical path from record to follow-up</h2>
            <p class="mb-0">The process follows the user roles and workflow described in the project.</p>
        </div>
        <div class="row g-3 workflow-steps">
            <div class="col-md-4" data-aos="fade-up">
                <article class="surface-card workflow-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="icon-tile"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
                        <span class="text-gradient fw-bold fs-4">01</span>
                    </div>
                    <h3 class="h5 fw-bold">Register</h3>
                    <p class="text-secondary mb-0">A parent creates an account and adds the child's details to begin a vaccination record.</p>
                </article>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="75">
                <article class="surface-card workflow-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="icon-tile"><i class="bi bi-calendar2-check" aria-hidden="true"></i></span>
                        <span class="text-gradient fw-bold fs-4">02</span>
                    </div>
                    <h3 class="h5 fw-bold">Request</h3>
                    <p class="text-secondary mb-0">The parent reviews the schedule and requests an appointment through an available hospital.</p>
                </article>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
                <article class="surface-card workflow-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="icon-tile"><i class="bi bi-clipboard2-heart" aria-hidden="true"></i></span>
                        <span class="text-gradient fw-bold fs-4">03</span>
                    </div>
                    <h3 class="h5 fw-bold">Update</h3>
                    <p class="text-secondary mb-0">After the appointment, hospital staff update its outcome so the parent can review the record.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="py-5 bg-body-tertiary">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7" data-aos="fade-right">
                    <span class="eyebrow mb-3">A closer look</span>
                    <h2 class="h2 fw-bold">Keep care information close at hand</h2>
                    <p class="text-secondary lh-lg">Digital records can make it easier for families and care teams to keep track of the next step. The schedule shown on this site is an overview, not a substitute for clinical advice.</p>
                    <button class="btn btn-vax rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#careLightbox">
                        <i class="bi bi-arrows-fullscreen me-2" aria-hidden="true"></i>View the care illustration
                    </button>
                </div>
                <div class="col-lg-5" data-aos="zoom-in">
                    <button class="btn p-0 border-0 rounded-4 overflow-hidden shadow-sm w-100" type="button" data-bs-toggle="modal" data-bs-target="#careLightbox" aria-label="Open the care illustration in a larger view">
                        <img src="images/care-illustration.svg" alt="A parent and child meet a healthcare professional beside a vaccination record" class="img-fluid w-100">
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="container section-space">
        <div class="section-heading" data-aos="fade-up">
            <span class="eyebrow mb-3">Common questions</span>
            <h2>Helpful answers</h2>
            <p class="mb-0">A quick overview of how the platform's roles and schedule work.</p>
        </div>
        <div class="accordion surface-card overflow-hidden" id="aboutFaq" data-aos="fade-up">
            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">Who can register a child?</button></h3>
                <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#aboutFaq"><div class="accordion-body text-secondary">A parent can create an account and add a child's details through the parent portal.</div></div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">How do parents request a hospital appointment?</button></h3>
                <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#aboutFaq"><div class="accordion-body text-secondary">Sign in to the parent portal to review available options and submit an appointment request.</div></div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree">How does a hospital join?</button></h3>
                <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#aboutFaq"><div class="accordion-body text-secondary">A hospital can submit an application through the hospital registration page. The application status can be checked in the hospital portal.</div></div>
            </div>
            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour" aria-expanded="false" aria-controls="faqFour">Should I use the age table as medical advice?</button></h3>
                <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#aboutFaq"><div class="accordion-body text-secondary">No. The milestones are a general project overview. Confirm vaccine-specific recommendations and timing with a qualified healthcare provider.</div></div>
            </div>
        </div>
    </section>
</main>

<div class="modal fade" id="careLightbox" tabindex="-1" aria-labelledby="careLightboxTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-0">
                <h2 class="modal-title fs-5" id="careLightboxTitle">E-Vaccine care illustration</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0"><img src="images/care-illustration.svg" alt="A parent and child meet a healthcare professional beside a vaccination record" class="img-fluid rounded-3 w-100"></div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
