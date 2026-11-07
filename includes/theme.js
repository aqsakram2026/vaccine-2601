(() => {
    const themeScript = document.currentScript;
    const siteRoot = themeScript ? new URL("../", themeScript.src) : new URL("/", window.location.href);
    let theme = "light";
    const cookies = document.cookie.split(";");
    for (const cookie of cookies) {
        const parts = cookie.trim().split("=");
        if (parts[0] === "evaccine_theme" && (parts[1] === "dark" || parts[1] === "light")) {
            theme = parts[1];
        }
    }
    document.documentElement.dataset.theme = theme;

    if (!document.querySelector('link[rel="icon"]')) {
        const favicon = document.createElement("link");
        favicon.rel = "icon";
        favicon.type = "image/png";
        favicon.href = new URL("images/Logo-2%20no_bg.png", siteRoot).href;
        document.head.append(favicon);
    }

    const splash = document.createElement("div");
    splash.className = "evax-splash";
    splash.setAttribute("role", "status");
    splash.setAttribute("aria-live", "polite");
    splash.setAttribute("aria-label", "Loading E-Vaccine");
    splash.innerHTML = `
        <div class="evax-splash-content">
            <img class="evax-splash-logo" src="${new URL("images/Logo-2%20no_bg.png", siteRoot).href}" alt="">
            <span class="evax-splash-name">E-Vaccine</span>
            <span class="evax-splash-tagline">Every dose, on schedule</span>
            <span class="evax-splash-indicator" aria-hidden="true"></span>
        </div>`;
    document.documentElement.classList.add("evax-loading");
    document.documentElement.setAttribute("aria-busy", "true");
    document.documentElement.append(splash);

    let splashDismissed = false;
    const revealPage = () => {
        if (splashDismissed) {
            return;
        }
        splashDismissed = true;
        const minimumDisplay = 450;
        const elapsed = performance.now() - splashStarted;
        const wait = Math.max(0, minimumDisplay - elapsed);
        window.setTimeout(() => {
            splash.classList.add("is-leaving");
            window.setTimeout(() => {
                splash.remove();
                document.documentElement.classList.remove("evax-loading");
                document.documentElement.removeAttribute("aria-busy");
            }, 350);
        }, wait);
    };
    const splashStarted = performance.now();
    window.addEventListener("load", revealPage, { once: true });
    window.setTimeout(revealPage, 1500);
    if (document.readyState === "complete") {
        revealPage();
    }

    const addPortalTools = () => {
        if (document.querySelector(".site-navbar") || document.querySelector(".evax-portal-tools")) {
            return;
        }

        const tools = document.createElement("div");
        tools.className = "evax-portal-tools";
        tools.setAttribute("role", "navigation");
        tools.setAttribute("aria-label", "Portal tools");

        const toggle = document.createElement("button");
        toggle.type = "button";
        toggle.className = "evax-theme-toggle";
        toggle.setAttribute("aria-pressed", String(theme === "dark"));

        const updateToggle = () => {
            const isDark = document.documentElement.dataset.theme === "dark";
            toggle.setAttribute("aria-pressed", String(isDark));
            toggle.setAttribute("aria-label", `Switch to ${isDark ? "light" : "dark"} theme`);
            toggle.innerHTML = isDark
                ? '<i class="bi bi-sun" aria-hidden="true"></i><span>Light</span>'
                : '<i class="bi bi-moon-stars" aria-hidden="true"></i><span>Dark</span>';
        };
        updateToggle();
        toggle.addEventListener("click", () => {
            theme = document.documentElement.dataset.theme === "dark" ? "light" : "dark";
            document.documentElement.dataset.theme = theme;
            document.cookie = `evaccine_theme=${theme}; Max-Age=31536000; Path=/; SameSite=Lax`;
            updateToggle();
            document.dispatchEvent(new CustomEvent("evax:themechange", { detail: { theme } }));
        });

        tools.append(toggle);
        document.body.append(tools);
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", addPortalTools, { once: true });
    } else {
        addPortalTools();
    }
})();
