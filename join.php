<?php
use Proxy\Api\Api;

require_once __DIR__ . '/proxy/api.php';
include 'lib/functions.php';
include 'config.php';
include 'lib/emailer.php';
session_start();
Api::__constructStatic();
$status = null;
$bases = null;
$res = Api::sendSync('GET', 'v1/operations/bases', null);
if ($res->getStatusCode() == 200) {
    $bases = json_decode($res->getBody(), false);
}
$firstname = null;
$surname = null;
$email = null;
$background = null;
$ivao_id = null;
$vatsim_id = null;
$custom1 = null;
$custom2 = null;
$custom3 = null;
$custom4 = null;
$custom5 = null;
$hub = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = cleanString($_POST['firstname']);
    $surname = cleanString($_POST['surname']);
    $email = cleanString($_POST['email']);
    $location = cleanString($_POST['location']);
    $password = cleanString($_POST['password']);
    $confirm = cleanString($_POST['confirm']);
    $background = cleanString($_POST['background']);
    $hub = cleanString($_POST['hub']);
    $ivao_id = cleanString($_POST['ivao_id']);
    $vatsim_id = cleanString($_POST['vatsim_id']);
    $custom1 = cleanString($_POST['custom1']);
    $custom2 = cleanString($_POST['custom2']);
    $custom3 = cleanString($_POST['custom3']);
    $custom4 = cleanString($_POST['custom4']);
    $custom5 = cleanString($_POST['custom5']);
    $bots = cleanString($_POST['count1']); //used to try and trick bots
    $name = $firstname . ' ' . $surname;

    if (!empty($bots)) {
        $status = "bots";
    }

    if (((((empty($firstname)) || (empty($surname)) || (empty($password)) || (empty($location)) || (empty($email)))))) {
        $status = "required_fields";
    }
    if ($password != $confirm) {
        $status = "password_match";
    }
    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password)) {
        $status = "password_regex";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = "email_invalid";
    }
    if ($location == -1) {
        $status = "location";
    }

    if (empty($status)) {
        Api::__constructStatic();
        $data = [
            'Name' => $name,
            'Email' => $email,
            'VatsimId' => $vatsim_id,
            'IvaoId' => $ivao_id,
            'HubId' => $hub,
            'Location' => $location,
            'Background' => $background,
            'Custom1' => $custom1,
            'Custom2' => $custom2,
            'Custom3' => $custom3,
            'Custom4' => $custom4,
            'Custom5' => $custom5,
            'Password' => $password,
        ];

        $res = Api::sendSync('POST', 'v1/account/registration', $data);

        switch ($res->getStatusCode()) {
            case 200:
                $status = "success";
                $subject = "[vaBase Alert] New pilot application";
                $message = "You have a new pilot application for <strong>" . $name . "</strong>. Login to your admin system to review the application.";
                echo sendEmail($subject, $message, airline_admin_email);
                if (enable_discord_new_pilot_alerts) {
                    $data = [
                        'Message' => '**' . $name . '** has just applied to join the VA.',
                    ];
                    $res = Api::sendSync('POST', 'v1/integrations/discord', $data);
                }
                $firstname = null;
                $surname = null;
                $email = null;
                $background = null;
                $ivao_id = null;
                $vatsim_id = null;
                $custom1 = null;
                $custom2 = null;
                $custom3 = null;
                $custom4 = null;
                $custom5 = null;
                $hub = null;
                break;
            case 400:
                $resBody = json_decode($res->getBody());
                if ($resBody == "Email") { //email already exists
                    $status = "email_exists";
                } else {
                    $status = "error";
                }
                break;
            default:
                $status = "error";
                break;
        }
    }
}
?>
<?php
$MetaPageTitle = "Join Our Virtual Airline";
$MetaPageDescription = "Apply to become a pilot with our virtual airline. Fill out the application form and join our community.";
$MetaPageKeywords = "virtual airline, pilot application, join, aviation, flight simulation";
?>
<?php include 'includes/header.php'; ?>
<style>
/* Join Section Styles with Parallax Background */
.join-section {
    position: relative;
    padding: 60px 0;
    min-height: calc(100vh - 128px);
    background-image: url('./assets/images/backgrounds/join_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

.join-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

.join-section .container {
    position: relative;
    z-index: 2;
}

.offset-header {
    padding-top: 100px;
}

/* Glassmorphism Card */
.glass-card {
    background: rgba(255, 255, 255, 0.20);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.glass-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Panel Styles */
.panel-heading {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.panel-title {
    color: #ffffff;
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.panel-body {
    padding: 20px;
    color: #ffffff;
}
.form_layout{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.form_submit_button{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    padding-top: 20px;
    width: 100%;
    border-top: 2px solid rgba(255, 215, 0, 1);
}
.form_submit_button input{
    min-width: 50%
}
@media (max-width: 612px){
    .form_layout{
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 20px;
    }
    .form_submit_button input{
        min-width: 100%
    }
}
/* Form Styles */
.form-group {
    margin-bottom: 0 !important;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.form-group label {
    width: 150px;
    color: #ffffff;
    font-weight: 600;
    margin-right: 10px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
}

.form-group input,
.form-group select,
.form-group textarea {
    flex-grow: 1;
    background: #ffffff;
    color: #000000;
    border: 1px solid rgba(255, 215, 0, 0.5);
    border-radius: 5px;
    padding: 8px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: rgba(255, 215, 0, 1);
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group .btn-success {
    background: rgba(21, 182, 6, 0.8);
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.form-group .btn-success:hover {
    background: rgba(21, 182, 6, 1);
}

/* Alert Styles */
.alert {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.alert-success {
    background: rgba(21, 182, 6, 0.2);
    border-color: rgba(21, 182, 6, 0.5);
}

.alert-danger {
    background: rgba(255, 0, 0, 0.2);
    border-color: rgba(255, 0, 0, 0.5);
}

/* Sticky Terms & Conditions */
.terms-card {
    position: sticky;
    top: 100px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 215, 0, 0.5) transparent;
}

.terms-card::-webkit-scrollbar {
    width: 6px;
}

.terms-card::-webkit-scrollbar-track {
    background: transparent;
}

.terms-card::-webkit-scrollbar-thumb {
    background: rgba(255, 215, 0, 0.5);
    border-radius: 3px;
}

.terms-card ul {
    padding-left: 20px;
    margin-bottom: 15px;
}

.terms-card li {
    margin-bottom: 10px;
}
.span-two-columns {
    grid-column: 1 / -1;
}
/* Responsive Design */
@media (max-width: 1199px) {
    .join-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 80px;
    }
    .terms-card {
        position: static;
        max-height: none;
        overflow-y: visible;
    }
    .form-group label {
        width: 120px;
        font-size: 13px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 13px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 15px;
    }
}

@media (max-width: 991px) {
    .join-section {
        background-attachment: scroll;
    }
    .form-group {
        flex-direction: column;
        align-items: stretch;
    }
    .form-group label {
        width: auto;
        margin-right: 0;
        margin-bottom: 5px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        font-size: 12px;
    }
}

@media (max-width: 767px) {
    .join-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 60px;
    }
    .form-group label {
        font-size: 12px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 12px;
        padding: 6px;
    }
    .panel-body {
        padding: 10px;
    }
    .alert {
        padding: 10px;
        font-size: 12px;
    }
}

@media (max-width: 575px) {
    .join-section {
        padding: 80px 0 !important;
    }
    .offset-header {
        padding-top: 40px;
    }
    .form-group label {
        font-size: 11px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 11px;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .panel-body {
        padding: 8px;
    }
}

/* Print Styles */
@media print {
    .join-section {
        background: white;
        padding: 80px 0 !important;
    }
    .join-section::before {
        display: none;
    }
    .glass-card, .panel-heading, .alert {
        background: white;
        border: 1px solid #ccc;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .panel-title, .panel-body, .form-group label, .form-group input, .form-group select, .form-group textarea, .alert {
        color: black;
        text-shadow: none;
    }
    .terms-card {
        position: static;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        background: white;
        border: 1px solid #000;
    }
}
</style>
<section id="content" class="join-section offset-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-12 mb-4">
                <div class="glass-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Pilot Application Form</h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($status == 'success') { ?>
                        <div class="alert alert-success" role="alert">
                            Your application has been submitted, please wait for a member of staff to review your application!
                            <br><br>Once you have been approved, you will receive an email and will be able to login to your account.
                        </div>
                        <?php } else { ?>
                        <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php
                            if ($status == 'required_fields') {
                                echo '<p>Please check you have completed all required fields.</p>';
                            }
                            if ($status == 'password_match') {
                                echo '<p>Your passwords do not match. Please enter them again.</p>';
                            }
                            if ($status == 'password_regex') {
                                echo '<p>Your password does not meet complexity requirements.</p>';
                            }
                            if ($status == 'email_invalid') {
                                echo '<p>Your email address is invalid.</p>';
                            }
                            if ($status == 'email_exists') {
                                echo '<p>An account with your email already exists.</p>';
                            }
                            if ($status == 'location') {
                                echo '<p>Please select a valid location.</p>';
                            }
                            if ($status == 'error') {
                                echo '<p>There was an error creating your application. Please try again later.</p>';
                            }
                            if ($status == 'hub') {
                                echo '<p>Please select a valid hub.</p>';
                            }
                            if ($status == 'bots') {
                                echo '<p>Invalid submission detected.</p>';
                            }
                            ?>
                        </div>
                        <?php } ?>
                        <form action="join.php" method="post" class="form">
                            <div class="form_layout">
                            <div class="form-group">
                                <label for="firstname">First Name<span style="color: red">*</span></label>
                                <input name="firstname" type="text" id="firstname" class="form-control" maxlength="50" value="<?php echo htmlspecialchars(strval($firstname)); ?>" required aria-label="First Name">
                            </div>
                            <div class="form-group">
                                <label for="surname">Last Name<span style="color: red">*</span></label>
                                <input name="surname" type="text" id="surname" class="form-control" maxlength="50" value="<?php echo htmlspecialchars(strval($surname)); ?>" required aria-label="Last Name">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address<span style="color: red">*</span></label>
                                <input name="email" type="email" id="email" class="form-control" maxlength="100" value="<?php echo htmlspecialchars(strval($email)); ?>" required aria-label="Email Address">
                            </div>
                            <div class="form-group">
                                <label for="location">Location<span style="color: red">*</span></label>
                                <select name="location" id="location" class="form-control" required aria-label="Location">
                                    <option value="-1" selected>Please select...</option>
                                    <?php include 'lib/country-list.php'; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vatsim_id">Vatsim ID</label>
                                <input name="vatsim_id" type="text" id="vatsim_id" maxlength="11" class="form-control" value="<?php echo htmlspecialchars(strval($vatsim_id)); ?>" aria-label="Vatsim ID">
                            </div>
                            <div class="form-group">
                                <label for="ivao_id">IVAO ID</label>
                                <input name="ivao_id" type="text" id="ivao_id" maxlength="11" class="form-control" value="<?php echo htmlspecialchars(strval($ivao_id)); ?>" aria-label="IVAO ID">
                            </div>
                            <div class="form-group country" style="display: none;">
                                <label for="count1">Country<span style="color: red">*</span></label>
                                <input name="count1" type="text" id="count1" class="form-control" maxlength="50" value="" aria-label="Country">
                            </div>
                            <div class="form-group">
                                <label for="hub">Requested Base<span style="color: red">*</span></label>
                                <select name="hub" id="hub" class="form-control" required aria-label="Requested Base">
                                    <option value="" selected>Please select...</option>
                                    <?php foreach ($bases as $base) { ?>
                                    <option value="<?php echo $base->id; ?>" <?php echo $hub == $base->id ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($base->hub . ' (' . $base->icao . ')'); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password<span style="color: red">*</span></label>
                                <input name="password" type="password" id="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="30" required aria-label="Password">
                            </div>
                            <div class="form-group">
                                <label for="confirm">Confirm Password<span style="color: red">*</span></label>
                                <input name="confirm" type="password" id="confirm" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" maxlength="30" required aria-label="Confirm Password">
                            </div>
                           
                            <div class="form-group">
                                <label for="custom1">Custom Field 1</label>
                                <input name="custom1" type="text" id="custom1" class="form-control" value="<?php echo htmlspecialchars(strval($custom1)); ?>" aria-label="Custom Field 1">
                            </div>
                            <div class="form-group">
                                <label for="custom2">Custom Field 2</label>
                                <input name="custom2" type="text" id="custom2" class="form-control" value="<?php echo htmlspecialchars(strval($custom2)); ?>" aria-label="Custom Field 2">
                            </div>
                            <div class="form-group">
                                <label for="custom3">Custom Field 3</label>
                                <input name="custom3" type="text" id="custom3" class="form-control" value="<?php echo htmlspecialchars(strval($custom3)); ?>" aria-label="Custom Field 3">
                            </div>
                            <div class="form-group">
                                <label for="custom4">Custom Field 4</label>
                                <input name="custom4" type="text" id="custom4" class="form-control" value="<?php echo htmlspecialchars(strval($custom4)); ?>" aria-label="Custom Field 4">
                            </div>
                            <div class="form-group">
                                <label for="custom5">Custom Field 5</label>
                                <input name="custom5" type="text" id="custom5" class="form-control" value="<?php echo htmlspecialchars(strval($custom5)); ?>" aria-label="Custom Field 5">
                            </div>
                             <div class="form-group span-two-columns">
                                <label for="background">Background</label>
                                <textarea name="background" rows="5" id="background" class="form-control" aria-label="Background"><?php echo htmlspecialchars(strval($background)); ?></textarea>
                            </div>
                            </div>
                            <div class="form_submit_button">
                                <input name="join" type="submit" id="join" value="Submit Application" class="btn btn-success" aria-label="Submit Application">
                            </div>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="glass-card terms-card">
                    <div class="panel-heading">
                        <h3 class="panel-title">Terms & Conditions</h3>
                    </div>
                    <div class="panel-body">
                        <p style="color: #fff; border-bottom: 1px solid rgba(255, 215, 0, 1); padding-bottom: 4px; width: fit-content; padding-inline: 15px;">By completing this application you agree to the following:</p>
                        <ul>
                            <li>You agree to complete at least 1 flight per month</li>
                            <li>You own a legal copy of MS Flight Simulator, Prepar3D, or X-Plane</li>
                            <li>There is to be NO foul, vulgar, sexist, racist, or other inappropriate remarks made by any of our members to anybody else, whether part of this virtual airline or not</li>
                            <li>You must have a valid email address</li>
                            <li>You must comply with all VATSIM/IVAO/PilotEdge Regulations</li>
                        </ul>
                        <p>It may take up to 24 hours for your application to be approved if you are successful.</p>
                        <h4 style="color: #fff; border-bottom: 1px solid rgba(255, 215, 0, 1); padding-bottom: 4px; width: fit-content; padding-inline: 15px;">Password Requirements</h4>
                        <p>Your password must contain at least:</p>
                        <ul>
                            <li>1 upper case letter</li>
                            <li>1 lower case letter</li>
                            <li>1 number</li>
                            <li>8 characters</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script type="text/javascript">
$(document).ready(function() {
    document.getElementById("count1").required = false;
    $(".country").hide();

    // Enhance sticky behavior for Terms & Conditions on large screens
    if ($(window).width() >= 1200) {
        const termsCard = $('.terms-card');
        const termsContentHeight = termsCard.find('.panel-body')[0].scrollHeight;
        const viewportHeight = $(window).height();
        const maxHeight = viewportHeight - 120; // Match max-height: calc(100vh - 120px)

        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const termsOffsetTop = termsCard.offset().top;
            const termsScrollPosition = termsCard.find('.panel-body').scrollTop();

            // If content height is less than max-height, no need for special handling
            if (termsContentHeight <= maxHeight) return;

            // If scrolled past the content height, keep sticky
            if (scrollTop > termsOffsetTop + termsContentHeight - maxHeight) {
                termsCard.css('top', '100px');
            } else {
                termsCard.css('top', 'auto');
            }
        });

        // Handle internal scrolling of terms-card
        termsCard.on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const termsScrollPosition = termsCard.scrollTop();
            if (termsScrollPosition + maxHeight >= termsContentHeight) {
                termsCard.css('top', '100px');
            }
        });
    }
});
</script>
<?php include 'includes/footer.php'; ?>