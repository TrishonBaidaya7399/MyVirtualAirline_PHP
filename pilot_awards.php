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
<?php include 'includes/header.php'; ?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Pilot Awards</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($awards)) { ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($awards as $key => $award) { ?>
                                    <tr>
                                        <td width="300">
                                            <img src="<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>"
                                                width="200" />
                                        </td>
                                        <td><?php echo $award->awardName; ?><br /><i>awarded
                                                on
                                                <?php echo (new DateTime($award->dateAwarded))->format('d M Y'); ?></i>
                                        </td>
                                        <td width="500"><?php echo $award->description; ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <?php } else { ?>
                            <hr />
                            There are currently no awards to display.
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';