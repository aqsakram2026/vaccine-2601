<?php
require __DIR__ . '/config.php';

$sitePage = 'awareness';
$pageTitle = 'Vaccine Awareness | E-Vaccine';
$pageDescription = 'A general age-based roadmap of vaccines listed in the E-Vaccine portal schedule.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Vaccine Awareness']
];

$vaccines = [];
$vaccineQuery = mysqli_query(
    $connect,
    "SELECT Vaccine_ID, Vaccine_Name, Dose, Age_Limit, description
     FROM vaccines
     WHERE Status = 'Active'
     ORDER BY Vaccine_ID"
);
$vaccineQueryAvailable = $vaccineQuery !== false;
if ($vaccineQueryAvailable) {
    while ($vaccine = mysqli_fetch_assoc($vaccineQuery)) {
        $vaccines[] = $vaccine;
    }
    mysqli_free_result($vaccineQuery);
} else {
    error_log('E-Vaccine awareness vaccine query failed: ' . mysqli_error($connect));
}

$milestones = [];
foreach ($vaccines as $vaccine) {
    $ageLabel = trim((string)$vaccine['Age_Limit']);
    $ageDays = null;
    if (preg_match('/^(?:at birth|birth)$/i', $ageLabel)) {
        $ageDays = 0;
    } elseif (preg_match('/^(\d+)(?:\s*-\s*\d+)?\s*(day|week|month|year)s?$/i', $ageLabel, $ageMatch)) {
        $ageAmount = (int)$ageMatch[1];
        $ageUnit = strtolower($ageMatch[2]);
        $ageDays = $ageAmount;
        if ($ageUnit === 'week') {
            $ageDays *= 7;
        } elseif ($ageUnit === 'month') {
            $ageDays *= 30;
        } elseif ($ageUnit === 'year') {
            $ageDays *= 365;
        }
    }
    if (!isset($milestones[$ageLabel])) {
        $milestones[$ageLabel] = ['sort' => $ageDays, 'vaccines' => []];
    }
    $milestones[$ageLabel]['vaccines'][] = $vaccine;
}
uasort($milestones, static function ($first, $second) {
    $firstAge = $first['sort'] ?? PHP_INT_MAX;
    $secondAge = $second['sort'] ?? PHP_INT_MAX;
    return $firstAge <=> $secondAge;
});

require __DIR__ . '/includes/site-header.php';
?>
<main>
    <section class="page-hero">
        <div class="container section-space">
            <span class="eyebrow mb-3" data-aos="fade-up"><i class="bi bi-shield-plus" aria-hidden="true"></i> Know what each dose does</span>
            <h1 class="display-title mb-3" data-aos="fade-up">Vaccine <span class="text-gradient">awareness.</span></h1>
            <p class="lead-copy mb-0" data-aos="fade-up" data-aos-delay="75">A general roadmap of the active vaccines in the portal schedule, organized by age so you can see what is listed at each milestone.</p>
        </div>
    </section>

    <section class="container section-space pt-4">
        <div class="surface-card p-4 p-lg-5 mb-4" data-aos="fade-up">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <h2 class="h4 fw-bold mb-2">Follow the age milestones</h2>
                    <p class="text-secondary mb-0">Start with the milestone that matches your child's age, then review the later milestones to see what the portal schedule lists next.</p>
                </div>
                <div class="col-lg-4">
                    <div class="rounded-4 p-3 bg-body-tertiary">
                        <div class="fw-semibold"><i class="bi bi-card-checklist me-2 text-success" aria-hidden="true"></i>Keep vaccine records handy</div>
                        <p class="small text-secondary mb-0 mt-1">Use your child's vaccination card to confirm which doses they have received and discuss upcoming doses with a health professional.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4" data-aos="fade-up">
            <div>
                <h2 class="h3 fw-bold mb-1">Vaccine schedule by age milestone</h2>
                <p class="text-secondary mb-0">Names, timing, doses, and descriptions are loaded from the active vaccine records.</p>
            </div>
            <span class="badge rounded-pill text-bg-light border"><?= count($vaccines) ?> active vaccine entries</span>
        </div>
        <?php if (!$vaccineQueryAvailable): ?>
            <div class="alert alert-warning" role="status">Vaccine schedule information is temporarily unavailable.</div>
        <?php elseif (!$vaccines): ?>
            <div class="alert alert-info" role="status">No active vaccines are currently listed.</div>
        <?php else: ?>
            <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="Vaccine schedule age milestones">
                <?php $milestoneIndex = 0; foreach ($milestones as $ageLabel => $milestone): $milestoneIndex++; ?>
                    <a class="btn btn-sm btn-outline-vax rounded-pill" href="#milestone-<?= $milestoneIndex ?>"><?= site_escape($ageLabel) ?></a>
                <?php endforeach; ?>
            </nav>
            <?php $milestoneIndex = 0; foreach ($milestones as $ageLabel => $milestone): $milestoneIndex++; ?>
                <section class="mb-5" id="milestone-<?= $milestoneIndex ?>" aria-labelledby="milestone-title-<?= $milestoneIndex ?>">
                    <h3 class="h4 fw-bold mb-3" id="milestone-title-<?= $milestoneIndex ?>"><?= site_escape($ageLabel) ?></h3>
                    <div class="row g-3">
                        <?php foreach ($milestone['vaccines'] as $vaccine): ?>
                            <div class="col-md-6 col-xl-4" data-aos="fade-up">
                                <article class="surface-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="icon-tile flex-shrink-0"><i class="bi bi-shield-plus" aria-hidden="true"></i></span>
                                        <div>
                                            <h4 class="h5 fw-bold mb-1"><?= site_escape($vaccine['Vaccine_Name']) ?></h4>
                                            <?php if ((int)$vaccine['Dose'] > 0): ?><span class="small text-secondary">Dose <?= (int)$vaccine['Dose'] ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                    <p class="text-secondary mb-0"><?= site_escape($vaccine['description'] ?: 'No description has been added yet.') ?></p>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="alert alert-info border-0 rounded-4 mt-5 mb-0" role="note" data-aos="fade-up">
            <h2 class="h6 fw-bold"><i class="bi bi-info-circle me-2" aria-hidden="true"></i>Important health information</h2>
            <p class="small mb-0">This page is a general roadmap, not a diagnosis or personal medical advice. Use your child's vaccination card to check their history and ask a qualified health professional about timing, missed doses, allergies, or possible side effects.</p>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
