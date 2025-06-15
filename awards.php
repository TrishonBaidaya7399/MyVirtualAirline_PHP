<?php
include 'lib/functions.php';
include 'config.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();

$awards = null;
$res = Api::sendSync('GET', 'v1/awards', null);
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
                    <h3 class="panel-title">Our Awards</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            Ut dignissim condimentum erat sit amet pellentesque. Aenean lacus mi, lobortis et sem
                            interdum, ultrices tincidunt felis. Praesent lacinia cursus arcu, id ultricies leo viverra
                            in. Phasellus rutrum nibh eu mollis dapibus. Aenean auctor quam lorem, a pulvinar odio
                            finibus ut. Vivamus commodo pulvinar quam, vel porta lorem vestibulum ut. Proin arcu lorem,
                            volutpat id justo et, venenatis ullamcorper tellus. Vivamus fringilla dolor quis urna
                            tristique aliquet. Praesent a tortor dignissim, molestie eros non, porta enim. Nullam at
                            mattis neque. Nunc ut tellus sit amet justo feugiat condimentum quis dapibus mauris.
                            Praesent nulla metus, posuere nec mollis quis, semper eu odio. Proin eget nisi suscipit,
                            dignissim risus et, tincidunt nulla.
                            <br /><br />
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
                                        <td><?php echo $award->name; ?>
                                        </td>
                                        <td width="500"><?php echo $award->description ?>
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