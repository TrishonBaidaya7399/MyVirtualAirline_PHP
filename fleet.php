<?php

use Proxy\Api\Api;

include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();
$res = Api::sendSync('GET', 'v1/operations/fleet', null);
$fleet = json_decode($res->getBody());
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Our Fleet</h2>
                <hr />
                <?php if (!empty($fleet)) { ?>
                    <?php foreach ($fleet as $key => $aircraft) { ?>
                        <div class="activity-card-container" data-fleetid="<?php echo $aircraft->id; ?>"
                            style="background-image:url(<?php echo website_base_url; ?>uploads/fleet/<?php echo $aircraft->imageUrl; ?>);background-color:#ccc;">
                            <div class="activity-card-hidden">
                                <div>
                                    <p><a href="<?php echo website_base_url; ?>fleet_info.php?id=<?php echo $aircraft->id; ?>"
                                            class="js_showloader"><?php echo $aircraft->description; ?></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>There is no aircraft to display.</p>
                <?php } ?>
            </div>
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
            window.location.href = "<?php echo website_base_url; ?>fleet_info.php?id=" + $(this).data(
                "fleetid");
        });
    });
</script>