<?php
session_start();

if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

$roles = ['Parent', 'Hospital', 'Other'];
$old = $_SESSION['contact_old'] ?? [
    'name' => '',
    'email' => '',
    'phone' => '',
    'role' => '',
    'address' => '',
    'message' => ''
];
unset($_SESSION['contact_old']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    foreach ($old as $key => $unused) {
        $old[$key] = '';
        if (isset($_POST[$key]) && is_string($_POST[$key])) {
            $old[$key] = trim($_POST[$key]);
        }
    }

    $error = '';
    $nameLength = strlen($old['name']);
    $addressLength = strlen($old['address']);
    $messageLength = strlen($old['message']);
    if (function_exists('mb_strlen')) {
        $nameLength = mb_strlen($old['name'], 'UTF-8');
        $addressLength = mb_strlen($old['address'], 'UTF-8');
        $messageLength = mb_strlen($old['message'], 'UTF-8');
    }
    if (
        !isset($_POST['csrf']) || !is_string($_POST['csrf'])
        || !hash_equals($_SESSION['contact_csrf'], $_POST['csrf'])
    ) {
        $error = 'Your form session expired. Please try again.';
    } elseif (!empty($_POST['website'])) {
        $_SESSION['contact_flash'] = [
            'variant' => 'success',
            'message' => 'Thank you. Your message has been received.'
        ];
        header('Location: contact.php#contact');
        exit;
    } elseif (
        $old['name'] === '' || $old['email'] === '' || $old['phone'] === ''
        || $old['address'] === '' || $old['message'] === ''
    ) {
        $error = 'Please complete all required fields.';
    } elseif (strlen($old['name']) > 240 || $nameLength < 2 || $nameLength > 60) {
        $error = 'Please enter a name between 2 and 60 characters.';
    } elseif (
        !filter_var($old['email'], FILTER_VALIDATE_EMAIL)
        || strlen($old['email']) > 100
    ) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($old['role'], $roles, true)) {
        $error = 'Please select a valid contact category.';
    } elseif (strlen($old['address']) > 600 || $addressLength > 150) {
        $error = 'Please keep the address within 150 characters.';
    } elseif ($messageLength < 5 || $messageLength > 1000) {
        $error = 'Please enter a message between 5 and 1000 characters.';
    }

    $phone = preg_replace('/\D+/', '', $old['phone']);
    if ($error === '' && (!is_string($phone) || strlen($phone) < 10 || strlen($phone) > 12)) {
        $error = 'Enter a phone number containing 10 to 12 digits.';
    }

    if ($error !== '') {
        $_SESSION['contact_old'] = $old;
        $_SESSION['contact_flash'] = ['variant' => 'danger', 'message' => $error];
        header('Location: contact.php#contact');
        exit;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    require __DIR__ . '/config.php';
    $connection = $connect;

    if (!mysqli_set_charset($connection, 'utf8mb4')) {
        error_log('E-Vaccine contact form could not set the database character set: ' . mysqli_error($connection));
        $_SESSION['contact_old'] = $old;
        $_SESSION['contact_flash'] = [
            'variant' => 'danger',
            'message' => 'Your message could not be saved right now. Please try again later.'
        ];
        mysqli_close($connection);
        header('Location: contact.php#contact');
        exit;
    }

    $statement = mysqli_prepare(
        $connection,
        'INSERT INTO contact_messages (name, email, phone, role, address, message, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())'
    );
    if (!$statement) {
        error_log('E-Vaccine contact form could not prepare its database insert: ' . mysqli_error($connection));
        $_SESSION['contact_old'] = $old;
        $_SESSION['contact_flash'] = [
            'variant' => 'danger',
            'message' => 'Your message could not be saved right now. Please import Database/vaccine_database_hamza.sql.'
        ];
        mysqli_close($connection);
        header('Location: contact.php#contact');
        exit;
    }

    mysqli_stmt_bind_param(
        $statement,
        'ssssss',
        $old['name'],
        $old['email'],
        $phone,
        $old['role'],
        $old['address'],
        $old['message']
    );
    if (!mysqli_stmt_execute($statement)) {
        error_log('E-Vaccine contact form insert failed: ' . mysqli_stmt_error($statement));
        $_SESSION['contact_old'] = $old;
        $_SESSION['contact_flash'] = [
            'variant' => 'danger',
            'message' => 'Your message could not be saved right now. Please try again later.'
        ];
    } else {
        $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['contact_flash'] = [
            'variant' => 'success',
            'message' => 'Thank you. Your message has been sent to the E-Vaccine team.'
        ];
        unset($_SESSION['contact_old']);
    }

    mysqli_stmt_close($statement);
    mysqli_close($connection);
    header('Location: contact.php#contact');
    exit;
}

$flash = $_SESSION['contact_flash'] ?? null;
unset($_SESSION['contact_flash']);
$toastMessage = '';
$toastVariant = 'danger';
$toastTitle = 'Could not send message';
if (is_array($flash)) {
    if (isset($flash['message'])) {
        $toastMessage = $flash['message'];
    }
    if (isset($flash['variant']) && $flash['variant'] === 'success') {
        $toastVariant = 'success';
        $toastTitle = 'Message received';
    }
}

$sitePage = 'contact';
$pageTitle = 'Contact | E-Vaccine';
$pageDescription = 'Contact E-Vaccine with questions about parent accounts, hospital applications and vaccination records.';
$breadcrumbs = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Contact']
];
require __DIR__ . '/includes/site-header.php';
?>
<style>
    #contact .page-hero {
        background:
            radial-gradient(ellipse at 12% 18%, rgba(84, 213, 196, .2), transparent 34%),
            radial-gradient(ellipse at 92% 78%, rgba(107, 177, 235, .2), transparent 38%),
            linear-gradient(135deg, #f8ffff, #eef7ff);
    }

    html[data-theme="dark"] #contact .page-hero {
        background:
            radial-gradient(ellipse at 12% 18%, rgba(33, 155, 145, .2), transparent 34%),
            radial-gradient(ellipse at 92% 78%, rgba(62, 124, 177, .18), transparent 38%),
            linear-gradient(135deg, #102536, #0d202f);
    }

    #contact .contact-form-card,
    #contact .surface-card {
        border: 1px solid rgba(255, 255, 255, .72) !important;
        background: rgba(255, 255, 255, .62) !important;
        box-shadow: 0 22px 60px rgba(18, 51, 74, .1);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }

    html[data-theme="dark"] #contact .contact-form-card,
    html[data-theme="dark"] #contact .surface-card {
        border-color: rgba(153, 196, 205, .2) !important;
        background: rgba(20, 40, 54, .72) !important;
        box-shadow: 0 22px 60px rgba(0, 0, 0, .2);
    }

    #contact .input-group-text,
    #contact .form-control,
    #contact .form-select {
        border-color: rgba(125, 162, 177, .3);
        background-color: rgba(255, 255, 255, .52);
    }

    html[data-theme="dark"] #contact .input-group-text,
    html[data-theme="dark"] #contact .form-control,
    html[data-theme="dark"] #contact .form-select {
        border-color: rgba(153, 196, 205, .24);
        background-color: rgba(8, 25, 37, .48);
    }

    #contact .input-group-text {
        color: var(--vax-teal);
    }

    #contact .contact-form-card .form-control::placeholder {
        color: var(--vax-muted);
        opacity: .8;
    }

    #contact .icon-tile {
        transition: transform .3s ease, box-shadow .3s ease;
    }

    @media (hover: hover) and (prefers-reduced-motion: no-preference) {
        #contact .surface-card:hover .icon-tile {
            transform: translateY(-3px) rotate(-5deg);
            box-shadow: 0 8px 20px rgba(7, 143, 135, .18);
        }

        #contact .contact-form-card .btn-vax:hover i {
            animation: contact-arrow-nudge .55s ease-in-out;
        }
    }

    @media (prefers-reduced-motion: no-preference) {
        #contact .contact-form-card {
            animation: contact-card-enter .8s cubic-bezier(.2, .75, .25, 1) both;
        }

        .contact-vcard .ring {
            animation: contact-ring-breathe 3.2s ease-in-out infinite;
        }
    }

    @keyframes contact-card-enter {
        from { opacity: 0; transform: translateY(22px) scale(.985); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes contact-ring-breathe {
        0%, 100% { box-shadow: 0 0 0 0 rgba(7, 143, 135, 0); }
        50% { box-shadow: 0 0 0 8px rgba(7, 143, 135, .08); }
    }

    @keyframes contact-arrow-nudge {
        50% { transform: translateX(4px); }
    }

    @media (prefers-reduced-motion: reduce) {
        #contact .icon-tile,
        #contact .contact-form-card .btn-vax i {
            transition: none;
            animation: none;
        }

        .contact-vcard .ring {
            animation: none;
        }
    }

    .contact-vcard {
        max-width: 520px;
        margin-inline: auto;
        padding: 1.5rem;
        border: 1px solid var(--vax-line);
        border-radius: 22px;
        background: var(--vax-surface);
        box-shadow: var(--vax-shadow);
    }

    .contact-vcard .vcard-top {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .contact-vcard .ring {
        --p: 75;
        display: flex;
        width: 78px;
        height: 78px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: radial-gradient(closest-side, var(--vax-surface) 78%, transparent 80% 99%), conic-gradient(var(--vax-teal), var(--p), #dcebed 0);
        color: var(--vax-teal);
        font-weight: 800;
    }

    .contact-vcard .vcard-top h3 {
        margin: 0 0 .2rem;
        font-size: 1.1rem;
        font-weight: 750;
    }

    .contact-vcard .vcard-top small {
        color: var(--vax-muted);
    }

    .contact-vcard .vlist {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .contact-vcard .vlist li {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .8rem 0;
        border-top: 1px solid var(--vax-line);
    }

    .contact-vcard .tick {
        display: flex;
        width: 28px;
        height: 28px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #087c68;
        background: #e4f7f1;
    }

    .contact-vcard .tick.due {
        color: #946200;
        background: #fff4d8;
    }

    .contact-vcard .vlist li>div {
        display: flex;
        flex: 1;
        flex-direction: column;
    }

    .contact-vcard .vlist .s {
        color: var(--vax-muted);
        font-size: .84rem;
    }

    .contact-vcard .badge {
        margin-left: auto;
        padding: .35rem .65rem;
        border-radius: 999px;
        color: #087c68;
        background: #e4f7f1;
        font-size: .75rem;
    }

    .contact-vcard .badge.due {
        color: #946200;
        background: #fff4d8;
    }
</style>
<main id="contact">
    <section class="page-hero">
        <div class="container section-space">
            <div class="row g-5">
                <div class="col-lg-5" data-aos="fade-right">
                    <span class="eyebrow mb-3"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> We are here to help</span>
                    <h1 class="display-title mb-4">Let's talk about <span class="text-gradient">better care.</span></h1>
                    <p class="lead-copy">Send a question about parent registration, hospital access or using the platform. Please do not include sensitive medical information.</p>
                    <div class="d-flex flex-column gap-3 mt-4">
                        <div class="surface-card p-3 d-flex gap-3 align-items-center" data-aos="fade-up" data-aos-delay="80">
                            <span class="icon-tile flex-shrink-0"><i class="bi bi-person-heart" aria-hidden="true"></i></span>
                            <div>
                                <div class="fw-bold">Parents</div><a class="text-decoration-none" href="parents/parent-login.php">Open the parent portal</a>
                            </div>
                        </div>
                        <div class="surface-card p-3 d-flex gap-3 align-items-center" data-aos="fade-up" data-aos-delay="150">
                            <span class="icon-tile flex-shrink-0"><i class="bi bi-hospital" aria-hidden="true"></i></span>
                            <div>
                                <div class="fw-bold">Hospitals</div><a class="text-decoration-none" href="hospital/hospital_login.php">Open the hospital portal</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-left">
                    <div class="surface-card contact-form-card p-4 p-md-5">
                        <span class="eyebrow mb-3"><i class="bi bi-chat-square-text-fill" aria-hidden="true"></i> Contact form</span>
                        
                        <h2 class="h3 fw-bold">Send us a message</h2>
                        <p class="text-secondary mb-4">Fields marked <span class="text-danger">*</span> are required.</p>
                        
                        <form action="contact.php#contact" method="post">
                            <input type="hidden" name="csrf" value="<?= site_escape($_SESSION['contact_csrf']) ?>">
                            
                            <div class="position-absolute" style="left:-10000px" aria-hidden="true">
                                <label for="contactWebsite">Leave this field empty</label>
                                <input id="contactWebsite" type="text" name="website" tabindex="-1" autocomplete="off">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactName">Full name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                                        <input class="form-control" id="contactName" name="name" maxlength="60" autocomplete="name" required value="<?= site_escape($old['name']) ?>" placeholder="Enter your full name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactEmail">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                        <input class="form-control" id="contactEmail" name="email" type="email" maxlength="100" autocomplete="email" required value="<?= site_escape($old['email']) ?>" placeholder="you@example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactPhone">Phone <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                                        <input class="form-control" id="contactPhone" name="phone" type="tel" inputmode="tel" maxlength="24" autocomplete="tel" required value="<?= site_escape($old['phone']) ?>" placeholder="e.g. 03001234567">
                                    </div>
                                    <div class="form-text">Use 10 to 12 digits; spaces and + are okay.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactRole">I am a <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
                                        <select class="form-select" id="contactRole" name="role" required>
                                            <option value="" disabled <?php if ($old['role'] === '') { echo 'selected'; } ?>>Select your role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= site_escape($role) ?>" <?php if ($old['role'] === $role) {
                                                                                            echo 'selected';
                                                                                        } ?>><?= site_escape($role) ?></option>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="contactAddress">Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                                        <input class="form-control" id="contactAddress" name="address" maxlength="150" autocomplete="street-address" required value="<?= site_escape($old['address']) ?>" placeholder="Enter your street address and city">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="contactMessage">Message <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text align-items-start pt-3"><i class="bi bi-chat-left-text" aria-hidden="true"></i></span>
                                        <textarea class="form-control" id="contactMessage" name="message" rows="5" maxlength="1000" required placeholder="How can we help? Please do not include sensitive medical information."><?= site_escape($old['message']) ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12 d-flex flex-wrap align-items-center gap-3">
                                    <button class="btn btn-vax rounded-pill px-4" type="submit" name="send_message" value="1">Send message <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></button>
                                    <span class="small text-secondary">Your details are used to reply to this enquiry.</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<section class="container section-space pt-0" aria-labelledby="sample-vaccine-title">
    <div class="row align-items-center g-4">
        <div class="col-lg-5" data-aos="fade-right">
            <span class="eyebrow mb-3"><i class="bi bi-card-checklist" aria-hidden="true"></i> Patient portal preview</span>
            <h2 class="h2 fw-bold" id="sample-vaccine-title">A clear view of vaccination progress</h2>
            <p class="text-secondary">This sample card shows how a patient can review completed doses and see what is coming next.</p>
        </div>
        <div class="col-lg-7" data-aos="fade-left">
            <div class="vcard contact-vcard" aria-label="Sample vaccination card">
                <div class="vcard-top">
                    <div class="ring" id="ring" role="img" aria-label="3 of 4 vaccines complete" style="--p: 75;"><b id="ringTxt">75%</b></div>
                    <div>
                        <h3>Ayaan, 9 months</h3>
                        <small>Sample vaccination card</small>
                    </div>
                </div>
                <ul class="vlist">
                    <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg></span>
                        <div><strong>BCG</strong><span class="s">Given at birth</span></div><span class="badge">Vaccinated</span>
                    </li>
                    <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg></span>
                        <div><strong>Polio (OPV)</strong><span class="s">6 weeks</span></div><span class="badge">Vaccinated</span>
                    </li>
                    <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg></span>
                        <div><strong>Pentavalent</strong><span class="s">10 weeks</span></div><span class="badge">Vaccinated</span>
                    </li>
                    <li><span class="tick due"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 7v5l3 2"></path>
                            </svg></span>
                        <div><strong>Measles</strong><span class="s">Due next week</span></div><span class="badge due">Upcoming</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="container section-space pt-0" aria-labelledby="hospital-map-title">
    <div class="row g-4 align-items-center">
        <div class="col-lg-4" data-aos="fade-up">
            <span class="eyebrow mb-3"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Karachi, Pakistan</span>
            <h2 class="h2 fw-bold" id="hospital-map-title">Jinnah Postgraduate Medical Centre</h2>
            <p class="text-secondary">Use the map to find JPMC. This location reference does not imply affiliation with or endorsement by E-Vaccine.</p>
            <a class="btn btn-outline-vax rounded-pill px-4" href="https://www.google.com/maps/search/?api=1&amp;query=Jinnah+Postgraduate+Medical+Centre%2C+Karachi" target="_blank" rel="noopener noreferrer">
                Open in Google Maps <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
            </a>
        </div>
        <div class="col-lg-8" data-aos="fade-up">
            <iframe
                class="map-frame"
                title="Map showing Jinnah Postgraduate Medical Centre in Karachi"
                src="https://maps.google.com/maps?q=Jinnah%20Postgraduate%20Medical%20Centre%2C%20Karachi&amp;z=14&amp;output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/site-footer.php'; ?>