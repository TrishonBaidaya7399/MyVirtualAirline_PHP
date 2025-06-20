<?php

use Proxy\Api\Api;

Api::__constructStatic();
$obj = null;
$res = Api::sendSync('GET', 'v1/stats/latestflights', null);
if ($res->getStatusCode() == 200) {
	$obj = json_decode($res->getBody(), false);
}
?>

<style>

.arrivals-section {
    position: relative;
    
    
}

.arrivals-section .container {
    position: relative;
    z-index: 2;
}


.arrivals-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px
}

.arrivals-title {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: 2px;
    text-transform: capitalize;
    font-family: 'Montserrat', sans-serif;
}

.arrivals-icon {
    font-size: 2.5rem;
    color: #fff;
    opacity: 0.8;
}


.arrivals-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.arrivals-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.arrivals-table-wrapper {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}


.arrivals-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.arrivals-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.arrivals-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(0, 123, 255, 0.5);
    border-radius: 4px;
}

.arrivals-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 123, 255, 0.7);
}

.arrivals-table {
    margin: 0;
    background: transparent;
    color: #333;
    min-width: 800px; 
    width: 100%;
}

.arrivals-table thead th {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    font-weight: 600;
    border: none;
    padding: 15px 12px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.arrivals-table tbody tr {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.arrivals-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: scale(1.01);
}

.arrivals-table tbody td {
    padding: 12px;
    border: none;
    vertical-align: middle;
    font-size: 13px;
    color: #333;
}

.arrivals-table tbody tr:last-child {
    border-bottom: none;
}


.pilot-profile-image-small {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.profile-small {
    font-size: 24px;
    color: #666;
}


.arrivals-table a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.arrivals-table a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.arrivals-table i {
    margin-right: 5px;
    color: #666;
}


.arrivals-table .fa-check {
    color: #28a745;
    font-size: 16px;
}

.arrivals-table .fa-times {
    color: #dc3545;
    font-size: 16px;
}


.landing-rate-good {
    color: #28a745;
    font-weight: 600;
}

.landing-rate-normal {
    color: #ffc107;
    font-weight: 600;
}

.landing-rate-hard {
    color: #dc3545;
    font-weight: 600;
}


.no-data-message {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
}


@media (max-width: 1200px) {
    .arrivals-title {
        font-size: 2.5rem;
    }
    
    .arrivals-icon {
        font-size: 2rem;
    }
    
    .arrivals-table thead th,
    .arrivals-table tbody td {
        padding: 10px 8px;
        font-size: 12px;
    }
}

@media (max-width: 992px) {
    .arrivals-section {
        padding: 30px 0;
    }
    
    .arrivals-title {
        font-size: 2.2rem;
        text-align: left;
        margin-bottom: 20px;
    }
    
    .arrivals-icon {
        font-size: 2rem;
    }
    
    .arrivals-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .arrivals-table-wrapper {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .arrivals-section {
        padding: 25px 0;
    }
    
    .arrivals-title {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .arrivals-icon {
        font-size: 1.8rem;
    }
    
    .arrivals-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .arrivals-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .arrivals-table-wrapper {
        max-height: 350px;
    }
    
    .arrivals-table {
        font-size: 11px;
        min-width: 700px; 
    }
    
    .arrivals-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .arrivals-table tbody td {
        padding: 10px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .pilot-profile-image-small {
        width: 25px;
        height: 25px;
    }
    
    .profile-small {
        font-size: 20px;
    }
}

@media (max-width: 576px) {
    .arrivals-title {
        font-size: 1.8rem;
    }
    
    .arrivals-icon {
        font-size: 1.5rem;
    }
    
    .arrivals-title-wrapper {
        gap: 10px;
    }
    
    .arrivals-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .arrivals-table-wrapper {
        max-height: 300px;
    }
    
    .arrivals-table {
        font-size: 10px;
        min-width: 600px; 
    }
    
    .arrivals-table thead th {
        padding: 10px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .arrivals-table tbody td {
        padding: 8px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .arrivals-title {
        font-size: 1.6rem;
    }
    
    .arrivals-icon {
        font-size: 1.3rem;
    }
    
    .arrivals-section {
        padding: 20px 0;
    }
    
    .arrivals-table-wrapper {
        max-height: 250px;
    }
    
    .arrivals-table {
        min-width: 550px; 
    }
    
    .arrivals-table thead th {
        padding: 8px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .arrivals-table tbody td {
        padding: 6px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .no-data-message {
        padding: 30px 15px;
        font-size: 14px;
    }
}
</style>

<section class="arrivals-section">
    <div class="container">
        <!-- Arrivals Title - Outside Card -->
        <div class="arrivals-title-wrapper">
            <h3 class="arrivals-title">arrivals</h3>
            <i class="fa fa-plane-arrival arrivals-icon"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="arrivals-glass-card">
                    <div class="arrivals-table-wrapper">
                        <table class="table table-striped arrivals-table">
                            <thead>
                                <tr>
                                    <th><strong>Pilot</strong></th>
                                    <th><strong>Flight Number</strong></th>
                                    <th><strong>Dep ICAO</strong></th>
                                    <th><strong>Arr ICAO</strong></th>
                                    <th><strong>Aircraft</strong></th>
                                    <th class="text-center"><strong>ACARS</strong></th>
                                    <th class="text-center"><strong>Landing Rate</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($obj)) { ?>
                                <?php foreach ($obj as $key => $flight) { ?>
                                <tr>
                                    <td>
                                        <?php if ($flight->profileImage != "") { ?>
                                        <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $flight->profileImage; ?>"
                                            class="img-circle pilot-profile-image-small" />
                                        <?php } else { ?>
                                        <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                        <?php } ?>&nbsp;
                                        <a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $flight->pilot; ?>"
                                            class="js_showloader"><?php echo explode(" ", $flight->name)[0]; ?>
                                            (<?php echo $flight->callsign; ?>)</a>
                                    </td>
                                    <td><i class="fa fa-plane"></i> <a
                                            href="<?php echo website_base_url; ?>pirep_info.php?id=<?php echo $flight->id; ?>"
                                            class="js_showloader">
                                            <?php
													if ($flight->flightNumber == '') {
														echo  $flight->id;
													} else {
														echo  $flight->flightNumber;
													}
													?>
                                        </a></td>
                                    <td><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->depIcao; ?>"
                                            class="js_showloader"><?php echo $flight->depIcao; ?></a>
                                    </td>
                                    <td><i class="fa fa-map-marker"></i> <a
                                            href="airport_info.php?airport=<?php echo $flight->arrIcao; ?>"
                                            class="js_showloader"><?php echo $flight->arrIcao; ?></a>
                                    </td>
                                    <td><span
                                            title="<?php echo $flight->aircraft; ?>"><?php echo limit($flight->aircraft, 25); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php echo !$flight->acarsFlight ? "<i class=\"fa fa-times\" title=\"Not Acars recorded flight\"></i>" : "<i class=\"fa fa-check\" title=\"Acars recorded flight\"></i>"; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        if (empty($flight->landingRate)) {
                                            echo "N/A";
                                        } else if ($flight->landingRate < 0) {
                                            $landingRate = abs($flight->landingRate);
                                            $class = '';
                                            if ($landingRate <= 100) {
                                                $class = 'landing-rate-good';
                                            } else if ($landingRate <= 200) {
                                                $class = 'landing-rate-normal';
                                            } else {
                                                $class = 'landing-rate-hard';
                                            }
                                            echo "<span class='{$class}'>" . number_format($flight->landingRate) . "fpm</span>";
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="7" class="no-data-message">
                                        There are no flights to display.
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>