<?php
include 'lib/functions.php';
include 'config.php';

use Proxy\Api\Api;

Api::__constructStatic();
session_start();

$awards = null;
$res = Api::sendSync('GET', 'v1/awards', null);
if ($res->getStatusCode() == 200) {
	$awards = json_decode($res->getBody(), false);
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
/* Awards Section Styles with Parallax Background */
.awards-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.awards-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.awards-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* Awards Description Glass Card */
.awards-description-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 50px;
    transition: all 0.3s ease;
}

.awards-description-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.awards-main-title {
    font-size: 4srem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 25px;
    margin-top: 0px;
    letter-spacing: 2px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    font-family: 'Montserrat', sans-serif;
    text-align: center;
}

.awards-description {
    font-size: 20px;
    line-height: 1.8;
    color: #ffffff;
    text-align: justify;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    margin: 0;
}

/* Awards Title - Outside Card */
.awards-title-wrapper {
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.awards-title {
    font-size: 3rem;
    font-weight: 300;
    color: #ffffff;
    margin: 0;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.awards-icon {
    font-size: 3rem;
    color: rgba(255, 215, 0, 1);
    opacity: 0.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Glassmorphism Card */
.awards-glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.awards-glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Table Styles */
.awards-table-wrapper {
    overflow-x: auto;
    max-height: 600px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

/* Custom scrollbar for webkit browsers */
.awards-table-wrapper::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.awards-table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.awards-table-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 193, 7, 0.6);
    border-radius: 4px;
}

.awards-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 193, 7, 0.8);
}

.awards-table {
    margin: 0;
    background: transparent;
      color: rgba(0,0,0,9);
    min-width: 700px;
    width: 100%;
}

.awards-table thead th {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: rgba(0,0,0,1);
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 15px 12px;
    font-size: 18px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
    z-index: 10;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    text-wrap: nowrap;
}

.awards-table tbody tr {
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}

.awards-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.002);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.awards-table tbody td {
    padding: 20px 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    vertical-align: middle;
    font-size: 14px;
     color: rgba(0,0,0,9);
}

.awards-table tbody tr:last-child td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

/* Award Image Container Styling */
.awards-table tbody td:first-child {
    text-align: center;
    padding: 15px;
    width: 150px;
}

.award-image-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.award-image-container img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.award-image-container:hover img {
    transform: scale(1.05);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 193, 7, 0.6);
}

/* Fullscreen Icon Overlay */
.fullscreen-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 8px;
}

.award-image-container:hover .fullscreen-overlay {
    opacity: 1;
}

.fullscreen-icon {
    color: #ffffff;
    font-size: 24px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Award Name Styling */
.awards-table tbody td:nth-child(2) {
    font-weight: 600;
   color: rgba(0,0,0,9);
    font-size: 16px;
    width: 200px;
}

/* Award Description Styling */
.awards-table tbody td:nth-child(3) {
    color: rgba(0,0,0,9);
    line-height: 1.6;
    font-size: 14px;
}

/* Fullscreen Modal */
.fullscreen-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.fullscreen-modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fullscreen-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.fullscreen-image {
    width: 90%;
    height: 90%;
    object-fit: contain;
    border-radius: 0;
    box-shadow: none;
    border: none;
}

.fullscreen-caption {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin-top: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
    text-align: center;
}

.close-fullscreen {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #ff6b6b;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 5px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.close-fullscreen:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

/* No Data Message */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #ffffff;
    font-style: italic;
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    border-radius: 15px;
    font-size: 1.2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .awards-section {
        padding: 50px 0;
    }
    
    .awards-main-title {
        font-size: 3rem;
    }
    
    .awards-title {
        font-size: 2.5rem;
    }
    
    .awards-icon {
        font-size: 2rem;
    }
    
    .awards-description-card {
        padding: 35px;
    }
    
    .award-image-container img {
        width: 90px;
        height: 90px;
    }
    
    .awards-table tbody td {
        padding: 18px 12px;
        font-size: 13px;
    }
}

@media (max-width: 992px) {
    .awards-section {
        padding: 40px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 80px;
    }
    
    .awards-main-title {
        font-size: 2.5rem;
    }
    
    .awards-title {
        font-size: 2.2rem;
        text-align: left;
    }
    
    .awards-icon {
        font-size: 2rem;
    }
    
    .awards-description-card {
        padding: 30px;
        margin-bottom: 40px;
    }
    
    .awards-description {
        font-size: 15px;
    }
    
    .awards-glass-card {
        margin: 0 15px;
        border-radius: 12px;
    }
    
    .awards-table-wrapper {
        max-height: 500px;
    }
    
    .award-image-container img {
        width: 80px;
        height: 80px;
    }
}

@media (max-width: 768px) {
    .awards-section {
        padding: 30px 0;
        background-attachment: scroll;
    }
    
    .offset-header {
        padding-top: 60px;
    }
    
    .awards-main-title {
        font-size: 2.2rem;
        margin-bottom: 20px;
    }
    
    .awards-title {
        font-size: 2rem;
    }
    
    .awards-icon {
        font-size: 1.8rem;
    }
    
    .awards-title-wrapper {
        justify-content: center;
        gap: 15px;
    }
    
    .awards-description-card {
        padding: 25px 20px;
        margin-bottom: 30px;
        border-radius: 15px;
    }
    
    .awards-description {
        font-size: 14px;
        text-align: left;
    }
    
    .awards-glass-card {
        margin: 0 10px;
        border-radius: 10px;
    }
    
    .awards-table-wrapper {
        max-height: 400px;
    }
    
    .awards-table {
        font-size: 12px;
        min-width: 600px;
    }
    
    .awards-table tbody td {
        padding: 15px 10px;
        font-size: 12px;
    }
    
    .awards-table tbody td:first-child {
        width: 120px;
    }
    
    .award-image-container img {
        width: 70px;
        height: 70px;
    }
    
    .awards-table tbody td:nth-child(2) {
        font-size: 14px;
        width: 150px;
    }
    
    .fullscreen-icon {
        font-size: 18px;
    }
}

@media (max-width: 576px) {
    .awards-section {
        padding: 25px 0;
    }
    
    .offset-header {
        padding-top: 50px;
    }
    
    .awards-main-title {
        font-size: 1.9rem;
        margin-bottom: 15px;
    }
    
    .awards-title {
        font-size: 1.8rem;
    }
    
    .awards-icon {
        font-size: 1.5rem;
    }
    
    .awards-title-wrapper {
        gap: 10px;
    }
    
    .awards-description-card {
        padding: 20px 15px;
        margin-bottom: 25px;
        border-radius: 12px;
    }
    
    .awards-description {
        font-size: 13px;
        line-height: 1.6;
    }
    
    .awards-glass-card {
        margin: 0 5px;
        border-radius: 8px;
    }
    
    .awards-table-wrapper {
        max-height: 350px;
    }
    
    .awards-table {
        font-size: 11px;
        min-width: 550px;
    }
    
    .awards-table tbody td {
        padding: 12px 8px;
        font-size: 11px;
    }
    
    .awards-table tbody td:first-child {
        width: 100px;
    }
    
    .award-image-container img {
        width: 60px;
        height: 60px;
    }
    
    .awards-table tbody td:nth-child(2) {
        font-size: 12px;
        width: 120px;
    }
    
    .fullscreen-icon {
        font-size: 16px;
    }
}

@media (max-width: 480px) {
    .awards-section {
        padding: 20px 0;
    }
    
    .offset-header {
        padding-top: 40px;
    }
    
    .awards-main-title {
        font-size: 1.7rem;
    }
    
    .awards-title {
        font-size: 1.6rem;
    }
    
    .awards-icon {
        font-size: 1.3rem;
    }
    
    .awards-description-card {
        padding: 15px 12px;
        border-radius: 10px;
    }
    
    .awards-description {
        font-size: 12px;
    }
    
    .awards-table-wrapper {
        max-height: 300px;
    }
    
    .awards-table {
        min-width: 500px;
    }
    
    .awards-table tbody td {
        padding: 10px 6px;
        font-size: 10px;
    }
    
    .awards-table tbody td:first-child {
        width: 90px;
    }
    
    .award-image-container img {
        width: 50px;
        height: 50px;
    }
    
    .awards-table tbody td:nth-child(2) {
        font-size: 11px;
        width: 100px;
    }
    
    .no-data-message {
        padding: 40px 15px;
        font-size: 16px;
    }
    
    .fullscreen-icon {
        font-size: 14px;
    }
    
    .close-fullscreen {
        top: -40px;
        font-size: 28px;
        width: 40px;
        height: 40px;
    }
    
    .fullscreen-caption {
        font-size: 16px;
        margin-top: 15px;
    }
}

/* High DPI Displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .award-image-container img,
    .fullscreen-image {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }
}

/* Print Styles */
@media print {
    .awards-section {
        background: white;
        padding: 20px 0;
    }
    
    .awards-section::before {
        display: none;
    }
    
    .awards-main-title,
    .awards-title {
        color: black;
        text-shadow: none;
    }
    
    .awards-description-card,
    .awards-glass-card,
    .awards-table-wrapper {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    
    .awards-description,
    .awards-table tbody td,
    .awards-table thead th {
        color: black;
        text-shadow: none;
    }
    
    .awards-table thead th,
    .awards-table tbody td {
        background: white;
        border: 1px solid #ccc;
    }
    
    .fullscreen-modal {
        display: none !important;
    }
    
    .fullscreen-overlay {
        display: none;
    }
}
/* Global Heading */
.global-heading {
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
}

.global-heading .global-title {
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0 !important;
    text-transform: lowercase;
    width: fit-content;
}
.global-heading i{
    font-size: 20px;
    margin-bottom: 3px;
}
</style>

<?php include 'includes/header.php';?>

<section id="content" class="section awards-section offset-header">
    <div class="container">
        <!-- Awards Description Glass Card -->
         <div class="global-heading">
                    <h3 class="global-title">Our Awards</h3>
                </div>
        <div class="awards-description-card">
            <p class="awards-description">
                Ut dignissim condimentum erat sit amet pellentesque. Aenean lacus mi, lobortis et sem
                interdum, ultrices tincidunt felis. Praesent lacinia cursus arcu, id ultricies leo viverra
                in. Phasellus rutrum nibh eu mollis dapibus. Aenean auctor quam lorem, a pulvinar odio
                finibus ut. Vivamus commodo pulvinar quam, vel porta lorem vestibulum ut. Proin arcu lorem,
                volutpat id justo et, venenatis ullamcorper tellus. Vivamus fringilla dolor quis urna
                tristique aliquet. Praesent a tortor dignissim, molestie eros non, porta enim. Nullam at
                mattis neque. Nunc ut tellus sit amet justo feugiat condimentum quis dapibus mauris.
                Praesent nulla metus, posuere nec mollis quis, semper eu odio. Proin eget nisi suscipit,
                dignissim risus et, tincidunt nulla.
            </p>
        </div>

        <!-- Awards Title - Outside Card -->
         <div class="global-heading" style="display: flex; align-items: center; justify-content: start; gap: 10px">
                    <h3 class="global-title">Awards Collection</h3>
            <i class="fa fa-trophy awards-icon" aria-hidden="true"></i></h3>
                </div>
      
        
        <!-- Glassmorphism Card -->
        <div class="row">
            <div class="col-12">
                <div class="awards-glass-card">
                    <?php if (!empty($awards)) { ?>
                    <div class="awards-table-wrapper">
                        <table class="table table-striped awards-table">
                            <thead>
                                <tr>
                                    <th><strong>Award Image</strong></th>
                                    <th><strong>Award Name</strong></th>
                                    <th><strong>Description</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($awards as $key => $award) { ?>
                                <tr>
                                    <td>
                                        <div class="award-image-container" onclick="openFullscreen('<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>', '<?php echo htmlspecialchars($award->name, ENT_QUOTES); ?>')">
                                            <img src="<?php echo website_base_url; ?>uploads/awards/<?php echo $award->imageUrl; ?>"
                                                 alt="<?php echo htmlspecialchars($award->name, ENT_QUOTES); ?>" />
                                            <div class="fullscreen-overlay">
                                                <i class="fa fa-expand fullscreen-icon" style="height: 30px; width: 30px; size: 30px; color: #fff" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($award->name, ENT_QUOTES); ?></td>
                                    <td><?php echo htmlspecialchars($award->description, ENT_QUOTES); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <div class="no-data-message">
                        <strong>There are currently no awards to display.</strong>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fullscreen Modal -->
<div id="fullscreenModal" class="fullscreen-modal" onclick="closeFullscreen()">
    <div class="fullscreen-content" onclick="event.stopPropagation()">
        <span class="close-fullscreen" onclick="closeFullscreen()">&times;</span>
        <img id="fullscreenImage" class="fullscreen-image" src="" alt="">
        <div id="fullscreenCaption" class="fullscreen-caption"></div>
    </div>
</div>

<script>
function openFullscreen(imageSrc, imageAlt) {
    const modal = document.getElementById('fullscreenModal');
    const image = document.getElementById('fullscreenImage');
    const caption = document.getElementById('fullscreenCaption');
    
    image.src = imageSrc;
    image.alt = imageAlt;
    caption.textContent = imageAlt;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeFullscreen() {
    const modal = document.getElementById('fullscreenModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeFullscreen();
    }
});

// Prevent context menu on images to enhance UX
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.award-image-container img');
    images.forEach(function(img) {
        img.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>