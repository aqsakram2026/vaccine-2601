<?php
$sitePage = $sitePage ?? 'index';
$pageTitle = $pageTitle ?? 'E-Vaccine | Vaccination Management';
$pageDescription = $pageDescription ?? 'A shared vaccination management portal for parents, hospitals and administrators.';
$breadcrumbs = $breadcrumbs ?? [];
$themePreference = 'light';
$savedTheme = $_COOKIE['evaccine_theme'] ?? '';

if (in_array($savedTheme, ['light', 'dark'], true)) {
    $themePreference = $savedTheme;
}
$themePressed = 'false';
$themeLabel = 'Switch to dark theme';
$themeIcon = 'moon-stars';
if ($themePreference === 'dark') {
    $themePressed = 'true';
    $themeLabel = 'Switch to light theme';
    $themeIcon = 'sun';
}

if (!function_exists('site_escape')) {
    function site_escape($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!doctype html>
<html lang="en" data-theme="<?= site_escape($themePreference) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= site_escape($pageDescription) ?>">
    <meta name="theme-color" content="#f5fbff">
    <title><?= site_escape($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="images/Logo-2%20no_bg.png">
    <link rel="apple-touch-icon" href="images/Logo-2%20no_bg.png">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="includes/theme.css?v=5" rel="stylesheet">
    <script src="includes/theme.js?v=6"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --vax-ink: #12334a;
            --vax-muted: #687782;
            --vax-teal: #078f87;
            --vax-blue: #7a2846;
            --vax-soft: #f1f3f5;
            --vax-line: #d7dfe4;
            --vax-shadow: 0 20px 55px rgba(18, 51, 74, .09);
            --vax-shadow-hover: 0 24px 55px rgba(18, 51, 74, .13);
            --vax-page: #f1f3f5;
            --vax-surface: rgba(255,255,255,.92);
            --vax-heading: #12334a;
        }
        html[data-theme="dark"] {
            color-scheme: dark;
            --vax-ink: #e6edf2;
            --vax-muted: #adbbc5;
            --vax-soft: #203545;
            --vax-line: #314554;
            --vax-shadow: 0 20px 55px rgba(0,0,0,.24);
            --vax-shadow-hover: 0 24px 55px rgba(0,0,0,.34);
            --vax-page: #101d2a;
            --vax-surface: rgba(24,42,57,.96);
            --vax-heading: #e6edf2;
        }

        * { box-sizing: border-box; }
        html { scroll-padding-top: 90px; }
        body {
            margin: 0;
            color: var(--vax-ink);
            background: var(--vax-page);
            font-family: "Segoe UI", Arial, sans-serif;
            overflow-x: hidden;
        }

        .site-navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: rgba(255,255,255,.94);
            border-bottom: 1px solid var(--vax-line);
            box-shadow: 0 5px 25px rgba(23, 60, 80, .05);
            backdrop-filter: blur(16px);
            transition: box-shadow .25s ease, background-color .25s ease;
        }

        .site-navbar .navbar { min-height: 76px; }
        .site-brand {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            color: var(--vax-teal);
            font-size: 1.35rem;
            font-weight: 850;
            letter-spacing: -.04em;
            text-decoration: none;
        }
        .site-brand .brand-logo { width: 48px; height: 48px; object-fit: contain; }
        .site-brand .brand-icon {
            position: relative;
            display: flex;
            width: 38px;
            height: 38px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(135deg, #0bb9a8, #087fbd);
            box-shadow: 0 7px 17px rgba(7, 155, 145, .2);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .site-brand:hover .brand-icon { transform: rotate(-7deg) scale(1.06); box-shadow: 0 10px 24px rgba(7,155,145,.3); }
        .site-navbar .nav-link {
            position: relative;
            color: #3b586b;
            font-size: .94rem;
            font-weight: 650;
            transition: color .2s ease;
        }
        .site-navbar .nav-link:hover,
        .site-navbar .nav-link.active { color: var(--vax-teal); }
        @media (min-width: 992px) {
            .site-navbar .nav-link:not(.dropdown-toggle)::after {
                position: absolute;
                right: .5rem;
                bottom: .1rem;
                left: .5rem;
                height: 2px;
                border-radius: 2px;
                background: linear-gradient(90deg, var(--vax-teal), var(--vax-blue));
                content: "";
                transform: scaleX(0);
                transform-origin: left;
                transition: transform .24s ease;
            }
            .site-navbar .nav-link:not(.dropdown-toggle):hover::after,
            .site-navbar .nav-link:not(.dropdown-toggle).active::after { transform: scaleX(1); }
        }

        .site-search { position: relative; width: min(230px, 100%); }
        .site-search .form-control {
            padding-left: 2.35rem;
            border-color: var(--vax-line);
            border-radius: 999px;
            background: var(--vax-surface);
        }
        .site-search .search-icon {
            position: absolute;
            top: 50%;
            left: .85rem;
            z-index: 3;
            color: #6a8493;
            transform: translateY(-50%);
        }
        .search-results {
            position: absolute;
            top: calc(100% + .55rem);
            right: 0;
            z-index: 1040;
            display: none;
            width: min(340px, calc(100vw - 2rem));
            padding: .4rem;
            border: 1px solid var(--vax-line);
            border-radius: 14px;
            background: var(--vax-surface);
            box-shadow: var(--vax-shadow);
        }
        .search-results.is-open { display: block; }
        .search-results a {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .65rem .75rem;
            border-radius: 9px;
            color: var(--vax-ink);
            text-decoration: none;
        }
        .search-results a:hover,
        .search-results a:focus { background: var(--vax-soft); }
        .search-results small { color: var(--vax-muted); }
        .search-empty { padding: .7rem .75rem; color: var(--vax-muted); }

        .page-breadcrumb { padding-top: 1rem; }
        .breadcrumb {
            display: inline-flex;
            align-items: center;
            padding: .48rem .8rem;
            border: 1px solid rgba(220,235,237,.85);
            border-radius: 999px;
            margin-bottom: 0;
            background: var(--vax-surface);
            font-size: .84rem;
        }
        .breadcrumb a { color: var(--vax-teal); text-decoration: none; }
        .breadcrumb a:hover { color: var(--vax-blue); }
        main { min-height: 60vh; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem .85rem;
            border: 1px solid #bdece4;
            border-radius: 999px;
            color: #087f77;
            background: #e4faf5;
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .display-title {
            max-width: 760px;
            font-size: clamp(2.45rem, 5.2vw, 4.65rem);
            font-weight: 850;
            letter-spacing: -.055em;
            line-height: 1.04;
            text-wrap: balance;
        }
        .text-gradient {
            color: var(--vax-teal);
            background: linear-gradient(90deg, #078f87, #7a2846);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .lead-copy { max-width: 650px; color: var(--vax-muted); font-size: 1.12rem; line-height: 1.8; }
        .section-space { 
            /* padding-top: clamp(3.5rem, 7vw, 6.5rem);  */
            padding-bottom: clamp(3.5rem, 7vw, 6.5rem); }

        .section-heading { max-width: 720px; margin-bottom: 2rem; }
        .section-heading h2 { font-size: clamp(1.9rem, 4vw, 3rem); font-weight: 820; letter-spacing: -.04em; text-wrap: balance; }
        .section-heading p { color: var(--vax-muted); line-height: 1.8; }
        .surface-card {
            height: 100%;
            border: 1px solid var(--vax-line);
            border-radius: 22px;
            background: var(--vax-surface);
            box-shadow: 0 12px 35px rgba(18, 51, 74, .045);
            transition: border-color .28s ease, box-shadow .28s ease, transform .28s ease;
        }
        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .surface-card:hover {
                border-color: #c4e7e5;
                box-shadow: var(--vax-shadow-hover);
                transform: translateY(-4px);
            }
        }
        .icon-tile {
            display: flex;
            width: 54px;
            height: 54px;
            align-items: center;
            justify-content: center;
            border-radius: 17px;
            color: #078f88;
            background: #e1f8f2;
            font-size: 1.4rem;
            transition: color .25s ease, background-color .25s ease, transform .3s ease;
        }

         .icon-til {
            width: 54px;
            height: 54px;
            border-radius: 17px;
            color: #078f88;
            background: #e1f8f2;
            font-size: 1.4rem;
            transition: color .25s ease, background-color .25s ease, transform .3s ease;
        }
        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .surface-card:hover .icon-tile { color: #fff; background: linear-gradient(135deg, #0bb9a8, #087fbd); transform: rotate(-5deg) scale(1.05); }
        }
        .btn-vax {
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #0bb9a8, #087fbd);
            box-shadow: 0 9px 22px rgba(7, 155, 145, .17);
            transition: box-shadow .25s ease, transform .25s ease, filter .25s ease;
        }
        .btn-vax:hover { color: #fff; filter: brightness(1.04); box-shadow: 0 13px 28px rgba(7,155,145,.28); transform: translateY(-2px); }
        .btn-outline-vax { border-color: var(--vax-teal); color: var(--vax-teal); transition: color .22s ease, background-color .22s ease, transform .22s ease; }
        .btn-outline-vax:hover { border-color: var(--vax-teal); color: #fff; background: var(--vax-teal); transform: translateY(-1px); }
        .form-control, .form-select { transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease; }
        .site-footer {
            margin-top: 3rem;
            padding: 3rem 0 1.25rem;
            color: #d5e6ed;
            background: #102c40;
        }
        .site-footer a { color: #e7f8f8; text-decoration: none; transition: color .2s ease; }
        .site-footer a:hover { color: #70e3d2; }
        .footer-brand { display: inline-flex; align-items: center; gap: .55rem; color: #75e2d5; font-size: 1.2rem; font-weight: 800; }
        .footer-brand img { width: 48px; height: 48px; object-fit: contain; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.12); }
        .footer-socials { display: flex; flex-wrap: wrap; gap: .55rem; }
        .footer-social-link {
            display: flex;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(231,248,248,.22);
            border-radius: 50%;
            font-size: 1.08rem;
            transition: color .2s ease, background-color .2s ease, border-color .2s ease, transform .2s ease;
        }
        .footer-social-link:hover { border-color: #70e3d2; background: rgba(112,227,210,.12); transform: translateY(-2px); }
        .footer-clock { display: inline-flex; align-items: center; gap: .4rem; }
        .whatsapp-note { color: rgba(231,248,248,.62); font-size: .75rem; }
        .theme-toggle {
            display: inline-flex;
            min-width: 42px;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            border: 1px solid var(--vax-line);
            border-radius: 999px;
            color: var(--vax-ink);
            background: var(--vax-surface);
            font-size: .88rem;
            transition: transform .2s ease, border-color .2s ease, background-color .2s ease;
        }
        .theme-toggle:hover { border-color: var(--vax-teal); transform: translateY(-1px); }
        .table thead th { color: #426173; background: #f1f8f9; white-space: nowrap; }
        .table > :not(caption) > * > * { padding: .9rem 1rem; }
        .table tbody tr { transition: background-color .2s ease; }
        .accordion-item { border-color: var(--vax-line); }
        .accordion-button:not(.collapsed) { color: #087f77; background: #eefaf7; box-shadow: none; }
        .accordion-button:focus { border-color: #9cddd3; box-shadow: 0 0 0 .2rem rgba(7,155,145,.12); }
        .form-control:focus, .form-select:focus {
            border-color: #72cfc2;
            box-shadow: 0 0 0 .2rem rgba(7,155,145,.12);
        }
        .page-hero {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(circle at 85% 22%, rgba(139,236,225,.28), transparent 30%),
                linear-gradient(135deg, #f8ffff, #eef7ff);
        }
        html[data-theme="dark"] .page-hero {
            background:
                radial-gradient(circle at 85% 22%, rgba(12,115,111,.2), transparent 30%),
                linear-gradient(135deg, #102536, #0d202f);
        }
        html[data-theme="dark"] .site-navbar { background: rgba(12,27,39,.94); }
        html[data-theme="dark"] .page-hero::before { opacity: .35; }
        html[data-theme="dark"] .page-hero::after { border-color: rgba(87,196,186,.14); box-shadow: 0 0 0 35px rgba(87,196,186,.035), 0 0 0 75px rgba(87,196,186,.025); }
        html[data-theme="dark"] .surface-card { border-color: var(--vax-line); background: var(--vax-surface); }
        html[data-theme="dark"] .home-visual-card { border-color: rgba(126,176,186,.28); background: rgba(16,37,51,.9); }
        html[data-theme="dark"] .home-visual-heading,
        html[data-theme="dark"] h1,
        html[data-theme="dark"] h2,
        html[data-theme="dark"] h3 { color: var(--vax-heading); }
        html[data-theme="dark"] .text-secondary,
        html[data-theme="dark"] .lead-copy,
        html[data-theme="dark"] .section-heading p,
        html[data-theme="dark"] .form-text { color: var(--vax-muted) !important; }
        html[data-theme="dark"] .breadcrumb { border-color: var(--vax-line); background: rgba(20,40,54,.85); color: var(--vax-muted); }
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .modal-content,
        html[data-theme="dark"] .popover { border-color: var(--vax-line); color: var(--vax-ink); background: #142836; }
        html[data-theme="dark"] .dropdown-item,
        html[data-theme="dark"] .popover-header,
        html[data-theme="dark"] .popover-body { color: var(--vax-ink); }
        html[data-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .dropdown-item:focus { color: #fff; background: #1b3a4d; }
        html[data-theme="dark"] .dropdown-header { color: var(--vax-muted); }
        html[data-theme="dark"] .search-results a { color: var(--vax-ink); }
        html[data-theme="dark"] .search-results a:hover,
        html[data-theme="dark"] .search-results a:focus { background: #1b3a4d; }
        html[data-theme="dark"] .table {
            --bs-table-color: var(--vax-ink);
            --bs-table-bg: #142836;
            --bs-table-border-color: var(--vax-line);
            --bs-table-hover-color: var(--vax-ink);
            --bs-table-hover-bg: #193444;
        }
        html[data-theme="dark"] .table thead th { color: #c7dce5; background: #193444; }
        html[data-theme="dark"] .badge.text-bg-light { color: #dceaf0 !important; background: #244253 !important; }
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select { border-color: var(--vax-line); color: var(--vax-ink); background-color: #102533; }
        html[data-theme="dark"] .form-control::placeholder { color: #90a8b5; }
        html[data-theme="dark"] .form-select option { color: var(--vax-ink); background: #102533; }
        html[data-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-select:focus { border-color: #56bdb1; color: var(--vax-ink); background-color: #132d3d; }
        html[data-theme="dark"] .accordion-item { color: var(--vax-ink); background: #142836; }
        html[data-theme="dark"] .accordion-button { color: var(--vax-ink); background: #142836; }
        html[data-theme="dark"] .accordion-button:not(.collapsed) { color: #75e2d5; background: #183545; }
        html[data-theme="dark"] .accordion-button::after { filter: invert(1); }
        html[data-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-footer { border-color: var(--vax-line); }
        html[data-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
        html[data-theme="dark"] main section[style*="#effafa"] { background: linear-gradient(135deg,#102b38,#15283b) !important; }
        html[data-theme="dark"] .sitemap-count { border-color: var(--vax-line); color: #75e2d5; background: #142836; }
        html[data-theme="dark"] .terms-toc a { color: #75e2d5; }
        html[data-theme="dark"] .contact-form-card .form-control,
        html[data-theme="dark"] .contact-form-card .form-select { background-color: #102533; }
        html[data-theme="dark"] .contact-form-card .form-control:focus,
        html[data-theme="dark"] .contact-form-card .form-select:focus { background-color: #132d3d; }
        html[data-theme="dark"] .map-frame { border-color: var(--vax-line); }
        html[data-theme="dark"] .theme-toggle { border-color: var(--vax-line); color: var(--vax-ink); background: #142836; }
        .glass-panel {
            border: 1px solid rgba(255,255,255,.42);
            border-radius: 24px;
            background: rgba(255,255,255,.68);
            box-shadow: 0 18px 48px rgba(18,51,74,.08);
            backdrop-filter: blur(16px);
        }
        .portal-card { min-height: 100%; }
        html[data-theme="dark"] .glass-panel { border-color: rgba(153,196,205,.18); background: rgba(20,40,54,.76); }
        .map-frame { width: 100%; min-height: 340px; border: 1px solid var(--vax-line); border-radius: 20px; }
        .chart-wrap { position: relative; height: 230px; }
        .review-card { border: 1px solid var(--vax-line); border-radius: 16px; background: var(--vax-page); }
        .review-stars { display: flex; gap: .18rem; color: #e2a72e; }
        .page-hero::before {
            position: absolute;
            top: -15rem;
            left: 37%;
            z-index: -1;
            width: 32rem;
            height: 32rem;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(91,220,204,.12), transparent 68%);
            content: "";
            animation: hero-glow-drift 18s ease-in-out infinite alternate;
            pointer-events: none;
        }
        @keyframes hero-glow-drift {
            from { transform: translate3d(-12px, 0, 0) scale(.96); }
            to { transform: translate3d(35px, 28px, 0) scale(1.06); }
        }
        .home-virus-scene {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: .3;
            pointer-events: none;
        }
        .home-virus-scene canvas { display: block; width: 100%; height: 100%; }
        .page-hero > .container { position: relative; z-index: 1; }
        .home-hero { display: flex; min-height: min(760px, calc(100svh - 76px)); align-items: center; }
        .home-hero .display-title { font-size: clamp(2.8rem, 6vw, 5.1rem); }
        .home-hero .container { width: 100%; }
        .home-hero .lead-copy { font-size: clamp(1.05rem, 1.5vw, 1.2rem); }
        .home-visual-card {
            border-color: rgba(255,255,255,.85);
            background: rgba(255,255,255,.88);
            box-shadow: 0 28px 75px rgba(18,51,74,.12);
            backdrop-filter: blur(12px);
        }
        .home-visual-heading { color: var(--vax-ink); }
        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: #087f77;
            font-size: .72rem;
            font-weight: 750;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .live-indicator::before {
            width: .48rem;
            height: .48rem;
            border-radius: 50%;
            background: #17b69f;
            box-shadow: 0 0 0 0 rgba(23,182,159,.38);
            content: "";
            animation: live-pulse 2s ease-out infinite;
        }
        @keyframes live-pulse {
            70% { box-shadow: 0 0 0 7px rgba(23,182,159,0); }
            100% { box-shadow: 0 0 0 0 rgba(23,182,159,0); }
        }
        .home-proof { border-top: 1px solid rgba(220,235,237,.8); }
        .home-proof-item { display: inline-flex; align-items: center; gap: .4rem; }
        .home-proof-item i { color: #079b91; }
        .workflow-steps { position: relative; }
        .workflow-card { position: relative; overflow: hidden; }
        .workflow-card::after {
            position: absolute;
            right: -2.6rem;
            bottom: -3rem;
            width: 7rem;
            height: 7rem;
            border: 1px solid rgba(7,155,145,.1);
            border-radius: 50%;
            box-shadow: 0 0 0 16px rgba(7,155,145,.035);
            content: "";
            transition: transform .35s ease;
        }
        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .workflow-card:hover::after { transform: scale(1.18); }
        }
        .contact-form-card { position: relative; overflow: hidden; }
        .contact-form-card::before {
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, #0bb9a8, #087fbd, #8b7ee8);
            content: "";
        }
        .contact-form-card .form-control,
        .contact-form-card .form-select { background-color: #fbfefe; }
        .contact-form-card .form-control:focus,
        .contact-form-card .form-select:focus { background-color: #fff; }
        .sitemap-count {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .42rem .75rem;
            border: 1px solid var(--vax-line);
            border-radius: 999px;
            color: #087f77;
            background: #f3fbfa;
            font-size: .82rem;
            font-weight: 750;
        }
        .page-progress {
            position: fixed;
            inset: 0 0 auto;
            z-index: 1090;
            height: 3px;
            pointer-events: none;
        }
        .page-progress span {
            display: block;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #12bba4, #1689ca);
            box-shadow: 0 0 12px rgba(18,187,164,.48);
            transform: scaleX(0);
            transform-origin: left;
        }
        .page-hero::after {
            position: absolute;
            right: -5rem;
            bottom: -8rem;
            z-index: -1;
            width: 24rem;
            height: 24rem;
            border: 1px solid rgba(7,155,145,.12);
            border-radius: 50%;
            box-shadow: 0 0 0 35px rgba(7,155,145,.035), 0 0 0 75px rgba(7,155,145,.025);
            content: "";
        }
        @media (max-width: 991.98px) {
            .site-search { width: 100%; margin-top: .75rem; }
            .site-search .search-results { left: 0; right: auto; }
            .home-hero { min-height: 0; }
            .home-hero .container { padding-top: 3.25rem !important; padding-bottom: 4rem !important; }
            .home-virus-scene { opacity: .2; }
        }
        @media (max-width: 575.98px) {
            .display-title { font-size: clamp(2.35rem, 11vw, 3.2rem); }
            .home-hero .display-title { font-size: clamp(2.8rem, 13vw, 3.65rem); }
            .page-breadcrumb { padding-top: .7rem; }
            .home-proof { gap: .65rem 1rem !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
<div class="page-progress" aria-hidden="true"><span id="pageProgressBar"></span></div>
<header class="site-navbar">
    <nav class="navbar navbar-expand-lg container" aria-label="Main navigation">
        <a class="site-brand" href="index.php" aria-label="E-Vaccine home">
            <img class="brand-logo" src="images/Logo-2%20no_bg.png" alt="">
            E-Vaccine
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNavigation" aria-controls="siteNavigation" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="siteNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link <?php if ($sitePage === 'index') { echo 'active'; } ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($sitePage === 'about') { echo 'active'; } ?>" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($sitePage === 'awareness') { echo 'active'; } ?>" href="awareness.php">Vaccine Awareness</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Portals</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Choose a portal</h6></li>
                        <li><a class="dropdown-item" href="parents/parent-login.php"><i class="bi bi-person-heart me-2 text-primary"></i>Parent sign in</a></li>
                        <li><a class="dropdown-item" href="parents/parent.php"><i class="bi bi-person-plus me-2 text-primary"></i>Parent registration</a></li>
                        <li><a class="dropdown-item" href="hospital/hospital_login.php"><i class="bi bi-hospital me-2 text-success"></i>Hospital sign in</a></li>
                        <li><a class="dropdown-item" href="hospital/hospital_register.php"><i class="bi bi-building-add me-2 text-success"></i>Hospital registration</a></li>
                        <li><a class="dropdown-item" href="admin/admin_login.php"><i class="bi bi-speedometer2 me-2"></i>Administration</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link <?php if ($sitePage === 'contact') { echo 'active'; } ?>" href="contact.php">Contact</a></li>
                <li class="nav-item"><a class="nav-link <?php if ($sitePage === 'sitemap') { echo 'active'; } ?>" href="sitemap.php">Sitemap</a></li>
                <li class="nav-item ms-lg-2">
                    <button class="theme-toggle px-3" id="themeToggle" type="button" aria-pressed="<?= $themePressed ?>" aria-label="<?= $themeLabel ?>">
                        <i class="bi bi-<?= $themeIcon ?>" aria-hidden="true"></i>
                        <span class="d-lg-none">Toggle theme</span>
                    </button>
                </li>
            </ul>
            <div class="site-search ms-lg-3" role="search">
                <i class="bi bi-search search-icon" aria-hidden="true"></i>
                <input class="form-control form-control-sm" id="siteSearch" type="search" placeholder="Search pages and portals" aria-label="Search pages and portals" aria-controls="siteSearchResults" autocomplete="off">
                <div class="search-results" id="siteSearchResults" role="listbox" aria-label="Search results"></div>
            </div>
        </div>
    </nav>
</header>
<?php if ($breadcrumbs): ?>
    <nav class="container page-breadcrumb" aria-label="Breadcrumb">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php $isLast = $index === array_key_last($breadcrumbs); ?>
                <?php
                $breadcrumbClass = '';
                $breadcrumbCurrent = '';
                if ($isLast) {
                    $breadcrumbClass = 'active';
                    $breadcrumbCurrent = 'aria-current="page"';
                }
                ?>
                <li class="breadcrumb-item <?= $breadcrumbClass ?>" <?= $breadcrumbCurrent ?>>
                    <?php if (!$isLast && !empty($crumb['url'])): ?>
                        <a href="<?= site_escape($crumb['url']) ?>"><?= site_escape($crumb['label']) ?></a>
                    <?php else: ?>
                        <?= site_escape($crumb['label']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>
