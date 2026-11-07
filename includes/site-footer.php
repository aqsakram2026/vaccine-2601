<?php
$toastMessage = $toastMessage ?? '';
$toastTitle = $toastTitle ?? 'E-Vaccine';
$toastVariant = $toastVariant ?? 'success';
$feedbackFlash = null;
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['feedback_flash'])) {
    $feedbackFlash = $_SESSION['feedback_flash'];
}
if (is_array($feedbackFlash)) {
    $toastMessage = $feedbackFlash['message'] ?? $toastMessage;
    if (($feedbackFlash['variant'] ?? '') === 'success') {
        $toastTitle = 'Feedback received';
        $toastVariant = 'success';
    } else {
        $toastTitle = 'Could not submit feedback';
        $toastVariant = 'danger';
    }
    unset($_SESSION['feedback_flash']);
}
$toastDotColor = 'danger';
if ($toastVariant === 'success') {
    $toastDotColor = 'success';
}
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 pb-4">
            <div class="col-lg-6">
                <a class="footer-brand text-decoration-none" href="index.php"><img src="images/Logo-2%20no_bg.png" alt="">E-Vaccine</a>
                <p class="mt-3 mb-0 text-white-50">A connected vaccination journey for parents, hospitals and administrators.</p>
                <div class="footer-socials mt-4" aria-label="Social media links">
                    <a class="footer-social-link" href="https://www.facebook.com/login/" target="_blank" rel="noopener noreferrer" aria-label="Facebook sign in" title="Facebook sign in"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a class="footer-social-link" href="https://www.instagram.com/accounts/login/" target="_blank" rel="noopener noreferrer" aria-label="Instagram sign in" title="Instagram sign in"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a class="footer-social-link" href="https://x.com/i/flow/login" target="_blank" rel="noopener noreferrer" aria-label="X sign in" title="X sign in"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a class="footer-social-link" href="https://www.linkedin.com/login" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn sign in" title="LinkedIn sign in"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                    <a class="footer-social-link" href="https://www.tiktok.com/login" target="_blank" rel="noopener noreferrer" aria-label="TikTok sign in" title="TikTok sign in"><i class="bi bi-tiktok" aria-hidden="true"></i></a>
                    <a class="footer-social-link" href="https://wa.me/923022459123" target="_blank" rel="noopener noreferrer" aria-label="Chat with E-Vaccine on WhatsApp" title="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <h2 class="h6 text-white">Explore</h2>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About the platform</a></li>
                    <li><a href="awareness.php">Vaccine awareness</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="sitemap.php">Sitemap</a></li>
                    <li><a href="terms.php">Terms of Service &amp; Privacy</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h2 class="h6 text-white">Portals</h2>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="parents/parent-login.php">Parent sign in</a></li>
                    <li><a href="parents/parent.php">Parent registration</a></li>
                    <li><a href="hospital/hospital_login.php">Hospital sign in</a></li>
                    <li><a href="hospital/hospital_register.php">Hospital registration</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2 pt-3 small text-white-50">
            <span>&copy; <?= date('Y') ?> E-Vaccine. All rights reserved.</span>
            <span>Every child, every dose, on time.</span>
            <time class="footer-clock" id="footerDateTime" aria-label="Current local date and time"><i class="bi bi-clock" aria-hidden="true"></i><span>Local time</span></time>
        </div>
    </div>
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1090">
    <div id="siteToast" class="toast border-0 shadow" role="status" aria-live="polite" aria-atomic="true"
         data-message="<?= site_escape($toastMessage) ?>"
         data-title="<?= site_escape($toastTitle) ?>"
         data-variant="<?= site_escape($toastVariant) ?>">
        <div class="toast-header">
            <span class="rounded-circle bg-<?= $toastDotColor ?> me-2" style="width:.65rem;height:.65rem" aria-hidden="true"></span>
            <strong class="me-auto" data-toast-title><?= site_escape($toastTitle) ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" data-toast-message><?= site_escape($toastMessage) ?></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script>
(() => {
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (window.AOS) {
        AOS.init({ once: true, duration: 650, offset: 70, easing: "ease-out-cubic", disable: reducedMotion });
    }

    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(element => {
        new bootstrap.Popover(element, { trigger: "focus", placement: "top" });
    });

    const themeToggle = document.getElementById("themeToggle");
    const applyTheme = theme => {
        const isDark = theme === "dark";
        let appliedTheme = "light";
        let nextThemeLabel = "dark";
        let iconName = "moon-stars";
        let themeColorValue = "#f5fbff";
        if (isDark) {
            appliedTheme = "dark";
            nextThemeLabel = "light";
            iconName = "sun";
            themeColorValue = "#0c1b27";
        }
        document.documentElement.dataset.theme = appliedTheme;
        if (themeToggle) {
            themeToggle.setAttribute("aria-pressed", String(isDark));
            themeToggle.setAttribute("aria-label", `Switch to ${nextThemeLabel} theme`);
            const icon = themeToggle.querySelector("i");
            if (icon) {
                icon.className = `bi bi-${iconName}`;
            }
        }
        const themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.content = themeColorValue;
        }
        document.dispatchEvent(new CustomEvent("evax:themechange", { detail: { theme: appliedTheme } }));
    };
    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            let theme = "dark";
            if (document.documentElement.dataset.theme === "dark") {
                theme = "light";
            }
            applyTheme(theme);
            document.cookie = `evaccine_theme=${theme}; Max-Age=31536000; Path=/; SameSite=Lax`;
        });
    }

    const dateTime = document.getElementById("footerDateTime");
    if (dateTime) {
        const formatter = new Intl.DateTimeFormat(undefined, { dateStyle: "medium", timeStyle: "short" });
        const updateDateTime = () => {
            const now = new Date();
            dateTime.dateTime = now.toISOString();
            dateTime.querySelector("span").textContent = `Local time · ${formatter.format(now)}`;
        };
        updateDateTime();
        window.setInterval(updateDateTime, 60000);
    }

    const progressBar = document.getElementById("pageProgressBar");
    if (progressBar) {
        let progressFrame = 0;
        const updateProgress = () => {
            if (progressFrame) return;
            progressFrame = requestAnimationFrame(() => {
                const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
                let progress = 0;
                if (scrollableHeight > 0) {
                    progress = window.scrollY / scrollableHeight;
                }
                progressBar.style.transform = `scaleX(${Math.min(1, Math.max(0, progress))})`;
                progressFrame = 0;
            });
        };
        window.addEventListener("scroll", updateProgress, { passive: true });
        window.addEventListener("resize", updateProgress, { passive: true });
        updateProgress();
    }

    const searchInput = document.getElementById("siteSearch");
    const searchResults = document.getElementById("siteSearchResults");
    const searchItems = [
        { label: "Home", detail: "Overview and vaccine schedule", url: "index.php", terms: "home schedule vaccination ages" },
        { label: "About the platform", detail: "How the vaccination workflow works", url: "about.php", terms: "about workflow faq information" },
        { label: "Vaccine awareness", detail: "Learn about vaccine benefits and the diseases they help prevent", url: "awareness.php", terms: "vaccine awareness diseases prevention childhood immunization" },
        { label: "Contact", detail: "Send a question to the team", url: "contact.php", terms: "contact message support" },
        { label: "Sitemap", detail: "Browse pages and portals", url: "sitemap.php", terms: "sitemap pages links" },
        { label: "Terms & Privacy", detail: "Read terms, privacy and feedback details", url: "terms.php", terms: "terms privacy policy legal" },
        { label: "Parent sign in", detail: "Open the parent portal", url: "parents/parent-login.php", terms: "parent login sign in family" },
        { label: "Parent registration", detail: "Create a parent account", url: "parents/parent.php", terms: "parent register registration child" },
        { label: "Hospital sign in", detail: "Open the hospital portal", url: "hospital/hospital_login.php", terms: "hospital login staff" },
        { label: "Hospital registration", detail: "Apply to join the platform", url: "hospital/hospital_register.php", terms: "hospital register registration apply" },
        { label: "Hospital approval status", detail: "Check a hospital application", url: "hospital/hospital_status.php", terms: "hospital approval application status" },
    ];

    function renderSearchResults(query) {
        if (!searchResults) return;
        const normalized = query.trim().toLowerCase();
        const matches = normalized
            ? searchItems.filter(item => `${item.label} ${item.detail} ${item.terms}`.toLowerCase().includes(normalized)).slice(0, 6)
            : searchItems.slice(0, 5);
        searchResults.replaceChildren();

        if (matches.length === 0) {
            const empty = document.createElement("div");
            empty.className = "search-empty small";
            empty.textContent = "No matching pages. Try a portal or page name.";
            searchResults.append(empty);
        } else {
            matches.forEach(item => {
                const link = document.createElement("a");
                link.href = item.url;
                link.setAttribute("role", "option");
                const label = document.createElement("span");
                label.textContent = item.label;
                const detail = document.createElement("small");
                detail.textContent = item.detail;
                link.append(label, detail);
                searchResults.append(link);
            });
        }
        searchResults.classList.add("is-open");
    }

    if (searchInput && searchResults) {
        searchInput.addEventListener("focus", () => renderSearchResults(searchInput.value));
        searchInput.addEventListener("input", () => renderSearchResults(searchInput.value));
        searchInput.addEventListener("keydown", event => {
            if (event.key === "Escape") searchResults.classList.remove("is-open");
            if (event.key === "Enter") {
                const firstResult = searchResults.querySelector("a");
                if (firstResult) window.location.href = firstResult.href;
            }
        });
        document.addEventListener("click", event => {
            if (!event.target.closest(".site-search")) searchResults.classList.remove("is-open");
        });
    }

    const counters = document.querySelectorAll("[data-counter]");
    const countUp = element => {
        const target = Number(element.dataset.counter);
        const suffix = element.dataset.suffix || "";
        if (!Number.isFinite(target)) return;
        if (reducedMotion) {
            element.textContent = `${target}${suffix}`;
            return;
        }
        const duration = 1100;
        const start = performance.now();
        const step = now => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = `${Math.round(target * eased)}${suffix}`;
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    if (counters.length) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    countUp(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });
        counters.forEach(counter => counterObserver.observe(counter));
    }

    const toast = document.getElementById("siteToast");
    if (toast && toast.dataset.message) {
        if (window.Swal) {
            let toastIcon = "error";
            if (toast.dataset.variant === "success") {
                toastIcon = "success";
            }
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: toastIcon,
                title: toast.dataset.title,
                text: toast.dataset.message,
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true
            });
        } else {
            bootstrap.Toast.getOrCreateInstance(toast, { delay: 6000 }).show();
        }
    }

    const feedbackForm = document.getElementById("feedbackForm");
    if (feedbackForm && window.Swal) {
        feedbackForm.addEventListener("submit", event => {
            if (!feedbackForm.reportValidity()) {
                event.preventDefault();
                return;
            }
            event.preventDefault();
            Swal.fire({
                title: "Submit your review?",
                text: "It will be checked before being shown publicly.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Submit review",
                cancelButtonText: "Keep editing",
                confirmButtonColor: "#079b91"
            }).then(result => {
                if (result.isConfirmed) feedbackForm.submit();
            });
        });
    }
})();
</script>
</body>
</html>
