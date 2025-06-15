<?php

use Proxy\Api\Api;

$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'lib/functions.php';
include 'config.php';
Api::__constructStatic();
session_start();

$bases = null;
$res = Api::sendSync('GET', 'v1/operations/bases', null);
if ($res->getStatusCode() == 200) {
    $bases = json_decode($res->getBody(), false);
}
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Bases</h2>
                <hr />
                <?php if (!empty($bases)) { ?>
                    <?php
                    foreach ($bases as $key => $base) { ?>
                        <div class="activity-card-container" data-baseid="<?php echo $base->id; ?>"
                            style="background-image:url(<?php echo website_base_url; ?>uploads/bases/<?php echo $base->imageUrl; ?>);background-color:#ccc;">
                            <div class="activity-card-hidden">
                                <div>
                                    <p><a href="<?php echo website_base_url; ?>base_info.php?id=<?php echo $base->id; ?>"
                                            class="js_showloader"><?php echo $base->hub; ?></a>
                                    </p>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                } else { ?>
                    <p>There are currently no bases.</p>
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
            window.location.href = "<?php echo website_base_url; ?>base_info.php?id=" + $(this).data(
                "baseid");
        });
    });
</script>