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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description"
                content="<?php echo (!isset($MetaPageDescription) || $MetaPageDescription == "" ? $DefaultMetaPageDescription : $MetaPageDescription); ?>">
        <meta name="keywords"
                content="<?php echo (!isset($MetaPageKeywords) || $MetaPageKeywords == "" ? $DefaultMetaPageKeywords : $MetaPageKeywords); ?>">
        <meta name="author" content="vaBase.com">
        <link rel="shortcut icon" href="<?php echo website_base_url; ?>favicon.ico">
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/font-awesome/css/font-awesome.min.css">
        <link id="stylesheet" rel="stylesheet" href="<?php echo website_base_url; ?>assets/css/styles.css">
        <script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/jquery-3.7.1.min.js">
        </script>
</head>

<body>
        <header id="header" class="header">
                <div class="container">

                        <h1 class="logo pull-left">
                                <a href="<?php echo website_base_url; ?>">
                                        <span class="logo-title"><i class="fa fa-plane"></i> <?php echo virtual_airline_name ?></span>
                                </a>
                        </h1>

                        <nav id="main-nav" class="main-nav navbar-right" role="navigation">
                                <div class="navbar-header">
                                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                                                <span class="sr-only">Toggle navigation</span>
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                        </button>
                                </div>
                                <div class="navbar-collapse collapse" id="navbar-collapse">
                                        <ul class="nav navbar-nav">
                                                <li class="active nav-item sr-only"><a href="/">Home</a></li>
                                                <li class="nav-item dropdown">
                                                        <a class="dropdown-toggle" aria-expanded="false" aria-haspopup="true" role="button"
                                                                data-toggle="dropdown" href="#"><span>Operations Centre <span
                                                                                class="caret"></span></span></a>
                                                        <ul class="dropdown-menu">
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>activities.php"
                                                                                class="js_showloader">Tours
                                                                                / Events</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>route_map.php"
                                                                                class="js_showloader">Route Map</a>
                                                                </li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>flight_search.php"
                                                                                class="js_showloader">Schedule</a>
                                                                </li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>bases.php">
                                                                                Bases</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>fleet.php"
                                                                                class="js_showloader">
                                                                                Fleet</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>live_flights.php"
                                                                                class="js_showloader">Live
                                                                                Flights</a></li>
                                                        </ul>
                                                </li>
                                                <li class="nav-item dropdown">
                                                        <a class="dropdown-toggle" aria-expanded="false" aria-haspopup="true" role="button"
                                                                data-toggle="dropdown" href="#"><span>About Us <span class="caret"></span></span></a>
                                                        <ul class="dropdown-menu">
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>airline.php">
                                                                                Airline</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>leaderboard.php"
                                                                                class="js_showloader">
                                                                                Pilot Leaderboard</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>statistics.php"
                                                                                class="js_showloader">
                                                                                Statistics</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>roster.php"
                                                                                class="js_showloader">Roster</a>
                                                                </li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>staff.php"
                                                                                class="js_showloader">
                                                                                Staff / Contact</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>ranks.php"
                                                                                class="js_showloader">
                                                                                Rank Structure</a></li>
                                                                <li class="nav-item"><a href="<?php echo website_base_url; ?>awards.php"
                                                                                class="js_showloader">
                                                                                Awards</a></li>
                                                        </ul>
                                                </li>
                                                <?php if (!isset($_SESSION['pilotid'])) { ?>
                                                        <li class="nav-item"><a href="<?php echo website_base_url; ?>join.php"
                                                                        class="js_showloader">Join</a>
                                                        </li>
                                                <?php } ?>
                                                <?php if (!isset($_SESSION['pilotid'])) { ?>
                                                        <li class="nav-item">
                                                                <a href="<?php echo website_base_url; ?>authentication/login.php?crew"
                                                                        class="js_showloader"><i class="fa fa-sign-in" aria-hidden="true"></i> Crew Centre</a>
                                                        </li>
                                                <?php } else { ?>
                                                        <li class="nav-item dropdown">
                                                                <a class="dropdown-toggle" aria-expanded="false" aria-haspopup="true" role="button"
                                                                        data-toggle="dropdown" href="#"><span><i class="fa fa-user" aria-hidden="true"></i>
                                                                                <?php echo $_SESSION['name']; ?> <span class="caret"></span></span></a>
                                                                <ul class="dropdown-menu">
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php"
                                                                                        class="js_showloader">
                                                                                        Dashboard</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/dispatch.php"
                                                                                        class="js_showloader">
                                                                                        Dispatch</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/pirep.php"
                                                                                        class="js_showloader">Manual
                                                                                        PIREP</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/logbook_map.php"
                                                                                        class="js_showloader">
                                                                                        Logbook Map</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/logbook.php?id=<?php echo $pid; ?>"
                                                                                        class="js_showloader">
                                                                                        Logbook & History</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/wallet.php"
                                                                                        class="js_showloader">
                                                                                        Wallet</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>profile.php?id=<?php echo $pid; ?>"
                                                                                        class="js_showloader">
                                                                                        My Profile</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>pilot_awards.php?id=<?php echo $pid; ?>"
                                                                                        class="js_showloader">
                                                                                        My Awards</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/downloads.php"
                                                                                        class="js_showloader">
                                                                                        Downloads</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/edit_profile.php"
                                                                                        class="js_showloader">
                                                                                        Edit Profile</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/inbox.php"
                                                                                        class="js_showloader">
                                                                                        Inbox
                                                                                        <span class="badge"><?php echo $unreadCount ?></span>
                                                                                </a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/send_message.php"
                                                                                        class="js_showloader">Send
                                                                                        Message</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>authentication/change_password.php"
                                                                                        class="js_showloader">Change
                                                                                        Password</a></li>
                                                                        <li class="nav-item"><a
                                                                                        href="<?php echo website_base_url ?>site_pilot_functions/pilot_centre.php?function=logout"
                                                                                        class="js_showloader">Logout</a>
                                                                        </li>
                                                                </ul>
                                                        </li>
                                                        <?php if ($_SESSION['site_level'] == true) { ?>
                                                                <li class="nav-item">
                                                                        <a href="<?php echo website_base_url; ?>admin" target="_blank"><i class="fa fa-cog"
                                                                                        aria-hidden="true"></i> Admin</a>
                                                                </li>
                                                        <?php } ?>
                                                <?php } ?>
                                                <li class="nav-item">
                                                        <a id="ct" style="min-width:100px"></a>
                                                </li>
                                        </ul>
                                </div>
                        </nav>
                </div>
        </header>

        <!-- Body content rendered here -->