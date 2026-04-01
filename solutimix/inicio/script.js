document.addEventListener("DOMContentLoaded", function() {

    // --- Controle do Menu Mobile ---
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenuCloseBtn = document.getElementById("mobile-menu-close-btn");
    const mobileMenu = document.getElementById("mobileMenu");
    const mobileNavLinks = document.querySelectorAll(".mobile-nav-link");

    const toggleMobileMenu = () => {
        if (mobileMenu) {
            mobileMenu.classList.toggle("active");
        }
    };

    if (mobileMenuBtn) mobileMenuBtn.addEventListener("click", toggleMobileMenu);
    if (mobileMenuCloseBtn) mobileMenuCloseBtn.addEventListener("click", toggleMobileMenu);

    // Fecha ao clicar em link
    mobileNavLinks.forEach((link) => {
        link.addEventListener("click", () => {
            if (mobileMenu && mobileMenu.classList.contains("active")) {
                toggleMobileMenu();
            }
        });
    });

    // --- Controle do Tema (Dark/Light) ---
    const themeToggleBtn = document.getElementById("theme-toggle-btn");

    const toggleTheme = () => {
        const body = document.body;
        const icon = themeToggleBtn.querySelector("i");

        body.classList.toggle("dark-mode");

        if (body.classList.contains("dark-mode")) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
            localStorage.setItem("theme", "dark");
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
            localStorage.setItem("theme", "light");
        }
    };

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener("click", toggleTheme);
    }

    // Carregar tema salvo
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
        if (themeToggleBtn) {
            const icon = themeToggleBtn.querySelector("i");
            if(icon) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        }
    }
});