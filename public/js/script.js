// ==== PREVENT WHITE FLASH ON DARK MODE ====
// ==== DARK MODE TOGGLER ====
// Toggles dark mode on/off and saves preference in localStorage
function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');

    const darkModeIcon = document.getElementById('darkModeIcon');
    const darkModeText = document.getElementById('darkModeText');
    const darkModeIconMobile = document.getElementById('darkModeIconMobile');
    const darkModeTextMobile = document.getElementById('darkModeTextMobile');

    if (isDark) {
        darkModeIcon?.classList.replace('fa-moon', 'fa-sun');
        darkModeText && (darkModeText.textContent = 'Light Mode');
        darkModeIconMobile?.classList.replace('fa-moon', 'fa-sun');
        darkModeTextMobile && (darkModeTextMobile.textContent = 'Light Mode');
    } else {
        darkModeIcon?.classList.replace('fa-sun', 'fa-moon');
        darkModeText && (darkModeText.textContent = 'Dark Mode');
        darkModeIconMobile?.classList.replace('fa-sun', 'fa-moon');
        darkModeTextMobile && (darkModeTextMobile.textContent = 'Dark Mode');
    }

    // Reset dropdown icons and visibility
    document.getElementById('adminDropdown')?.classList.add('hidden');
    document.getElementById('adminDropdownMobile')?.classList.add('hidden');
    document.getElementById('adminDropdownIcon')?.classList.remove('fa-chevron-up');
    document.getElementById('adminDropdownIcon')?.classList.add('fa-chevron-down');
    document.getElementById('adminDropdownIconMobile')?.classList.remove('fa-chevron-up');
    document.getElementById('adminDropdownIconMobile')?.classList.add('fa-chevron-down');
}

// ==== PAGE INITIALIZATION ====
// Runs after DOM is fully loaded
window.addEventListener('DOMContentLoaded', () => {
    // Apply saved dark mode preference
    const darkModeSetting = localStorage.getItem('darkMode');
    const darkModeEnabled = darkModeSetting === 'enabled';

    if (darkModeEnabled) {
        document.documentElement.classList.add('dark');

        const darkModeIcon = document.getElementById('darkModeIcon');
        const darkModeText = document.getElementById('darkModeText');
        const darkModeIconMobile = document.getElementById('darkModeIconMobile');
        const darkModeTextMobile = document.getElementById('darkModeTextMobile');

        darkModeIcon?.classList.replace('fa-moon', 'fa-sun');
        darkModeText && (darkModeText.textContent = 'Light Mode');
        darkModeIconMobile?.classList.replace('fa-moon', 'fa-sun');
        darkModeTextMobile && (darkModeTextMobile.textContent = 'Light Mode');
    }

    // Set up event listeners for dark mode toggles
    document.getElementById('darkModeToggle')?.addEventListener('click', toggleDarkMode);
    document.getElementById('darkModeToggleMobile')?.addEventListener('click', toggleDarkMode);

    // Attach loader to all internal (same-origin) links
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', e => {
            const url = new URL(link.href);
            if (url.origin === location.origin) {
                showLoader();
            }
        });
    });
});

// ==== PAGE LOAD: HIDE LOADER ====
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.classList.add('opacity-0');
        setTimeout(() => loader.style.display = 'none', 300); // Matches fade duration
    }
});

// ==== SHOW LOADER FUNCTION ====
function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.display = 'flex';
        requestAnimationFrame(() => {
            loader.classList.remove('opacity-0');
        });
    }
}

// ==== LOGOUT FUNCTION ====
function logout() {
    showLoader();
    setTimeout(() => {
        window.location.href = '/';
    }, 100);
}

// ==== FALLBACK DARK MODE ENFORCER ====
if (localStorage.getItem('darkMode') === 'enabled') {
    document.documentElement.classList.add('dark');
}

// ==== SIDE PANEL ====
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.getElementById("stickyHeader").style.display = "none";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("stickyHeader").style.display = "flex";
}

// ==== LOG OUT MODAL ====
function openLogoutModal() {
    closeNav();
    resetAdminDropdownMobile();
    document.getElementById("logoutModal").classList.remove("hidden");
}

function resetAdminDropdownMobile() {
    const dropdown = document.getElementById('adminDropdownMobile');
    const icon = document.getElementById('adminDropdownIconMobile');

    dropdown.classList.add('hidden');
    dropdown.classList.remove('dropdown-enter', 'dropdown-enter-active', 'dropdown-leave', 'dropdown-leave-active');

    if (icon.classList.contains('fa-chevron-up')) {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function closeLogoutModal() {
    document.getElementById("logoutModal").classList.add("hidden");
}

// ==== ADDITIONAL INFO FOR REGISTRATION ====
function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

// ==== ADMIN DROPDOWN ====
function toggleAdminDropdown() {
    const dropdown = document.getElementById('adminDropdown');
    const icon = document.getElementById('adminDropdownIcon');
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden', 'dropdown-leave', 'dropdown-leave-active');
        dropdown.classList.add('dropdown-enter');
        setTimeout(() => dropdown.classList.add('dropdown-enter-active'), 10);
        dropdown.dataset.dropdownOpen = "true";
    } else {
        dropdown.classList.remove('dropdown-enter', 'dropdown-enter-active');
        dropdown.classList.add('dropdown-leave');
        setTimeout(() => {
            dropdown.classList.add('dropdown-leave-active');
            setTimeout(() => dropdown.classList.add('hidden'), 300);
        }, 10);
        dropdown.dataset.dropdownOpen = "false";
    }

    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

function toggleAdminDropdownMobile() {
    const dropdown = document.getElementById('adminDropdownMobile');
    const icon = document.getElementById('adminDropdownIconMobile');
    const isHidden = dropdown.classList.contains('hidden');

    if (isHidden) {
        dropdown.classList.remove('hidden', 'dropdown-leave', 'dropdown-leave-active');
        dropdown.classList.add('dropdown-enter');
        setTimeout(() => dropdown.classList.add('dropdown-enter-active'), 10);
        dropdown.dataset.dropdownOpen = "true";
    } else {
        dropdown.classList.remove('dropdown-enter', 'dropdown-enter-active');
        dropdown.classList.add('dropdown-leave');
        setTimeout(() => {
            dropdown.classList.add('dropdown-leave-active');
            setTimeout(() => dropdown.classList.add('hidden'), 300);
        }, 10);
        dropdown.dataset.dropdownOpen = "false";
    }

    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

// ==== DARKMODE ====
// (These are now already inside DOMContentLoaded block above, so you can optionally remove these)
document.getElementById('darkModeToggle')?.addEventListener('click', toggleDarkMode);
document.getElementById('darkModeToggleMobile')?.addEventListener('click', toggleDarkMode);

// ==== RESIZE LISTENER FIX ====
window.addEventListener('resize', function () {
    const sidenav = document.getElementById("mySidenav");
    const stickyHeader = document.getElementById("stickyHeader");

    if (window.innerWidth >= 768) {
        // Desktop: hide sidenav if open and show sticky header
        sidenav.style.width = "0";
        stickyHeader.style.display = "flex";
    } else {
        // Mobile: ensure sidenav stays hidden unless user opens it
        if (sidenav.dataset.keepOpen === "true") {
            sidenav.style.width = "250px";
            stickyHeader.style.display = "none";
        } else {
            sidenav.style.width = "0";
            stickyHeader.style.display = "flex";
        }
    }

    // Restore admin dropdown state on resize
    const adminDropdown = document.getElementById('adminDropdown');
    const adminDropdownMobile = document.getElementById('adminDropdownMobile');

    if (adminDropdown && adminDropdown.dataset.dropdownOpen === "true") {
        adminDropdown.classList.remove('hidden');
    } else if (adminDropdown) {
        adminDropdown.classList.add('hidden');
    }

    if (adminDropdownMobile && adminDropdownMobile.dataset.dropdownOpen === "true") {
        adminDropdownMobile.classList.remove('hidden');
    } else if (adminDropdownMobile) {
        adminDropdownMobile.classList.add('hidden');
    }
});

