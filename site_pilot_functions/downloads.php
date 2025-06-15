<?php
include '../lib/functions.php';
include '../config.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();

validateSession();

$downloads = null;
$res = Api::sendSync('GET', 'v1/downloads', null);
if ($res->getStatusCode() == 200) {
	$downloads = json_decode($res->getBody(), true);
}
if (!empty($downloads)) {
	foreach ($downloads as &$download) {
		$download["button"] = '<a href="' . file_upload_dir . $download["link"] . '" title="download" target="_blank"><i class="fa fa-download"></i></a>';
	}
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="../assets/plugins/datatables/datatables.min.css" />
<section id="content" class="cp section offset-header">
	<div class="container">
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Downloads</h3>
				</div>
				<div class="panel-body">
					<h3>ACARS</h3>
					<p>We use an automatic ACARS application to track your flights. You can download our ACARS and use
						this on a Windows PC. It is compatible with all of the main Flight Simulators including X-Plane,
						Prepar3d, Microsoft FSX.</p>
					<p>X-Plane users are required to download XPUIPC which can be found by <a
							href="https://acars.vabase.com/downloads/XPUIPC%202.0.3.5%20and%20XPWideClient%202.0.0.3.zip"
							target="_blank">clicking here</a>. For all other simulators you can download the latest
						FSUIPC client for free by <a href="http://www.schiratti.com/dowson.html"
							target="_blank">clicking here</a>.</p>
					<p>Our ACARS will automatically update whenever there is an update so there is no need to check back
						here for updates.</p>
					<a href="https://acars.vabase.com/Client/publish_client.htm" class="btn btn-sm btn-success"
						target="_blank">Download ACARS</a>
					<h3>Other Downloads</h3>
					<table class="table table-striped" id="downloads">
						<thead>
							<tr>
								<th><strong>File Name</strong></th>
								<th><strong>Description</strong></th>
								<th><strong>Category</strong></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<?php include '../includes/footer.php'; ?>
<script type="text/javascript" src="../assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
	var dataSet = <?php echo json_encode($downloads); ?>;

	$(document).ready(function() {
		$('#downloads').DataTable({
			data: dataSet,
			"pageLength": 25,
			columns: [{
					data: "fileName"
				},
				{
					data: "description",
					"orderable": false
				},
				{
					data: "type"
				},
				{
					data: "button",
					"orderable": false
				}
			],
			colReorder: false,
			"order": [
				[2, 'asc']
			]
		});
	});
</script>