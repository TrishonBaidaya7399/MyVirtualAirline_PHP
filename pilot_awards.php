<?php
include 'lib/functions.php';
include 'config.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();
$id = cleanString($_GET['id']);

$awards = null;
$res = Api::sendSync('GET', 'v1/award/assigned/pilot/' . $id, null);
if ($res->getStatusCode() == 200) {
    $awards = json_decode($res->getBody(), false);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.pilot_award {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('./assets/images/backgrounds/leaderboard_sky_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    overflow-y: auto;
}

.pilot_award::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.pilot_award .container {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.pilot-awards-header {
    padding-top: 80px;
}

/* Global Heading */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}

/* Glassmorphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 20px;
    width: 100%;
    max-width: 100%;
    color: #fff;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Styles */
.panel-body {
    padding: 20px;
    color: #fff;
}

/* Table Styles */
.awards-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.awards-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.awards-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.awards-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.6);
    border-radius: 4px;
}

.awards-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.8);
}

.awards-table {
    margin: 0;
    background: transparent;
    min-width: 700px;
    width: 100%;
}

.awards-table thead th {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 15px 12px;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    text-wrap: nowrap;
}

.awards-table tbody tr {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}

.awards-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.002);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.awards-table tbody td {
    padding: 20px 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    vertical-align: middle;
    font-size: 14px;
    color: #fff;
}

.awards-table tbody tr:last-child td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

/* Award Image Container Styling */
.awards-table tbody td:first-child {
    text-align: center;
    padding: 15px;
    width: 150px;
}

.award-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.award-image:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 193, 7, 0.6);
}

/* No Data Message */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #fff;
    font-style: italic;
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    font-size: 2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .pilot_award {
        padding: 50px 0;
    }
    .pilot-awards-header {
        padding-top: 80px;
    }
    .panel-body {
        padding: 15px;
    }
    .awards-table tbody td {
        padding: 18px 12px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .pilot_award {
        padding: 40px 0;
        background-attachment: scroll;
    }
    .pilot-awards-header {
        padding-top: 80px;
    }
    .awards-table {
        min-width: 600px;
    }
    .awards-table tbody td {
        padding: 15px 10px;
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .pilot_award {
        padding: 30px 0;
    }
    .pilot-awards-header {
        padding-top: 80px;
    }
    .awards-table-wrapper {
        max-height: 400px;
    }
    .awards-table tbody td {
        padding: 12px 8px;
        font-size: 11px;
    }
    .award-image {
        width: 80px;
        height: 80px;
    }
}

@media (max-width: 576px) {
    .pilot_award {
        padding: 25px 0;
    }
    .pilot-awards-header {
        padding-top: 80px;
    }
    .awards-table-wrapper {
        max-height: 350px;
    }
    .awards-table {
        min-width: 500px;
    }
    .awards-table tbody td {
        padding: 10px 6px;
        font-size: 10px;
    }
    .award-image {
        width: 60px;
        height: 60px;
    }
}

/* Print Styles */
@media print {
    .pilot_award {
        background: white;
        padding: 20px 0;
    }
    .pilot_award::before {
        display: none;
    }
    .glass-card {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-body, .awards-table tbody td {
        color: black;
        text-shadow: none;
    }
    .awards-table thead th, .awards-table tbody td {
        background: white;
        border: 1px solid #ccc;
    }
}
</style>

<?php include 'includes/header.php'; ?>
<section id="content" class="pilot_award section pilot-awards-header">
    <div class="container">
        <div class="row" style='width: 100%'>
            <div class="col-12 mb-4">
                <div class="global-heading">
                    <h3 class="global-title">Pilot Awards</h3>
                </div>
            </div>
        </div>
        <div class="row" style='width: 100%'>
            <div class="col-12">
                <div class="glass-card">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12">
                                <?php if (!empty($awards)) { ?>
                                <div class="awards-table-wrapper">
                                    <table class="table table-striped awards-table">
                                        <thead>
                                            <tr>
                                                <th><strong>Award Image</strong></th>
                                                <th><strong>Award Name</strong></th>
                                                <th><strong>Description</strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($awards as $key => $award) { ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>"
                                                         class="award-image"
                                                         alt="<?php echo htmlspecialchars($award->awardName, ENT_QUOTES); ?>" />
                                                </td>
                                                <td><?php echo htmlspecialchars($award->awardName, ENT_QUOTES); ?><br /><i>awarded
                                                        on
                                                        <?php echo (new DateTime($award->dateAwarded))->format('d M Y'); ?></i>
                                                </td>
                                                <td><?php echo htmlspecialchars($award->description, ENT_QUOTES); ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } else { ?>
                                <div class="no-data-message">
                                    <strong>There are currently no awards to display.</strong>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>