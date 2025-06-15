<?php
require_once __DIR__ . '/../../lib/functions.php';
require_once __DIR__ . '/../../proxy/api.php';
require_once __DIR__ . '/../../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();
if (!userHasPermission(3)) {
    header('Location: ' . website_base_url . 'admin/access_denied.php');
    exit();
}

$id = cleanString($_GET['id']);
$news = null;
$res = Api::sendAsync('GET', 'v1/news/' . $id, null);
if ($res->getStatusCode() == 200) {
    $news = json_decode($res->getBody());
} else {
    header('Location: ' . website_base_url . 'site_admin_functions/news');
    die();
}

$status = "";
$responseMessage = null;
$title = $news->title;
$news = $news->news;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = cleanString($_POST['title']);
    $news = $_POST['editorContent'];

    $editorValid = json_decode($news)->blocks != null;

    if (empty($title) || empty($news) || !$editorValid) {
        $status = "required_fields";
    }

    if (empty($status)) {
        $data = [
            'id' => $id,
            'title' => trim($title),
            'news' => trim($news),

        ];

        $res = Api::sendSync('PUT', 'v1/news', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                break;
            case 400:
                $status = "error";
                $responseMessage = $res->getBody();
                break;
            default:
                $status = "error";
                break;
        }
    }
}
?>
<?php include '../includes/nav.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<main>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-fluid px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="align-left"></i></div>
                            Edit News
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="index.php">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to News
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">News</div>
            <div class="card-body">
                <?php if ($status == "success") { ?>
                    <div class="alert alert-success alert-dismissible fade show">News has been updated.
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } else { ?>
                    <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                            if ($status == 'error') {
                                if (!empty($responseMessage)) {
                                    echo $responseMessage;
                                } else {
                                    echo 'An error occurred when updating news. Please try again later.';
                                }
                            }
                            if ($status == 'required_fields') {
                                echo 'Please check all fields have been completed correctly.';
                            }
                            ?>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                <?php } ?>
                <form method="post" class="form" id="editorForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="mb-1">Title*</label>
                        <input name="title" type="text" id="title" class="form-control"
                            value="<?php echo htmlspecialchars(strval($title)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1">Article*</label>
                        <div id="editorJs" class="form-control"></div>
                    </div>
                    <div class="mb-3">
                        <div class=" text-right">
                            <input name="editorContent" type="hidden" id="editorContent" />
                            <button type="submit" id="submitButton" class="btn btn-primary">Save
                                Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@5.19.2"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@2.8.8"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@2.7.6"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/raw@2.5.0"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-parser@1/build/Parser.browser.min.js"></script>
<script src="<?php echo website_base_url; ?>admin/js/editor.js"></script>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript">
    contentJson = '<?php echo $news != null ? addslashes(preg_replace("/\r|\n/", "", $news)) : ""; ?>';
</script>