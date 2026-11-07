<?php
session_start();
include 'connection.php';

// Contact details
$CONTACT = [
    'phone'   => '+00 000 0000000',
    'email'   => 'support@vaxcare.com',
    'address' => 'Your office address, City',
    'hours'   => 'Open 24 hours, 7 days a week',
];

// Helper function for safe HTML output
function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$swal     = null;     // SweetAlert message
$errField = '';       // Field with error
$old      = ['name' => '', 'email' => '', 'phone' => '', 'role' => 'Parent', 'address' => '', 'message' => ''];
$roles    = ['Parent', 'Hospital', 'Other'];

// Check for session flash messages
if (!empty($_SESSION['swal'])) {
    $swal = $_SESSION['swal'];
    unset($_SESSION['swal']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {

    // 1. Basic trimming and getting input data
    foreach ($old as $k => $v) {
        $old[$k] = isset($_POST[$k]) ? trim($_POST[$k]) : '';
    }

    // Honeypot check (spam bots trap)
    if (!empty($_POST['website'])) {
        $_SESSION['swal'] = ['success', 'Success', 'Thank you! Your message has been sent.'];
        header('Location: index.php#contact');
        exit;
    }

    $error = '';

    // 2. CSRF Token verification
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
        $error = 'Your session expired. Please refresh the page and try again.';
    } 
    // 3. Empty fields check
    elseif ($old['name'] === '' || $old['email'] === '' || $old['phone'] === '' || $old['address'] === '' || $old['message'] === '') {
        $error = 'Please fill all fields!';
    } 
    // 4. Name length check (Simple)
    elseif (strlen($old['name']) < 2 || strlen($old['name']) > 60) {
        $error = 'Name must be between 2 and 60 characters!';
        $errField = 'name';
    } 
    // 5. Email format check
    elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
        $errField = 'email';
    } 
    // 6. Phone number check (Digits only, e.g., 10 to 15 digits)
    elseif (!ctype_digit($old['phone']) || strlen($old['phone']) < 10 || strlen($old['phone']) > 15) {
        $error = 'Please enter a valid phone number (10 to 15 digits)!';
        $errField = 'phone';
    } 
    // 7. Role check
    elseif (!in_array($old['role'], $roles, true)) {
        $error = 'Please choose a valid role!';
        $errField = 'role';
    } 
    // 8. Message length check
    elseif (strlen($old['message']) < 5) {
        $error = 'Message is too short!';
        $errField = 'message';
    }

    // 9. Save into Database using standard MySQLi
    if ($error === '') {
        if (!isset($connect) || !$connect) {
            $error = 'Database connection error. Please try again later.';
        } else {
            // Escape values safely for MySQLi
            $n = mysqli_real_escape_string($connect, $old['name']);
            $e = mysqli_real_escape_string($connect, $old['email']);
            $p = mysqli_real_escape_string($connect, $old['phone']);
            $r = mysqli_real_escape_string($connect, $old['role']);
            $a = mysqli_real_escape_string($connect, $old['address']);
            $m = mysqli_real_escape_string($connect, $old['message']);

            $query = "INSERT INTO contact_messages (name, email, phone, role, address, message, created_at) 
                      VALUES ('$n', '$e', '$p', '$r', '$a', '$m', NOW())";
            
            $run = mysqli_query($connect, $query);

            if ($run) {
                $_SESSION['swal'] = ['success', 'Success', 'Thank you! Your message has been sent.'];
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                header('Location: index.php#contact');
                exit;
            } else {
                $error = 'Failed to send your message. Please try again later.';
            }
        }
    }

    if ($error !== '') {
        $swal = ['error', 'Error', $error];
    }
}

$telLink = 'tel:' . preg_replace('/[^0-9+]/', '', $CONTACT['phone']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vaccination Management System</title>
    <meta name="description" content="E-vaccination system for infants and children: register, book a hospital, track vaccination dates and reports.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0f2a47;
            --navy-deep: #0a1d33;
            --teal: #0f9d94;
            --teal-dark: #0b7d76;
            --mint: #e4f5f3;
            --mint-2: #f0faf9;
            --sky: #f4f9fb;
            --ink: #16293c;
            --muted: #5a6d7f;
            --line: #d8e5ea;
            --amber: #f0a73a;
            --danger: #c0392b;
            --white: #fff;
            --radius: 18px;
            --shadow: 0 18px 50px -20px rgba(15, 42, 71, .35);
            --head: "Bricolage Grotesque", system-ui, sans-serif;
            --body: "Figtree", system-ui, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 96px
        }

        body {
            font-family: var(--body);
            color: var(--ink);
            background: var(--white);
            line-height: 1.65;
            font-size: 17px;
            -webkit-font-smoothing: antialiased
        }

        img,
        svg {
            max-width: 100%;
            display: block
        }

        a {
            color: inherit;
            text-decoration: none
        }

        ul {
            list-style: none
        }

        :focus-visible {
            outline: 3px solid var(--amber);
            outline-offset: 3px;
            border-radius: 6px
        }

        h1,
        h2,
        h3 {
            font-family: var(--head);
            line-height: 1.12;
            color: var(--navy);
            letter-spacing: -.02em
        }

        h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800
        }

        h3 {
            font-size: 1.25rem;
            font-weight: 700
        }

        .wrap {
            width: min(1180px, 92%);
            margin-inline: auto
        }

        section {
            padding: 96px 0
        }

        .lead {
            color: var(--muted);
            max-width: 60ch;
            font-size: 1.1rem;
            margin-top: 16px
        }

        .center {
            text-align: center
        }

        .center .lead {
            margin-inline: auto
        }

        /* ---------- Buttons ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid transparent;
            cursor: pointer;
            font-family: inherit;
            transition: transform .25s ease, background .25s ease, box-shadow .25s ease, color .25s ease, border-color .25s ease
        }

        .btn-primary {
            background: var(--teal);
            color: #fff;
            box-shadow: 0 10px 24px -10px rgba(15, 157, 148, .8)
        }

        .btn-primary:hover {
            background: var(--teal-dark);
            transform: translateY(-3px);
            box-shadow: 0 16px 28px -12px rgba(15, 157, 148, .9)
        }

        .btn-ghost {
            border-color: rgba(255, 255, 255, .45);
            color: #fff
        }

        .btn-ghost:hover {
            background: #fff;
            color: var(--navy);
            transform: translateY(-3px)
        }

        /* ---------- Navbar ---------- */
        header {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 50;
            transition: background .35s ease, box-shadow .35s ease, padding .35s ease;
            padding: 14px 0
        }

        header.scrolled {
            background: rgba(10, 29, 51, .96);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 30px -12px rgba(0, 0, 0, .5);
            padding: 8px 0
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            font-family: var(--head);
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -.02em
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--teal);
            display: grid;
            place-items: center;
            transition: transform .4s ease
        }

        .brand:hover .brand-mark {
            transform: rotate(-12deg) scale(1.06)
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .nav-links a.link {
            color: rgba(255, 255, 255, .82);
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 500;
            position: relative;
            transition: color .25s ease, background .25s ease
        }

        .nav-links a.link:hover,
        .nav-links a.link.active {
            color: #fff;
            background: rgba(255, 255, 255, .1)
        }

        .nav-cta {
            display: flex;
            gap: 10px;
            margin-left: 14px
        }

        .nav-cta .btn {
            padding: 11px 22px;
            font-size: .95rem
        }

        .burger {
            display: none;
            background: none;
            border: 0;
            cursor: pointer;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            color: #fff
        }

        .burger span {
            display: block;
            width: 24px;
            height: 2px;
            background: currentColor;
            margin: 5px auto;
            transition: transform .3s ease, opacity .3s ease
        }

        .burger[aria-expanded="true"] span:nth-child(1) {
            transform: translateY(7px) rotate(45deg)
        }

        .burger[aria-expanded="true"] span:nth-child(2) {
            opacity: 0
        }

        .burger[aria-expanded="true"] span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg)
        }

        /* ---------- Hero ---------- */
        .hero {
            background: radial-gradient(900px 500px at 85% 10%, rgba(15, 157, 148, .35), transparent 60%), linear-gradient(160deg, var(--navy-deep), var(--navy) 70%);
            color: #fff;
            padding: 170px 0 110px;
            position: relative;
            overflow: hidden
        }

        .hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 70px;
            background: var(--white);
            clip-path: ellipse(60% 100% at 50% 100%)
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 56px;
            align-items: center
        }

        .hero h1 {
            color: #fff;
            font-size: clamp(2.6rem, 5.6vw, 4.6rem);
            font-weight: 800
        }

        .hero p {
            color: rgba(255, 255, 255, .8);
            font-size: 1.18rem;
            max-width: 52ch;
            margin: 22px 0 34px
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px
        }

        .hero-points {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 26px;
            margin-top: 38px;
            color: rgba(255, 255, 255, .75);
            font-size: .98rem
        }

        .hero-points li {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .hero-points svg {
            color: var(--teal);
            flex: none
        }

        .vcard {
            background: #fff;
            color: var(--ink);
            border-radius: 26px;
            padding: 28px;
            box-shadow: 0 40px 80px -30px rgba(0, 0, 0, .6);
            position: relative;
            animation: rise .9s cubic-bezier(.2, .8, .2, 1) both
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(40px) rotate(1.5deg)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .vcard-top {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 1px dashed var(--line)
        }

        .ring {
            --p: 0;
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: conic-gradient(var(--teal) calc(var(--p)*1%), var(--mint) 0);
            display: grid;
            place-items: center;
            flex: none
        }

        .ring b {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            font-family: var(--head);
            font-size: 1.05rem;
            color: var(--navy)
        }

        .vcard-top h3 {
            font-size: 1.15rem
        }

        .vcard-top small {
            color: var(--muted)
        }

        .vlist {
            margin-top: 14px
        }

        .vlist li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 0;
            border-bottom: 1px solid var(--sky);
            opacity: 0;
            animation: fadeIn .6s ease forwards
        }

        .vlist li:nth-child(1) {
            animation-delay: .7s
        }

        .vlist li:nth-child(2) {
            animation-delay: 1s
        }

        .vlist li:nth-child(3) {
            animation-delay: 1.3s
        }

        .vlist li:nth-child(4) {
            animation-delay: 1.6s
        }

        @keyframes fadeIn {
            to {
                opacity: 1
            }
        }

        .tick {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            flex: none;
            background: var(--mint);
            color: var(--teal-dark)
        }

        .tick.due {
            background: #fff3dc;
            color: #b97a10;
            animation: pulse 2s ease-in-out infinite
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(240, 167, 58, .5)
            }

            50% {
                box-shadow: 0 0 0 10px rgba(240, 167, 58, 0)
            }
        }

        .vlist strong {
            display: block;
            font-size: .98rem;
            line-height: 1.3
        }

        .vlist span.s {
            font-size: .85rem;
            color: var(--muted)
        }

        .badge {
            margin-left: auto;
            font-size: .78rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            background: var(--mint);
            color: var(--teal-dark);
            white-space: nowrap
        }

        .badge.due {
            background: #fff3dc;
            color: #b97a10
        }

        .vcard-note {
            position: absolute;
            right: -18px;
            bottom: -22px;
            background: var(--teal);
            color: #fff;
            border-radius: 16px;
            padding: 12px 18px;
            font-size: .9rem;
            font-weight: 600;
            box-shadow: 0 16px 30px -12px rgba(0, 0, 0, .5);
            animation: float 4s ease-in-out infinite 2s
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-8px)
            }
        }

        /* ---------- About ---------- */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center
        }

        .compare {
            display: grid;
            gap: 18px
        }

        .panel {
            border-radius: var(--radius);
            padding: 30px;
            transition: transform .3s ease, box-shadow .3s ease
        }

        .panel:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow)
        }

        .panel.before {
            background: var(--sky);
            border: 1px solid var(--line)
        }

        .panel.after {
            background: var(--navy);
            color: #fff
        }

        .panel.after h3 {
            color: #fff
        }

        .panel p {
            margin-top: 10px;
            color: var(--muted)
        }

        .panel.after p {
            color: rgba(255, 255, 255, .78)
        }

        .about-text p+p {
            margin-top: 16px
        }

        .about-text p {
            color: var(--muted)
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 34px
        }

        .stat {
            border-left: 3px solid var(--teal);
            padding-left: 14px
        }

        .stat b {
            display: block;
            font-family: var(--head);
            font-size: 1.6rem;
            color: var(--navy)
        }

        .stat span {
            font-size: .92rem;
            color: var(--muted)
        }

        /* ---------- Modules ---------- */
        .modules {
            background: var(--sky)
        }

        .tabs {
            display: inline-flex;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px;
            margin: 32px 0 36px;
            gap: 4px
        }

        .tab {
            border: 0;
            background: none;
            font: inherit;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 999px;
            cursor: pointer;
            color: var(--muted);
            transition: background .3s ease, color .3s ease
        }

        .tab:hover {
            color: var(--navy)
        }

        .tab[aria-selected="true"] {
            background: var(--navy);
            color: #fff
        }

        .tabpanel {
            display: none
        }

        .tabpanel.on {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 20px;
            animation: swap .5s ease
        }

        @keyframes swap {
            from {
                opacity: 0;
                transform: translateY(14px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .mod {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 26px;
            transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease
        }

        .mod:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow);
            border-color: var(--teal)
        }

        .mod .ic {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--mint);
            color: var(--teal-dark);
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            transition: background .3s ease, color .3s ease
        }

        .mod:hover .ic {
            background: var(--teal);
            color: #fff
        }

        .mod p {
            color: var(--muted);
            margin-top: 8px;
            font-size: .98rem
        }

        /* ---------- Flow ---------- */
        .flow {
            margin-top: 56px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            position: relative;
            counter-reset: s
        }

        .flow::before {
            content: "";
            position: absolute;
            top: 27px;
            left: 8%;
            right: 8%;
            height: 2px;
            background: repeating-linear-gradient(90deg, var(--teal) 0 8px, transparent 8px 16px)
        }

        .step {
            text-align: center;
            position: relative;
            counter-increment: s
        }

        .step i {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            background: var(--navy);
            color: #fff;
            font-style: normal;
            font-family: var(--head);
            font-weight: 800;
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
            transition: transform .3s ease, background .3s ease
        }

        .step i::before {
            content: counter(s)
        }

        .step:hover i {
            transform: scale(1.15);
            background: var(--teal)
        }

        .step h3 {
            font-size: 1.05rem
        }

        .step p {
            color: var(--muted);
            font-size: .93rem;
            margin-top: 6px
        }

        /* ---------- FAQ ---------- */
        .faq {
            background: #fff
        }

        .faq-list {
            max-width: 820px;
            margin: 48px auto 0;
            display: grid;
            gap: 14px
        }

        details {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            transition: border-color .3s ease, box-shadow .3s ease
        }

        details:hover {
            border-color: var(--teal)
        }

        details[open] {
            border-color: var(--teal);
            box-shadow: var(--shadow);
            background: var(--mint-2)
        }

        summary {
            list-style: none;
            cursor: pointer;
            padding: 22px 26px;
            font-family: var(--head);
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--navy);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px
        }

        summary::-webkit-details-marker {
            display: none
        }

        summary::after {
            content: "+";
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--mint);
            color: var(--teal-dark);
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            flex: none;
            transition: transform .3s ease, background .3s ease, color .3s ease
        }

        details[open] summary::after {
            transform: rotate(45deg);
            background: var(--teal);
            color: #fff
        }

        details p {
            padding: 0 26px 24px;
            color: var(--muted);
            animation: swap .4s ease
        }

        /* ---------- Contact (light section, one card) ---------- */
        .contact {
            background: radial-gradient(700px 360px at 10% 0%, rgba(15, 157, 148, .12), transparent 70%), radial-gradient(600px 320px at 95% 100%, rgba(240, 167, 58, .12), transparent 70%), var(--sky)
        }

        .contact-card {
            margin-top: 52px;
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 30px 70px -30px rgba(15, 42, 71, .45)
        }

        .contact-info {
            background: linear-gradient(165deg, var(--navy), var(--navy-deep));
            color: #fff;
            padding: 46px 40px;
            position: relative;
            overflow: hidden
        }

        .contact-info::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(15, 157, 148, .22);
            right: -110px;
            bottom: -110px
        }

        .contact-info::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .1);
            right: 30px;
            bottom: 40px
        }

        .contact-info h3 {
            color: #fff;
            font-size: 1.7rem
        }

        .contact-info>p {
            color: rgba(255, 255, 255, .72);
            margin-top: 10px
        }

        .open-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: .92rem;
            font-weight: 600
        }

        .open-badge i {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #3ddc97;
            animation: live 2s infinite
        }

        @keyframes live {
            0% {
                box-shadow: 0 0 0 0 rgba(61, 220, 151, .6)
            }

            70%,
            100% {
                box-shadow: 0 0 0 12px rgba(61, 220, 151, 0)
            }
        }

        .info {
            display: grid;
            gap: 14px;
            margin-top: 30px;
            position: relative;
            z-index: 1
        }

        .info li {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            transition: background .3s ease, transform .3s ease, border-color .3s ease
        }

        .info li:hover {
            background: rgba(255, 255, 255, .12);
            transform: translateX(6px);
            border-color: var(--teal)
        }

        .info .ic {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--teal);
            display: grid;
            place-items: center;
            color: #fff;
            flex: none
        }

        .info small {
            display: block;
            color: rgba(255, 255, 255, .6);
            font-size: .82rem;
            line-height: 1.3
        }

        .info span,
        .info a {
            font-weight: 600;
            word-break: break-word
        }

        .info a:hover {
            color: #7be0d8
        }

        .contact-form {
            padding: 46px 46px 42px
        }

        .contact-form h3 {
            font-size: 1.7rem
        }

        .contact-form>p.sub {
            color: var(--muted);
            margin: 8px 0 26px
        }

        .field {
            margin-bottom: 18px
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        label {
            display: block;
            font-weight: 600;
            font-size: .92rem;
            margin-bottom: 6px;
            color: var(--navy)
        }

        label small {
            font-weight: 400;
            color: var(--muted)
        }

        input,
        select,
        textarea {
            width: 100%;
            font: inherit;
            padding: 13px 16px;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            background: var(--sky);
            color: var(--ink);
            transition: border-color .25s ease, background .25s ease, box-shadow .25s ease
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--teal);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(15, 157, 148, .15)
        }

        textarea {
            min-height: 130px;
            resize: vertical
        }

        .has-error input,
        .has-error select,
        .has-error textarea {
            border-color: var(--danger);
            background: #fdf3f1
        }

        .hp {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden
        }

        .form-foot {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap
        }

        .form-foot small {
            color: var(--muted)
        }

        /* ---------- Footer ---------- */
        footer {
            background: var(--navy-deep);
            color: rgba(255, 255, 255, .72);
            padding: 80px 0 0
        }

        .foot-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 44px;
            padding-bottom: 56px
        }

        footer h4 {
            font-family: var(--head);
            color: #fff;
            font-size: 1.05rem;
            margin-bottom: 18px
        }

        footer li {
            margin-bottom: 10px
        }

        footer a {
            transition: color .25s ease, padding .25s ease
        }

        footer li a:hover {
            color: var(--teal);
            padding-left: 6px
        }

        footer .brand {
            margin-bottom: 16px
        }

        footer p {
            font-size: .96rem;
            max-width: 36ch
        }

        .foot-cta {
            margin-top: 20px
        }

        .foot-bottom {
            border-top: 1px solid rgba(255, 255, 255, .1);
            padding: 24px 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: .9rem
        }

        /* ---------- Reveal ---------- */
        .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .8s ease, transform .8s cubic-bezier(.2, .8, .2, 1)
        }

        .reveal.in {
            opacity: 1;
            transform: none
        }

        /* ---------- Responsive ---------- */
        @media (max-width:1020px) {

            .hero-grid,
            .about-grid,
            .contact-card {
                grid-template-columns: 1fr
            }

            .foot-grid {
                grid-template-columns: 1fr 1fr
            }

            .flow {
                grid-template-columns: 1fr;
                max-width: 420px;
                margin-inline: auto
            }

            .flow::before {
                display: none
            }

            .step {
                display: grid;
                grid-template-columns: 56px 1fr;
                gap: 0 18px;
                text-align: left
            }

            .step i {
                margin: 0;
                grid-row: span 2
            }

            .vcard-note {
                right: 10px
            }
        }

        @media (max-width:920px) {
            .burger {
                display: block
            }

            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--navy-deep);
                flex-direction: column;
                align-items: stretch;
                padding: 18px 4%;
                gap: 4px;
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                transition: max-height .4s ease, opacity .3s ease, padding .4s ease;
                padding-block: 0
            }

            .nav-links.open {
                max-height: 560px;
                opacity: 1;
                padding-block: 18px
            }

            .nav-cta {
                margin: 10px 0 0;
                flex-direction: column
            }
        }

        @media (max-width:600px) {
            section {
                padding: 70px 0
            }

            .row,
            .stat-row {
                grid-template-columns: 1fr
            }

            .foot-grid {
                grid-template-columns: 1fr
            }

            .tabs {
                display: flex;
                width: 100%
            }

            .tab {
                flex: 1;
                padding: 12px 8px
            }

            .contact-info,
            .contact-form {
                padding: 30px 22px
            }
        }

        @media (prefers-reduced-motion:reduce) {

            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important
            }

            .reveal,
            .vlist li {
                opacity: 1;
                transform: none
            }
        }
    </style>
</head>

<body>

    <!-- ================= NAVBAR ================= -->
    <header id="top">
        <div class="wrap nav">
            <a href="#top" class="brand" aria-label="Vaccination Management System home">
                <span class="brand-mark">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 2 4 4" />
                        <path d="m17 7 3-3" />
                        <path d="M19 9 8.7 19.3a1 1 0 0 1-1.4 0L4.7 16.7a1 1 0 0 1 0-1.4L15 5" />
                        <path d="m9 11 4 4" />
                        <path d="m5 19-3 3" />
                        <path d="m14 4 6 6" />
                    </svg>
                </span>
                VaxCare
            </a>
            <button class="burger" aria-label="Open menu" aria-expanded="false" aria-controls="menu"><span></span><span></span><span></span></button>
            <nav class="nav-links" id="menu">
                <a class="link" href="#top">Home</a>
                <a class="link" href="#about">About</a>
                <a class="link" href="#modules">Modules</a>
                <a class="link" href="#how">How it works</a>
                <a class="link" href="#faq">FAQ</a>
                <a class="link" href="#contact">Contact</a>
                <!-- <a class="link" href="#hospital">Hospital</a>
                <a class="link" href="#admin">Admin</a>
                <a class="link" href="#parent">Parent</a> -->
                <div class="nav-cta">
                    <a class="btn btn-ghost" href="login.php">Login</a>
                    <a class="btn btn-primary" href="register.php">Register</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- ================= HERO ================= -->
        <section class="hero">
            <div class="wrap hero-grid">
                <div>
                    <h1>No child should miss a vaccine.</h1>
                    <p>Register your child once and we keep track for you. See upcoming vaccination dates, book a hospital, and download reports of every dose taken.</p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="register.php">Register as parent</a>
                        <a class="btn btn-ghost" href="#how">See how it works</a>
                    </div>
                    <ul class="hero-points">
                        <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>Date reminders on your dashboard</li>
                        <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>Book a hospital online</li>
                        <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>Complete vaccination reports</li>
                    </ul>
                </div>

                <div style="position:relative">
                    <div class="vcard" aria-label="Sample vaccination card">
                        <div class="vcard-top">
                            <div class="ring" id="ring" role="img" aria-label="3 of 4 vaccines complete"><b id="ringTxt">0%</b></div>
                            <div>
                                <h3>Ayaan, 9 months</h3>
                                <small>Sample vaccination card</small>
                            </div>
                        </div>
                        <ul class="vlist">
                            <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg></span>
                                <div><strong>BCG</strong><span class="s">Given at birth</span></div><span class="badge">Vaccinated</span>
                            </li>
                            <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg></span>
                                <div><strong>Polio (OPV)</strong><span class="s">6 weeks</span></div><span class="badge">Vaccinated</span>
                            </li>
                            <li><span class="tick"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg></span>
                                <div><strong>Pentavalent</strong><span class="s">10 weeks</span></div><span class="badge">Vaccinated</span>
                            </li>
                            <li><span class="tick due"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 2" />
                                    </svg></span>
                                <div><strong>Measles</strong><span class="s">Due next week</span></div><span class="badge due">Upcoming</span>
                            </li>
                        </ul>
                    </div>
                    <div class="vcard-note">Reminder sent to parent</div>
                </div>
            </div>
        </section>

        <!-- ================= ABOUT ================= -->
        <section id="about">
            <div class="wrap about-grid">
                <div class="about-text reveal">
                    <h2>From manual rounds to one online record</h2>
                    <p class="lead" style="margin-top:20px">Today, infants and children below teenage are vaccinated through visits such as school camps. Some children miss their turn for unavoidable reasons, and they can then catch diseases like polio.</p>
                    <p>Waiting to reach every child in every area takes time. The e-vaccination system collects each child's details on one website, so parents can take another chance to protect their child, and staff save many man-hours.</p>
                    <div class="stat-row">
                        <div class="stat"><b>3 roles</b><span>Admin, parent and hospital</span></div>
                        <div class="stat"><b>1 record</b><span>For every child</span></div>
                        <div class="stat"><b>0 paper</b><span>Reports are online</span></div>
                    </div>
                </div>
                <div class="compare">
                    <div class="panel before reveal">
                        <h3>Existing system</h3>
                        <p>Vaccination happens manually, for example through school visits. Children who are absent can be missed, and reaching everyone in every place is slow.</p>
                    </div>
                    <div class="panel after reveal">
                        <h3>Proposed e-vaccination system</h3>
                        <p>Parents register their children online, get date alerts, request a hospital and see reports. The admin approves requests and hospitals update the status.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= MODULES ================= -->
        <section class="modules" id="modules">
            <div class="wrap">
                <div class="reveal">
                    <h2>What each user can do</h2>
                    <p class="lead">The system has three modules. Choose a role to see its features.</p>
                </div>

                <div class="tabs reveal" role="tablist" aria-label="User roles">
                    <button class="tab" role="tab" aria-selected="true" aria-controls="p-admin" id="t-admin">Admin</button>
                    <button class="tab" role="tab" aria-selected="false" aria-controls="p-parent" id="t-parent" tabindex="-1">Parent</button>
                    <button class="tab" role="tab" aria-selected="false" aria-controls="p-hospital" id="t-hospital" tabindex="-1">Hospital</button>
                </div>

                <!-- ADMIN -->
                <div class="tabpanel on" role="tabpanel" id="p-admin" aria-labelledby="t-admin">
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 21v-2a4 4 0 0 0-3-3.9" />
                                <path d="M16 3.1a4 4 0 0 1 0 7.8" />
                            </svg></div>
                        <h3>All child details</h3>
                        <p>View the complete profile of every registered child.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path d="M16 2v4M8 2v4M3 10h18" />
                            </svg></div>
                        <h3>Date and time of vaccination</h3>
                        <p>See the upcoming vaccination dates of all children.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <path d="M14 2v6h6M8 13h8M8 17h8" />
                            </svg></div>
                        <h3>Vaccination reports</h3>
                        <p>Date-wise reports by child and by vaccination.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.5 20.5 3.5 13.5a5 5 0 0 1 7-7l7 7a5 5 0 0 1-7 7z" />
                                <path d="m8.5 8.5 7 7" />
                            </svg></div>
                        <h3>List of vaccines</h3>
                        <p>Check whether each vaccine is available or unavailable.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4" />
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                            </svg></div>
                        <h3>Parent requests</h3>
                        <p>Approve or reject appointment requests sent by parents.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M10 21v-4h4v4" />
                            </svg></div>
                        <h3>Add and manage hospitals</h3>
                        <p>Add a hospital, then update or delete its details. View the full list of hospitals.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                                <rect x="9" y="3" width="6" height="4" rx="1" />
                                <path d="M9 12h6M9 16h4" />
                            </svg></div>
                        <h3>Booking details</h3>
                        <p>View every vaccination booking made from the parent side.</p>
                    </article>
                </div>

                <!-- PARENT -->
                <div class="tabpanel" role="tabpanel" id="p-parent" aria-labelledby="t-parent">
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <path d="m10 17 5-5-5-5M15 12H3" />
                            </svg></div>
                        <h3>Register and login</h3>
                        <p>Create a parent account and sign in securely.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21a8 8 0 0 1 16 0" />
                            </svg></div>
                        <h3>Details of child</h3>
                        <p>Add, update and maintain your child's vaccination details.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.9 1.9 0 0 0 3.4 0" />
                            </svg></div>
                        <h3>Vaccination dates</h3>
                        <p>Get notified on your dashboard about upcoming vaccinations.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg></div>
                        <h3>Book hospital</h3>
                        <p>Search the list of hospitals and book a slot for the vaccine date.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 2 11 13M22 2l-7 20-4-9-9-4z" />
                            </svg></div>
                        <h3>Request for hospital</h3>
                        <p>Send an appointment request that the admin approves.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <path d="M14 2v6h6M9 15l2 2 4-4" />
                            </svg></div>
                        <h3>Report of vaccination taken</h3>
                        <p>See the status of your infant's previous vaccinations.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <circle cx="9" cy="11" r="2.5" />
                                <path d="M15 9h3M15 13h3M5.5 17a4 4 0 0 1 7 0" />
                            </svg></div>
                        <h3>My profile</h3>
                        <p>View and edit your own account details.</p>
                    </article>
                </div>

                <!-- HOSPITAL -->
                <div class="tabpanel" role="tabpanel" id="p-hospital" aria-labelledby="t-hospital">
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18M5 21V7l7-4 7 4v14M12 9v6M9 12h6" />
                            </svg></div>
                        <h3>Register and login</h3>
                        <p>Sign up with hospital name, address and location details, then log in.</p>
                    </article>
                    <article class="mod">
                        <div class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4" />
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                            </svg></div>
                        <h3>Update vaccine status</h3>
                        <p>Receive each booked appointment, and once the dose is given, mark the child as Vaccinated or Not vaccinated.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ================= HOW IT WORKS ================= -->
        <section id="how">
            <div class="wrap">
                <div class="reveal center">
                    <h2>How a vaccination gets booked</h2>
                    <p class="lead">Five steps from registration to a report in your account.</p>
                </div>
                <div class="flow">
                    <div class="step reveal"><i></i>
                        <h3>Register</h3>
                        <p>Parent creates an account and adds the child's details.</p>
                    </div>
                    <div class="step reveal"><i></i>
                        <h3>Get dates</h3>
                        <p>Upcoming vaccination dates show on the dashboard.</p>
                    </div>
                    <div class="step reveal"><i></i>
                        <h3>Book</h3>
                        <p>Parent searches hospitals and requests an appointment.</p>
                    </div>
                    <div class="step reveal"><i></i>
                        <h3>Approve</h3>
                        <p>Admin approves the request and the hospital receives it.</p>
                    </div>
                    <div class="step reveal"><i></i>
                        <h3>Update</h3>
                        <p>Hospital marks the child Vaccinated and the report updates.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= FAQ ================= -->
        <section class="faq" id="faq" style="padding-top:0">
            <div class="wrap">
                <div class="reveal center">
                    <h2>Common questions</h2>
                    <p class="lead">Quick answers for parents and hospitals.</p>
                </div>
                <div class="faq-list">
                    <details class="reveal" open>
                        <summary>Who can register a child?</summary>
                        <p>A parent registers on the website, then adds the child's details from their own account.</p>
                    </details>
                    <details class="reveal">
                        <summary>How do I know when the next vaccine is due?</summary>
                        <p>Upcoming vaccination dates appear as notifications on the dashboard of your parent account.</p>
                    </details>
                    <details class="reveal">
                        <summary>Who approves my appointment request?</summary>
                        <p>You search the hospital list and send a request. The admin approves or rejects it, and the hospital then receives the appointment.</p>
                    </details>
                    <details class="reveal">
                        <summary>How is the vaccination status updated?</summary>
                        <p>After the dose is given, the hospital marks the child as Vaccinated or not. The report in your account shows the result.</p>
                    </details>
                    <details class="reveal">
                        <summary>How can a hospital join?</summary>
                        <p>A hospital can register with its name, address and location details, or the admin can add it directly.</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- ================= CONTACT ================= -->
        <section class="contact" id="contact">
            <div class="wrap">
                <div class="reveal center">
                    <h2>Contact us</h2>
                    <p class="lead">Have a question about registering a child or adding your hospital? Call us or send a message. We are open 24 hours.</p>
                </div>

                <!-- one single card -->
                <div class="contact-card reveal">

                    <div class="contact-info">
                        <h3>Get in touch</h3>
                        <p>Reach us any time of the day or night.</p>
                        <span class="open-badge"><i></i><?= h($CONTACT['hours']) ?></span>

                        <ul class="info">
                            <li>
                                <span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z" />
                                    </svg></span>
                                <div><small>Phone</small><a href="<?= h($telLink) ?>"><?= h($CONTACT['phone']) ?></a></div>
                            </li>
                            <li>
                                <span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg></span>
                                <div><small>Address</small><span><?= h($CONTACT['address']) ?></span></div>
                            </li>
                            <li>
                                <span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                                        <path d="m22 7-10 6L2 7" />
                                    </svg></span>
                                <div><small>Email</small><a href="mailto:<?= h($CONTACT['email']) ?>"><?= h($CONTACT['email']) ?></a></div>
                            </li>
                            <li>
                                <span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg></span>
                                <div><small>Working hours</small><span>24 hours, every day</span></div>
                            </li>
                        </ul>
                    </div>

                    <form class="contact-form" id="contactForm" action="index.php#contact" method="post" novalidate>
                        <h3>Send a message</h3>
                        <p class="sub">Fill in the form and we will reply by email.</p>

                        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                        <div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                        <div class="row">
                            <div class="field <?= $errField === 'name' ? 'has-error' : '' ?>">
                                <label for="name">Full name</label>
                                <input id="name" name="name" maxlength="60" autocomplete="name" value="<?= h($old['name']) ?>">
                            </div>
                            <div class="field <?= $errField === 'email' ? 'has-error' : '' ?>">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" maxlength="100" autocomplete="email" value="<?= h($old['email']) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="field <?= $errField === 'phone' ? 'has-error' : '' ?>">
                                <label for="phone">Phone <small>(12 digits)</small></label>
                                <input id="phone" name="phone" type="tel" inputmode="numeric" maxlength="12" placeholder="923001234567" autocomplete="tel" value="<?= h($old['phone']) ?>">
                            </div>
                            <div class="field <?= $errField === 'role' ? 'has-error' : '' ?>">
                                <label for="who">I am a</label>
                                <select id="who" name="role">
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= h($r) ?>" <?= $old['role'] === $r ? 'selected' : '' ?>><?= h($r) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="field <?= $errField === 'address' ? 'has-error' : '' ?>">
                            <label for="address">Address</label>
                            <input id="address" name="address" maxlength="150" autocomplete="street-address" placeholder="House, street, area, city" value="<?= h($old['address']) ?>">
                        </div>

                        <div class="field <?= $errField === 'message' ? 'has-error' : '' ?>">
                            <label for="msg">Message</label>
                            <textarea id="msg" name="message" maxlength="1000"><?= h($old['message']) ?></textarea>
                        </div>

                        <div class="form-foot">
                            <button class="btn btn-primary" type="submit" name="send_message" value="1">Send message</button>
                            <small>We reply by email.</small>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer>
        <div class="wrap foot-grid">
            <div>
                <a href="#top" class="brand"><span class="brand-mark"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m18 2 4 4" />
                            <path d="m17 7 3-3" />
                            <path d="M19 9 8.7 19.3a1 1 0 0 1-1.4 0L4.7 16.7a1 1 0 0 1 0-1.4L15 5" />
                            <path d="m9 11 4 4" />
                            <path d="m5 19-3 3" />
                            <path d="m14 4 6 6" />
                        </svg></span>VaxCare</a>
                <p>Vaccination Management System for infants and children. One online record for parents, hospitals and the admin.</p>
                <div class="foot-cta"><a class="btn btn-primary" href="register.php">Get started</a></div>
            </div>
            <div>
                <h4>Quick links</h4>
                <ul>
                    <li><a href="#top">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#modules">Modules</a></li>
                    <li><a href="#how">How it works</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Portals</h4>
                <ul>
                    <li><a href="admin_login.php">Admin login</a></li>
                    <li><a href="parent_login.php">Parent login</a></li>
                    <li><a href="parent_register.php">Parent register</a></li>
                    <li><a href="hospital_login.php">Hospital login</a></li>
                    <li><a href="hospital_register.php">Hospital register</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul>
                    <li><?= h($CONTACT['email']) ?></li>
                    <li><?= h($CONTACT['phone']) ?></li>
                    <li><?= h($CONTACT['address']) ?></li>
                    <li>Open 24 hours, 7 days a week</li>
                </ul>
            </div>
        </div>
        <div class="wrap foot-bottom">
            <span>&copy; <span id="yr">2026</span> VaxCare Vaccination Management System. All rights reserved.</span>
            <span>Every child, every dose, on time.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Navbar: shadow on scroll + mobile menu
        const header = document.querySelector('header');
        const burger = document.querySelector('.burger');
        const menu = document.getElementById('menu');
        addEventListener('scroll', () => header.classList.toggle('scrolled', scrollY > 30), {
            passive: true
        });
        burger.addEventListener('click', () => {
            const open = menu.classList.toggle('open');
            burger.setAttribute('aria-expanded', open);
        });
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
            menu.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        }));

        // Active link on scroll
        const links = [...document.querySelectorAll('.nav-links a.link')];
        const targets = links.map(l => document.querySelector(l.getAttribute('href')));
        addEventListener('scroll', () => {
            const y = scrollY + 140;
            let cur = 0;
            targets.forEach((t, i) => {
                if (t && t.offsetTop <= y) cur = i
            });
            links.forEach((l, i) => l.classList.toggle('active', i === cur));
        }, {
            passive: true
        });

        // Hero ring count-up (3 of 4 = 75%)
        (function() {
            const ring = document.getElementById('ring'),
                txt = document.getElementById('ringTxt');
            const goal = 75,
                start = performance.now() + 700,
                dur = 1300;

            function tick(t) {
                const k = Math.min(Math.max((t - start) / dur, 0), 1),
                    v = Math.round(goal * (1 - Math.pow(1 - k, 3)));
                ring.style.setProperty('--p', v);
                txt.textContent = v + '%';
                if (k < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        })();

        // Role tabs
        const tabs = [...document.querySelectorAll('.tab')];
        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => select(tab));
            tab.addEventListener('keydown', e => {
                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                    const n = tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
                    n.focus();
                    select(n);
                }
            });
        });

        function select(tab) {
            tabs.forEach(t => {
                const on = t === tab;
                t.setAttribute('aria-selected', on);
                t.tabIndex = on ? 0 : -1;
                document.getElementById(t.getAttribute('aria-controls')).classList.toggle('on', on);
            });
        }

        // FAQ: keep one open at a time
        const dets = [...document.querySelectorAll('details')];
        dets.forEach(d => d.addEventListener('toggle', () => {
            if (d.open) dets.forEach(o => {
                if (o !== d) o.open = false
            });
        }));

        // Reveal on scroll
        const io = new IntersectionObserver(es => es.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target)
            }
        }), {
            threshold: .15
        });
        document.querySelectorAll('.reveal').forEach((el, i) => {
            el.style.transitionDelay = (i % 5) * 70 + 'ms';
            io.observe(el);
        });

        // After sending the form: scroll to the contact section and show it
        <?php if ($swal): ?>
                (function() {
                    const c = document.getElementById('contact');
                    c.querySelectorAll('.reveal').forEach(el => el.classList.add('in'));
                    c.scrollIntoView();
                })();
        <?php endif; ?>

        document.getElementById('yr').textContent = new Date().getFullYear();
    </script>
    <?php if ($swal): ?>
        <script>
            Swal.fire({
                icon: <?= json_encode($swal[0], JSON_HEX_TAG) ?>,
                title: <?= json_encode($swal[1], JSON_HEX_TAG) ?>,
                text: <?= json_encode($swal[2], JSON_HEX_TAG) ?>
            });
        </script>
    <?php endif; ?>
</body>

</html>