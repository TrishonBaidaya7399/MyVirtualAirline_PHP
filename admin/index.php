<?php
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';

session_start();

use Proxy\Api\Api;

Api::__constructStatic();

validateAdminSession();

$res = Api::sendSync('GET', 'v1/stats/admindashconfig', null);
if ($res->getStatusCode() == 200) {
    $config = json_decode($res->getBody());
}

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
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Hello, <?php echo explode(" ", $_SESSION['name'])[0]; ?>.
                        </h1>
                        <div class="page-header-subtitle">
                            Welcome to your airline management.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-lg border-start-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-primary mb-1">Scheduled Departures</div>
                                <div class="h5"><?php echo $config->scheduledDepartures; ?></div>
                            </div>
                            <div class="ms-2"><i class="fas fa-plane-departure fa-2x text-gray-200"></i></div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-dark stretched-link" href="/admin/operations/bookings.php">View Departures</a>
                        <div class="text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-lg border-start-secondary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-secondary mb-1">Flights Today</div>
                                <div class="h5"><?php echo $config->pilotReportsToday; ?></div>
                            </div>
                            <div class="ms-2"><i class="fas fa-file fa-2x text-gray-200"></i></div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-dark stretched-link" href="/admin/pireps/index.php">View Reports</a>
                        <div class="text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-lg border-start-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-success mb-1">New Pilot Applications</div>
                                <div class="h5"><?php echo $config->pendingPilotApplications; ?></div>
                            </div>
                            <div class="ms-2"><i class="fas fa-user fa-2x text-gray-200"></i></div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-dark stretched-link" href="/admin/pilots/applications.php">View Applications</a>
                        <div class="text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start-lg border-start-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-warning mb-1">Suspended Pilots</div>
                                <div class="h5"><?php echo $config->suspendedPilots; ?></div>
                            </div>
                            <div class="ms-2"><i class="fas fa-user-slash fa-2x text-gray-200"></i></div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-between small">
                        <a class="text-dark stretched-link" href="/admin/pilots/applications.php">View Pilots</a>
                        <div class="text-dark"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header" id="12m-header">
                        Data By Year (monthly) <span class="12mloading chart-loading"><i
                                class="fas fa-spinner fa-spin"></i>
                            Loading...</span>
                    </div>
                    <div class="card-body">
                        <select id="12mType" class="form-select w-25 d-inline" onchange="Load12MonthFlightChart()">
                            <option value="flights">Flights</option>
                            <option value="hours">Hours</option>
                            <option value="cargo">Cargo (<?php echo (cargo_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="fuel">Fuel Burnt (<?php echo (fuel_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="passengers">Passengers</option>
                            <option value="miles">Miles</option>
                            <option value="averageLandingRate">Avg Landing Rate</option>
                            <option value="averageFlightPerformanceScore">Avg Performance Score</option>
                        </select>
                        <select id="12mYear" class="form-select w-25 d-inline" onchange="Load12MonthFlightChart()">
                            <?php
                            foreach ($config->pirepAvailableYears as $year) {
                            ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                            <?php
                            } ?>
                        </select>
                        <div class="chart-area"><canvas id="12MonthFlights" width="100%" height="30"></canvas></div>
                    </div>
                    <div class="card-footer small text-muted">This chart is updated every 24 hours.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header" id="30d-header">
                        <?php echo date("F"); ?> Data (daily) <span class="30dloading chart-loading"><i
                                class="fas fa-spinner fa-spin"></i> Loading...</span>
                    </div>
                    <div class="card-body">
                        <select id="30dType" class="form-select w-25 d-inline" onchange="Load30DayFlightChart()">
                            <option value="flights">Flights</option>
                            <option value="hours">Hours</option>
                            <option value="cargo">Cargo (<?php echo (cargo_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="fuel">Fuel Burnt (<?php echo (fuel_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="passengers">Passengers</option>
                            <option value="miles">Miles</option>
                            <option value="averageLandingRate">Avg Landing Rate</option>
                            <option value="averageFlightPerformanceScore">Avg Performance Score</option>
                        </select>
                        <div class="chart-bar"><canvas id="CurrentMonthFlights" width="100%" height="30"></canvas></div>
                    </div>
                    <div class="card-footer small text-muted">This chart is updated every 24 hours.</div>
                </div>
            </div>
            <div class="col-xl-6 mb-4">
                <div class="card card-header-actions h-100">
                    <div class="card-header" id="hub-header">Base Activity (<?php echo date("F"); ?>) <span
                            class="hubloading chart-loading right"><i class="fas fa-spinner fa-spin"></i>
                            Loading...</span>
                    </div>
                    <div class="card-body">
                        <select id="hubType" class="form-select w-25 d-inline" onchange="LoadHubMonthChart()">
                            <option value="flights">Flights</option>
                            <option value="hours">Hours</option>
                            <option value="cargo">Cargo (<?php echo (cargo_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="fuel">Fuel Burnt (<?php echo (fuel_weight_display == 0 ? "kg" : "lb"); ?>)
                            </option>
                            <option value="passengers">Passengers</option>
                            <option value="miles">Miles</option>
                            <option value="averageLandingRate">Avg Landing Rate</option>
                            <option value="averageFlightPerformanceScore">Avg Performance Score</option>
                        </select>
                        <div class="chart-pie"><canvas id="hubChart" width="100%" height="50"></canvas></div>
                    </div>
                    <div class="card-footer small text-muted">This chart is updated every 24 hours.</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4 all-stats">
                    <div class="card-header">All-time Statistics</div>
                    <div class="list-group list-group-flush">
                        <div class="row gx-0">
                            <div class="col-lg-6">
                                <span class="list-group-item ">
                                    <i class="fas fa-user fa-fw text-blue me-2"></i>
                                    <span class="total-pilots"><i class="fas fa-spinner fa-spin"></i></span> Total
                                    Pilots
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-clock fa-fw text-purple me-2"></i>
                                    <span class="total-hours"><i class="fas fa-spinner fa-spin"></i></span> Total Hours
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-plane-departure fa-fw text-green me-2"></i>
                                    <span class="total-flights"><i class="fas fa-spinner fa-spin"></i></span> Total
                                    Flights
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-box fa-fw text-yellow me-2"></i>
                                    <span class="total-cargo"><i class="fas fa-spinner fa-spin"></i></span> Total Cargo
                                </span>
                            </div>
                            <div class="col-lg-6">
                                <span class="list-group-item">
                                    <i class="fas fa-globe fa-fw text-teal me-2"></i>
                                    <span class="total-miles"><i class="fas fa-spinner fa-spin"></i></span> Total Miles
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-gas-pump fa-fw text-red me-2"></i>
                                    <span class="total-fuel"><i class="fas fa-spinner fa-spin"></i></span> Total Fuel
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-person-walking-luggage fa-fw text-cyan me-2"></i>
                                    <span class="total-pax"><i class="fas fa-spinner fa-spin"></i></span> Total
                                    Passengers
                                </span>
                                <span class="list-group-item">
                                    <i class="fas fa-calendar fa-fw text-orange me-2"></i>
                                    <span class="total-schedules"><i class="fas fa-spinner fa-spin"></i></span>
                                    Total Schedules
                                </span>
                            </div>
                        </div>
                        <div class="card-footer small text-muted">This data is updated every 24 hours.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
</script>
<script type="text/javascript">
    let url = '<?php echo website_base_url; ?>';
    var month = '<?php echo date("F"); ?>';

    $(document).ready(function() {
        LoadAllTimeStats();
    });

    async function LoadAllTimeStats() {

        await fetch('<?php echo website_base_url; ?>includes/all_time_stats.php').then(function(response) {
            return response.json();
        }).then(function(json) {

            $(".total-pilots").html(json["activePilots"]);
            $(".total-hours").html(json["hours"]);
            $(".total-flights").html(json["flights"]);
            $(".total-cargo").html(json["cargo"]);
            $(".total-miles").html(json["miles"]);
            $(".total-fuel").html(json["fuel"]);
            $(".total-pax").html(json["passengers"]);
            $(".total-schedules").html(json["totalSchedules"]);
        }).catch(function(error) {
            console.error(error);
        });
    }
</script>
<script src="<?php echo website_base_url ?>/admin/charts/chart.js"></script>
<?php include 'includes/footer.php'; ?>