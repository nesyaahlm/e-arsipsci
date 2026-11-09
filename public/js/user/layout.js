(function () {
    var root = document.documentElement;
    var toggle = document.querySelector('[data-ua-toggle]');
    var overlay = document.querySelector('[data-ua-overlay]');
    var sidebar = document.getElementById('ua-sidebar');
    var userBox = document.querySelector('[data-ua-user]');
    var userToggle = document.querySelector('[data-ua-user-toggle]');
    var userMenu = document.querySelector('[data-ua-user-menu]');
    var desktop = window.matchMedia('(min-width: 992px)');
    var storageKey = 'ua-sidebar-collapsed';

    function saveCollapsed(value) {
        try {
            localStorage.setItem(storageKey, value ? '1' : '0');
        } catch (e) {}
    }

    function isExpanded() {
        if (desktop.matches) {
            return !root.classList.contains('ua-sidebar-collapsed');
        }
        return root.classList.contains('ua-sidebar-open');
    }

    function syncAria() {
        if (toggle) {
            toggle.setAttribute('aria-expanded', isExpanded() ? 'true' : 'false');
        }
    }

    function closeSidebar() {
        root.classList.remove('ua-sidebar-open');
        syncAria();
    }

    function closeUserMenu() {
        if (userMenu && userToggle) {
            userMenu.hidden = true;
            userToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            if (desktop.matches) {
                saveCollapsed(root.classList.toggle('ua-sidebar-collapsed'));
            } else {
                root.classList.toggle('ua-sidebar-open');
            }
            syncAria();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    if (sidebar) {
        sidebar.addEventListener('click', function (event) {
            if (!desktop.matches && event.target.closest('a')) {
                closeSidebar();
            }
        });
    }

    if (userToggle && userMenu) {
        userToggle.addEventListener('click', function () {
            var willOpen = userMenu.hidden;
            userMenu.hidden = !willOpen;
            userToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    }

    document.addEventListener('click', function (event) {
        if (userBox && !userBox.contains(event.target)) {
            closeUserMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
            closeUserMenu();
        }
    });

    function onViewportChange() {
        closeSidebar();
    }

    if (desktop.addEventListener) {
        desktop.addEventListener('change', onViewportChange);
    } else if (desktop.addListener) {
        desktop.addListener(onViewportChange);
    }

    syncAria();
})();
