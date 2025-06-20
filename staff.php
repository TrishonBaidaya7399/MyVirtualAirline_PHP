<?php
include 'lib/functions.php';
include 'config.php';

use Proxy\Api\Api;

Api::__constructStatic();

$staffs = null;
$res = Api::sendSync('GET', 'v1/staffs', null);
if ($res->getStatusCode() == 200) {
	$staffs = json_decode($res->getBody(), false);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>


<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.css">

<style>

.team-section {
    position: relative;
    padding: 60px 0;
    background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.team-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.team-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}


.team-description-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 50px;
    text-align: center;
    transition: all 0.3s ease;
}

.team-description-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.team-title {
    font-size: 4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 25px;
    letter-spacing: 2px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    font-family: 'Montserrat', sans-serif;
    margin-top: 0px !important;
}


.team-description {
    font-size: 18px;
    line-height: 1.8;
    color: #ffffff;
    text-align: center;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    margin: 0;
}


.team-members-wrapper {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.team-members-title {
    font-size: 2.5rem;
    font-weight: 600;
    color: #ffffff;
    text-align: center;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    border-bottom: 2px solid rgba(4, 217, 245, 0.5);
    padding-bottom: 6px;
    width: fit-content;
}


.team-swiper {
    width: 100%;
    padding: 20px 0 50px;
    height: fit-content;
}

.team-swiper .swiper-slide {
    height: auto;
    display: flex;
    justify-content: center;
}


.team-member-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    width: 100%;
    max-width: 320px;
    margin: 0 auto;
}

.team-member-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
    background: rgba(255, 255, 255, 0.25);
}


.member-image-wrapper {
    margin-bottom: 20px;
    position: relative;
}

.member-profile-image {
    width: 120px;
    height: 120px;
    min-width: 120px;
    min-height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.member-profile-image:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.8);
}

.image-circle {
    width: 120px;
    height: 120px;
    min-width: 120px;
    min-height: 120px;
    font-size: 120px;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
    border-radius: 50%;
    line-height: 1;
    background: rgba(255, 255, 255, 0.7);
}

.image-circle::before {
    height: 120px;

}
.member-name {
    font-size: 1.4rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 8px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.member-name a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.member-name a:hover {
    color: #f0f8ff;
    text-decoration: underline;
}

.member-role {
    font-size: 1.1rem;
    font-weight: 600;
    color: #e6f3ff;
    margin-bottom: 10px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.member-email {
    font-size: 13px;
    color: #cce7ff;
    margin-bottom: 12px;
    word-break: break-word;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.member-email i {
    margin-right: 5px;
    color: #b3d9ff;
}

.member-description {
    font-size: 13px;
    line-height: 1.5;
    color: #e6f3ff;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    margin: 0;
}


.team-swiper .swiper-button-next,
.team-swiper .swiper-button-prev {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin-top: -25px;
    transition: all 0.3s ease;
}

.team-swiper .swiper-button-next:hover,
.team-swiper .swiper-button-prev:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.team-swiper .swiper-button-next:after,
.team-swiper .swiper-button-prev:after {
    font-size: 18px;
    font-weight: 600;
}


.team-swiper .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.5);
    opacity: 0.7;
    width: 12px;
    height: 12px;
}

.team-swiper .swiper-pagination-bullet-active {
    background: #ffffff;
    opacity: 1;
    transform: scale(1.2);
}


.no-staff-message {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 40px;
    text-align: center;
    color: #ffffff;
    font-size: 1.2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}


@media (max-width: 1200px) {
    .team-title {
        font-size: 3rem;
    }
    
    .team-description-card {
        padding: 35px;
    }
    
    .member-profile-image {
        width: 100px;
        height: 100px;
    }
    
    .image-circle {
        font-size: 100px;
    }
}

@media (max-width: 992px) {
    .team-section {
        padding: 40px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 80px;
    }
    
    .team-title {
        font-size: 2.5rem;
    }
    
    .team-description-card {
        padding: 30px;
        margin-bottom: 40px;
    }
    
    .team-description {
        font-size: 15px;
    }
    
    .team-members-title {
        font-size: 2.2rem;
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    .team-section {
        padding: 30px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 60px;
    }
    
    .team-title {
        font-size: 2.2rem;
        margin-bottom: 20px;
    }
    
    .team-description-card {
        padding: 25px 20px;
        margin-bottom: 30px;
        border-radius: 15px;
    }
    
    .team-description {
        font-size: 14px;
        text-align: center;
    }
    
    .team-members-title {
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .team-member-card {
        padding: 25px 15px;
        max-width: 280px;
    }
    
    .member-profile-image {
        width: 90px;
        height: 90px;
    }
    
    .image-circle {
        font-size: 90px;
    }
    
    .member-name {
        font-size: 1.2rem;
    }
    
    .member-role {
        font-size: 1rem;
    }
    
    .team-swiper .swiper-button-next,
    .team-swiper .swiper-button-prev {
        width: 40px;
        height: 40px;
        margin-top: -20px;
    }
    
    .team-swiper .swiper-button-next:after,
    .team-swiper .swiper-button-prev:after {
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .team-section {
        padding: 25px 0;
    }
    
    .offset-header {
        padding-top: 50px;
    }
    
    .team-title {
        font-size: 1.9rem;
        margin-bottom: 15px;
    }
    
    .team-description-card {
        padding: 20px 15px;
        margin-bottom: 25px;
        border-radius: 12px;
    }
    
    .team-description {
        font-size: 13px;
        line-height: 1.6;
    }
    
    .team-members-title {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .team-member-card {
        padding: 20px 12px;
        max-width: 260px;
        border-radius: 15px;
    }
    
    .member-profile-image {
        width: 80px;
        height: 80px;
    }
    
    .image-circle {
        font-size: 80px;
    }
    
    .member-name {
        font-size: 1.1rem;
    }
    
    .member-role {
        font-size: 0.95rem;
    }
    
    .member-email,
    .member-description {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .team-section {
        padding: 20px 0;
    }
    
    .offset-header {
        padding-top: 40px;
    }
    
    .team-title {
        font-size: 1.7rem;
    }
    
    .team-description-card {
        padding: 15px 12px;
        border-radius: 10px;
    }
    
    .team-description {
        font-size: 12px;
    }
    
    .team-members-title {
        font-size: 1.6rem;
    }
    
    .team-member-card {
        padding: 15px 10px;
        max-width: 240px;
    }
    
    .member-profile-image {
        width: 70px;
        height: 70px;
    }
    
    .image-circle {
        font-size: 70px;
    }
    
    .member-name {
        font-size: 1rem;
    }
    
    .member-role {
        font-size: 0.9rem;
    }
    
    .member-email,
    .member-description {
        font-size: 11px;
    }
}


@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .member-profile-image {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}


@media (prefers-color-scheme: dark) {
    .team-section {
        background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                          url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    }
}
</style>
<?php include 'includes/header.php'; ?>
<section id="content" class="section team-section offset-header">
    <div class="container">
        <!-- Team Description Glass Card -->
        <div class="team-description-card">
            <h1 class="team-title">Meet the team</h1>
            <p class="team-description">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                printer took a galley of type and scrambled it to make a type specimen book. It has survived not
                only five centuries, but also the leap into electronic typesetting, remaining essentially
                unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem
                Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker
                including versions of Lorem Ipsum.
            </p>
        </div>

        <!-- Team Members Section -->
        <div class="team-members-wrapper">
            <?php if (!empty($staffs)) { ?>
                <h2 class="team-members-title">Our Team Members</h2>
                
                <!-- Swiper Container -->
                <div class="swiper team-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($staffs as $key => $staff) { ?>
                        <div class="swiper-slide">
                            <div class="team-member-card">
                                <div class="member-image-wrapper">
                                    <?php if ($staff->profileImage != "") { ?>
                                    <img src="<?php echo website_base_url; ?>uploads/profiles/<?php echo $staff->profileImage ?>"
                                         class="member-profile-image" 
                                         alt="<?php echo $staff->staffName ?>" />
                                    <?php } else { ?>
                                    <img src="./images/avatar.webp"
                                         class="member-profile-image" 
                                         alt="avatar" />
                                    <?php } ?>
                                </div>
                                
                                <h3 class="member-name">
                                    <a href="<?php echo website_base_url; ?>profile.php?id=<?php echo $staff->staffPilotId; ?>">
                                        <?php echo $staff->staffName ?>
                                    </a>
                                </h3>
                                
                                <p class="member-role"><?php echo $staff->roleName ?></p>
                                
                                <?php if ($staff->contactEmail != "") { ?>
                                <p class="member-email">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                    <?php echo $staff->contactEmail ?>
                                </p>
                                <?php } ?>
                                
                                <p class="member-description"><?php echo $staff->roleDescription ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    
                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
                
            <?php } else { ?>
                <div class="no-staff-message">
                    <strong>There are currently no staff members profiles to display.</strong>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.7/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    const swiper = new Swiper('.team-swiper', {
        // Responsive breakpoints
        slidesPerView: 1,
        spaceBetween: 30,
        centeredSlides: false,
        
        breakpoints: {
            // When window width is >= 768px
            768: {
                slidesPerView: 2,
                spaceBetween: 30,
                centeredSlides: false,
            },
            // When window width is >= 1024px
            1024: {
                slidesPerView: 3,
                spaceBetween: 30,
                centeredSlides: false,
            }
        },
        
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        
        // Auto height
        autoHeight: false,
        
        // Loop if more than slides per view
        loop: <?php echo (count($staffs) > 3) ? 'true' : 'false'; ?>,
        
        // Grab cursor
        grabCursor: true,
        
        // Touch events
        touchEventsTarget: 'container',
        simulateTouch: true,
        
        // Autoplay (optional)
        // autoplay: {
        //     delay: 5000,
        //     disableOnInteraction: false,
        // },
        
        // Smooth transitions
        speed: 600,
        effect: 'slide',
        
        // Accessibility
        a11y: {
            prevSlideMessage: 'Previous team member',
            nextSlideMessage: 'Next team member',
        }
    });
    
    // Optional: Update swiper when window resizes
    window.addEventListener('resize', function() {
        swiper.update();
    });
});
</script>

<?php include 'includes/footer.php'; ?>