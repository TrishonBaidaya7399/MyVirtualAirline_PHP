<?php
use Proxy\Api\Api;

Api::__constructStatic();
$bids = null;
$res = Api::sendSync('GET', 'v1/bids', null);
if ($res->getStatusCode() == 200) {
    $bids = json_decode($res->getBody(), false);
}
?>

<style>
<style>

.departures-section {
    position: relative;
    
    
}

.departures-section .container {
    position: relative;
    z-index: 2;
}


.departures-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px
}

.departures-title {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: 2px;
    text-transform: lowercase;
    font-family: 'Montserrat', sans-serif;
}

.departures-icon {
    font-size: 2.5rem;
    color: #fff;
    opacity: 0.8;
}


.departures-glass-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.departures-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.departures-table-wrapper {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}


.departures-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.departures-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.departures-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(0, 123, 255, 0.5);
    border-radius: 4px;
}

.departures-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 123, 255, 0.7);
}

.departures-table {
    margin: 0;
    background: transparent;
    color: #333;
    min-width: 900px; 
    width: 100%;
}

.departures-table thead th {
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

.departures-table tbody tr {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.departures-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: scale(1.01);
}

.departures-table tbody td {
    padding: 12px;
    border: none;
    vertical-align: middle;
    font-size: 13px;
    color: #333;
}

.departures-table tbody tr:last-child {
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


.departures-table a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.departures-table a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.departures-table i {
    margin-right: 5px;
    color: #666;
}


.status-dispatched {
    color: #28a745;
    font-weight: 500;
}

.status-progress {
    color: #ffc107;
    font-weight: 500;
}


.no-data-message {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
}


@media (max-width: 1200px) {
    .departures-title {
        font-size: 2.5rem;
    }
    
    .departures-table thead th,
    .departures-table tbody td {
        padding: 10px 8px;
        font-size: 12px;
    }
}

@media (max-width: 992px) {
    .departures-section {
        padding: 30px 0;
    }
    
    .departures-title {
        font-size: 2.2rem;
        text-align: left;
        margin-bottom: 20px;
    }
    
    .departures-icon {
        font-size: 2rem;
    }
    
    .departures-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .departures-table-wrapper {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .departures-section {
        padding: 25px 0;
    }
    
    .departures-title {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .departures-icon {
        font-size: 1.8rem;
    }
    
    .departures-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .departures-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .departures-table-wrapper {
        max-height: 350px;
    }
    
    .departures-table {
        font-size: 11px;
        min-width: 800px; 
    }
    
    .departures-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .departures-table tbody td {
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
    .departures-title {
        font-size: 1.8rem;
    }
    
    .departures-icon {
        font-size: 1.5rem;
    }
    
    .departures-title-wrapper {
        gap: 10px;
    }
    
    .departures-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .departures-table-wrapper {
        max-height: 300px;
    }
    
    .departures-table {
        font-size: 10px;
        min-width: 700px; 
    }
    
    .departures-table thead th {
        padding: 10px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .departures-table tbody td {
        padding: 8px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .departures-title {
        font-size: 1.6rem;
    }
    
    .departures-icon {
        font-size: 1.3rem;
    }
    
    .departures-section {
        padding: 20px 0;
    }
    
    .departures-table-wrapper {
        max-height: 250px;
    }
    
    .departures-table {
        min-width: 650px; 
    }
    
    .departures-table thead th {
        padding: 8px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .departures-table tbody td {
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

<section class="departures-section">
    <div class="container">
        <!-- Departures Title - Outside Card -->
        <div class="departures-title-wrapper">
            <h3 class="departures-title">departures</h3>
            <i class="fa fa-plane-departure departures-icon"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="departures-glass-card">
                    <div class="departures-table-wrapper">
                        <table class="table table-striped departures-table">
                            <thead>
                                <tr>
                                    <th><strong>Pilot</strong></th>
                                    <th><strong>Flight No.</strong></th>
                                    <th><strong>Type</strong></th>
                                    <th><strong>Dep ICAO</strong></th>
                                    <th><strong>Arr ICAO</strong></th>
                                    <th><strong>PAX</strong></th>
                                    <th><strong>Cargo</strong></th>
                                    <th><strong>Aircraft</strong></th>
                                    <th><strong>Status</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bids)) {?>
                                <?php foreach ($bids as $key => $bid) {?>
                                <tr>
                                    <td>
                                        <?php if ($bid->profileImage != "") {?>
                                        <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $bid->profileImage; ?>"
                                            class="img-circle pilot-profile-image-small" />
                                        <?php } else {?>
                                        <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                        <?php }?>&nbsp;
                                        <a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $bid->pilotId; ?>"
                                            class="js_showloader"><?php echo explode(" ", $bid->name)[0]; ?></a>
                                    </td>
                                    <td><i class="fa fa-plane"></i>
                                        <?php if ($bid->bidType == "activity") {?>
                                        <a href="<?php echo website_base_url; ?>activity_leg.php?id=<?php echo $bid->activityLegId; ?>"
                                            class="js_showloader"><?php echo empty($bid->flightNumber) ? "NA" : $bid->flightNumber; ?></a>
                                        <?php } elseif ($bid->bidType == "scheduled") {?>
                                        <a href="<?php echo website_base_url; ?>flight_info.php?id=<?php echo $bid->scheduleId; ?>"
                                            class="js_showloader"><?php echo empty($bid->flightNumber) ? "NA" : $bid->flightNumber; ?></a>
                                        <?php } else {?>
                                        <?php echo empty($bid->flightNumber) ? "NA" : $bid->flightNumber; ?>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <?php if ($bid->bidType == "activity") {?>
                                        Tour/Event
                                        <?php } elseif ($bid->bidType == "scheduled") {?>
                                        Scheduled
                                        <?php } else {?>
                                        Charter
                                        <?php }?>
                                    </td>
                                    <td><i class="fa fa-map-marker"></i>
                                        <?php if (!empty($bid->departureIcao)) {?>
                                        <a href="airport_info.php?airport=<?php echo $bid->departureIcao; ?>"
                                            class="js_showloader"><?php echo $bid->departureIcao; ?></a>
                                        <?php } else {?>
                                        Any
                                        <?php }?>
                                    </td>
                                    <td><i class="fa fa-map-marker"></i>
                                        <?php if (!empty($bid->arrivalIcao)) {?>
                                        <a href="airport_info.php?airport=<?php echo $bid->arrivalIcao; ?>"
                                            class="js_showloader"><?php echo $bid->arrivalIcao; ?></a>
                                        <?php } else {?>
                                        Any
                                        <?php }?>
                                    </td>
                                    <td><?php echo $bid->totalPax; ?>
                                    </td>
                                    <td><?php echo getCargoDisplayValue($bid->cargo); ?>
                                    </td>
                                    <td><?php echo empty($bid->aircraft) ? "NA" : $bid->aircraft; ?>
                                    </td>
                                    <td>
                                        <span class="<?php echo $bid->status < 1 ? 'status-dispatched' : 'status-progress'; ?>">
                                            <?php echo $bid->status < 1 ? '<i class="fa fa-clock-o" aria-hidden="true"></i> Dispatched' : '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i> In Progress'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php }?>
                                <?php } else {?>
                                <tr>
                                    <td colspan="9" class="no-data-message">
                                        There are no active bookings.
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>