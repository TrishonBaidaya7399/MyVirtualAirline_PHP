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
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Events</h2>
                <hr />
                <?php if (!empty($activities)) { ?>
                    <?php
                    $noEvents = true;
                    foreach ($activities as $key => $activity) {
                        if ($activity->type == "Event") {
                            $noEvents = false; ?>
                            <div class="activity-card-container" data-activityid="<?php echo $activity->id; ?>"
                                style="background-image:url(<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>);background-color:#ccc;">
                                <div class="activity-card-hidden">
                                    <div>
                                        <p><a href="<?php echo website_base_url; ?>activity.php?id=<?php echo $activity->id; ?>"
                                                class="js_showloader"><?php echo $activity->title; ?></a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } ?>
                    <?php if ($noEvents) { ?>
                        <p>There are currently no events.</p><?php } ?>
                <?php } else { ?>
                    <p>There are currently no events.</p>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>Tours</h2>
                <hr />
                <?php if (!empty($activities)) { ?>
                    <?php
                    $noTours = true;
                    foreach ($activities as $key => $activity) {
                        if ($activity->type == "Tour") {
                            $noTours = false; ?>
                            <div class="activity-card-container" data-activityid="<?php echo $activity->id; ?>"
                                style="background-image:url(<?php echo website_base_url; ?>uploads/activities/<?php echo $activity->banner; ?>);background-color:#ccc;">
                                <div class="activity-card-hidden">
                                    <div>
                                        <p><a href="<?php echo website_base_url; ?>activity.php?id=<?php echo $activity->id; ?>"
                                                class="js_showloader"><?php echo $activity->title; ?></a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php if ($noTours) { ?>
                                <p>There are currently no tours.</p><?php } ?>
                    <?php
                        }
                    } ?>
                <?php } else { ?>
                    <p>There are currently no tours.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function() {
        $(".activity-card-container").hover(function() {
            $(this).children('.activity-card-hidden').fadeIn();
        }, function() {
            $(this).children('.activity-card-hidden').hide();
        });
        $(".activity-card-container").click(function() {
            Loader.start();
            window.location.href = "<?php echo website_base_url; ?>activity.php?id=" + $(this).data(
                "activityid");
        });
    });
</script>