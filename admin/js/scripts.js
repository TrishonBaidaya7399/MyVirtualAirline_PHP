    window.addEventListener('DOMContentLoaded', event => {
    // Activate feather
    feather.replace();

    // Enable tooltips globally
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enable popovers globally
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sidenav-toggled'));
        });
    }

    // Close side navigation when width < LG
    const sidenavContent = document.body.querySelector('#layoutSidenav_content');
    if (sidenavContent) {
        sidenavContent.addEventListener('click', event => {
            const BOOTSTRAP_LG_WIDTH = 992;
            if (window.innerWidth >= 992) {
                return;
            }
            if (document.body.classList.contains("sidenav-toggled")) {
                document.body.classList.toggle("sidenav-toggled");
            }
        });
    }

    // Add active state to sidbar nav links
    let activatedPath = window.location.pathname;

    if (activatedPath) {
        if(activatedPath == "admin/") activatedPath = "/admin/index.php";
         if(activatedPath.includes("admin/pilots/edit.php")) activatedPath = "/admin/pilots/index.php";
         if(activatedPath.includes("admin/pilots/review.php")) activatedPath = "/admin/pilots/applications.php";
         if(activatedPath.includes("admin/activity/edit.php")) activatedPath = "/admin/activity/index.php";
         if(activatedPath.includes("admin/activity/create.php")) activatedPath = "/admin/activity/index.php";
         if(activatedPath.includes("admin/activity/create_leg.php")) activatedPath = "/admin/activity/index.php";
         if(activatedPath.includes("admin/activity/edit_leg.php")) activatedPath = "/admin/activity/index.php";
         if(activatedPath.includes("admin/pireps/edit.php")) activatedPath = "/admin/pireps/index.php";
         if(activatedPath.includes("admin/schedule/edit.php")) activatedPath = "/admin/schedule/index.php";
         if(activatedPath.includes("admin/schedule/create.php")) activatedPath = "/admin/schedule/index.php";
         if(activatedPath.includes("admin/bases/edit.php")) activatedPath = "/admin/bases/index.php";
         if(activatedPath.includes("admin/fleet/edit.php")) activatedPath = "/admin/fleet/index.php";
         if(activatedPath.includes("admin/fleet/create.php")) activatedPath = "/admin/fleet/index.php";
         if(activatedPath.includes("admin/news/edit.php")) activatedPath = "/admin/news/index.php";
         if(activatedPath.includes("admin/ranks/edit.php")) activatedPath = "/admin/ranks/index.php";
         if(activatedPath.includes("admin/staff/edit.php")) activatedPath = "/admin/staff/index.php";
         if(activatedPath.includes("admin/awards/edit.php")) activatedPath = "/admin/awards/index.php";
         if(activatedPath.includes("admin/awards/assignaward.php")) activatedPath = "/admin/awards/index.php";
         if(activatedPath.includes("admin/downloads/edit.php")) activatedPath = "/admin/downloads/index.php";
        activatedPath = activatedPath;
    } else {
        activatedPath = 'index.html';
    }

    const targetAnchors = document.body.querySelectorAll('[href="' + activatedPath + '"].nav-link');

    targetAnchors.forEach(targetAnchor => {
        let parentNode = targetAnchor.parentNode;
        while (parentNode !== null && parentNode !== document.documentElement) {
            if (parentNode.classList.contains('collapse')) {
                parentNode.classList.add('show');
                const parentNavLink = document.body.querySelector(
                    '[data-bs-target="#' + parentNode.id + '"]'
                );
                parentNavLink.classList.remove('collapsed');
                parentNavLink.classList.add('active');
            }
            parentNode = parentNode.parentNode;
        }
        targetAnchor.classList.add('active');
    });
});
