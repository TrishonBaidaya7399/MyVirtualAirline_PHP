<?php
use Proxy\Api\Api;
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();
$ranks = null;
$res = Api::sendSync('GET', 'v1/ranks', null);
if ($res->getStatusCode() == 200) {
    $ranks = json_decode($res->getBody(), true);
}
if (!empty($ranks)) {
    foreach ($ranks as &$rank) {
        $rank["image"] = '<img src="' . website_base_url . 'uploads/ranks/' . $rank["imageUrl"] . '" width="80"/>';
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
/* Ranks Section Styles with Parallax Background */
.ranks-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.ranks-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.ranks-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* Ranks Title - Outside Card */
.ranks-title-wrapper {
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ranks-title {
    font-size: 3rem;
    font-weight: 300;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.ranks-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Glassmorphism Card */
.ranks-glass-card {
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.ranks-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Table Styles */
.ranks-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}

/* Custom scrollbar for webkit browsers */
.ranks-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.ranks-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

.ranks-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.5);
    border-radius: 4px;
}

.ranks-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.7);
}

.ranks-table {
    margin: 0;
    background: transparent;
    color: #333;
    min-width: 600px; /* Ensure minimum width for all columns */
    width: 100%;
}

.ranks-table thead th {
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

.ranks-table tbody tr {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.ranks-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: scale(1.01);
}

.ranks-table tbody td {
    padding: 15px 12px;
    border: none;
    vertical-align: middle;
    font-size: 14px;
    color: #333;
}

.ranks-table tbody tr:last-child {
    border-bottom: none;
}

/* Rank Name Styling */
.ranks-table tbody td:first-child {
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

/* Epaulette Image Styling */
.ranks-table tbody td:nth-child(2) {
    text-align: left;
    padding: 10px;
}

.ranks-table tbody td:nth-child(2) img {
    max-width: 80px;
    height: auto;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.ranks-table tbody td:nth-child(2) img:hover {
    transform: scale(1.1);
}

/* Hours and XP Styling */
.ranks-table tbody td:nth-child(3),
.ranks-table tbody td:nth-child(4) {
    font-weight: 600;
    color: #17a2b8;
    text-align: left;
}

/* No Data Message */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #ffffff;
    font-style: italic;
    background: rgba(255, 255, 255, 0.50);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    font-size: 1.2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Rank Level Indicators */
.ranks-table tbody tr:nth-child(1) {
    background: linear-gradient(45deg, rgba(255, 215, 0, 0.3), rgba(255, 255, 255, 0.8));
}

.ranks-table tbody tr:nth-child(2) {
    background: linear-gradient(45deg, rgba(192, 192, 192, 0.3), rgba(255, 255, 255, 0.8));
}

.ranks-table tbody tr:nth-child(3) {
    background: linear-gradient(45deg, rgba(205, 127, 50, 0.3), rgba(255, 255, 255, 0.8));
}

/* Responsive Design */
@media (max-width: 1200px) {
    .ranks-section {
        padding: 50px 0;
    }
    
    .ranks-title {
        font-size: 2.5rem;
    }
    
    .ranks-icon {
        font-size: 2rem;
    }
    
    .ranks-table thead th,
    .ranks-table tbody td {
        padding: 12px 10px;
        font-size: 13px;
    }
    
    .ranks-table tbody td:nth-child(2) img {
        max-width: 70px;
    }
}

@media (max-width: 992px) {
    .ranks-section {
        padding: 40px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 80px;
    }
    
    .ranks-title {
        font-size: 2.2rem;
        text-align: left;
    }
    
    .ranks-icon {
        font-size: 2rem;
    }
    
    .ranks-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .ranks-table-wrapper {
        max-height: 500px;
    }
}

@media (max-width: 768px) {
    .ranks-section {
        padding: 30px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 60px;
    }
    
    .ranks-title {
        font-size: 2rem;
    }
    
    .ranks-icon {
        font-size: 1.8rem;
    }
    
    .ranks-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .ranks-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .ranks-table-wrapper {
        max-height: 400px;
    }
    
    .ranks-table {
        font-size: 12px;
        min-width: 550px;
    }
    
    .ranks-table thead th {
        padding: 12px 8px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td {
        padding: 12px 8px;
        font-size: 12px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td:nth-child(2) img {
        max-width: 60px;
    }
}

@media (max-width: 576px) {
    .ranks-section {
        padding: 25px 0;
    }
    
    .offset-header {
        padding-top: 50px;
    }
    
    .ranks-title {
        font-size: 1.8rem;
    }
    
    .ranks-icon {
        font-size: 1.5rem;
    }
    
    .ranks-title-wrapper {
        gap: 10px;
    }
    
    .ranks-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .ranks-table-wrapper {
        max-height: 350px;
    }
    
    .ranks-table {
        font-size: 11px;
        min-width: 500px;
    }
    
    .ranks-table thead th {
        padding: 10px 6px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td {
        padding: 10px 6px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td:nth-child(2) img {
        max-width: 50px;
    }
}

@media (max-width: 480px) {
    .ranks-section {
        padding: 20px 0;
    }
    
    .offset-header {
        padding-top: 40px;
    }
    
    .ranks-title {
        font-size: 1.6rem;
    }
    
    .ranks-icon {
        font-size: 1.3rem;
    }
    
    .ranks-table-wrapper {
        max-height: 300px;
    }
    
    .ranks-table {
        min-width: 450px;
    }
    
    .ranks-table thead th {
        padding: 8px 4px;
        font-size: 9px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td {
        padding: 8px 4px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    .ranks-table tbody td:nth-child(2) img {
        max-width: 40px;
    }
    
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
}

/* High DPI Displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .ranks-table tbody td img {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Print Styles */
@media print {
    .ranks-section {
        background: white;
        padding: 20px 0;
    }
    
    .ranks-section::before {
        display: none;
    }
    
    .ranks-title {
        color: black;
        text-shadow: none;
        font-size: 2rem;
    }
    
    .ranks-glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
    }
    
    .ranks-table thead th,
    .ranks-table tbody td {
        background: white;
        color: black;
    }
}
</style>
<?php include 'includes/header.php';?>
<section id="content" class="section ranks-section offset-header">
    <div class="container">
        <!-- Ranks Title - Outside Card -->
        <div class="ranks-title-wrapper">
            <h3 class="ranks-title">Rank Structure</h3>
            <i class="fa fa-star ranks-icon" aria-hidden="true"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="ranks-glass-card">
                    <?php if (!empty($ranks)) {?>
                    <div class="ranks-table-wrapper">
                        <table class="table table-striped ranks-table">
                            <thead>
                                <tr>
                                    <th><strong>Rank</strong></th>
                                    <th><strong>Epaulette</strong></th>
                                    <th><strong>Minimum Hours</strong></th>
                                    <th><strong>Minimum XP</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranks as $r) {?>
                                <tr>
                                    <td><?php echo $r["name"]; ?></td>
                                    <td><?php echo $r["image"]; ?></td>
                                    <td><?php echo number_format($r["hours"]); ?> hrs</td>
                                    <td><?php echo number_format($r["xp"]); ?> XP</td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else {?>
                    <div class="no-data-message">
                        <strong>There are currently no ranks to display.</strong>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>