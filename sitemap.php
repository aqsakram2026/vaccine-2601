<?php
$sitePage = 'sitemap';
$pageTitle = 'Sitemap | E-Vaccine';
$pageDescription = 'Browse E-Vaccine information pages and choose the parent, hospital or administrator portal.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Sitemap']
];
require __DIR__ . '/includes/site-header.php';

$siteLinks = [
    ['Information', 'Home', 'Platform overview and age milestones', 'index.php'],
    ['Information', 'About', 'Roles, workflow and frequently asked questions', 'about.php'],
    ['Information', 'Vaccine Awareness', 'Learn what childhood vaccines help prevent and why they matter', 'awareness.php'],
    ['Information', 'Contact', 'Ask a question or send a platform enquiry', 'contact.php'],
    ['Information', 'Sitemap', 'Browse all pages and portal links', 'sitemap.php'],
    ['Information', 'Terms & Privacy', 'Read website terms, privacy and feedback details', 'terms.php'],
    ['Patient Portal', 'Patient sign in', 'Open the patient account portal', 'parents/parent-login.php'],
    ['Patient Portal', 'Patient registration', 'Create a patient or parent account', 'parents/parent.php'],
    ['Patient Portal', 'Patient dashboard', 'Manage children, appointment requests, and vaccination status', 'parents/parent_dashboard.php'],
    ['Patient Portal', 'Child registration', 'Add a child to the patient account', 'parents/child.php'],
    ['Patient Portal', 'Appointment booking', 'Request a vaccination appointment at an available hospital', 'parents/vaccine_request.php'],
    ['Patient Portal', 'Edit patient profile', 'Update contact details and profile image', 'parents/profile.php'],
    ['Hospital Portal', 'Hospital sign in', 'Open the hospital management portal', 'hospital/hospital_login.php'],
    ['Hospital Portal', 'Hospital registration', 'Submit a hospital application', 'hospital/hospital_register.php'],
    ['Hospital Portal', 'Hospital approval status', 'Check an application status', 'hospital/hospital_status.php'],
    ['Hospital Portal', 'Hospital dashboard', 'Review appointments and generate vaccination reports', 'hospital/hospital_dashboard.php'],
    ['Hospital Portal', 'Manage vaccine stock', 'Update the hospital vaccine inventory', 'hospital/hospital_vaccine.php'],
    ['Hospital Portal', 'Edit hospital profile', 'Update hospital details and profile image', 'hospital/profile.php'],
    ['Administration Portal', 'Admin sign in', 'Open administrative tools', 'admin/admin_login.php'],
    ['Administration Portal', 'Admin dashboard', 'Manage the platform and review pending hospital applications', 'admin/admin.php'],
    ['Administration Portal', 'Vaccine management', 'View vaccines and add or edit the vaccine list', 'admin/vaccines.php'],
    ['Administration Portal', 'Hospital management', 'Review hospital applications and manage hospital accounts', 'admin/hospitalUpdate.php'],
    ['Administration Portal', 'Parent management', 'Manage registered patient and guardian accounts', 'admin/parentsUpdate.php'],
    ['Administration Portal', 'Vaccination report', 'Review vaccination records and overall approved patient totals', 'admin/vaccination_report.php']
];
$siteCategory = static function ($category) {
    return str_replace(' ', '-', strtolower($category));
};
?>
<main>
    <section class="page-hero">
        <div class="container section-space">
            <span class="eyebrow mb-3" data-aos="fade-up"><i class="bi bi-signpost-2-fill" aria-hidden="true"></i> Find your way</span>
            <h1 class="display-title mb-3" data-aos="fade-up">Explore the <span class="text-gradient">site map.</span></h1>
            <p class="lead-copy mb-0" data-aos="fade-up" data-aos-delay="75">Browse information pages and choose a portal by category. Use the search in the navigation for a quick link.</p>
        </div>
    </section>

    <section class="container section-space pt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4" data-aos="fade-up">
            <div>
                <h2 class="h3 fw-bold mb-1">Pages and portals</h2>
                <p class="text-secondary mb-0">Select a category to narrow the list.</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="sitemap-count" id="sitemapCount" role="status" aria-live="polite">
                    <i class="bi bi-link-45deg" aria-hidden="true"></i>
                    <span><?= count($siteLinks) ?> destinations</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold small" for="sitemapCategory">Category</label>
                    <select class="form-select" id="sitemapCategory" style="min-width:190px">
                    <option value="all">All categories</option>
                    <option value="information">Information</option>
                    <option value="patient-portal">Patient Portal</option>
                    <option value="hospital-portal">Hospital Portal</option>
                    <option value="administration-portal">Administration Portal</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="surface-card overflow-hidden" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="sitemapTable">
                    <caption class="visually-hidden">E-Vaccine information pages and portal links</caption>
                    <thead>
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Page</th>
                            <th scope="col">Description</th>
                            <th scope="col" class="text-end">Open</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siteLinks as [$category, $label, $description, $url]): ?>
                            <tr data-category="<?= site_escape($siteCategory($category)) ?>">
                                <td><span class="badge rounded-pill text-bg-light border"><?= site_escape($category) ?></span></td>
                                <th scope="row"><a class="text-decoration-none fw-semibold" href="<?= site_escape($url) ?>"><?= site_escape($label) ?></a></th>
                                <td class="text-secondary"><?= site_escape($description) ?></td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-vax rounded-pill" href="<?= site_escape($url) ?>" aria-label="Open <?= site_escape($label) ?>"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="sitemapEmpty" class="p-4 text-center text-secondary d-none">There are no pages in this category.</div>
        </div>
        <div class="mt-4 small text-secondary" data-aos="fade-up">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            Portal sign-in is intended for the account role shown in each category.
            <button type="button" class="btn btn-link btn-sm p-0 align-baseline" data-bs-toggle="popover" title="Need a hand?" data-bs-content="If you are unsure which portal to use, visit Contact and send the team a message.">Need help choosing?</button>
        </div>
    </section>
</main>
<script>
(() => {
    const categorySelect = document.getElementById("sitemapCategory");
    const tableRows = [...document.querySelectorAll("#sitemapTable tbody tr")];
    const emptyState = document.getElementById("sitemapEmpty");
    const countLabel = document.querySelector("#sitemapCount span");
    if (!categorySelect || !emptyState || !countLabel) return;

    function updateCategory() {
        let visibleCount = 0;
        tableRows.forEach(row => {
            const visible = categorySelect.value === "all" || row.dataset.category === categorySelect.value;
            row.classList.toggle("d-none", !visible);
            if (visible) visibleCount++;
        });
        emptyState.classList.toggle("d-none", visibleCount > 0);
        let destinationLabel = "destinations";
        if (visibleCount === 1) {
            destinationLabel = "destination";
        }
        countLabel.textContent = `${visibleCount} ${destinationLabel}`;
    }

    categorySelect.addEventListener("change", updateCategory);
    updateCategory();
})();
</script>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
