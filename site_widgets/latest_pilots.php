<?php

use Proxy\Api\Api;

Api::__constructStatic();
$obj = null;
$res = Api::sendSync('GET', 'v1/stats/latestpilots', null);
if ($res->getStatusCode() == 200) {
    $obj = json_decode($res->getBody(), false);
}
?>

<style>

.new-pilots-section {
    position: relative;
    
    
}

.new-pilots-section .container {
    position: relative;
    z-index: 2;
}


.new-pilots-title-wrapper {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.new-pilots-title {
    font-size: 3rem;
    font-weight: 800;
    color: #ffff;
    margin: 0;
    letter-spacing: 2px;
    text-transform: lowercase;
    font-family: 'Montserrat', sans-serif;
}

.new-pilots-icon {
    font-size: 2.5rem;
    color: #fff;
    opacity: 0.8;
}


.new-pilots-glass-card {
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.new-pilots-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}


.new-pilots-table-wrapper {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}


.new-pilots-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.new-pilots-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.new-pilots-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(40, 167, 69, 0.5);
    border-radius: 4px;
}

.new-pilots-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(40, 167, 69, 0.7);
}

.new-pilots-table {
    margin: 0;
    background: transparent;
    color: #333;
    min-width: 600px; 
    width: 100%;
}

.new-pilots-table thead th {
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

.new-pilots-table tbody tr {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.new-pilots-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: scale(1.01);
}

.new-pilots-table tbody td {
    padding: 12px;
    border: none;
    vertical-align: middle;
    font-size: 13px;
    color: #333;
}

.new-pilots-table tbody tr:last-child {
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


.new-pilots-table a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.new-pilots-table a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.new-pilots-table i {
    margin-right: 5px;
    color: #666;
}


.country-flag {
    width: 20px;
    height: 20px;
    margin-right: 8px;
    border-radius: 2px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    vertical-align: middle;
}


.hub-name {
    color: #495057;
    font-weight: 500;
}


.hire-date {
    color: #28a745;
    font-weight: 500;
    background: rgba(40, 167, 69, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
}


.new-pilot-badge {
    background: linear-gradient(45deg, rgba(40, 167, 69, 0.1), rgba(255, 255, 255, 0.8));
    position: relative;
}

.new-pilot-badge::before {
    content: "NEW";
    position: absolute;
    top: 5px;
    right: 5px;
    background: #28a745;
    color: white;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
}


.no-data-message {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-style: italic;
    background: rgba(255, 255, 255, 0.8);
}


@media (max-width: 1200px) {
    .new-pilots-title {
        font-size: 2.5rem;
    }
    
    .new-pilots-icon {
        font-size: 2rem;
    }
    
    .new-pilots-table thead th,
    .new-pilots-table tbody td {
        padding: 10px 8px;
        font-size: 12px;
    }
}

@media (max-width: 992px) {
    .new-pilots-section {
        padding: 30px 0;
    }
    
    .new-pilots-title {
        font-size: 2.2rem;
        text-align: left;
        margin-bottom: 20px;
    }
    
    .new-pilots-icon {
        font-size: 2rem;
    }
    
    .new-pilots-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .new-pilots-table-wrapper {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .new-pilots-section {
        padding: 25px 0;
    }
    
    .new-pilots-title {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .new-pilots-icon {
        font-size: 1.8rem;
    }
    
    .new-pilots-title-wrapper {
        justify-content: center;
        gap: 10px;
    }
    
    .new-pilots-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .new-pilots-table-wrapper {
        max-height: 350px;
    }
    
    .new-pilots-table {
        font-size: 11px;
        min-width: 550px; 
    }
    
    .new-pilots-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .new-pilots-table tbody td {
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
    
    .country-flag {
        width: 18px;
        height: 18px;
        margin-right: 5px;
    }
}

@media (max-width: 576px) {
    .new-pilots-title {
        font-size: 1.8rem;
    }
    
    .new-pilots-icon {
        font-size: 1.5rem;
    }
    
    .new-pilots-title-wrapper {
        gap: 10px;
    }
    
    .new-pilots-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .new-pilots-table-wrapper {
        max-height: 300px;
    }
    
    .new-pilots-table {
        font-size: 10px;
        min-width: 500px; 
    }
    
    .new-pilots-table thead th {
        padding: 10px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .new-pilots-table tbody td {
        padding: 8px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .country-flag {
        width: 16px;
        height: 16px;
        margin-right: 4px;
    }
}

@media (max-width: 480px) {
    .new-pilots-title {
        font-size: 1.6rem;
    }
    
    .new-pilots-icon {
        font-size: 1.3rem;
    }
    
    .new-pilots-section {
        padding: 20px 0;
    }
    
    .new-pilots-table-wrapper {
        max-height: 250px;
    }
    
    .new-pilots-table {
        min-width: 450px; 
    }
    
    .new-pilots-table thead th {
        padding: 8px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .new-pilots-table tbody td {
        padding: 6px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .country-flag {
        width: 14px;
        height: 14px;
        margin-right: 3px;
    }
    
    .no-data-message {
        padding: 30px 15px;
        font-size: 14px;
    }
}
</style>

<section class="new-pilots-section">
    <div class="container">
        <!-- New Pilots Title - Outside Card -->
        <div class="new-pilots-title-wrapper">
            <h3 class="new-pilots-title">latest pilots</h3>
            <i class="fa fa-user-plus new-pilots-icon" aria-hidden="true"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="new-pilots-glass-card">
                    <div class="new-pilots-table-wrapper">
                        <table class="table table-striped new-pilots-table">
                            <thead>
                                <tr>
                                    <th><strong>Pilot</strong></th>
                                    <th><strong>Country</strong></th>
                                    <th><strong>Hub</strong></th>
                                    <th><strong>Hired Date</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($obj)) { ?>
                                    <?php foreach ($obj as $key => $pilot) { ?>
                                        <tr class="<?php echo $key < 3 ? 'new-pilot-badge' : ''; ?>">
                                            <td>
                                                <?php if ($pilot->profileImage != "") { ?>
                                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $pilot->profileImage ?>"
                                                        class="img-circle pilot-profile-image-small" />
                                                <?php } else { ?>
                                                    <i class="fa fa-user-circle profile-small" aria-hidden="true"></i>
                                                    <?php } ?>&nbsp;
                                                    <a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $pilot->id; ?>"
                                                        class="js_showloader"><?php echo explode(" ", $pilot->name)[0];  ?>
                                                        (<?php echo $pilot->callsign;  ?>)</a>
                                            </td>
                                            <td>
                                                <img src="<?php echo website_base_url; ?>images/flags/<?php echo $pilot->location; ?>.gif"
                                                    alt="<?php echo $pilot->location; ?>" class="country-flag">
                                                <?php echo $pilot->location; ?>
                                            </td>
                                            <td class="hub-name">
                                                <i class="fa fa-building"></i> <?php echo $pilot->hubName; ?>
                                            </td>
                                            <td>
                                                <span class="hire-date">
                                                    <i class="fa fa-calendar"></i> <?php echo (new DateTime($pilot->joinDate))->format('d M Y') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4" class="no-data-message">
                                            There are no pilots to display.
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