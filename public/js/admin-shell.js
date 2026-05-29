(function () {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const menuBtn = document.querySelector('[data-admin-sidebar-open]');

    if (!sidebar || !backdrop) {
        return;
    }

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.hidden = false;
        document.body.classList.add('admin-sidebar-open');
        menuBtn?.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.hidden = true;
        document.body.classList.remove('admin-sidebar-open');
        menuBtn?.setAttribute('aria-expanded', 'false');
    }

    document.querySelectorAll('[data-admin-sidebar-open]').forEach((el) => {
        el.addEventListener('click', openSidebar);
    });

    document.querySelectorAll('[data-admin-sidebar-close]').forEach((el) => {
        el.addEventListener('click', closeSidebar);
    });

    sidebar.querySelectorAll('.admin-sidebar__link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 991.98px)').matches) {
                closeSidebar();
            }
        });
    });

    window.addEventListener('resize', () => {
        if (window.matchMedia('(min-width: 992px)').matches) {
            closeSidebar();
        }
    });
})();
