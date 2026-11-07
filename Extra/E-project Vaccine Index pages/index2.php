<?php
$pageTitle = "E-Vaccine | 3D Virus Experience";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?></title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --cyan: #4df4df;
    --blue: #4bafff;
    --bg: #04121f;
}

html { scroll-behavior: smooth; }

body {
    font-family: Arial, Helvetica, sans-serif;
    background: var(--bg);
    color: #fff;
    overflow-x: hidden;
    min-height: 100vh;
}

#virus-scene {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}

.background-overlay {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background:
        radial-gradient(ellipse at 75% 45%,
            rgba(0, 170, 190, .10), transparent 42%),
        linear-gradient(90deg,
            rgba(4, 18, 31, .98) 0%,
            rgba(4, 18, 31, .83) 42%,
            rgba(4, 18, 31, .30) 100%);
}

nav, main, footer {
    position: relative;
    z-index: 1;
}

nav {
    min-height: 82px;
    padding: 18px 8%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    background: rgba(3, 15, 26, .68);
    border-bottom: 1px solid rgba(77, 244, 223, .15);
    backdrop-filter: blur(12px);
}

.logo {
    font-size: 27px;
    font-weight: 800;
    letter-spacing: -.8px;
    color: var(--cyan);
}

.logo span { color: white; }

.nav-links {
    display: flex;
    align-items: center;
    gap: 28px;
}

.nav-links a {
    text-decoration: none;
    color: #d2e8f1;
    font-size: 14px;
    transition: .3s;
}

.nav-links a:hover { color: var(--cyan); }

.nav-links .nav-btn {
    border: 1px solid var(--cyan);
    padding: 11px 17px;
    border-radius: 8px;
}

.hero {
    min-height: 660px;
    padding: 100px 8% 90px;
    display: flex;
    align-items: center;
}

.hero-content {
    max-width: 650px;
    animation: enterLeft 1s ease both;
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    border: 1px solid rgba(77, 244, 223, .28);
    background: rgba(77, 244, 223, .06);
    border-radius: 30px;
    color: var(--cyan);
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 28px;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: var(--cyan);
    border-radius: 50%;
    box-shadow: 0 0 14px var(--cyan);
    animation: blink 1.5s infinite;
}

h1 {
    font-size: clamp(42px, 6vw, 76px);
    line-height: 1.07;
    letter-spacing: -2.5px;
    margin-bottom: 25px;
}

h1 .gradient-text {
    background: linear-gradient(90deg, var(--cyan), var(--blue));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.description {
    max-width: 520px;
    color: #afc6d5;
    font-size: 17px;
    line-height: 1.9;
    margin-bottom: 30px;
}

.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 15px 23px;
    border-radius: 9px;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    transition: .3s;
}

.btn-primary {
    background: var(--cyan);
    color: #03201f;
    box-shadow: 0 0 25px rgba(77, 244, 223, .15);
}

.btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 35px rgba(77, 244, 223, .4);
}

.btn-secondary {
    border: 1px solid rgba(255,255,255,.25);
    color: white;
    background: rgba(255,255,255,.04);
}

.btn-secondary:hover {
    border-color: var(--cyan);
    color: var(--cyan);
}

.hero-note {
    color: #7898aa;
    font-size: 12px;
    margin-top: 27px;
    letter-spacing: .5px;
}

.services {
    padding: 75px 8% 90px;
    background: rgba(3, 16, 27, .85);
    border-top: 1px solid rgba(77,244,223,.12);
}

.section-label {
    color: var(--cyan);
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 3px;
    margin-bottom: 14px;
}

.services h2 {
    font-size: clamp(30px, 4vw, 44px);
    margin-bottom: 14px;
}

.section-description {
    color: #a5bdcc;
    line-height: 1.8;
    max-width: 600px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
    margin-top: 40px;
}

.card {
    padding: 29px;
    min-height: 210px;
    background: linear-gradient(
        145deg,
        rgba(20, 54, 72, .72),
        rgba(7, 27, 43, .85)
    );
    border: 1px solid rgba(111, 207, 224, .16);
    border-radius: 16px;
    transition: .3s;
}

.card:hover {
    transform: translateY(-7px);
    border-color: rgba(77,244,223,.6);
    box-shadow: 0 15px 45px rgba(0,0,0,.2);
}

.card-icon {
    font-size: 29px;
    margin-bottom: 20px;
}

.card h3 {
    font-size: 19px;
    margin-bottom: 12px;
    color: #e9ffff;
}

.card p {
    color: #9bb5c5;
    line-height: 1.8;
    font-size: 14px;
}

footer {
    text-align: center;
    padding: 24px 8%;
    background: rgba(2, 11, 19, .96);
    border-top: 1px solid rgba(77,244,223,.12);
    color: #8ba7b7;
    font-size: 13px;
}

@keyframes enterLeft {
    from {
        opacity: 0;
        transform: translateX(-35px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: .3; }
}

@media (max-width: 800px) {
    nav {
        padding: 18px 5%;
        flex-wrap: wrap;
    }

    .nav-links {
        gap: 14px;
        flex-wrap: wrap;
    }

    .nav-links a { font-size: 12px; }

    .hero {
        min-height: 650px;
        padding: 85px 6%;
    }

    .background-overlay {
        background: linear-gradient(
            90deg,
            rgba(4,18,31,.93),
            rgba(4,18,31,.62)
        );
    }

    .cards { grid-template-columns: 1fr; }
    h1 { letter-spacing: -1px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
</head>

<body>

<div id="virus-scene"></div>
<div class="background-overlay"></div>

<nav>
    <div class="logo">E-<span>Vaccine</span></div>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="#services">Services</a>
        <a href="register.php">Register</a>
        <a href="hospital_login.php" class="nav-btn">Hospital Login ↗</a>
    </div>
</nav>

<main>
    <section class="hero">
        <div class="hero-content">

            <div class="eyebrow">
                <span class="live-dot"></span>
                Smart Vaccination Platform
            </div>

            <h1>
                Protect Lives.<br>
                Build a<br>
                <span class="gradient-text">Healthier Future.</span>
            </h1>

            <p class="description">
                Welcome to E-Vaccine, a digital vaccination management
                platform designed to help organize vaccination records,
                appointments and healthcare services in one place.
            </p>

            <div class="actions">
                <a href="register.php" class="btn btn-primary">
                    register your childern
                </a>

                <a href="#services" class="btn btn-secondary">
                    know more about us
                </a>
            </div>

            <p class="hero-note">
                ✦ &nbsp; DIGITAL HEALTHCARE &nbsp; · &nbsp; SMART RECORDS
            </p>
        </div>
    </section>

    <section class="services" id="services">
        <p class="section-label">Our Platform</p>
        <h2>Healthcare, Reimagined.</h2>

        <p class="section-description">
            Simple digital tools to make vaccination management
            more organized and accessible.
        </p>

        <div class="cards">

            <div class="card">
                <div class="card-icon">✚</div>
                <h3>Vaccination Records</h3>
                <p>
                    Keep vaccination details organized through
                    a digital record management system.
                </p>
            </div>

            <div class="card">
                <div class="card-icon">◷</div>
                <h3>Appointments</h3>
                <p>
                    Build a convenient workflow for managing
                    vaccination appointment information.
                </p>
            </div>

            <div class="card">
                <div class="card-icon">⌂</div>
                <h3>Hospital Portal</h3>
                <p>
                    Give authorized hospital staff access to
                    the relevant management features.
                </p>
                <br>
                <a href="hospital_login.php" class="btn btn-secondary">
                    Hospital Login →
                </a>
            </div>

        </div>
    </section>
</main>

<footer>
    &copy; <?php echo date("Y"); ?> E-Vaccine. All rights reserved.
</footer>

<script type="module">
import * as THREE from
'https://cdn.jsdelivr.net/npm/three@0.166.1/build/three.module.js';

const container = document.getElementById('virus-scene');

const scene = new THREE.Scene();

const camera = new THREE.PerspectiveCamera(
    45,
    window.innerWidth / window.innerHeight,
    0.1,
    100
);

camera.position.z = 11;

const renderer = new THREE.WebGLRenderer({
    alpha: true,
    antialias: true
});

renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.outputColorSpace = THREE.SRGBColorSpace;

container.appendChild(renderer.domElement);

// Lighting
scene.add(new THREE.AmbientLight(0x8beaff, 2.0));

const cyanLight = new THREE.PointLight(0x35ffe0, 65);
cyanLight.position.set(4, 4, 5);
scene.add(cyanLight);

const blueLight = new THREE.PointLight(0x368bff, 50);
blueLight.position.set(-5, -3, 4);
scene.add(blueLight);

// Main virus group
const virus = new THREE.Group();
scene.add(virus);
virus.position.set(3.2, 0.1, 0);

// Main spherical virus body
const bodyGeometry = new THREE.IcosahedronGeometry(1.65, 5);

const bodyMaterial = new THREE.MeshPhysicalMaterial({
    color: 0x15bfc3,
    roughness: 0.25,
    metalness: 0.12,
    transmission: 0.12,
    thickness: 0.7,
    clearcoat: 1,
    clearcoatRoughness: 0.15,
    emissive: 0x003e4a,
    emissiveIntensity: 0.6
});

const body = new THREE.Mesh(bodyGeometry, bodyMaterial);
virus.add(body);

// Inner glow sphere
const innerGeometry = new THREE.SphereGeometry(1.38, 48, 48);

const innerMaterial = new THREE.MeshBasicMaterial({
    color: 0x21d8d0,
    transparent: true,
    opacity: 0.12,
    side: THREE.BackSide
});

const innerGlow = new THREE.Mesh(innerGeometry, innerMaterial);
virus.add(innerGlow);

// Virus spike materials
const spikeMaterial = new THREE.MeshStandardMaterial({
    color: 0x7bfff0,
    roughness: 0.23,
    metalness: 0.12,
    emissive: 0x087e8b,
    emissiveIntensity: 0.8
});

const spikeTipMaterial = new THREE.MeshStandardMaterial({
    color: 0x63bfff,
    roughness: 0.2,
    metalness: 0.1,
    emissive: 0x104f9c,
    emissiveIntensity: 0.65
});

const spikeStemGeometry = new THREE.CylinderGeometry(
    0.065, 0.105, 0.38, 10
);

const spikeTipGeometry = new THREE.SphereGeometry(
    0.135, 12, 12
);

// Distribute virus spikes around the sphere
const spikeCount = 150;
const goldenAngle = Math.PI * (3 - Math.sqrt(5));

for (let i = 0; i < spikeCount; i++) {
    const y = 1 - (i / (spikeCount - 1)) * 2;
    const radius = Math.sqrt(1 - y * y);
    const theta = goldenAngle * i;

    const x = Math.cos(theta) * radius;
    const z = Math.sin(theta) * radius;

    const direction = new THREE.Vector3(x, y, z).normalize();
    const spike = new THREE.Group();

    const stem = new THREE.Mesh(
        spikeStemGeometry,
        spikeMaterial
    );

    stem.position.y = 0.16;
    spike.add(stem);

    const tip = new THREE.Mesh(
        spikeTipGeometry,
        spikeTipMaterial
    );

    tip.position.y = 0.39;
    spike.add(tip);

    spike.quaternion.setFromUnitVectors(
        new THREE.Vector3(0, 1, 0),
        direction
    );

    spike.position.copy(direction).multiplyScalar(1.58);
    virus.add(spike);
}

// Outer virus wireframe
const wireGeometry = new THREE.IcosahedronGeometry(1.72, 2);

const wireMaterial = new THREE.MeshBasicMaterial({
    color: 0x5af8e6,
    wireframe: true,
    transparent: true,
    opacity: 0.12
});

const wireframe = new THREE.Mesh(wireGeometry, wireMaterial);
virus.add(wireframe);

// Orbiting virus particles
const orbitGroup = new THREE.Group();
virus.add(orbitGroup);

const particleGeometry = new THREE.SphereGeometry(0.035, 8, 8);

const particleMaterial = new THREE.MeshBasicMaterial({
    color: 0x8bfff5
});

for (let i = 0; i < 35; i++) {
    const particle = new THREE.Mesh(
        particleGeometry,
        particleMaterial
    );

    const angle = (i / 35) * Math.PI * 2;
    const radius = 2.05 + Math.random() * 0.45;

    particle.position.set(
        Math.cos(angle) * radius,
        (Math.random() - 0.5) * 3.5,
        Math.sin(angle) * radius
    );

    orbitGroup.add(particle);
}

// Background particles
const starCount = 800;
const starPositions = new Float32Array(starCount * 3);

for (let i = 0; i < starCount; i++) {
    starPositions[i * 3] = (Math.random() - 0.5) * 24;
    starPositions[i * 3 + 1] = (Math.random() - 0.5) * 16;
    starPositions[i * 3 + 2] = -2 - Math.random() * 14;
}

const starGeometry = new THREE.BufferGeometry();

starGeometry.setAttribute(
    'position',
    new THREE.BufferAttribute(starPositions, 3)
);

const starMaterial = new THREE.PointsMaterial({
    color: 0x69dcd9,
    size: 0.025,
    transparent: true,
    opacity: 0.7
});

const stars = new THREE.Points(starGeometry, starMaterial);
scene.add(stars);

// =====================================
// RED BLOOD CELLS - GREEN CELLS REMOVED
// =====================================

const redBloodCells = new THREE.Group();
scene.add(redBloodCells);

const redCellItems = [];

const redCellMaterial = new THREE.MeshPhysicalMaterial({
    color: 0xd7193f,
    roughness: 0.38,
    metalness: 0.03,
    emissive: 0x35000a,
    emissiveIntensity: 0.25
});

const redCellCenterMaterial = new THREE.MeshStandardMaterial({
    color: 0x850d28,
    roughness: 0.55,
    metalness: 0.02
});

// Create 22 red blood cells
for (let i = 0; i < 22; i++) {
    const cellGroup = new THREE.Group();
    const size = 0.22 + Math.random() * 0.18;

    // Main flattened red blood cell
    const cell = new THREE.Mesh(
        new THREE.SphereGeometry(size, 24, 16),
        redCellMaterial
    );

    cell.scale.set(1.25, 1.0, 0.38);
    cellGroup.add(cell);

    // Dark center for the biconcave appearance
    const center = new THREE.Mesh(
        new THREE.SphereGeometry(size * 0.48, 20, 12),
        redCellCenterMaterial
    );

    center.scale.set(1.1, 1.0, 0.22);
    center.position.z = size * 0.12;
    cellGroup.add(center);

    // Position cells around the screen
    cellGroup.position.set(
        (Math.random() - 0.5) * 18,
        (Math.random() - 0.5) * 10,
        -1.5 - Math.random() * 5
    );

    cellGroup.rotation.set(
        Math.random() * Math.PI,
        Math.random() * Math.PI,
        Math.random() * Math.PI
    );

    redBloodCells.add(cellGroup);

    redCellItems.push({
        mesh: cellGroup,
        phase: Math.random() * Math.PI * 2,
        drift: 0.15 + Math.random() * 0.25
    });
}

// Mouse movement interaction
let mouseX = 0;
let mouseY = 0;

window.addEventListener('pointermove', (event) => {
    mouseX = (event.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (event.clientY / window.innerHeight - 0.5) * 2;
});

// Responsive canvas
function resizeScene() {
    const width = window.innerWidth;
    const height = window.innerHeight;

    camera.aspect = width / height;
    camera.position.z = width < 600 ? 13 : 11;
    camera.updateProjectionMatrix();

    renderer.setSize(width, height);

    if (width < 800) {
        virus.position.x = 1.0;
        virus.scale.setScalar(0.78);
    } else {
        virus.position.x = 3.2;
        virus.scale.setScalar(1);
    }
}

window.addEventListener('resize', resizeScene);
resizeScene();

// Animation loop
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);

    const time = clock.getElapsedTime();

    // Rotate the virus
    virus.rotation.y = time * 0.24 + mouseX * 0.18;
    virus.rotation.x = Math.sin(time * 0.35) * 0.12
                     + mouseY * 0.12;
    virus.rotation.z = Math.sin(time * 0.25) * 0.08;

    // Floating virus movement
    virus.position.y = 0.1 + Math.sin(time * 0.8) * 0.22;

    // Rotate outer wireframe
    wireframe.rotation.y = -time * 0.12;
    wireframe.rotation.x = time * 0.08;

    // Rotate orbiting particles
    orbitGroup.rotation.y = time * 0.35;
    orbitGroup.rotation.x = Math.sin(time * 0.4) * 0.15;

    // Background star movement
    stars.rotation.y = mouseX * 0.025 + time * 0.008;
    stars.rotation.x = mouseY * 0.02;

    // Animate red blood cells
    redCellItems.forEach((item) => {
        item.mesh.rotation.x += 0.002;
        item.mesh.rotation.y += 0.003;
        item.mesh.rotation.z += 0.0015;

        item.mesh.position.y +=
            Math.sin(time * 0.6 + item.phase) * 0.0015;

        item.mesh.position.x +=
            Math.sin(time * 0.35 + item.phase) * 0.001 * item.drift;
    });

    renderer.render(scene, camera);
}

animate();
</script>

</body>
</html>