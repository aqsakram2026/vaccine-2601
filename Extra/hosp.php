
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

#virus-scene {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    opacity: .14;
    pointer-events: none;
}

#virus-scene canvas {
    display: block;
    width: 100%;
    height: 100%;
}

.hero,
.services,
.hospital-section,
footer {
    position: relative;
    z-index: 2;
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
        radial-gradient(circle at 80% 40%, rgba(185, 243, 235, .5), transparent 36%),
        linear-gradient(135deg, rgba(248, 255, 255, .9), rgba(238, 247, 255, .9));
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
    background: rgba(255, 255, 255, .94);
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

<div id="virus-scene" aria-hidden="true"></div>

<nav class="navbar-custom">
    <a class="logo" href="index.html">
        <i class="bi bi-shield-plus"></i> E-Vaccine
    </a>

    <div class="nav-links">
        <a href="index.html">Home</a>
        <a href="#services">Services</a>
        <a href="#hospital">Hospitals</a>
        <a class="nav-btn" href="hospital/hospital_login.php">Hospital Login <i class="bi bi-arrow-up-right"></i></a>
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

        <a href="parents/parent.php" class="primary-btn">
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
            <a href="parents/parent.php" class="text-success fw-bold text-decoration-none">
                Get started <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="service-card">
            <div class="service-icon"><i class="bi bi-journal-medical"></i></div>
            <h3>Vaccination Records</h3>
            <p>Keep track of vaccination information through your account.</p>
            <a href="parents/parent-login.php" class="text-success fw-bold text-decoration-none">
                Login <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="service-card">
            <div class="service-icon"><i class="bi bi-hospital"></i></div>
            <h3>Hospital Portal</h3>
            <p>Give registered hospitals access to their dedicated portal.</p>
            <a href="hospital/hospital_login.php" class="text-success fw-bold text-decoration-none">
                Hospital Login <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="hospital-section" id="hospital">
    <h2>Healthcare Partners Welcome</h2>
    <p>Connect your hospital with our vaccination management platform.</p>

    <a href="hospital/hospital_register.php" class="primary-btn">
        Register Hospital <i class="bi bi-arrow-right"></i>
    </a>

    <a href="hospital/hospital_login.php" class="secondary-btn">
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

<script type="module">
import * as THREE from
'https://cdn.jsdelivr.net/npm/three@0.166.1/build/three.module.js';

const container = document.getElementById("virus-scene");
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(
    45,
    window.innerWidth / window.innerHeight,
    0.1,
    100
);
camera.position.z = 11;

const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.outputColorSpace = THREE.SRGBColorSpace;
container.appendChild(renderer.domElement);

scene.add(new THREE.AmbientLight(0xffc2c9, 1.8));

const redLight = new THREE.PointLight(0xff254f, 35);
redLight.position.set(4, 4, 5);
scene.add(redLight);

const roseLight = new THREE.PointLight(0xff8c78, 28);
roseLight.position.set(-5, -3, 4);
scene.add(roseLight);

const virusCount = 32;
const spikesPerVirus = 16;
const bodyMaterial = new THREE.MeshPhysicalMaterial({
    color: 0xd7193f,
    roughness: 0.32,
    metalness: 0.03,
    emissive: 0x35000a,
    emissiveIntensity: 0.35,
    clearcoat: 0.7,
    clearcoatRoughness: 0.24
});
const spikeMaterial = new THREE.MeshStandardMaterial({
    color: 0xff6278,
    roughness: 0.28,
    metalness: 0.02,
    emissive: 0x6b071b,
    emissiveIntensity: 0.5
});
const tipMaterial = new THREE.MeshStandardMaterial({
    color: 0xffb0a7,
    roughness: 0.24,
    metalness: 0.02,
    emissive: 0x7a1825,
    emissiveIntensity: 0.45
});
const dimpleMaterial = new THREE.MeshStandardMaterial({
    color: 0x780d26,
    roughness: 0.48,
    metalness: 0,
    emissive: 0x26000a,
    emissiveIntensity: 0.2
});

const virusBodies = new THREE.InstancedMesh(
    new THREE.IcosahedronGeometry(1, 3),
    bodyMaterial,
    virusCount
);
const virusDimples = new THREE.InstancedMesh(
    new THREE.SphereGeometry(0.42, 12, 8),
    dimpleMaterial,
    virusCount
);
const virusStems = new THREE.InstancedMesh(
    new THREE.CylinderGeometry(0.045, 0.065, 0.2, 7),
    spikeMaterial,
    virusCount * spikesPerVirus
);
const virusTips = new THREE.InstancedMesh(
    new THREE.SphereGeometry(0.075, 8, 8),
    tipMaterial,
    virusCount * spikesPerVirus
);
scene.add(virusBodies, virusDimples, virusStems, virusTips);

const up = new THREE.Vector3(0, 1, 0);
const goldenAngle = Math.PI * (3 - Math.sqrt(5));
const spikeDirections = [];
const spikeOrientations = [];
for (let i = 0; i < spikesPerVirus; i++) {
    const y = 1 - (i / (spikesPerVirus - 1)) * 2;
    const radius = Math.sqrt(1 - y * y);
    const direction = new THREE.Vector3(
        Math.cos(goldenAngle * i) * radius,
        y,
        Math.sin(goldenAngle * i) * radius
    ).normalize();
    spikeDirections.push(direction);
    spikeOrientations.push(
        new THREE.Quaternion().setFromUnitVectors(up, direction)
    );
}

const virusParticles = Array.from({ length: virusCount }, () => ({
    x: 0,
    y: 0,
    spreadX: Math.random() - 0.5,
    spreadY: Math.random() - 0.5,
    z: -1 - Math.random() * 6,
    size: 0.17 + Math.random() * 0.16,
    phase: Math.random() * Math.PI * 2,
    drift: 0.35 + Math.random() * 0.4,
    flow: 0.22 + Math.random() * 0.4,
    rotationX: Math.random() * Math.PI,
    rotationY: Math.random() * Math.PI,
    rotationZ: Math.random() * Math.PI
}));
const transform = new THREE.Object3D();
const virusRotation = new THREE.Quaternion();
const spikeRotation = new THREE.Quaternion();
const rotatedDirection = new THREE.Vector3();
const position = new THREE.Vector3();
const dimpleNormal = new THREE.Vector3(0, 0, 1);
const dimpleDirection = new THREE.Vector3();
const dimpleScale = new THREE.Vector3(1, 1, 0.18);

const starCount = 500;
const starPositions = new Float32Array(starCount * 3);
for (let i = 0; i < starCount; i++) {
    starPositions[i * 3] = (Math.random() - 0.5) * 24;
    starPositions[i * 3 + 1] = (Math.random() - 0.5) * 16;
    starPositions[i * 3 + 2] = -2 - Math.random() * 14;
}
const starGeometry = new THREE.BufferGeometry();
starGeometry.setAttribute(
    "position",
    new THREE.BufferAttribute(starPositions, 3)
);
const stars = new THREE.Points(
    starGeometry,
    new THREE.PointsMaterial({
        color: 0xf17888,
        size: 0.035,
        transparent: true,
        opacity: 0.3
    })
);
scene.add(stars);

let mouseX = 0;
let mouseY = 0;
window.addEventListener("pointermove", event => {
    mouseX = (event.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (event.clientY / window.innerHeight - 0.5) * 2;
});

function resizeScene() {
    const width = window.innerWidth;
    const height = window.innerHeight;
    camera.aspect = width / height;
    camera.position.z = width < 600 ? 15 : 12;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
    const viewHeight = 2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2))
        * (camera.position.z + 4);
    const viewWidth = viewHeight * camera.aspect;
    virusParticles.forEach(particle => {
        particle.x = particle.spreadX * viewWidth;
        particle.y = particle.spreadY * viewHeight * 0.9;
        particle.flowBounds = viewWidth / 2 + 0.8;
    });
}

window.addEventListener("resize", resizeScene);
resizeScene();

const clock = new THREE.Clock();
function animate() {
    requestAnimationFrame(animate);
    const time = clock.getElapsedTime();
    let spikeIndex = 0;

    virusParticles.forEach((particle, index) => {
        const flowWidth = particle.flowBounds * 2;
        const flowX = (
            (particle.x + time * particle.flow + particle.flowBounds) % flowWidth
            + flowWidth
        ) % flowWidth - particle.flowBounds;
        position.set(
            flowX + Math.sin(time * particle.drift + particle.phase) * 0.35,
            particle.y + Math.sin(time * 0.7 + particle.phase) * 0.48,
            particle.z
        );
        virusRotation.setFromEuler(new THREE.Euler(
            particle.rotationX + time * 0.22 + mouseY * 0.12,
            particle.rotationY + time * 0.32 + mouseX * 0.12,
            particle.rotationZ + time * 0.18
        ));

        transform.position.copy(position);
        transform.quaternion.copy(virusRotation);
        transform.scale.setScalar(particle.size);
        transform.updateMatrix();
        virusBodies.setMatrixAt(index, transform.matrix);

        transform.position.copy(position).addScaledVector(
            dimpleDirection.copy(dimpleNormal).applyQuaternion(virusRotation),
            particle.size * 0.78
        );
        transform.quaternion.copy(virusRotation);
        transform.scale.copy(dimpleScale).multiplyScalar(particle.size);
        transform.updateMatrix();
        virusDimples.setMatrixAt(index, transform.matrix);

        spikeDirections.forEach((direction, directionIndex) => {
            rotatedDirection.copy(direction).applyQuaternion(virusRotation);

            transform.position.copy(position).addScaledVector(
                rotatedDirection,
                particle.size * 0.9
            );
            spikeRotation.copy(virusRotation).multiply(
                spikeOrientations[directionIndex]
            );
            transform.quaternion.copy(spikeRotation);
            transform.scale.setScalar(particle.size);
            transform.updateMatrix();
            virusStems.setMatrixAt(spikeIndex, transform.matrix);

            transform.position.copy(position).addScaledVector(
                rotatedDirection,
                particle.size * 1.08
            );
            transform.updateMatrix();
            virusTips.setMatrixAt(spikeIndex, transform.matrix);
            spikeIndex++;
        });
    });

    virusBodies.instanceMatrix.needsUpdate = true;
    virusDimples.instanceMatrix.needsUpdate = true;
    virusStems.instanceMatrix.needsUpdate = true;
    virusTips.instanceMatrix.needsUpdate = true;
    stars.rotation.y = mouseX * 0.025 + time * 0.008;
    stars.rotation.x = mouseY * 0.02;
    renderer.render(scene, camera);
}

animate();
</script>

</body>
</html>
