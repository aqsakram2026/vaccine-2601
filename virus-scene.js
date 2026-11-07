import * as THREE from "https://cdn.jsdelivr.net/npm/three@0.166.1/build/three.module.js";

const container = document.getElementById("virus-scene");
if (!container) {
    throw new Error("The virus scene container is missing.");
}

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100);
const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.setClearColor(0x000000, 0);
container.appendChild(renderer.domElement);

scene.add(new THREE.AmbientLight(0xffc2c9, 1.8));
const redLight = new THREE.PointLight(0xff254f, 35);
redLight.position.set(4, 4, 5);
scene.add(redLight);
const roseLight = new THREE.PointLight(0xff8c78, 28);
roseLight.position.set(-5, -3, 4);
scene.add(roseLight);

const virusCount = 44;
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
for (let index = 0; index < spikesPerVirus; index++) {
    const y = 1 - (index / (spikesPerVirus - 1)) * 2;
    const radius = Math.sqrt(1 - y * y);
    const direction = new THREE.Vector3(
        Math.cos(goldenAngle * index) * radius,
        y,
        Math.sin(goldenAngle * index) * radius
    ).normalize();
    spikeDirections.push(direction);
    spikeOrientations.push(new THREE.Quaternion().setFromUnitVectors(up, direction));
}

const virusParticles = Array.from({ length: virusCount }, () => ({
    x: 0,
    y: 0,
    spreadX: Math.random() - 0.5,
    spreadY: Math.random() - 0.5,
    z: -1 - Math.random() * 6,
    size: 0.2 + Math.random() * 0.18,
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

const starCount = 220;
const starPositions = new Float32Array(starCount * 3);
const starGeometry = new THREE.BufferGeometry();
starGeometry.setAttribute("position", new THREE.BufferAttribute(starPositions, 3));
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

const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
let mouseX = 0;
let mouseY = 0;
let viewWidth = 1;
let viewHeight = 1;

function resizeScene() {
    const bounds = container.getBoundingClientRect();
    const width = Math.round(bounds.width);
    const height = Math.round(bounds.height);
    if (!width || !height) return;

    camera.aspect = width / height;
    camera.position.z = width < 600 ? 15 : 12;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
    viewHeight = 2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2))
        * (camera.position.z + 4);
    viewWidth = viewHeight * camera.aspect;
    virusParticles.forEach(particle => {
        particle.x = particle.spreadX * viewWidth;
        particle.y = particle.spreadY * viewHeight * 0.9;
        particle.flowBounds = viewWidth / 2 + 0.8;
    });

    for (let index = 0; index < starCount; index++) {
        starPositions[index * 3] = (Math.random() - 0.5) * viewWidth;
        starPositions[index * 3 + 1] = (Math.random() - 0.5) * viewHeight;
        starPositions[index * 3 + 2] = -2 - Math.random() * 14;
    }
    starGeometry.attributes.position.needsUpdate = true;
}

function render(time) {
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
            spikeRotation.copy(virusRotation).multiply(spikeOrientations[directionIndex]);
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

function animate(now) {
    render(now * 0.001);
    if (!reducedMotion && !document.hidden) {
        requestAnimationFrame(animate);
    } else if (!reducedMotion && document.hidden) {
        document.addEventListener("visibilitychange", () => {
            if (!document.hidden) requestAnimationFrame(animate);
        }, { once: true });
    }
}

window.addEventListener("pointermove", event => {
    mouseX = (event.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (event.clientY / window.innerHeight - 0.5) * 2;
}, { passive: true });
window.addEventListener("pointerleave", () => {
    mouseX = 0;
    mouseY = 0;
}, { passive: true });
window.addEventListener("resize", resizeScene, { passive: true });
if ("ResizeObserver" in window) {
    new ResizeObserver(resizeScene).observe(container);
}
resizeScene();
requestAnimationFrame(animate);
