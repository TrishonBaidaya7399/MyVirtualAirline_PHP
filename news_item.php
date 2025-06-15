<?php
include 'lib/functions.php';
include 'config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

$id = cleanString($_GET['id']);
$article = null;
$res = Api::sendSync('GET', 'v1/news/' . $id, null);
if ($res->getStatusCode() == 200) {
	$article = json_decode($res->getBody(), false);
} else {
	header('Location: ' . website_base_url);
	die();
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
            <div class="col-md-12">
                <h2 class="title"><?php echo $article->title; ?>
                </h2>
                <span class="news-posted-by-main">posted by <?php echo $article->poster; ?>
                    on <?php echo (new DateTime($article->date))->format('d M Y'); ?></span>
            </div>
        </div>
        <div class="row">
            <br />
            <div class="col-md-12 article-content">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script type="text/javascript">
var articleJson = '<?php echo addslashes(preg_replace("/\r|\n/", "", $article->news)); ?>';
$(window).on('load', function() {
    try {
        var parser = new edjsParser({
            embed: {
                useProvidedLength: false,
            }
        });
        var html = parser.parse(JSON.parse(articleJson));
        $(".article-content").html(html)
        console.log(html);
    } catch (e) {
        $(".article-content").html(articleJson);
    }
});
</script>
<?php include 'includes/footer.php'; ?>