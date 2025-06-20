<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php
include 'lib/functions.php';
include 'config.php';
session_start();
?>
<style>
    .about-section {
        position: relative;
        padding: 80px 0;
        min-height: calc(100vh - 128px);
        background-image: url('./assets/images/backgrounds/homepg_lower_bg.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .about-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
}
.about-section .container {
    position: relative;
    z-index: 2;
}
    .glass-card{
          background: rgba(255, 255, 255, 0.20) !important;
    backdrop-filter: blur(15px) !important;
    -webkit-backdrop-filter: blur(15px) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 15px !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    margin-bottom: 0px !important;
    }
    @media screen and (max-width: 840px){
        .img-circle{
            max-height: 300px;   
        }
    }
    @media screen and (max-width: 680px){
        .img-circle{
            max-height: 270px;   
        }
    }
    @media screen and (max-width: 400px){
        .img-circle{
            max-height: 240px;   
        }
    }
    
</style>
    <?php include 'includes/header.php';?>
<section id="content" class="section about-section">
    <div class="container">
        <div class="row">
            <div class="jumbotron glass-card">
                <h1 class="text-center" style="margin-top: 0px">About Us</h1>
                <img src="<?php echo website_base_url; ?>images/about-img.png" style="float:right; padding:15px;"
                    class="img-circle" />
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit
                aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione
                voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet,
                consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et
                dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum
                exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi
                consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil
                molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
                <br /><br />
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
                cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                We have a saying here, fly quality over quantity. <br /><br />
                Ut dignissim condimentum erat sit amet pellentesque. Aenean lacus mi, lobortis et sem
                interdum, ultrices tincidunt felis. Praesent lacinia cursus arcu, id ultricies leo viverra
                in. Phasellus rutrum nibh eu mollis dapibus. Aenean auctor quam lorem, a pulvinar odio
                finibus ut. Vivamus commodo pulvinar quam, vel porta lorem vestibulum ut. Proin arcu lorem,
                volutpat id justo et, venenatis ullamcorper tellus. Vivamus fringilla dolor quis urna
                tristique aliquet. Praesent a tortor dignissim, molestie eros non, porta enim. Nullam at
                mattis neque. Nunc ut tellus sit amet justo feugiat condimentum quis dapibus mauris.
                Praesent nulla metus, posuere nec mollis quis, semper eu odio. Proin eget nisi suscipit,
                dignissim risus et, tincidunt nulla.
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';