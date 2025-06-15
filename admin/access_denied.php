<?php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';

session_start();
?>
<?php include 'includes/nav.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="lock"></i></div>
                            Access Denied
                        </h1>
                        <div class="page-header-subtitle">
                            Sorry, but access to this resource is blocked by the administrator.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</main>
<?php include 'includes/footer.php'; ?>