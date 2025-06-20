<?php

use Proxy\Api\Api;

Api::__constructStatic();
//This is the default page META tags. You can specify custom META tags on the individual pages that with overwrite these defaults.
$DefaultMetaPageTitle = "MyVirtualAirline - vaBase.com - Virtual Airline Management Software";
$DefaultMetaPageDescription = "MyVirtualAirline default website description.";
$DefaultMetaPageKeywords = "";
?>
<?php
if (isset($_SESSION['pilotid'])) {
        $pid = $_SESSION['pilotid'];
        $unreadCount = 0;
        $res = Api::sendAsync('GET', 'v1/mailbox/unreads', null);
        if ($res->getStatusCode() == 200) {
                $unreadCount = $res->getBody();
        }
}
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
        <title><?php echo (!isset($MetaPageTitle) || $MetaPageTitle == "" ? $DefaultMetaPageTitle : $MetaPageTitle); ?>
        </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <meta name="description"
                content="<?php echo (!isset($MetaPageDescription) || $MetaPageDescription == "" ? $DefaultMetaPageDescription : $MetaPageDescription); ?>">
        <meta name="keywords"
                content="<?php echo (!isset($MetaPageKeywords) || $MetaPageKeywords == "" ? $DefaultMetaPageKeywords : $MetaPageKeywords); ?>">
        <meta name="author" content="vaBase.com">
        <link rel="shortcut icon" href="<?php echo website_base_url; ?>favicon.ico">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Oswald:wght@200..700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/font-awesome/css/font-awesome.min.css">
        <link id="stylesheet" rel="stylesheet" href="<?php echo website_base_url; ?>assets/css/styles.css">
        <script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/jquery-3.7.1.min.js">
        </script>
        
        <style>
#logo-image {
  max-height: 40px;
  height: auto;
  width: auto;
}
.nav-item a{
  font-weight: 800;
  text-transform: uppercase;
}

@media (max-width: 500px) {
  #logo-image {
    max-height: 24px;
  }
}

@media (min-width: 768px) {
  #logo-image {
    max-height: 40px;
  }
}

/* Custom navbar styles */
.navbar-custom {
  background: transparent;
  border: none;
  /* padding: 15px 0; */
}

.navbar-custom .container {
  max-width: 1200px;
}

/* Desktop navbar - centered layout */
@media (min-width: 1200px) {
  .desktop-navbar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
  }
  
  .navbar-nav-desktop {
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  
  .navbar-nav-desktop .nav-item {
    position: relative;
  }
  
  .navbar-nav-desktop .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    display: none;
  }
  
  .navbar-nav-desktop .dropdown:hover .dropdown-menu {
    display: block;
  }
}

/* Mobile navbar */
@media (max-width: 1200px) {
  .desktop-navbar {
    display: none;
  }
  
  .mobile-navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
  }
  
  .mobile-navbar .logo {
    flex: 1;
  }
  
  .mobile-hamburger {
    background: none;
    border: none;
    font-size: 24px;
    color: #333;
    cursor: pointer;
    padding: 10px;
  }
}

/* Mobile drawer */
.mobile-drawer {
  position: fixed;
  top: 0;
  left: -300px;
  width: 300px;
  height: 100vh;
  background: #fff;
  z-index: 9999;
  transition: left 0.3s ease;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.mobile-drawer.open {
  left: 0;
}

.mobile-drawer-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 9998;
  display: none;
}

.mobile-drawer-overlay.open {
  display: block;
}

.mobile-drawer-header {
  padding: 20px;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.mobile-drawer-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.mobile-drawer-menu {
  padding: 0;
  margin: 0;
  list-style: none;
}

.mobile-drawer-menu .nav-item {
  border-bottom: 1px solid #eee;
}

.mobile-drawer-menu .nav-item > a {
  display: block;
  padding: 15px 20px;
  color: #fff;
  text-decoration: none;
  font-weight: 500;
}

.mobile-drawer-menu .nav-item > a:hover {
  background: #f8f9fa;
}

.mobile-drawer-submenu {
  background: #f8f9fa;
  padding: 0;
  margin: 0;
  list-style: none;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
}

.mobile-drawer-submenu.open {
  max-height: 500px;
}

.mobile-drawer-submenu .nav-item > a {
  padding: 12px 40px;
  font-size: 14px;
  font-weight: normal;
}

.mobile-submenu-toggle {
  float: right;
  background: none;
  border: none;
  color: #666;
  font-size: 12px;
  cursor: pointer;
}

.badge {
  background: #dc3545;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 10px;
  margin-left: 5px;
}

@media (min-width: 1200px) {
  .mobile-navbar,
  .mobile-drawer,
  .mobile-drawer-overlay {
    display: none !important;
  }
}
</style>
</head>

<body>
        <header id="header" class="header">
                <div class="container">
                        <nav class="navbar-custom">
                                <!-- Desktop Navbar -->
                                <div class="desktop-navbar d-none d-md-flex">
                                        <!-- Logo -->
                                        <div class="logo" style="padding-top: 0px">
                                                <a href="<?php echo website_base_url; ?>">
                                                        <img src="https://www.dalvirtual.com/flightcenter/images/fltctr_logo_400x32.png" alt="MyVirtualAirline Logo" id="logo-image">
                                                </a>
                                        </div>

                                        <!-- Navigation Items -->
                                        <ul class="navbar-nav-desktop">
                                                <li class="nav-item dropdown">
                                                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                                                Operations Center <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                                <li><a href="<?php echo website_base_url; ?>activities.php" class="js_showloader">Tours / Events</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>route_map.php" class="js_showloader">Route Map</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>flight_search.php" class="js_showloader">Schedule</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>bases.php">Bases</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>fleet.php" class="js_showloader">Fleet</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>live_flights.php" class="js_showloader">Live Flights</a></li>
                                                        </ul>
                                                </li>

                                                <li class="nav-item dropdown">
                                                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                                                About Us <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                                <li><a href="<?php echo website_base_url; ?>airline.php">Airline</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>leaderboard.php" class="js_showloader">Pilot Leaderboard</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>statistics.php" class="js_showloader">Statistics</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>roster.php" class="js_showloader">Roster</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>staff.php" class="js_showloader">Staff / Contact</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>ranks.php" class="js_showloader">Rank Structure</a></li>
                                                                <li><a href="<?php echo website_base_url; ?>awards.php" class="js_showloader">Awards</a></li>
                                                        </ul>
                                                </li>

                                                <?php if (!isset($_SESSION['pilotid'])) { ?>
                                                        <li class="nav-item">
                                                                <a class="nav-link" href="<?php echo website_base_url; ?>join.php" class="js_showloader">Join</a>
                                                        </li>
                                                        <li class="nav-item">
                                                                <a class="nav-link" href="<?php echo website_base_url; ?>authentication/login.php?crew" class="js_showloader">
                                                                        <i class="fa fa-sign-in"></i> Crew Check-In
                                                                </a>
                                                        </li>
                                                <?php } else { ?>
                                                        <li class="nav-item dropdown">
                                                                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                                                        <i class="fa fa-user"></i> <?php echo $_SESSION['name']; ?> <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                                </a>
                                                                <ul class="dropdown-menu">
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php" class="js_showloader">Dashboard</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/dispatch.php" class="js_showloader">Dispatch</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/pirep.php" class="js_showloader">Manual PIREP</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/logbook_map.php" class="js_showloader">Logbook Map</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/logbook.php?id=<?php echo $pid; ?>" class="js_showloader">Logbook & History</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/wallet.php" class="js_showloader">Wallet</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>profile.php?id=<?php echo $pid; ?>" class="js_showloader">My Profile</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>pilot_awards.php?id=<?php echo $pid; ?>" class="js_showloader">My Awards</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/downloads.php" class="js_showloader">Downloads</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/edit_profile.php" class="js_showloader">Edit Profile</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/inbox.php" class="js_showloader">Inbox <span class="badge"><?php echo $unreadCount ?></span></a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/send_message.php" class="js_showloader">Send Message</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>authentication/change_password.php" class="js_showloader">Change Password</a></li>
                                                                        <li><a href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php?function=logout" class="js_showloader">Logout</a></li>
                                                                </ul>
                                                        </li>
                                                        <?php if ($_SESSION['site_level'] == true) { ?>
                                                                <li class="nav-item">
                                                                        <a class="nav-link" href="<?php echo website_base_url; ?>admin" target="_blank">
                                                                                <i class="fa fa-cog"></i> Admin
                                                                        </a>
                                                                </li>
                                                        <?php } ?>
                                                <?php } ?>

                                                <li class="nav-item" style="min-width: 110px">
                                                        <a id="ct" class="nav-link"></a>
                                                </li>
                                        </ul>
                                </div>

                                <!-- Mobile Navbar -->
                                <div class="mobile-navbar d-md-none">
                                        <div class="logo" style="padding-top: 0px">
                                                <a href="<?php echo website_base_url; ?>">
                                                        <img src="https://www.dalvirtual.com/flightcenter/images/fltctr_logo_400x32.png" alt="MyVirtualAirline Logo" id="logo-image">
                                                </a>
                                        </div>
                                        <button class="mobile-hamburger" id="mobileMenuToggle">
                                                <i class="fa fa-bars"></i>
                                        </button>
                                </div>
                        </nav>
                </div>

                <!-- Mobile Drawer -->
                <div class="mobile-drawer" id="mobileDrawer">
                        <div class="mobile-drawer-header">
                                <h5>Menu</h5>
                                <button class="mobile-drawer-close" id="mobileDrawerClose">
                                        <i class="fa fa-times"></i>
                                </button>
                        </div>

                        <ul class="mobile-drawer-menu">
                                <li class="nav-item">
                                        <a href="#">
                                                Operations Center
                                                <button class="mobile-submenu-toggle" data-target="operations-submenu">
                                                        <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                </button>
                                        </a>
                                        <ul class="mobile-drawer-submenu" id="operations-submenu">
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>activities.php" class="js_showloader">Tours / Events</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>route_map.php" class="js_showloader">Route Map</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>flight_search.php" class="js_showloader">Schedule</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>bases.php">Bases</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>fleet.php" class="js_showloader">Fleet</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>live_flights.php" class="js_showloader">Live Flights</a></li>
                                        </ul>
                                </li>

                                <li class="nav-item">
                                        <a href="#">
                                                About Us
                                                <button class="mobile-submenu-toggle" data-target="about-submenu">
                                                        <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                </button>
                                        </a>
                                        <ul class="mobile-drawer-submenu" id="about-submenu">
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>airline.php">Airline</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>leaderboard.php" class="js_showloader">Pilot Leaderboard</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>statistics.php" class="js_showloader">Statistics</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>roster.php" class="js_showloader">Roster</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>staff.php" class="js_showloader">Staff / Contact</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>ranks.php" class="js_showloader">Rank Structure</a></li>
                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>awards.php" class="js_showloader">Awards</a></li>
                                        </ul>
                                </li>

                                <?php if (!isset($_SESSION['pilotid'])) { ?>
                                        <li class="nav-item">
                                                <a href="<?php echo website_base_url; ?>join.php" class="js_showloader">Join</a>
                                        </li>
                                        <li class="nav-item">
                                                <a href="<?php echo website_base_url; ?>authentication/login.php?crew" class="js_showloader">
                                                        <i class="fa fa-sign-in"></i> Crew Check-In
                                                </a>
                                        </li>
                                <?php } else { ?>
                                        <li class="nav-item">
                                                <a href="#">
                                                        <i class="fa fa-user"></i> <?php echo $_SESSION['name']; ?>
                                                        <button class="mobile-submenu-toggle" data-target="user-submenu">
                                                                <i class="fa fa-chevron-down" style="font-size: 10px"></i>
                                                        </button>
                                                </a>
                                                <ul class="mobile-drawer-submenu" id="user-submenu">
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php" class="js_showloader">Dashboard</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/dispatch.php" class="js_showloader">Dispatch</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/pirep.php" class="js_showloader">Manual PIREP</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/logbook_map.php" class="js_showloader">Logbook Map</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/logbook.php?id=<?php echo $pid; ?>" class="js_showloader">Logbook & History</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/wallet.php" class="js_showloader">Wallet</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>profile.php?id=<?php echo $pid; ?>" class="js_showloader">My Profile</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>pilot_awards.php?id=<?php echo $pid; ?>" class="js_showloader">My Awards</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/downloads.php" class="js_showloader">Downloads</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/edit_profile.php" class="js_showloader">Edit Profile</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/inbox.php" class="js_showloader">Inbox <span class="badge"><?php echo $unreadCount ?></span></a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/send_message.php" class="js_showloader">Send Message</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>authentication/change_password.php" class="js_showloader">Change Password</a></li>
                                                        <li class="nav-item"><a href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php?function=logout" class="js_showloader">Logout</a></li>
                                                </ul>
                                        </li>
                                        <?php if ($_SESSION['site_level'] == true) { ?>
                                                <li class="nav-item">
                                                        <a href="<?php echo website_base_url; ?>admin" target="_blank">
                                                                <i class="fa fa-cog"></i> Admin
                                                        </a>
                                                </li>
                                        <?php } ?>
                                <?php } ?>

                                <li class="nav-item" style="min-width: 110px">
                                        <a id="ct-mobile"></a>
                                </li>
                        </ul>
                </div>

                <!-- Mobile Drawer Overlay -->
                <div class="mobile-drawer-overlay" id="mobileDrawerOverlay"></div>
        </header>

        <script>
        $(document).ready(function() {
                
                const mobileMenuToggle = $('#mobileMenuToggle');
                const mobileDrawer = $('#mobileDrawer');
                const mobileDrawerClose = $('#mobileDrawerClose');
                const mobileDrawerOverlay = $('#mobileDrawerOverlay');

                
                mobileMenuToggle.on('click', function() {
                        mobileDrawer.addClass('open');
                        mobileDrawerOverlay.addClass('open');
                        $('body').css('overflow', 'hidden');
                });

                
                function closeMobileDrawer() {
                        mobileDrawer.removeClass('open');
                        mobileDrawerOverlay.removeClass('open');
                        $('body').css('overflow', '');
                }

                mobileDrawerClose.on('click', closeMobileDrawer);
                mobileDrawerOverlay.on('click', closeMobileDrawer);

                
                $('.mobile-submenu-toggle').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const targetId = $(this).data('target');
                        const submenu = $('#' + targetId);
                        const icon = $(this).find('i');
                        
                        if (submenu.hasClass('open')) {
                                submenu.removeClass('open');
                                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                        } else {
                                
                                $('.mobile-drawer-submenu').removeClass('open');
                                $('.mobile-submenu-toggle i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                                
                                
                                submenu.addClass('open');
                                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                        }
                });

                
                $('.mobile-drawer-menu a:not(.mobile-submenu-toggle)').on('click', function() {
                        if (!$(this).find('.mobile-submenu-toggle').length) {
                                closeMobileDrawer();
                        }
                });

                
                function updateTime() {
                        const now = new Date();
                        const timeString = now.toLocaleTimeString();
                        $('#ct').text(timeString);
                        $('#ct-mobile').text(timeString);
                }
                
                updateTime();
                setInterval(updateTime, 1000);
        });
        </script>

        <!-- Body content rendered here -->