<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sidenav shadow-right sidenav-light">
            <div class="sidenav-menu">
                <div class="nav accordion" id="accordionSidenav">
                    <div class="sidenav-menu-heading"></div>
                    <a class="nav-link" href="/admin/">
                        <div class="nav-link-icon"><i data-feather="activity"></i>
                        </div>
                        Dashboard
                    </a>
                    <div class="sidenav-menu-heading">Main</div>
                    <?php if (userHasPermission(2)) { ?>
                        <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                            data-bs-target="#collapseHr" aria-expanded="false" aria-controls="collapsePages">
                            <div class="nav-link-icon"><i data-feather="user"></i></div>
                            Human Resources
                            <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseHr" data-bs-parent="#accordionSidenav">
                            <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesMenu">
                                <a class="nav-link" href="/admin/pilots/applications.php">New/Suspended Pilots</a>
                                <a class="nav-link" href="/admin/pilots/index.php">Manage Pilots</a>
                                <a class="nav-link" href="/admin/pilots/activity.php">Pilot Activity</a>
                            </nav>
                        </div>
                    <?php } ?>
                    <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse"
                        data-bs-target="#collapseOps" aria-expanded="false" aria-controls="collapsePages">
                        <div class="nav-link-icon"><i data-feather="grid"></i></div>
                        Operations
                        <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseOps" data-bs-parent="#accordionSidenav">
                        <nav class="sidenav-menu-nested nav accordion" id="accordionSidenavPagesMenu">
                            <?php if (userHasPermission(7)) { ?>
                                <a class="nav-link" href="/admin/pireps/index.php">Pilot Reports</a>
                            <?php } ?>
                            <?php if (userHasPermission(11)) { ?>
                                <a class="nav-link" href="/admin/activity/index.php">Tours / Events</a>
                            <?php } ?>
                            <?php if (userHasPermission(10)) { ?>
                                <a class="nav-link" href="/admin/operations/bookings.php">Bookings</a>
                            <?php } ?>
                            <?php if (userHasPermission(4)) { ?>
                                <a class="nav-link" href="/admin/schedule/index.php">Schedule</a>
                            <?php } ?>
                            <?php if (userHasPermission(8)) { ?>
                                <a class="nav-link" href="/admin/bases/index.php">Bases</a>
                            <?php } ?>
                        </nav>
                    </div>
                    <?php if (userHasPermission(1)) { ?>
                        <a class="nav-link" href="/admin/fleet/index.php">
                            <div class="nav-link-icon"><i data-feather="send"></i></div>
                            Fleet
                        </a>
                    <?php } ?>
                    <?php if ($_SESSION['owner'] == 1) { ?>
                        <a class="nav-link" href="/admin/staff/index.php">
                            <div class="nav-link-icon"><i data-feather="shield"></i></div>
                            Staff
                        </a>
                    <?php } ?>
                    <?php if (userHasPermission(5)) { ?>
                        <a class="nav-link" href="/admin/awards/index.php">
                            <div class="nav-link-icon"><i data-feather="award"></i></div>
                            Awards
                        </a>
                    <?php } ?>
                    <?php if (userHasPermission(2)) { ?>
                        <a class="nav-link" href="/admin/ranks/index.php">
                            <div class="nav-link-icon"><i data-feather="star"></i></div>
                            Ranks
                        </a>
                    <?php } ?>
                    <?php if (userHasPermission(3)) { ?>
                        <a class="nav-link" href="/admin/news/index.php">
                            <div class="nav-link-icon"><i data-feather="align-left"></i></div>
                            News
                        </a>
                    <?php } ?>
                    <?php if (userHasPermission(6)) { ?>
                        <a class="nav-link" href="/admin/downloads/index.php">
                            <div class="nav-link-icon"><i data-feather="download"></i></div>
                            Downloads
                        </a>
                    <?php } ?>
                    <?php if (userHasPermission(9)) { ?>
                        <div class="sidenav-menu-heading">Utilities</div>
                        <a class="nav-link" href="/admin/utilities/mailinglist.php">
                            <div class="nav-link-icon"><i data-feather="bar-chart"></i></div>
                            Pilot Mailing List
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="sidenav-footer">
                <div class="sidenav-footer-content">
                    <div class="sidenav-footer-subtitle">Logged in as:</div>
                    <div class="sidenav-footer-title"><?php echo $_SESSION['name']; ?></div>
                </div>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">