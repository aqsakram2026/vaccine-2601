<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['feedback_csrf'])) {
    $_SESSION['feedback_csrf'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/config.php';

$databaseMetrics = null;
$bookingStatusLabels = [];
$bookingStatusValues = [];
$approvedReviews = [];
$reviewsAvailable = false;
$scheduleMilestoneCount = 0;

if ($connect) {
    $metricsResult = mysqli_query(
        $connect,
        "SELECT
            (SELECT COUNT(*) FROM children) AS child_profiles,
            (SELECT COUNT(*) FROM bookings) AS appointment_requests,
            (SELECT COUNT(*) FROM hospitals WHERE STATUS = 'accepted') AS partner_hospitals,
            (SELECT COUNT(*) FROM customer_feedback WHERE is_approved = 1) AS published_reviews,
            (SELECT ROUND(AVG(rating), 1) FROM customer_feedback WHERE is_approved = 1) AS average_rating"
    );
    if ($metricsResult) {
        $databaseMetrics = mysqli_fetch_assoc($metricsResult);
        mysqli_free_result($metricsResult);
    } else {
        error_log('E-Vaccine homepage metric query failed: ' . mysqli_error($connect));
    }

    $milestoneResult = mysqli_query(
        $connect,
        "SELECT COUNT(DISTINCT Age_Limit) AS total FROM vaccines WHERE Status = 'Active'"
    );
    if ($milestoneResult) {
        $milestoneRow = mysqli_fetch_assoc($milestoneResult);
        $scheduleMilestoneCount = (int)($milestoneRow['total'] ?? 0);
        mysqli_free_result($milestoneResult);
    } else {
        error_log('E-Vaccine homepage vaccine milestone query failed: ' . mysqli_error($connect));
    }

    $statusResult = mysqli_query(
        $connect,
        "SELECT COALESCE(NULLIF(TRIM(vaccination_status), ''), 'Pending') AS status_label, COUNT(*) AS status_count
         FROM bookings
         GROUP BY COALESCE(NULLIF(TRIM(vaccination_status), ''), 'Pending')
         ORDER BY status_label"
    );
    if ($statusResult) {
        while ($statusRow = mysqli_fetch_assoc($statusResult)) {
            $bookingStatusLabels[] = $statusRow['status_label'];
            $bookingStatusValues[] = (int)$statusRow['status_count'];
        }
        mysqli_free_result($statusResult);
    } else {
        error_log('E-Vaccine homepage appointment chart query failed: ' . mysqli_error($connect));
    }

    $reviewsResult = mysqli_query(
        $connect,
        'SELECT display_name, role, rating, review, created_at
         FROM customer_feedback
         WHERE is_approved = 1
         ORDER BY created_at DESC
         LIMIT 4'
    );
    if ($reviewsResult) {
        $reviewsAvailable = true;
        while ($reviewRow = mysqli_fetch_assoc($reviewsResult)) {
            $approvedReviews[] = $reviewRow;
        }
        mysqli_free_result($reviewsResult);
    } else {
        error_log('E-Vaccine homepage approved review query failed: ' . mysqli_error($connect));
    }
}

$sitePage = 'index';
$pageTitle = 'E-Vaccine | Every dose, on schedule';
$pageDescription = 'A shared vaccination management portal for parents, hospitals and administrators, with key schedule milestones from birth to 15 months.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php']
];
require __DIR__ . '/includes/site-header.php';
?>

<main>

    <section class="page-hero home-hero">

        <div id="virus-scene" class="home-virus-scene" aria-hidden="true"></div>


        <div class="container py-5 py-lg-4">
            <div class="row align-items-center g-4 py-lg-5">

                <div class="col-lg-7" data-aos="fade-up">

                    <span class="eyebrow mb-4"><i class="bi bi-heart-pulse-fill" aria-hidden="true"></i> A calmer way to stay on schedule</span>
                    <h1 class="display-title mb-4">Every dose,<br><span class="text-gradient">on schedule.</span></h1>
                    <p class="lead-copy mb-4">Keep a child's vaccination journey organized in one place. Parents can manage records, hospitals can update care, and administrators can coordinate the process.</p>

                    <div class="d-flex flex-wrap gap-3">
                        <a class="btn btn-vax btn-lg rounded-pill px-4" href="parents/parent.php">Create parent account <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i></a>
                        <a class="btn btn-outline-vax btn-lg rounded-pill px-4" href="about.php">Explore the platform</a>
                    </div>

                    <div class="home-proof d-flex flex-wrap gap-4 mt-4 pt-3 text-secondary small">
                        <span class="home-proof-item"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Personal records</span>
                        <span class="home-proof-item"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Hospital portal</span>
                        <span class="home-proof-item"><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Schedule milestones</span>
                    </div>
                </div>


                <div class="col-lg-5" data-aos="fade-left" data-aos-delay="100">

                    <div class="surface-card home-visual-card p-3 p-md-4 position-relative overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <span class="live-indicator">Schedule preview</span>
                                <h2 class="h5 home-visual-heading mt-1 mb-0">A journey worth keeping on track</h2>
                            </div>
                            <span class="icon-tile"><i class="bi bi-calendar2-heart" aria-hidden="true"></i></span>
                        </div>
                        <canvas id="vaccineCanvas" class="w-100 rounded-4" height="270" role="img" aria-label="Animated vaccination timeline with six milestone markers"></canvas>
                        <div class="d-flex justify-content-between gap-2 mt-2 small text-secondary">
                            <span><i class="bi bi-calendar2-check me-1 text-success" aria-hidden="true"></i><?= $scheduleMilestoneCount ?> age milestones</span>
                            <span>From active vaccine schedule</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="container section-space" aria-labelledby="portals-heading">
        <div class="section-heading text-center mx-auto" data-aos="fade-up">
            <span class="eyebrow mb-3">One connected system</span>
            <h2 id="portals-heading">Choose your portal</h2>
            <p class="mb-0">Start in the area that matches your role. Each portal has its own secure sign-in and workflow.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <article class="surface-card glass-panel portal-card p-4">
                    <span class="icon-tile mb-3"><i class="bi bi-person-heart" aria-hidden="true"></i></span>
                    <h3 class="h5 fw-bold">Parents</h3>
                    <p class="text-secondary">Register a child, check vaccination information and manage appointment requests.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-vax rounded-pill px-3" href="parents/parent-login.php">Sign in</a>
                        <a class="btn btn-sm btn-outline-vax rounded-pill px-3" href="parents/parent.php">Create account</a>
                    </div>
                    <button class="btn btn-link btn-sm text-secondary mt-3 p-0" type="button" data-bs-toggle="popover" title="Parent portal" data-bs-content="Use your parent account to add a child and manage their vaccination journey.">What can parents do?</button>
                </article>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="80">
                <article class="surface-card glass-panel portal-card p-4">
                    <span class="icon-tile mb-3"><i class="bi bi-hospital" aria-hidden="true"></i></span>
                    <h3 class="h5 fw-bold">Hospitals</h3>
                    <p class="text-secondary">Apply to join the platform and manage vaccination appointments through the hospital portal.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-sm btn-vax rounded-pill px-3" href="hospital/hospital_login.php">Sign in</a>
                        <a class="btn btn-sm btn-outline-vax rounded-pill px-3" href="hospital/hospital_register.php">Apply to join</a>
                    </div>
                    <button class="btn btn-link btn-sm text-secondary mt-3 p-0" type="button" data-bs-toggle="popover" title="Hospital portal" data-bs-content="Hospital applications are reviewed before access to the management portal is enabled.">How do hospitals join?</button>
                </article>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="160">
                <article class="surface-card glass-panel portal-card p-4">
                    <span class="icon-tile mb-3"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
                    <h3 class="h5 fw-bold">Administration</h3>
                    <p class="text-secondary">Coordinate parent and hospital activity and manage the administrative workflow.</p>
                    <a class="btn btn-sm btn-vax rounded-pill px-3" href="admin/admin_login.php">Administrator sign in</a>
                    <div><button class="btn btn-link btn-sm text-secondary mt-3 p-0" type="button" data-bs-toggle="popover" title="Administration" data-bs-content="Administrative access is reserved for authorized staff.">About administrator access</button></div>
                </article>
            </div>
        </div>
    </section>

    <style>
        .approval-stepper {
            position: relative;
        }

        .approval-step {
            position: relative;
            padding: 1.5rem;
            border: 1px solid var(--vax-line);
            border-radius: 20px;
            background: var(--vax-surface);
            box-shadow: var(--vax-shadow);
        }

        .approval-step-number {
            display: flex;
            width: 42px;
            height: 42px;
            margin-bottom: 1rem;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            color: #fff;
            background: linear-gradient(135deg, var(--vax-teal), var(--vax-blue));
            font-weight: 800;
        }

        .approval-step:not(:last-child)::after {
            position: absolute;
            top: 40px;
            right: -1.05rem;
            z-index: 1;
            width: 1.1rem;
            height: 2px;
            background: linear-gradient(90deg, var(--vax-teal), var(--vax-blue));
            content: "";
        }

        @media (max-width: 991.98px) {
            .approval-step:not(:last-child)::after {
                display: none;
            }
        }
    </style>

    <section class="container section-space" aria-labelledby="approval-flow-title">
    
    <div class="section-heading" data-aos="fade-up">
            <span class="eyebrow mb-3">Patient journey</span>
            <h2 id="approval-flow-title">From appointment to verified vaccination</h2>
            <p class="mb-0">Each request moves through the patient, hospital, and administrator steps before it is marked complete.</p>
        </div>
     
        <!-- Bootstrap row/columns keep each workflow step responsive. -->
        <ol class="approval-stepper row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 list-unstyled mb-0">
            <li class="approval-step col" data-aos="fade-up">
                <span class="approval-step-number">01</span>
                <h3 class="h5 fw-bold">Patient books</h3>
                <p class="text-secondary mb-0">The parent selects a child and an approved hospital, then submits an appointment request.</p>
            </li>
            <li class="approval-step col" data-aos="fade-up" data-aos-delay="60">
                <span class="approval-step-number">02</span>
                <h3 class="h5 fw-bold">Hospital approves</h3>
                <p class="text-secondary mb-0">Hospital staff review the request and approve or reject the appointment.</p>
            </li>
            <li class="approval-step col" data-aos="fade-up" data-aos-delay="120">
                <span class="approval-step-number">03</span>
                <h3 class="h5 fw-bold">Vaccination recorded</h3>
                <p class="text-secondary mb-0">After an approved visit, the hospital records whether the patient was vaccinated.</p>
            </li>
            <li class="approval-step col" data-aos="fade-up" data-aos-delay="180">
                <span class="approval-step-number">04</span>
                <h3 class="h5 fw-bold">Admin reviews</h3>
                <p class="text-secondary mb-0">An administrator reviews the hospital's vaccination record and approves or rejects it.</p>
            </li>
            <li class="approval-step col" data-aos="fade-up" data-aos-delay="240">
                <span class="approval-step-number">05</span>
                <h3 class="h5 fw-bold">Report available</h3>
                <p class="text-secondary mb-0">Once approved, the hospital can print a vaccination completion certificate and the patient record is included in admin totals.</p>
            </li>
        </ol>
    </section>

    <section class="py-5 database-stats" style="background:linear-gradient(135deg,#effafa,#f4f8ff)">
        <div class="container">
            <div class="row g-3 text-center">
                <div class="col-6 col-lg-4" data-aos="zoom-in">
                    <div class="surface-card p-4">
                        <span class="icon-til  mb-3"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                        <span class="h2 fw-bold text-gradient mb-1">
                            <?php if ($databaseMetrics): ?><span data-counter="<?= (int)$databaseMetrics['child_profiles'] ?>">0</span><?php else: ?>—<?php endif; ?>
                        </span>
                        <div class="text-secondary">child profiles in records</div>
                    </div>
                </div>
                <div class="col-6 col-lg-4" data-aos="zoom-in" data-aos-delay="80">
                    <div class="surface-card p-4">
                        <span class="icon-til  mb-3"><i class="bi bi-calendar2-check-fill" aria-hidden="true"></i></span>
                        <span class="h2 fw-bold text-gradient mb-1">
                            <?php if ($databaseMetrics): ?><span data-counter="<?= (int)$databaseMetrics['appointment_requests'] ?>">0</span><?php else: ?>—<?php endif; ?>
                            </span>
                        <div class="text-secondary">appointment records</div>
                    </div>
                </div>
                <div class="col-12 col-lg-4" data-aos="zoom-in" data-aos-delay="160">
                    <div class="surface-card p-4">
                        <span class="icon-til mb-3"><i class="bi bi-hospital-fill" aria-hidden="true"></i></span>
                        <span class="h2 fw-bold text-gradient mb-1">
                            <?php if ($databaseMetrics): ?><span data-counter="<?= (int)$databaseMetrics['partner_hospitals'] ?>">0</span><?php else: ?>—<?php endif; ?>
                            </span>
                        <div class="text-secondary">approved hospital partners</div>
                    </div>
                </div>
            </div>
            <p class="small text-secondary text-center mt-3 mb-0">Live aggregate totals from this database; they are not patient-satisfaction or clinical-outcome claims.</p>
        </div>
    </section>

    <section class="container section-space" id="feedback">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7" data-aos="fade-up">
                <article class="glass-panel p-4 p-md-5 h-100">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <span class="eyebrow mb-3"><i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i> Live overview</span>
                            <h2 class="h3 fw-bold mb-1">Appointment status</h2>
                            <p class="text-secondary mb-0">Counts are read from appointment records in this database.</p>
                        </div>
                        <span class="icon-tile"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i></span>
                    </div>
                    <?php if ($databaseMetrics): ?>
                        <div class="chart-wrap">
                            <canvas id="appointmentsChart" role="img" aria-label="Chart showing appointment counts by vaccination status"></canvas>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm align-middle mb-0">
                                <caption class="visually-hidden">Appointment count by vaccination status</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Appointments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($bookingStatusLabels): ?>
                                        <?php foreach ($bookingStatusLabels as $statusIndex => $statusLabel): ?>
                                            <tr>
                                                <th scope="row"><?= site_escape($statusLabel) ?></th>
                                                <td class="text-end"><?= (int)$bookingStatusValues[$statusIndex] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <th scope="row" colspan="2" class="text-secondary">No appointment records are available yet.</th>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0" role="status">Live database metrics are temporarily unavailable.</div>
                    <?php endif; ?>
                </article>
            </div>


            <div class="col-lg-5" id="reviews" data-aos="fade-up" data-aos-delay="100">
                <article class="surface-card p-4 p-md-5 h-100">
                    <span class="eyebrow mb-3"><i class="bi bi-chat-heart-fill" aria-hidden="true"></i> Customer feedback</span>
                    <h2 class="h3 fw-bold">Reviews from our community</h2>
                    <?php if ($databaseMetrics && $reviewsAvailable): ?>
                        <?php
                        $averageRatingDisplay = '—';
                        if ($databaseMetrics['average_rating'] !== null) {
                            $averageRatingDisplay = number_format((float)$databaseMetrics['average_rating'], 1);
                        }
                        ?>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="h2 fw-bold text-gradient mb-0"><?= $averageRatingDisplay ?></span>
                            <span class="text-secondary">average · <?= (int)$databaseMetrics['published_reviews'] ?> approved reviews</span>
                        </div>
                        <?php if ($approvedReviews): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($approvedReviews as $review): ?>
                                    <?php
                                    $reviewTime = strtotime($review['created_at']);
                                    $reviewDisplayName = $review['display_name'];
                                    if ($reviewDisplayName === '') {
                                        $reviewDisplayName = 'Community member';
                                    }
                                    ?>
                                    <blockquote class="review-card p-3 mb-0">
                                        <div class="review-stars" aria-label="<?= (int)$review['rating'] ?> out of 5 stars">
                                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                                <?php
                                                $starIcon = '';
                                                if ($star <= (int)$review['rating']) {
                                                    $starIcon = '-fill';
                                                }
                                                ?>
                                                <i class="bi bi-star<?= $starIcon ?>" aria-hidden="true"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="mt-2 mb-2">“<?= site_escape($review['review']) ?>”</p>
                                        <footer class="small text-secondary">
                                            <?= site_escape($reviewDisplayName) ?>
                                            · <?= site_escape($review['role']) ?>
                                            <?php if ($reviewTime !== false): ?>· <time datetime="<?= date('Y-m-d', $reviewTime) ?>"><?= date('M j, Y', $reviewTime) ?></time><?php endif; ?>
                                        </footer>
                                    </blockquote>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-secondary">No approved reviews have been published yet.</p>
                        <?php endif; ?>
                    <?php elseif (!$databaseMetrics || !$reviewsAvailable): ?>
                        <p class="text-secondary">Published feedback is temporarily unavailable.</p>
                    <?php endif; ?>
                    <button class="btn btn-vax rounded-pill px-4 mt-2" type="button" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                        Share feedback <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i>
                    </button>
                </article>
            </div>
        </div>
    </section>

    <section class="container section-space" id="schedule">
        <div class="section-heading" data-aos="fade-up">
            <span class="eyebrow mb-3">At a glance</span>
            <h2>Schedule milestones</h2>
            <p class="mb-0">These ages are the milestones listed in the project's reference schedule. Follow your healthcare provider's advice for vaccine-specific timing.</p>
        </div>
        <div class="surface-card overflow-hidden" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Milestone</th>
                            <th scope="col">Age</th>
                            <th scope="col">Reminder</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">01</th>
                            <td>At birth</td>
                            <td>Start a child's vaccination record.</td>
                        </tr>
                        <tr>
                            <th scope="row">02</th>
                            <td>6 weeks</td>
                            <td>Review the upcoming age milestone.</td>
                        </tr>
                        <tr>
                            <th scope="row">03</th>
                            <td>10 weeks</td>
                            <td>Keep the child's appointment details organized.</td>
                        </tr>
                        <tr>
                            <th scope="row">04</th>
                            <td>14 weeks</td>
                            <td>Check records with your healthcare provider.</td>
                        </tr>
                        <tr>
                            <th scope="row">05</th>
                            <td>9 months</td>
                            <td>Review the next milestone on the schedule.</td>
                        </tr>
                        <tr>
                            <th scope="row">06</th>
                            <td>15 months</td>
                            <td>Keep the vaccination record up to date.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <form action="feedback.php" method="post" id="feedbackForm">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow mb-2"><i class="bi bi-chat-square-heart" aria-hidden="true"></i> Your experience</span>
                        <h2 class="modal-title fs-5" id="feedbackModalTitle">Share feedback</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf" value="<?= site_escape($_SESSION['feedback_csrf']) ?>">
                    <div class="position-absolute" style="left:-10000px" aria-hidden="true">
                        <label for="feedbackWebsite">Leave this field empty</label>
                        <input id="feedbackWebsite" type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="feedbackName">Display name <span class="text-secondary fw-normal">(optional)</span></label>
                        <input class="form-control" id="feedbackName" name="display_name" maxlength="60" autocomplete="name" placeholder="Leave blank to post as Community member">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-semibold" for="feedbackRole">I am a</label>
                            <select class="form-select" id="feedbackRole" name="role" required>
                                <option value="Parent">Parent</option>
                                <option value="Hospital">Hospital</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-semibold" for="feedbackRating">Rating</label>
                            <select class="form-select" id="feedbackRating" name="rating" required>
                                <option value="5">5 — Great</option>
                                <option value="4">4 — Good</option>
                                <option value="3">3 — Okay</option>
                                <option value="2">2 — Poor</option>
                                <option value="1">1 — Bad</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="feedbackReview">Your review</label>
                        <textarea class="form-control" id="feedbackReview" name="review" rows="4" minlength="5" maxlength="700" required placeholder="Please avoid including personal or medical details."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" id="feedbackConsent" name="public_consent" value="1" type="checkbox" required>
                        <label class="form-check-label small" for="feedbackConsent">I agree that this review may be published after moderation. My review will not be published without approval.</label>
                    </div>
                    <p class="form-text mb-0 mt-2">Feedback is stored without your phone number or email address.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-vax rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-vax rounded-pill px-4">Submit review</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<script>
    (() => {
        const chartCanvas = document.getElementById("appointmentsChart");
        if (chartCanvas) {
            if (window.Chart) {
                const labels = <?= json_encode($bookingStatusLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
                const values = <?= json_encode($bookingStatusValues, JSON_NUMERIC_CHECK) ?>;
                const chartDark = document.documentElement.dataset.theme === "dark";
                let chartLabels = labels;
                let chartValues = values;
                if (labels.length === 0) {
                    chartLabels = ["No appointments"];
                }
                if (values.length === 0) {
                    chartValues = [0];
                }
                let chartAnimation = {
                    duration: 700
                };
                if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                    chartAnimation = false;
                }
                let xTickColor = "#637989";
                let xGridColor = "rgba(99,121,137,.12)";
                let yTickColor = "#12334a";
                if (chartDark) {
                    xTickColor = "#a9bdc8";
                    xGridColor = "rgba(169,189,200,.12)";
                    yTickColor = "#e5f1f5";
                }
                new Chart(chartCanvas, {
                    type: "bar",
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: "Appointments",
                            data: chartValues,
                            backgroundColor: ["rgba(7,155,145,.78)", "rgba(8,124,190,.78)", "rgba(111,131,229,.76)", "rgba(234,157,73,.78)"],
                            borderRadius: 9,
                            maxBarThickness: 38
                        }]
                    },
                    options: {
                        indexAxis: "y",
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: chartAnimation,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: item => {
                                        let suffix = "s";
                                        if (item.parsed.x === 1) {
                                            suffix = "";
                                        }
                                        return `${item.parsed.x} appointment${suffix}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: xTickColor
                                },
                                grid: {
                                    color: xGridColor
                                }
                            },
                            y: {
                                ticks: {
                                    color: yTickColor
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                document.addEventListener("evax:themechange", event => {
                    const dark = event.detail.theme === "dark";
                    const chart = Chart.getChart(chartCanvas);
                    if (!chart) return;
                    let updatedXTickColor = "#637989";
                    let updatedXGridColor = "rgba(99,121,137,.12)";
                    let updatedYTickColor = "#12334a";
                    if (dark) {
                        updatedXTickColor = "#a9bdc8";
                        updatedXGridColor = "rgba(169,189,200,.12)";
                        updatedYTickColor = "#e5f1f5";
                    }
                    chart.options.scales.x.ticks.color = updatedXTickColor;
                    chart.options.scales.x.grid.color = updatedXGridColor;
                    chart.options.scales.y.ticks.color = updatedYTickColor;
                    chart.update("none");
                });
            } else {
                chartCanvas.insertAdjacentHTML("afterend", '<p class="small text-warning mt-2" role="status">Chart unavailable. Appointment totals remain listed below.</p>');
            }
        }

        const canvas = document.getElementById("vaccineCanvas");
        if (!canvas) return;
        const context = canvas.getContext("2d");
        if (!context) return;

        const labels = ["Birth", "6 wk", "10 wk", "14 wk", "9 mo", "15 mo"];
        const particles = Array.from({
            length: 18
        }, (_, index) => ({
            x: (index * 67 + 28) % 500,
            y: (index * 43 + 22) % 250,
            radius: 2 + (index % 3),
            speed: 0.12 + (index % 4) * 0.06,
            phase: index * 0.7
        }));
        let width = 0;
        let height = 0;
        const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        function resize() {
            const bounds = canvas.getBoundingClientRect();
            const ratio = Math.min(window.devicePixelRatio || 1, 2);
            width = bounds.width;
            height = 270;
            canvas.width = Math.round(width * ratio);
            canvas.height = Math.round(height * ratio);
            context.setTransform(ratio, 0, 0, ratio, 0, 0);
        }

        function draw(time) {
            context.clearRect(0, 0, width, height);
            const centerY = height * 0.51;
            const startX = 28;
            const endX = width - 28;
            const step = (endX - startX) / (labels.length - 1);
            let progress = (time % 9000) / 9000;
            if (reducedMotion) {
                progress = 1;
            }

            context.strokeStyle = "#d4e8e8";
            context.lineWidth = 5;
            context.lineCap = "round";
            context.beginPath();
            context.moveTo(startX, centerY);
            context.lineTo(endX, centerY);
            context.stroke();
            const journeyGradient = context.createLinearGradient(startX, centerY, endX, centerY);
            journeyGradient.addColorStop(0, "rgba(7,155,145,.7)");
            journeyGradient.addColorStop(1, "rgba(8,124,190,.25)");
            context.strokeStyle = "#12a99a";
            context.lineWidth = 4;
            context.beginPath();
            context.moveTo(startX, centerY);
            context.lineTo(endX, centerY);
            context.strokeStyle = journeyGradient;
            context.stroke();

            labels.forEach((label, index) => {
                const x = startX + step * index;
                let pulse = (Math.sin(time * 0.002 + index) + 1) * 2;
                if (reducedMotion) {
                    pulse = 0;
                }
                context.beginPath();
                context.arc(x, centerY, 8 + pulse, 0, Math.PI * 2);
                context.fillStyle = "#fff";
                context.fill();
                context.lineWidth = 3.5;
                context.strokeStyle = "#8bcfc8";
                if (index === 0) {
                    context.strokeStyle = "#079b91";
                }
                context.stroke();
                context.fillStyle = "#36566a";
                context.font = "600 12px Segoe UI, Arial, sans-serif";
                context.textAlign = "center";
                context.fillText(label, x, centerY + 36);
                context.beginPath();
                context.arc(x, centerY - 38, 2.5, 0, Math.PI * 2);
                context.fillStyle = "rgba(7,155,145,.35)";
                context.fill();
            });

            const travelerX = startX + (endX - startX) * progress;
            if (!reducedMotion) {
                const travelerGlow = context.createRadialGradient(travelerX, centerY, 1, travelerX, centerY, 20);
                travelerGlow.addColorStop(0, "rgba(7,155,145,.28)");
                travelerGlow.addColorStop(1, "rgba(7,155,145,0)");
                context.beginPath();
                context.arc(travelerX, centerY, 20, 0, Math.PI * 2);
                context.fillStyle = travelerGlow;
                context.fill();
            }
            context.beginPath();
            context.arc(travelerX, centerY, 5, 0, Math.PI * 2);
            context.fillStyle = "#087cbe";
            context.fill();
            context.lineWidth = 2;
            context.strokeStyle = "#fff";
            context.stroke();

            if (!reducedMotion) {
                particles.forEach(particle => {
                    particle.x = (particle.x + particle.speed) % Math.max(width, 1);
                    const y = 22 + ((particle.y + Math.sin(time * .001 + particle.phase) * 8) % 205);
                    context.beginPath();
                    context.arc(particle.x / 500 * width, y, particle.radius, 0, Math.PI * 2);
                    context.fillStyle = "rgba(7,155,145,.13)";
                    context.fill();
                });
                requestAnimationFrame(draw);
            }
        }

        resize();
        draw(0);
        window.addEventListener("resize", () => {
            resize();
            if (reducedMotion) draw(9000);
        }, {
            passive: true
        });
        if (reducedMotion) draw(9000);
    })();
</script>

<script type="module" src="virus-scene.js"></script>
<?php require __DIR__ . '/includes/site-footer.php'; ?>