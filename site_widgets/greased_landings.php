<?php

use Proxy\Api\Api;

Api::__constructStatic();
$obj = null;
$res = Api::sendSync('GET', 'v1/stats/greasedlandings', null);
if ($res->getStatusCode() == 200) {
    $obj = json_decode($res->getBody(), false);
}
?>

<style>

.top-landings-section {
    position: relative;
    
    
}

.top-landings-section .container {
    position: relative;
    z-index: 2;
}


.top-landings-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.top-landings-title {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: 2px;
    text-transform: capitalize;
    font-family: 'Montserrat', sans-serif;
}

.top-landings-icon {
    font-size: 2.5rem;
    color: #ffc107;
    opacity: 0.9;
}

.gold {
    color: #ffc107;
}


.top-landings-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.top-landings-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.top-landings-table-wrapper {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}


.top-landings-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.top-landings-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.top-landings-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.top-landings-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

.top-landings-table {
    margin: 0;
    background: transparent;
    color: #333;
    min-width: 800px; 
    width: 100%;
}

.top-landings-table thead th {
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

.top-landings-table tbody tr {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.top-landings-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: scale(1.01);
}

.top-landings-table tbody td {
    padding: 12px;
    border: none;
    vertical-align: middle;
    font-size: 13px;
    color: #333;
}

.top-landings-table tbody tr:last-child {
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


.top-landings-table a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.top-landings-table a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.top-landings-table i {
    margin-right: 5px;
    color: #666;
}


.landing-rate-excellent {
    color: #28a745;
    font-weight: 700;
    background: rgba(40, 167, 69, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}

.landing-rate-good {
    color: #17a2b8;
    font-weight: 600;
    background: rgba(23, 162, 184, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
}


.performance-excellent {
    color: #28a745;
    font-weight: 700;
}

.performance-good {
    color: #17a2b8;
    font-weight: 600;
}

.performance-average {
    color: #ffc107;
    font-weight: 500;
}


.top-landings-table tbody tr:nth-child(1) {
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.2), rgba(255, 255, 255, 0.8));
}

.top-landings-table tbody tr:nth-child(2) {
    background: linear-gradient(45deg, rgba(192, 192, 192, 0.2), rgba(255, 255, 255, 0.8));
}

.top-landings-table tbody tr:nth-child(3) {
    background: linear-gradient(45deg, rgba(205, 127, 50, 0.2), rgba(255, 255, 255, 0.8));
}


.no-data-message {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
}


@media (max-width: 1200px) {
    .top-landings-title {
        font-size: 2.5rem;
    }
    
    .top-landings-icon {
        font-size: 2rem;
    }
    
    .top-landings-table thead th,
    .top-landings-table tbody td {
        padding: 10px 8px;
        font-size: 12px;
    }
}

@media (max-width: 992px) {
    .top-landings-section {
        padding: 30px 0;
    }
    
    .top-landings-title {
        font-size: 2.2rem;
        text-align: left;
        margin-bottom: 20px;
    }
    
    .top-landings-icon {
        font-size: 2rem;
    }
    
    .top-landings-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .top-landings-table-wrapper {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .top-landings-section {
        padding: 25px 0;
    }
    
    .top-landings-title {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .top-landings-icon {
        font-size: 1.8rem;
    }
    
    .top-landings-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .top-landings-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .top-landings-table-wrapper {
        max-height: 350px;
    }
    
    .top-landings-table {
        font-size: 11px;
        min-width: 700px; 
    }
    
    .top-landings-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .top-landings-table tbody td {
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
    .top-landings-title {
        font-size: 1.8rem;
    }
    
    .top-landings-icon {
        font-size: 1.5rem;
    }
    
    .top-landings-title-wrapper {
        gap: 10px;
    }
    
    .top-landings-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .top-landings-table-wrapper {
        max-height: 300px;
    }
    
    .top-landings-table {
        font-size: 10px;
        min-width: 600px; 
    }
    
    .top-landings-table thead th {
        padding: 10px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .top-landings-table tbody td {
        padding: 8px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .top-landings-title {
        font-size: 1.6rem;
    }
    
    .top-landings-icon {
        font-size: 1.3rem;
    }
    
    .top-landings-section {
        padding: 20px 0;
    }
    
    .top-landings-table-wrapper {
        max-height: 250px;
    }
    
    .top-landings-table {
        min-width: 550px; 
    }
    
    .top-landings-table thead th {
        padding: 8px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .top-landings-table tbody td {
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

<section class="top-landings-section">
    <div class="container">
        <!-- Top Landings Title - Outside Card -->
        <div class="top-landings-title-wrapper">
            <h3 class="top-landings-title">top 5 landings this week</h3>
            <i class="fa fa-trophy top-landings-icon gold" aria-hidden="true"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="top-landings-glass-card">
                    <div class="top-landings-table-wrapper">
                        <table class="table table-striped top-landings-table">
                            <thead>
                                <tr>
                                    <th><strong>Pilot</strong></th>
                                    <th><strong>Flight Number</strong></th>
                                    <th><strong>Airport</strong></th>
                                    <th><strong>Date</strong></th>
                                    <th><strong>Landing Rate</strong></th>
                                    <th><strong>Performance Score</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($obj)) { ?>
                                    <?php foreach ($obj as $key => $flight) { ?>
                                        <tr>
                                            <td>
                                                <?php if ($flight->profileImage != "") { ?>
                                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $flight->profileImage ?>" class="img-circle pilot-profile-image-small" />
                                                <?php } else { ?>
                                                    <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                                    <?php } ?>&nbsp;
                                                    <a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $flight->pilot; ?>" class="js_showloader"><?php echo explode(" ", $flight->name)[0]; ?>
                                                        (<?php echo $flight->callsign ?>)</a>
                                            </td>
                                            <td><i class="fa fa-plane"></i> <a href="<?php echo website_base_url; ?>pirep_info.php?id=<?php echo $flight->id; ?>" class="js_showloader"><?php echo $flight->flightNumber; ?></a>
                                            </td>
                                            <td><i class="fa fa-map-marker"></i> <?php
                                                if ($flight->arrivedAlternate) {
                                                    echo $flight->altIcao;
                                                } else {
                                                    echo $flight->arrIcao;
                                                }
                                                ?>
                                            </td>
                                            <td><i class="fa fa-calendar"></i> <?php echo (new DateTime($flight->date))->format('d M Y'); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $landingRate = abs($flight->landingRate);
                                                $class = '';
                                                if ($landingRate <= 100) {
                                                    $class = 'landing-rate-excellent';
                                                } else if ($landingRate <= 200) {
                                                    $class = 'landing-rate-good';
                                                }
                                                ?>
                                                <span class="<?php echo $class; ?>"><?php echo number_format($flight->landingRate); ?>fpm</span>
                                            </td>
                                            <td>
                                                <?php 
                                                $score = $flight->score;
                                                $scoreClass = '';
                                                if ($score >= 90) {
                                                    $scoreClass = 'performance-excellent';
                                                } else if ($score >= 80) {
                                                    $scoreClass = 'performance-good';
                                                } else if ($score >= 70) {
                                                    $scoreClass = 'performance-average';
                                                }
                                                ?>
                                                <span class="<?php echo $scoreClass; ?>"><?php echo $score; ?>%</span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="no-data-message">
                                            Nobody has performed an awesome landing yet!
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