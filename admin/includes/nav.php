<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo virtual_airline_name; ?> Admin | Virtual Airline Management</title>
    <link rel="icon" type="image/x-icon" href="<?php echo website_base_url; ?>favicon.ico" />
    <link href='//fonts.googleapis.com/css?family=Lato:300,400,500,300italic,400italic' rel='stylesheet'
        type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/jquery-3.7.1.min.js"></script>
    <script data-search-pseudo-elements defer
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
    <link href="https://cdn.datatables.net/2.1.3/css/dataTables.bootstrap5.css" rel="stylesheet" />
    <link href="<?php echo website_base_url ?>/admin/assets/css/styles.css" rel="stylesheet" />
</head>

<body class="nav-fixed">
    <nav class="topnav navbar navbar-expand shadow justify-content-between justify-content-sm-start navbar-light bg-blue"
        id="sidenavAccordion">
        <!-- Sidenav Toggle Button-->
        <button class="btn btn-icon btn-transparent-light order-1 order-lg-0 me-2 ms-lg-2 me-lg-0" id="sidebarToggle"><i
                data-feather="menu"></i></button>
        <a class="navbar-brand pe-3 ps-4 ps-lg-2"
            href="<?php echo website_base_url; ?>admin/"><?php echo virtual_airline_name; ?> Admin</a>

        <!-- Navbar Items-->
        <ul class="navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown no-caret d-none d-md-block me-3">
                <a class="nav-link dropdown-toggle" id="navbarDropdownDocs" href="javascript:void(0);" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="fw-500">Help & Docs</div>
                    <i class="fas fa-chevron-right dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0 me-sm-n15 me-lg-0 o-hidden animated--fade-in-up"
                    aria-labelledby="navbarDropdownDocs">
                    <a class="dropdown-item py-3" href="https://docs.vabase.com/" target="_blank">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="code"></i></div>
                        <div>
                            <div class="small text-gray-500">Documentation</div>
                            Code snippets and reference
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="https://discord.gg/KYDjfjZ7wD" target="_blank">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="message-square"></i>
                        </div>
                        <div>
                            <div class="small text-gray-500">Community Discord</div>
                            Get help from the community
                        </div>
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item py-3" href="mailto:support@vabase.com" target="_blank">
                        <div class="icon-stack bg-primary-soft text-primary me-4"><i data-feather="book"></i></div>
                        <div>
                            <div class="small text-gray-500">Suport</div>
                            Raise a suport ticket
                        </div>
                    </a>
                </div>
            </li>
            <!-- User Dropdown-->
            <li class="nav-item dropdown no-caret dropdown-user me-3 me-lg-4">
                <a class="btn btn-icon btn-transparent-dark dropdown-toggle" id="navbarDropdownUserImage"
                    href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <?php if (isset($_SESSION['profileImage'])) { ?>
                    <img class="img-fluid"
                        src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $_SESSION['profileImage']; ?>" />
                    <?php } else { ?>
                    <i class="fa fa-user-circle profile" style="font-size:35px;" aria-hidden="true"></i>
                    <?php } ?></a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow animated--fade-in-up"
                    aria-labelledby="navbarDropdownUserImage">
                    <a class="dropdown-item"
                        href="<?php echo website_base_url; ?>site_pilot_functions/pilot_centre.php?function=logout">
                        <div class="dropdown-item-icon"><i data-feather="log-out"></i></div>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>