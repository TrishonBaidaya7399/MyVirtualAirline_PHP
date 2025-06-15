<?php
include 'lib/functions.php';
include 'config.php';

use Proxy\Api\Api;

Api::__constructStatic();

$staffs = null;
$res = Api::sendSync('GET', 'v1/staffs', null);
if ($res->getStatusCode() == 200) {
	$staffs = json_decode($res->getBody(), false);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="team-boxed">
            <div class="container">
                <div class="jumbotron">
                    <h1 class="text-center">Meet the team</h1>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                        printer took a galley of type and scrambled it to make a type specimen book. It has survived not
                        only five centuries, but also the leap into electronic typesetting, remaining essentially
                        unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem
                        Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker
                        including versions of Lorem Ipsum.</p>
                </div>
                <hr />
                <div class="row people">
                    <?php if (!empty($staffs)) { ?>
                    <?php foreach ($staffs as $key => $staff) { ?>
                    <div class="col-md-4 col-sm-6 item">
                        <div class="box"><?php if ($staff->profileImage != "") { ?>
                            <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $staff->profileImage ?>"
                                width="200" class="img-circle" />
                            <?php } else { ?>
                            <i class="fa fa-user-circle image-circle" aria-hidden="true"></i>
                            <?php } ?>
                            <h3 class="name"><a
                                    href="<?php echo website_base_url; ?>profile.php?id=<?php echo $staff->staffPilotId; ?>"><?php echo $staff->staffName ?></a>
                            </h3>
                            <p class="title"><strong><?php echo $staff->roleName ?></strong>
                            </p>
                            <?php if ($staff->contactEmail == "") { ?>
                            <p class="description">&nbsp;</p>
                            <?php } else { ?>
                            <p class="description"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <?php echo $staff->contactEmail ?>
                            </p>
                            <?php } ?>
                            <p class="description"><?php echo $staff->roleDescription ?>
                            </p>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <strong>There are currently no staff members profiles to display.</strong>
                    <?php } ?>
                </div>
                <hr />
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';