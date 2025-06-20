<?php
include 'lib/functions.php';
include 'config.php';
session_start();
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
/* Statistics Section Styles with Parallax Background */
.statistics-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 200px);
    background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.statistics-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.statistics-section .container {
    position: relative;
    z-index: 2;
}

/* Statistics Title - Outside Card */
.statistics-title-wrapper {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.top-landings-title-wrapper{
    margin-bottom: 20px !important;
}
.top-landings-title{
    margin-bottom: 0px !important;
}
.arrivals-title-wrapper{
    margin-bottom: 20px !important;
}
.arrivals-title{
    margin-bottom: 0px !important;
}
.statistics-title {
    font-size: 3rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    text-transform: capitalize;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.statistics-icon {
    font-size: 2.5rem;
    color: #ffffff;
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Glassmorphism Card */
.statistics-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    padding: 30px;
    margin-bottom: 40px;
}

.statistics-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Statistics Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

/* Individual Stat Column */
.stat-column {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-column:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.stat-column-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #333;
    /* margin-bottom: 20px; */
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid #17a2b8;
}

/* Stat Row */
.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-row:last-child {
    border-bottom: none;
}

.stat-row:hover {
    background: rgba(23, 162, 184, 0.1);
    border-radius: 6px;
    padding-left: 10px;
    padding-right: 10px;
}

.stat-label {
    font-weight: 600;
    color: #555;
    font-size: 14px;
}

.stat-value {
    font-weight: 700;
    color: #17a2b8;
    font-size: 16px;
    min-width: 60px;
    text-align: right;
}

.stat-value i {
    color: #17a2b8;
}

/* Special styling for different stat types */
.stat-column.week-stats .stat-column-title {
    border-bottom-color: #28a745;
}

.stat-column.week-stats .stat-value {
    color: #28a745;
}

.stat-column.week-stats .stat-value i {
    color: #28a745;
}

.stat-column.month-stats .stat-column-title {
    border-bottom-color: #ffc107;
}

.stat-column.month-stats .stat-value {
    color: #ffc107;
}

.stat-column.month-stats .stat-value i {
    color: #ffc107;
}

.stat-column.alltime-stats .stat-column-title {
    border-bottom-color: #dc3545;
}

.stat-column.alltime-stats .stat-value {
    color: #dc3545;
}

.stat-column.alltime-stats .stat-value i {
    color: #dc3545;
}

/* Widgets Section */
.widgets-section {
    /* margin-top: 40px; */
    margin-left: -15px;
    display: flex;
    flex-direction: column;
    gap: 30px
}

/* Responsive Design */
@media (max-width: 1200px) {
    .statistics-section {
        padding: 50px 0;
    }
    
    .statistics-title {
        font-size: 2.5rem;
    }
    
    .statistics-icon {
        font-size: 2rem;
    }
    
    .statistics-glass-card {
        padding: 25px;
    }
    
    .stats-grid {
        gap: 25px;
    }
    
    .stat-column {
        padding: 20px;
    }
}

@media (max-width: 992px) {
    .statistics-section {
        padding: 40px 0;
        background-attachment: scroll;
    }
    
    .statistics-title {
        font-size: 2.2rem;
        text-align: left;
    }
    
    .statistics-icon {
        font-size: 2rem;
    }
    
    .statistics-glass-card {
        margin: 0 15px 30px;
        border-radius: 12px;
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .statistics-section {
        padding: 30px 0;
        background-attachment: scroll;
    }
    
    .statistics-title {
        font-size: 2rem;
    }
    
    .statistics-icon {
        font-size: 1.8rem;
    }
    
    .statistics-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .statistics-glass-card {
        margin: 0 10px 25px;
        border-radius: 10px;
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-column {
        padding: 15px;
    }
    
    .stat-column-title {
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
    
    .stat-row {
        padding: 10px 0;
    }
    
    .stat-label {
        font-size: 13px;
    }
    
    .stat-value {
        font-size: 14px;
    }
    .widgets-section {
    /* margin-top: 40px; */
    margin-left: -15px;
    display: flex;
    flex-direction: column;
    gap: 0px
}
}

@media (max-width: 576px) {
    .statistics-section {
        padding: 25px 0;
    }
    .widgets-section {
    /* margin-top: 40px; */
    margin-left: 0px;
    display: flex;
    flex-direction: column;
    gap: 0px
    }
    
    .statistics-title {
        font-size: 1.8rem;
    }
    
    .statistics-icon {
        font-size: 1.5rem;
    }
    
    .statistics-title-wrapper {
        gap: 10px;
    }
    
    .statistics-glass-card {
        margin: 0 5px 20px;
        border-radius: 8px;
        padding: 12px;
    }
    
    .stat-column {
        padding: 12px;
    }
    
    .stat-column-title {
        font-size: 1.1rem;
        margin-bottom: 12px;
    }
    
    .stat-row {
        padding: 8px 0;
    }
    
    .stat-label {
        font-size: 12px;
    }
    
    .stat-value {
        font-size: 13px;
        min-width: 50px;
    }
}

@media (max-width: 480px) {
    .statistics-section {
        padding: 20px 0;
    }
    
    .statistics-title {
        font-size: 1.6rem;
    }
    
    .statistics-icon {
        font-size: 1.3rem;
    }
    
    .statistics-glass-card {
        padding: 10px;
    }
    
    .stat-column {
        padding: 10px;
    }
    
    .stat-column-title {
        font-size: 1rem;
        margin-bottom: 10px;
    }
    
    .stat-row {
        padding: 6px 0;
    }
    
    .stat-label {
        font-size: 11px;
    }
    
    .stat-value {
        font-size: 12px;
    }
}

/* Loading animation enhancement */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.fa-spin {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>
<?php include 'includes/header.php'; ?>
<section id="content" class="section statistics-section offset-header">
    <div class="container">
        <!-- Statistics Title - Outside Card -->
        <div class="statistics-title-wrapper">
            <h3 class="statistics-title">airline performance statistics</h3>
            <i class="fa fa-chart-bar statistics-icon" aria-hidden="true"></i>
        </div>
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="statistics-glass-card">
                    <div class="stats-grid">
                        <!-- 7 Day Statistics -->
                        <div class="stat-column week-stats">
                            <h4 class="stat-column-title">7 Day Statistics</h4>
                            
                            <div class="stat-row">
                                <span class="stat-label">Active Pilots</span>
                                <span class="stat-value" name="week-active-pilots">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Hours</span>
                                <span class="stat-value" name="week-hours">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Flights</span>
                                <span class="stat-value" name="week-flights">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Miles (nm)</span>
                                <span class="stat-value" name="week-miles">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Fuel Used</span>
                                <span class="stat-value" name="week-fuel">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Passengers</span>
                                <span class="stat-value" name="week-pax">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Cargo</span>
                                <span class="stat-value" name="week-cargo">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                        </div>

                        <!-- 30 Day Statistics -->
                        <div class="stat-column month-stats">
                            <h4 class="stat-column-title">30 Day Statistics</h4>
                            
                            <div class="stat-row">
                                <span class="stat-label">Active Pilots</span>
                                <span class="stat-value" name="thirty-active-pilots">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Hours</span>
                                <span class="stat-value" name="thirty-hours">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Flights</span>
                                <span class="stat-value" name="thirty-flights">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Miles</span>
                                <span class="stat-value" name="thirty-miles">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Fuel Used</span>
                                <span class="stat-value" name="thirty-fuel">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Passengers</span>
                                <span class="stat-value" name="thirty-pax">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Cargo</span>
                                <span class="stat-value" name="thirty-cargo">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                        </div>

                        <!-- All-time Statistics -->
                        <div class="stat-column alltime-stats">
                            <h4 class="stat-column-title">All-time Statistics</h4>
                            
                            <div class="stat-row">
                                <span class="stat-label">Total Pilots</span>
                                <span class="stat-value" name="all-pilots">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Hours</span>
                                <span class="stat-value" name="all-hours">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Flights</span>
                                <span class="stat-value" name="all-flights">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Miles (nm)</span>
                                <span class="stat-value" name="all-miles">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Fuel Used</span>
                                <span class="stat-value" name="all-fuel">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Passengers</span>
                                <span class="stat-value" name="all-pax">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Cargo</span>
                                <span class="stat-value" name="all-cargo">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                            
                            <div class="stat-row">
                                <span class="stat-label">Total Schedules</span>
                                <span class="stat-value" name="all-schedules">
                                    <i class="fa fa-circle-o-notch fa-spin"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Widgets Section -->
        <div class="widgets-section">
            <?php include_once 'site_widgets/greased_landings.php'; ?>
            <?php include_once 'site_widgets/latest_pireps.php'; ?>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        LoadAllTimeStats();
        Load7DayStats();
        Load30DayStats();
    });

    async function LoadAllTimeStats() {
        await fetch('<?php echo website_base_url; ?>includes/all_time_stats.php').then(function(response) {
            return response.json();
        }).then(function(json) {
            $("[name=all-pilots]").html(json["activePilots"]);
            $("[name=all-hours]").html(json["hours"]);
            $("[name=all-flights]").html(json["flights"]);
            $("[name=all-cargo]").html(json["cargo"]);
            $("[name=all-miles]").html(json["miles"]);
            $("[name=all-fuel]").html(json["fuel"]);
            $("[name=all-pax]").html(json["passengers"]);
            $("[name=all-schedules]").html(json["totalSchedules"]);
        }).catch(function(error) {
            console.error(error);
        });
    }
    
    async function Load7DayStats() {
        await fetch('<?php echo website_base_url; ?>includes/seven_day_stats.php').then(function(response) {
            return response.json();
        }).then(function(json) {
            $("[name=week-active-pilots]").html(json["activePilots"]);
            $("[name=week-hours]").html(json["hours"]);
            $("[name=week-flights]").html(json["flights"]);
            $("[name=week-cargo]").html(json["cargo"]);
            $("[name=week-miles]").html(json["miles"]);
            $("[name=week-fuel]").html(json["fuel"]);
            $("[name=week-pax]").html(json["passengers"]);
            $("[name=week-schedules]").html(json["totalSchedules"]);
        }).catch(function(error) {
            console.error(error);
        });
    }
    
    async function Load30DayStats() {
        await fetch('<?php echo website_base_url; ?>includes/thirty_day_stats.php').then(function(response) {
            return response.json();
        }).then(function(json) {
            $("[name=thirty-active-pilots]").html(json["activePilots"]);
            $("[name=thirty-hours]").html(json["hours"]);
            $("[name=thirty-flights]").html(json["flights"]);
            $("[name=thirty-cargo]").html(json["cargo"]);
            $("[name=thirty-miles]").html(json["miles"]);
            $("[name=thirty-fuel]").html(json["fuel"]);
            $("[name=thirty-pax]").html(json["passengers"]);
            $("[name=thirty-schedules]").html(json["totalSchedules"]);
        }).catch(function(error) {
            console.error(error);
        });
    }
</script>

<?php include 'includes/footer.php'; ?>