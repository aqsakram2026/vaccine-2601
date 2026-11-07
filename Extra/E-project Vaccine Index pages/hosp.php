
<?php
$pageTitle = "E-Vaccine | Smart Vaccination";
$year = date("Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f5fbff;
    color: #12334a;
    overflow-x: hidden;
}

.navbar-custom {
    padding: 18px 7%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(16px);
    box-shadow: 0 5px 25px #173c5010;
    position: sticky;
    top: 0;
    z-index: 100;
}

.logo {
    font-size: 27px;
    font-weight: 800;
    color: #079b91;
    text-decoration: none;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 24px;
}

.nav-links a {
    color: #234358;
    text-decoration: none;
    font-weight: 600;
}

.nav-links a:hover { color: #079b91; }

.nav-btn, .primary-btn {
    display: inline-block;
    padding: 13px 23px;
    border-radius: 12px;
    color: white !important;
    background: linear-gradient(135deg, #0bb9a8, #087fbd);
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 9px 22px #079b9130;
    transition: .3s;
}

.nav-btn:hover, .primary-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px #079b9145;
}

.hero {
    min-height: 650px;
    padding: 75px 8%;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 30px;
    background:
        radial-gradient(circle at 80% 40%, #b9f3eb80, transparent 36%),
        linear-gradient(135deg, #f8ffff, #eef7ff);
    overflow: hidden;
}

.hero-content { animation: enterLeft 1s ease both; }

.tag {
    display: inline-block;
    color: #078f88;
    background: #d9f8f1;
    border: 1px solid #b8eee4;
    padding: 9px 15px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
}

.hero h1 {
    font-size: clamp(40px, 5vw, 65px);
    line-height: 1.08;
    letter-spacing: -2px;
    font-weight: 850;
    margin: 24px 0;
}

.hero h1 span {
    background: linear-gradient(90deg, #06a99a, #087cbe);
    color: transparent;
    background-clip: text;
}

.hero p {
    color: #637989;
    font-size: 17px;
    line-height: 1.9;
    max-width: 510px;
    margin-bottom: 28px;
}

.secondary-btn {
    display: inline-block;
    padding: 12px 22px;
    border: 1px solid #d4e6eb;
    border-radius: 12px;
    text-decoration: none;
    color: #24485a;
    font-weight: 700;
    margin: 7px;
    background: white;
    transition: .3s;
}

.secondary-btn:hover { transform: translateY(-3px); }

.trust {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 28px;
    color: #526d7c;
    font-size: 13px;
}

.trust i { color: #079b91; }

/* 3D SCENE */

.scene {
    min-height: 460px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    perspective: 1000px;
    animation: enterRight 1.2s ease both;
}

.orbit {
    position: absolute;
    width: 380px;
    height: 380px;
    border: 1px solid #18b6ae45;
    border-radius: 50%;
    transform: rotateX(66deg) rotateZ(-20deg);
    animation: orbitSpin 18s linear infinite;
}

.orbit.two {
    width: 310px;
    height: 310px;
    border-color: #188acb40;
    transform: rotateX(66deg) rotateZ(40deg);
    animation-direction: reverse;
}

.glow {
    width: 290px;
    height: 290px;
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(circle, #8dece1a0, #d5f7f300 70%);
    filter: blur(10px);
}

/* CSS 3D shield */

.shield-wrap {
    position: relative;
    z-index: 2;
    animation: float 4s ease-in-out infinite;
    transform-style: preserve-3d;
}

.shield {
    width: 230px;
    height: 260px;
    position: relative;
    clip-path: polygon(50% 0%, 94% 15%, 90% 62%, 75% 83%, 50% 100%, 25% 83%, 10% 62%, 6% 15%);
    background: linear-gradient(135deg, #e6fffb 0%, #65d8cd 35%, #079eab 72%, #086a9b 100%);
    filter: drop-shadow(12px 24px 20px #087cbd30);
    display: flex;
    justify-content: center;
    align-items: center;
}

.shield-inner {
    width: 185px;
    height: 215px;
    clip-path: polygon(50% 0%, 94% 15%, 90% 62%, 75% 83%, 50% 100%, 25% 83%, 10% 62%, 6% 15%);
    background: linear-gradient(145deg, #bffbf0, #20b9ba 65%, #0a8db0);
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: inset 0 0 25px #ffffff70;
}

.cross {
    position: relative;
    width: 48px;
    height: 145px;
    background: linear-gradient(90deg, #ffffff, #e7ffff, #ffffff);
    border-radius: 9px;
    filter: drop-shadow(4px 7px 5px #005b7560);
}

.cross::after {
    content: "";
    position: absolute;
    width: 145px;
    height: 48px;
    top: 48px;
    left: -48px;
    border-radius: 9px;
    background: linear-gradient(180deg, #ffffff, #d9ffff, #ffffff);
    box-shadow: inset 0 2px 5px #ffffff;
}

.floating-pill {
    position: absolute;
    z-index: 4;
    width: 105px;
    height: 43px;
    border-radius: 30px;
    background: linear-gradient(90deg, #ffffff 0 49%, #0eb8a8 50% 100%);
    border: 5px solid white;
    box-shadow: 0 12px 28px #146f8d30;
    transform: rotate(-36deg);
    top: 65px;
    right: 0;
    animation: pillFloat 4s ease-in-out infinite;
}

.floating-pill::after {
    content: "";
    position: absolute;
    inset: 0 50% 0 auto;
    border-left: 2px solid #d7eeed;
}

.syringe {
    position: absolute;
    z-index: 5;
    width: 125px;
    height: 25px;
    left: 0;
    bottom: 85px;
    border-radius: 6px;
    background: linear-gradient(180deg, #e8ffff, #8de0e5, #ffffff);
    border: 2px solid #83cfd8;
    box-shadow: 0 10px 20px #1d718d30;
    transform: rotate(-43deg);
    animation: syringeFloat 5s ease-in-out infinite;
}

.syringe::before {
    content: "";
    position: absolute;
    width: 35px;
    height: 17px;
    left: -37px;
    top: 2px;
    border: 3px solid #0a9caa;
    border-radius: 4px;
}

.syringe::after {
    content: "";
    position: absolute;
    width: 45px;
    height: 2px;
    right: -44px;
    top: 10px;
    background: #078baf;
}

.mini-card {
    position: absolute;
    z-index: 6;
    background: #ffffffdb;
    backdrop-filter: blur(12px);
    padding: 13px 17px;
    border: 1px solid #ffffff;
    border-radius: 17px;
    box-shadow: 0 14px 35px #164a5c15;
    animation: float 4.5s ease-in-out infinite;
}

.mini-card i {
    font-size: 22px;
    color: #079b91;
    margin-right: 8px;
}

.card-top { top: 50px; left: 2%; }
.card-bottom { bottom: 35px; right: 0; animation-delay: 1s; }

.services {
    padding: 85px 8%;
    text-align: center;
    background: white;
}

.section-tag {
    color: #079b91;
    font-weight: 800;
    letter-spacing: 2px;
    font-size: 12px;
}

.services h2 {
    font-size: clamp(30px, 4vw, 43px);
    font-weight: 850;
    margin: 12px 0;
}

.section-desc { color: #728492; margin-bottom: 40px; }

.service-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    text-align: left;
}

.service-card {
    padding: 30px;
    border-radius: 22px;
    border: 1px solid #e5f0f2;
    background: linear-gradient(145deg, #fff, #f4fbff);
    box-shadow: 0 10px 30px #173c5008;
    transition: .35s;
    opacity: 0;
    transform: translateY(25px);
}

.service-card.show {
    opacity: 1;
    transform: translateY(0);
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px #173c5015;
}

.service-icon {
    width: 62px;
    height: 62px;
    display: grid;
    place-items: center;
    border-radius: 18px;
    background: #dff9f3;
    color: #078f88;
    font-size: 28px;
    margin-bottom: 22px;
}

.service-card h3 { font-size: 21px; font-weight: 800; }
.service-card p { color: #718391; line-height: 1.8; }

.hospital-section {
    margin: 50px 8%;
    padding: 55px 35px;
    text-align: center;
    border-radius: 28px;
    color: white;
    background: radial-gradient(circle at top right, #1b8eb9, transparent 40%),
                linear-gradient(120deg, #12334a, #087e87);
}

.hospital-section h2 { font-weight: 850; font-size: 36px; }
.hospital-section p { color: #d5e9ef; margin: 15px 0 25px; }

footer {
    padding: 25px;
    background: #102c40;
    text-align: center;
    color: #c7dce5;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotateY(-5deg); }
    50% { transform: translateY(-17px) rotateY(5deg); }
}

@keyframes pillFloat {
    0%, 100% { transform: translateY(0) rotate(-36deg); }
    50% { transform: translateY(-16px) rotate(-25deg); }
}

@keyframes syringeFloat {
    0%, 100% { transform: translate(0,0) rotate(-43deg); }
    50% { transform: translate(8px,-13px) rotate(-35deg); }
}

@keyframes orbitSpin {
    to { rotate: 360deg; }
}

@keyframes enterLeft {
    from { opacity: 0; transform: translateX(-45px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes enterRight {
    from { opacity: 0; transform: translateX(45px); }
    to { opacity: 1; transform: translateX(0); }
}

@media (max-width: 850px) {
    .hero { grid-template-columns: 1fr; text-align: center; padding: 55px 6%; }
    .hero p { margin-left: auto; margin-right: auto; }
    .trust { justify-content: center; }
    .scene { min-height: 420px; }
    .service-grid { grid-template-columns: 1fr; }
    .nav-links { gap: 10px; flex-wrap: wrap; justify-content: center; }
    .navbar-custom { gap: 15px; flex-direction: column; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation: none !important; transition: none !important; }
    .service-card { opacity: 1; transform: none; }
}
</style>
</head>

<body>

<nav class="navbar-custom">
    <a class="logo" href="index.php">
        <i class="bi bi-shield-plus"></i> E-Vaccine
    </a>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="#services">Services</a>
        <a href="#hospital">Hospitals</a>
        <a class="nav-btn" href="hospital_login.php">Hospital Login <i class="bi bi-arrow-up-right"></i></a>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <span class="tag"><i class="bi bi-stars"></i> SMART HEALTHCARE PLATFORM</span>

        <h1>Protect Lives.<br>Build a <span>Healthier</span> Future.</h1>

        <p>
            Your family's vaccination journey, organized in one place.
            Manage vaccination records and connect with healthcare services
            through our smart vaccination management system.
        </p>

        <a href="register.php" class="primary-btn">
            Get Started <i class="bi bi-arrow-right"></i>
        </a>

        <a href="#services" class="secondary-btn">
            Explore Services
        </a>

        <div class="trust">
            <span><i class="bi bi-shield-check"></i> Health-focused</span>
            <span><i class="bi bi-heart-pulse"></i> Easy management</span>
            <span><i class="bi bi-hospital"></i> Hospital portal</span>
        </div>
    </div>

    <!-- CSS 3D MEDICAL MODEL -->
    <div class="scene">
        <div class="glow"></div>
        <div class="orbit"></div>
        <div class="orbit two"></div>

        <div class="mini-card card-top">
            <i class="bi bi-heart-pulse-fill"></i>
            <strong>Health First</strong>
        </div>

        <div class="shield-wrap">
            <div class="shield">
                <div class="shield-inner">
                    <div class="cross"></div>
                </div>
            </div>
        </div>

        <div class="floating-pill"></div>
        <div class="syringe"></div>

        <div class="mini-card card-bottom">
            <i class="bi bi-check-circle-fill"></i>
            <strong>Care & Protection</strong>
        </div>
    </div>
</section>

<section class="services" id="services">
    <span class="section-tag">WHAT WE OFFER</span>
    <h2>Healthcare Made Simpler</h2>
    <p class="section-desc">Everything you need to manage vaccination services.</p>

    <div class="service-grid">
        <div class="service-card">
            <div class="service-icon"><i class="bi bi-calendar2-check"></i></div>
            <h3>Appointments</h3>
            <p>Access your vaccination appointment management options in one place.</p>
            <a href="register.php" class="text-success fw-bold text-decoration-none">
                Get started <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="service-card">
            <div class="service-icon"><i class="bi bi-journal-medical"></i></div>
            <h3>Vaccination Records</h3>
            <p>Keep track of vaccination information through your account.</p>
            <a href="login.php" class="text-success fw-bold text-decoration-none">
                Login <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="service-card">
            <div class="service-icon"><i class="bi bi-hospital"></i></div>
            <h3>Hospital Portal</h3>
            <p>Give registered hospitals access to their dedicated portal.</p>
            <a href="hospital_login.php" class="text-success fw-bold text-decoration-none">
                Hospital Login <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="hospital-section" id="hospital">
    <h2>Healthcare Partners Welcome</h2>
    <p>Connect your hospital with our vaccination management platform.</p>

    <a href="hospital_register.php" class="primary-btn">
        Register Hospital <i class="bi bi-arrow-right"></i>
    </a>

    <a href="hospital_login.php" class="secondary-btn">
        Hospital Login
    </a>
</section>

<footer>
    <p class="mb-0">&copy; <?php echo $year; ?> E-Vaccine. Vaccination Management System.</p>
</footer>

<script>
const serviceCards = document.querySelectorAll(".service-card");

if ("IntersectionObserver" in window) {
    const cardObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    serviceCards.forEach((card, index) => {
        card.style.transitionDelay = (index * 150) + "ms";
        cardObserver.observe(card);
    });
} else {
    serviceCards.forEach(card => card.classList.add("show"));
}
</script>

</body>
</html>



<?php
// Base64 image ko PHP se save karna
$imagePath = "images/medical-model.jpg";

if (!file_exists($imagePath)) {
    if (!is_dir("images")) {
        mkdir("images", 0755, true);
    }

    // Yahan apni poori Base64 string paste karni hai
    $base64 = "data:image/jpeg;base64,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";

    $base64 = preg_replace('/^data:image\/jpeg;base64,/', '', $base64);
    $imageData = base64_decode($base64, true);

    if ($imageData !== false) {
        file_put_contents($imagePath, $imageData);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-Vaccine | Home</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #f4fffd, #eaf5ff);
    color: #17394c;
}

nav {
    padding: 20px 8%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    box-shadow: 0 5px 20px #0000000c;
}

.logo {
    font-size: 27px;
    font-weight: bold;
    color: #079b91;
}

nav a {
    color: #17394c;
    text-decoration: none;
    margin-left: 20px;
}

.hero {
    min-height: 600px;
    padding: 60px 8%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
}

.content {
    flex: 1 1 350px;
    animation: slideLeft 1s ease;
}

h1 {
    font-size: clamp(38px, 5vw, 62px);
    line-height: 1.12;
}

h1 span {
    color: #079b91;
}

p {
    color: #627987;
    font-size: 17px;
    line-height: 1.8;
}

.btn {
    display: inline-block;
    padding: 14px 23px;
    background: #079b91;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    margin-top: 15px;
    transition: .3s;
}

.btn:hover {
    background: #087a91;
    transform: translateY(-4px);
}

.model-area {
    flex: 1 1 320px;
    min-height: 400px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    animation: slideRight 1.2s ease;
}

.model-glow {
    position: absolute;
    width: 300px;
    height: 300px;
    background: #5ce2d540;
    border-radius: 50%;
    filter: blur(40px);
}

.model {
    position: relative;
    width: 100%;
    max-width: 420px;
    height: 400px;
    object-fit: contain;
    border-radius: 25px;
    mix-blend-mode: multiply;
    filter: drop-shadow(0 20px 25px #087cbd30);
    animation: floating 4s ease-in-out infinite;
    transition: transform .4s;
}

.model:hover {
    transform: scale(1.05) rotateY(8deg);
}

.services {
    background: white;
    padding: 70px 8%;
    text-align: center;
}

.services h2 {
    font-size: 38px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-top: 35px;
}

.card {
    padding: 30px;
    border-radius: 18px;
    background: #f4fbff;
    border: 1px solid #e0efef;
    transition: .3s;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px #00000012;
}

.card h3 {
    color: #079b91;
}

footer {
    background: #17394c;
    color: white;
    text-align: center;
    padding: 22px;
}

footer p {
    color: white;
    margin: 0;
}

@keyframes floating {
    0%, 100% {
        transform: translateY(0) rotateY(-4deg);
    }
    50% {
        transform: translateY(-15px) rotateY(4deg);
    }
}

@keyframes slideLeft {
    from { opacity: 0; transform: translateX(-40px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideRight {
    from { opacity: 0; transform: translateX(40px); }
    to { opacity: 1; transform: translateX(0); }
}

@media(max-width: 700px) {
    nav {
        flex-direction: column;
        gap: 15px;
    }

    nav a {
        margin: 0 7px;
    }

    .hero {
        text-align: center;
    }

    .model {
        height: 320px;
    }
}
</style>
</head>

<body>

<nav>
    <div class="logo">E-Vaccine</div>
    <div>
        <a href="index.php">Home</a>
        <a href="#services">Services</a>
        <a href="hospital_login.php">Hospital Login</a>
    </div>
</nav>

<section class="hero">
    <div class="content">
        <h1>Protect Lives.<br>Build a <span>Healthier Future.</span></h1>

        <p>
            Manage vaccination records and connect with healthcare
            services through our smart vaccination management system.
        </p>

        <a class="btn" href="register.php">Get Started →</a>
    </div>

    <div class="model-area">
        <div class="model-glow"></div>

        <?php if (file_exists($imagePath)): ?>
            <img
                src="<?php echo htmlspecialchars($imagePath); ?>"
                class="model"
                alt="Medical vaccination model"
            >
        <?php else: ?>
            <p>Image save nahi hui. Base64 string check karein.</p>
        <?php endif; ?>
    </div>
</section>

<section class="services" id="services">
    <h2>Our Services</h2>

    <div class="cards">
        <div class="card">
            <h3>Vaccination Records</h3>
            <p>Manage vaccination information in one place.</p>
        </div>

        <div class="card">
            <h3>Appointments</h3>
            <p>Organize your vaccination appointments.</p>
        </div>

        <div class="card">
            <h3>Hospital Portal</h3>
            <p>Access hospital management services.</p>
            <a class="btn" href="hospital_login.php">Login</a>
        </div>
    </div>
</section>

<footer>
    <p>&copy; <?php echo date("Y"); ?> E-Vaccine. All rights reserved.</p>
</footer>

</body>
</html>

