<div id="virus-scene" aria-hidden="true"></div>
<style>
    body {
        position: relative;
        isolation: isolate;
        background-color: transparent !important;
    }
    #virus-scene {
        position: fixed;
        inset: 0;
        z-index: -1;
        overflow: hidden;
        pointer-events: none;
        opacity: .28;
        background:
            radial-gradient(ellipse at 75% 45%, rgba(255, 88, 112, .12), transparent 48%),
            linear-gradient(135deg, rgba(247, 252, 255, .92), rgba(241, 250, 249, .88));
    }
    #virus-scene canvas {
        display: block;
        width: 100%;
        height: 100%;
    }
    body > .container,
    body > form {
        position: relative;
        z-index: 1;
    }
</style>
<script type="module" src="../virus-scene.js"></script>
