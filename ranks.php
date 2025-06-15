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
<?php include 'includes/header.php';?>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Rank Structure</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($ranks)) {?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Epaulette</th>
                                        <th>Minimum Hours</th>
                                        <th>Minimum XP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ranks as $r) {?>
                                    <tr>
                                        <td><?php echo $r["name"]; ?></td>
                                        <td><?php echo $r["image"]; ?></td>
                                        <td><?php echo $r["hours"]; ?></td>
                                        <td><?php echo $r["xp"]; ?></td>
                                    </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                            <?php } else {?>
                            <hr />
                            There are currently no ranks to display.
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';