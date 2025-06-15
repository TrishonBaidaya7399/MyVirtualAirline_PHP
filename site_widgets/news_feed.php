<?php

use Proxy\Api\Api;

Api::__constructStatic();
$obj = null;
$res = Api::sendSync('GET', 'v1/news/latest', null);
if ($res->getStatusCode() == 200) {
    $obj = json_decode($res->getBody(), false);
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">NOTAMs</h3>
                </div>
                <div class="panel-body">
                    <?php if (!empty($obj)) { ?>
                        <?php foreach ($obj as $key => $news) { ?>
                            <div class="col-md-12">
                                <i class="fa fa-bullhorn" aria-hidden="true"></i> <a href="<?php echo website_base_url; ?>news_item.php?id=<?php echo $news->id; ?>" class="news-link">
                                    <?php echo $news->title; ?>
                                </a><br />
                                <span class="news-posted-by">posted by <?php echo $news->poster; ?>
                                    on <?php echo (new DateTime($news->date))->format('d M Y'); ?></span><br /><br />
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>There are no news articles.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>