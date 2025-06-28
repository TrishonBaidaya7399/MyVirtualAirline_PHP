<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'config.php';
include 'lib/functions.php';
session_start();
?>
<?php include 'includes/header.php'; ?>
<style>
.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-banner {
    position: relative;
    min-height: 90vh;
    background-image: url('./assets/images/backgrounds/slc-line_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed; 
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 80px 0;
}

.hero-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
}

.hero-banner .container {
    position: relative;
    z-index: 2;
}

.glass-card {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 5px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-content {
    color: #333;
}

.dispatch-title {
    font-size: 4rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 20px;
    letter-spacing: 2px;
    text-transform: lowercase;
}

.flight-booking-section,
.charter-section {
    padding: 15px 30px;
}

.booking-text,
.charter-text {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
    font-weight: 400;
}

.find-flights-btn {
    background: #007bff;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin: 10px 0;
}

.find-flights-btn:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.charter-btn {
    background: transparent;
    border: 2px solid #6c757d;
    color: #495057;
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin: 10px 0;
}

.charter-btn:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.divider {
    position: relative;
    text-align: center;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.divider span {
    background: rgba(255, 255, 255, 0.8);
    padding: 0 20px;
    color: #666;
    font-weight: 500;
    position: relative;
    z-index: 2;
}

.btn i {
    margin-right: 8px;
}

.stats-parallax-section {
    position: relative;
    background-image: url('./assets/images/backgrounds/air_stats_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    padding: 60px 0;
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.stats-parallax-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.stats-parallax-section .stats.section {
    position: relative;
    z-index: 2;
    background: transparent;
}

.features.section {
    position: relative;
    background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    padding: 60px 0;
}

.features.section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.features.section .container-fluid {
    position: relative;
    z-index: 2;
}

.zoom-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    padding: 0 !important;
}

.zoom-img {
    width: 100%;
    height: 350px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.zoom-wrapper:hover .zoom-img {
    transform: scale(1.05);
}

.overlay-title {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0));
    color: white;
    padding: 100px 15px 15px;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    text-transform: lowercase;
    letter-spacing: 1px;
    z-index: 3;
}

.features .title {
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    position: relative;
    z-index: 2;
}

@media (max-width: 768px) {
    .hero-section {
        min-height: 50vh;
    }
    #myCarousel, .carousel-inner, .carousel-inner .item img{
        min-height: 50vh;
    }
    .features.section {
        background-attachment: scroll;
        padding: 40px 0;
    }
    .zoom-img {
        height: 200px;
    }
    .overlay-title {
        font-size: 16px;
        padding: 15px 10px 12px;
    }
}

@media (max-width: 576px) {
    .features.section {
        padding: 30px 0;
    }
    .zoom-img {
        height: 180px;
    }
    .overlay-title {
        font-size: 14px;
        padding: 12px 8px 10px;
    }
}

@media (max-width: 768px) {
    .stats-parallax-section {
        background-attachment: scroll;
        padding: 40px 0;
    }
}

@media (max-width: 576px) {
    .stats-parallax-section {
        padding: 30px 0;
    }
}

@media (max-width: 768px) {
    .flight-booking-section,
    .charter-section {
        padding: 10px 20px;
    }
    .dispatch-title {
        font-size: 2.5rem;
    }
    .find-flights-btn,
    .charter-btn {
        width: 100%;
        margin: 15px 0;
    }
    .hero-banner {
        background-attachment: scroll;
        padding: 60px 0;
    }
}

.card-img {
    margin-bottom: 20px;
}

@media (min-width: 992px) {
    .card-img {
        margin-bottom: 0px;
    }
}

@media (max-width: 480px) {
    .dispatch-title {
        font-size: 2rem;
    }
    .flight-booking-section,
    .charter-section {
        padding: 5px 10px;
    }
}

.join.section {
    padding: 60px 0;
    background: #f8f9fa;
}

.join.section .title {
    font-size: 2.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0;
    line-height: 1.2;
}

.btn-cta-primary {
    background: #007bff;
    color: white;
    padding: 15px 30px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    text-align: center;
    min-width: 150px;
}

.btn-cta-primary:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}
.carousel-control{
    display: flex;
    align-items: center;
    justify-content: center;
}
.slider_nav_btn{
    padding: 6px;
    border-radius: 50%;
    height: 40px;
    width: 40px;
    background: rgba(255,255, 255, 0.3);
}
@media (min-width: 768px) {
    .join-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
    }
    .join-text {
        flex: 1;
        text-align: right;
    }
    .join-button {
        flex-shrink: 0;
    }
}

@media (max-width: 767px) {
    .join.section {
        padding: 40px 0;
    }
    .join.section .title {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 25px;
    }
    .join-content {
        text-align: center;
    }
    .join-button {
        margin-top: 20px;
    }
    .btn-cta-primary {
        width: 100%;
        max-width: 250px;
        margin: 0 auto;
        display: block;
    }
}

@media (max-width: 576px) {
    .join.section {
        padding: 30px 0;
    }
    .join.section .title {
        font-size: 1.8rem;
        margin-bottom: 20px;
        line-height: 1.3;
    }
    .btn-cta-primary {
        padding: 12px 25px;
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .join.section .title {
        font-size: 1.6rem;
        padding: 0 15px;
    }
    .join.section .container {
        padding: 0 15px;
    }
}
</style>
<section class="hero-section">
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="item active">
                <img src="./assets/images/slides/slider1.jpeg">
                <div class="carousel-caption">
                    <h3>Welcome to MyAirline</h3>
                    <p><a href="#features" class="scrollto">Begin your decent into a new virtual career <i
                                class="fa fa-arrow-down bounce" aria-hidden="true"></i></a></p>
                </div>
            </div>
            <div class="item">
                <img src="./assets/images/slides/slider2.jpeg">
                <div class="carousel-caption">
                    <h3>A Modern Fleet</h3>
                    <p>With state of the art customizable flight tracking app
                    </p>
                </div>
            </div>
            <div class="item">
                <img src="./assets/images/slides/slider3.jpeg">
                <div class="carousel-caption">
                    <h3>Over 300 Destinations Worldwide</h3>
                    <p>A modern dispatch system including SimBrief integration
                    </p>
                </div>
            </div>
        </div>
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
        <div class="slider_nav_btn">

            <span class="fa-solid fa-chevron-left"></span>
            <span class="sr-only">Previous</span>
        </div>    
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
        <div class="slider_nav_btn">

            <span class="fa-solid fa-chevron-right"></span>
            <span class="sr-only">Next</span>
        </div>    
        </a>
    </div>
</section>
<section id="about" class="about section">
    <div class="container mini-stats-bar">
        <?php include_once 'site_widgets/mini_stats_bar.php'; ?>
    </div>
</section>
<div class="strip"></div>
<section id="features" class="features section">
  <div class="container-fluid" style="max-width: 98%; margin: 0 auto;">
    <h3 class="title text-left mb-4">features</h3>
    <div class="row">
     <div class="col-md-4 col-sm-12 card-img">
       <div class="zoom-wrapper position-relative">
       <a href="https://example.com/feature1" target="_blank">
         <img src="https://www.dalvirtual.com/flightcenter/images/pilots.jpg?text=Feature+1" alt="Feature 1" class="zoom-img">
         <div class="overlay-title">pilot leaderboard</div>
       </a>
     </div>
    </div>
      <div class="col-md-4 col-sm-12 card-img">
  <div class="zoom-wrapper position-relative">
    <a href="https://example.com/feature1" target="_blank">
      <img src="https://www.dalvirtual.com/flightcenter/images/schedules.jpg?text=Feature+1" alt="Feature 1" class="zoom-img">
      <div class="overlay-title">flight schedules</div>
    </a>
  </div>
</div>
      <div class="col-md-4 col-sm-12 card-img">
  <div class="zoom-wrapper position-relative">
    <a href="https://example.com/feature1" target="_blank">
      <img src="https://www.dalvirtual.com/flightcenter/images/notams.jpg?text=Feature+1" alt="Feature 1" class="zoom-img">
      <div class="overlay-title">airline notams</div>
    </a>
  </div>
</div>
    </div>
  </div>
</section>
<div class="strip"></div>
<section id="map-cont" class="map section">
    <?php include_once 'site_widgets/map_acars.php'; ?>
    <div id="map"></div>
    <div class="container">
            <p>Use the controls in the <strong>top left</strong> of the map to <strong>zoom in</strong> and
                <strong>out</strong>. <a href="live_flights.php"><i class="fa fa-external-link"></i> click here to
                    view
                    flights</a>.
            </p>
    </div>
</section>
<div class="stats-parallax-section">
    <section id="stats" class="stats section">
        <?php include_once 'site_widgets/bookings.php'; ?>
    </section>
    <section id="stats" class="stats section">
    <?php include_once 'site_widgets/latest_pireps.php'; ?>
</section>
<section id="stats" class="stats section">
    <?php include_once 'site_widgets/greased_landings.php'; ?>
</section>
<section id="stats" class="stats section">
    <?php include_once 'site_widgets/latest_pilots.php'; ?>
</section>
</div>
<section id="join" class="join section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="join-content">
                    <div class="join-text">
                        <h2 class="title">Are you looking for a new career in flight simulation?</h2>
                    </div>
                    <div class="join-button">
                        <a class="btn btn-cta-primary" href="../join.php">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';