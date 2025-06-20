<?php

use Proxy\Api\Api;

$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'lib/functions.php';
include 'config.php';
session_start();
Api::__constructStatic();

$activities = null;
$res = Api::sendSync('GET', 'v1/activities/active-lite', null);
if ($res->getStatusCode() == 200) {
    $activities = json_decode($res->getBody(), false);
}
?>
<style>
    .activity-section {
        position: relative;
        padding: 60px 0;
        min-height: calc(100vh - 128px);
        background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
        
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
.activity-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
}
.activity-section .container {
    position: relative;
    z-index: 2;
}

    .activity-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .activity-header hr {
        border: none;
        height: 2px;
        background: linear-gradient(45deg, rgba(255, 215, 0, 0.8), rgba(255, 255, 255, 0.3));
        margin: 15px auto;
        width: 70%;
        border-radius: 2px;
    }

    .activity-title-wrapper {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        justify-content: center;
        width: 100%;
        flex-wrap: wrap;
    }

    .activity-title {
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: 2px;
        font-family: 'Montserrat', sans-serif;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        text-align: center;
    }

    .activity-title-icon {
        font-size: 3rem;
        color: rgba(255, 215, 0, 1);
        opacity: 0.9;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .activity-card-container {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 200px;
        width: 100% !important;
        min-width: 100% !important;
        position: relative;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin: 0 !important;
        border-radius: 10px;

    }

    .activity-card-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .activity-card-hidden {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        min-width: 100% !important; 
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 10px !important;
        
        box-sizing: border-box;
    }

    .activity-card-hidden div {
        text-align: center;
        width: 100%;
        min-width: 100% !important;
        border-radius: 10px 10px 0 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-card-hidden p {
        margin: 0;
        padding: 10px;
    }

    .activity-card-hidden a {
        color: #ffffff;
        text-decoration: none;
        font-size: 2rem;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        transition: color 0.3s ease;
    }

    .activity-card-hidden a:hover {
        color: rgba(255, 215, 0, 1);
        text-decoration: none;
    }

    
    .cards-grid {
        display: grid;
        gap: 20px;
        margin: 0 auto;
        max-width: 100%;
    }
    
    .card-item {
        
        
        
    }

    .activity-card-container:hover .activity-card-hidden {
    display: flex;
    align-items: start;
}

.no-event{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 3rem !important;
}
    
    
    @media (max-width: 612px) {
        .cards-grid {
            grid-template-columns: repeat(1, 1fr);
            justify-content: center;
        }
        
        .card-item {
            width: 100%;
          
        }
    }
    
    @media (min-width: 613px) and (max-width: 800px) {
        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            justify-content: center;
        }
        
        .card-item {
            width: calc(50% - 10px);
            max-width: 280px;
        }
    }
    
    @media (min-width: 801px) {
        .cards-grid {
            grid-template-columns: repeat(3, 1fr);
            justify-content: center;
        }
        
        .card-item {
            width: 100%;
        }
    }

    .text-white {
        color: #ffffff;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        font-size: 1.1rem;
        margin: 20px 0;
    }

    .section-divider {
        margin: 60px 0;
    }

    
    @media (max-width: 1200px) {
        .activity-title {
            font-size: 2.5rem;
        }
        
        .activity-title-icon {
            font-size: 2.5rem;
        }
        
        .activity-section {
            padding: 40px 0;
        }
    }

    @media (max-width: 992px) {
        .activity-title {
            font-size: 2.2rem;
        }
        
        .activity-title-icon {
            font-size: 2.2rem;
        }
        
        .activity-card-container {
            min-height: 180px;
        }
        
        .activity-header hr {
            width: 80%;
        }
    }

    @media (max-width: 768px) {
        .activity-section {
            padding: 30px 0;
            background-attachment: scroll;
        }
        
        .activity-title {
            font-size: 1.8rem;
            letter-spacing: 1px;
        }
        
        .activity-title-icon {
            font-size: 1.8rem;
        }
        
        .activity-title-wrapper {
            gap: 12px;
        }
        
        .activity-card-container {
            min-height: 160px;
            
        }
        
        .activity-card-hidden a {
            font-size: 1.1rem;
        }
        
        .activity-header {
            margin-bottom: 25px;
        }
        
        .section-divider {
            margin: 40px 0;
        }
        .cards-grid{
            margin-inline: 20px !important;
            max-width: calc(100% - 70px)
        }
        .card-item{
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    
    @media (max-width: 576px) {
        .cards-grid{
            margin-inline: auto !important;
            max-width: calc(100% - 70px)
        }
        .card-item{
            width: 100% !important;
            max-width: 100% !important;
        }
        .activity-title-wrapper {
            gap: 10px;
            flex-direction: row;
            align-items: center;
        }
        
        .activity-header hr {
            width: 100%;
        }
        
        .activity-title {
            font-size: 1.6rem;
        }
        
        .activity-title-icon {
            font-size: 1.6rem;
        }
        
        .activity-card-container {
            min-height: 140px;
            
        }
        
        .activity-card-hidden {
            padding: 15px;
        }
        
        .activity-card-hidden a {
            font-size: 1rem;
        }
        
        .text-white {
            font-size: 1rem;
        }
        
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
    }

    @media (max-width: 480px) {
        .activity-section {
            padding: 20px 0;
        }
        
        .activity-title {
            font-size: 1.4rem;
        }
        
        .activity-title-icon {
            font-size: 1.4rem;
        }
        
        .activity-card-container {
            min-height: 120px;
        }
        
        .activity-card-hidden a {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 360px) {
        .activity-title {
            font-size: 1.2rem;
        }
        
        .activity-title-icon {
            font-size: 1.2rem;
        }
        
        .activity-title-wrapper {
            gap: 8px;
        }
        
        .container {
            padding-left: 10px;
            padding-right: 10px;
        }
    }

    
    @media (hover: none) and (pointer: coarse) {
        .activity-card-container:hover {
            transform: none;
            box-shadow: none;
        }
        
        .activity-card-container:active {
            transform: scale(0.98);
        }
    }

    
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .activity-section {
            background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=4148&q=80');
        }
    }

    
    @media (prefers-color-scheme: dark) {
        .activity-card-hidden {
            background: rgba(0, 0, 0, 0.8);
        }
    }

    
    @media (prefers-reduced-motion: reduce) {
        .activity-card-container {
            transition: none;
        }
        
        .activity-card-hidden a {
            transition: none;
        }
    }
</style>

<?php include 'includes/header.php'; ?>

<section id="content" class="section offset-header activity-section">
    <div class="container">
        <!-- Events Section -->
        <div class="row">
            <div class="col-12">
                <div class="activity-header">
                    <div class="activity-title-wrapper">
                        <h3 class="activity-title">Events</h3>
                        <i class="fa fa-calendar activity-title-icon" aria-hidden="true"></i>
                    </div>
                    <hr />
                </div>
                
                <?php if (!empty($activities)) { ?>
                    <?php
                        $noEvents = true;
                        foreach ($activities as $key => $activity) {
                            if ($activity->type == "Event") {
                                $noEvents = false; ?>
                                <div class="cards-grid">
                                <div class="card-item">
                                    <div class="activity-card-container rounded" data-activityid="<?php echo $activity->id; ?>"
                                        style="background-image:url(<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>);background-color:#ccc;">
                                        <div class="activity-card-hidden rounded">
                                            <div class=" d-flex align-items-center justify-content-center">
                                                <p class="text-xl"><a href="<?php echo website_base_url; ?>activity.php?id=<?php echo $activity->id; ?>"
                                                        class="js_showloader"><?php echo $activity->title; ?></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                        <?php
                            }
                        }
                        if ($noEvents) { ?>
                            <p class="no-event text-align-center text-white">There are currently no events.</p>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="no-event text-align-center text-white">There are currently no events.</p>
                    <?php } ?>
            </div>
        </div>

        <!-- Section Divider -->
        <div class="section-divider"></div>

        <!-- Tours Section -->
        <div class="row">
            <div class="col-12">
                <div class="activity-header">
                    <div class="activity-title-wrapper">
                        <h3 class="activity-title">Tours</h3>
                        <i class="fa fa-plane activity-title-icon" aria-hidden="true"></i>
                    </div>
                    <hr />
                </div>
                
                <div class="cards-grid">
                    <?php if (!empty($activities)) { ?>
                        <?php
                        $noTours = true;
                        foreach ($activities as $key => $activity) {
                            if ($activity->type == "Tour") {
                                $noTours = false; ?>
                                <div class="card-item">
                                    <div class="activity-card-container rounded" data-activityid="<?php echo $activity->id; ?>"
                                        style="background-image:url(<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>);background-color:#ccc;">
                                        <div class="activity-card-hidden rounded">
                                            <div>
                                                <p><a href="<?php echo website_base_url; ?>activity.php?id=<?php echo $activity->id; ?>"
                                                        class="js_showloader"><?php echo $activity->title; ?></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        if ($noTours) { ?>
                            <p class="text-align-center text-white">There are currently no tours.</p>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-align-center text-white">There are currently no tours.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
    $(document).ready(function() {
        // Handle hover effects for desktop
        if (window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
            $(".activity-card-container").hover(function() {
                $(this).children('.activity-card-hidden').fadeIn(200);
            }, function() {
                $(this).children('.activity-card-hidden').fadeOut(150);
            });
        } else {
            // For touch devices, show overlay on touch
            $(".activity-card-container").on('touchstart', function(e) {
                e.preventDefault();
                var $overlay = $(this).children('.activity-card-hidden');
                
                // Hide other overlays
                $('.activity-card-hidden').not($overlay).hide();
                
                if ($overlay.is(':visible')) {
                    // If overlay is visible, navigate to the activity
                    Loader.start();
                    window.location.href = "<?php echo website_base_url; ?>activity.php?id=" + $(this).data("activityid");
                } else {
                    // Show overlay
                    $overlay.fadeIn(200);
                }
            });
            
            // Hide overlay when touching outside
            $(document).on('touchstart', function(e) {
                if (!$(e.target).closest('.activity-card-container').length) {
                    $('.activity-card-hidden').hide();
                }
            });
        }
        
        // Handle click events for desktop
        $(".activity-card-container").on('click', function(e) {
            // Only handle click if not on touch device or if overlay is already visible
            if (window.matchMedia("(hover: hover) and (pointer: fine)").matches || 
                $(this).children('.activity-card-hidden').is(':visible')) {
                Loader.start();
                window.location.href = "<?php echo website_base_url; ?>activity.php?id=" + $(this).data("activityid");
            }
        });
        
        // Keyboard accessibility
        $(".activity-card-container").on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                Loader.start();
                window.location.href = "<?php echo website_base_url; ?>activity.php?id=" + $(this).data("activityid");
            }
        });
        
        // Make cards focusable for keyboard navigation
        $(".activity-card-container").attr('tabindex', '0');
        $(".activity-card-container").attr('role', 'button');
        $(".activity-card-container").attr('aria-label', function() {
            return 'View activity: ' + $(this).find('a').text();
        });
    });
</script>