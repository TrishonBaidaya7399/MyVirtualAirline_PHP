<?php
use Proxy\Api\Api;

require_once '../proxy/api.php';
include '../lib/functions.php';
include '../config.php';

session_start();
Api::__constructStatic();

if (!empty($_SESSION['pilotid'])) {
    header('Location: ' . website_base_url . 'site_pilot_functions/pilot_centre.php');
    exit();
}
$status = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = cleanString($_POST['email']);
    $password = cleanString($_POST['password']);
    if (empty($email) || empty($password)) {
        $status = "error";
    } else {
        if (empty($status)) {
            $data = [
                'Email' => $email,
                'Password' => $password
            ];
            $res = Api::sendSync('POST', 'v1/account/auth/login', $data);
            $pilot = json_decode($res->getBody());
            $responseCode = $res->getStatusCode();
            $airlineRes = getAirlineDetails();
            if ($airlineRes->getStatusCode() != 200) {
                $status = "error";
            } else {
                $airlineDetails = json_decode($airlineRes->getBody());
            }
            if ($responseCode != 200) {
                $status = "error";
            } else {
                if ($pilot->pilot->activated == '0') {
                    $status = "activate_account";
                } else {
                    Api::updateAuthAccessToken($pilot->token);
                    $_SESSION['callsign'] = $pilot->pilot->callsign;
                    $_SESSION['pilotid'] = $pilot->pilot->id;
                    $_SESSION['email'] = $pilot->pilot->email;
                    $_SESSION['name'] = $pilot->pilot->name;
                    $_SESSION['booking_expire_hours'] = $airlineDetails->bookingExpireHours;
                    $_SESSION['site_level'] = $pilot->pilot->siteLevel;
                    $_SESSION['owner'] = $pilot->pilot->owner;
                    $_SESSION['permissions'] = "";
                    $_SESSION['profileImage'] = $pilot->pilot->profileImage;
                    if (!$pilot->pilot->owner && $pilot->pilot->siteLevel && $pilot->pilot->staffPrivileges != null) {
                        $_SESSION['role_description'] = $pilot->pilot->staffPrivileges->roleDescription;
                        $_SESSION['role_name'] = $pilot->pilot->staffPrivileges->roleName;
                        $_SESSION['permissions'] = explode(",", $pilot->pilot->staffPrivileges->permissions);
                    }
                    header('Location: ' . website_base_url . 'site_pilot_functions/pilot_centre.php');
                    exit();
                }
            }
        }
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.hero-section {
    position: relative;
    min-height: calc(100vh - 128px);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* -------------------------------- Global title ------------------------------------ */
.global-heading{
    width: 100%;
    margin-bottom: 20px;
}
.global-heading .global-title{
    font-size: 40px;
    font-weight: 800;
    color: #fff;
    margin-top: 0px !important;
    text-transform: lowercase;
}
@media (max-width: 612px){
    .global-heading .global-title{
        font-size: 30px;
        font-weight: 700;
    }
}
/* -------------------------------- X ------------------------------------ */
.hero-banner {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: calc(100vh - 128px);
    background-image: url('../assets/images/backgrounds/login_220_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}

.hero-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: calc(100vh - 128px); 
    background: rgba(0, 0, 0, 0.3); 
    z-index: 2;
}

.login-container {
    position: relative;
    z-index: 3;
    padding: 20px;
    width: 600px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 100%;
    margin: 0 auto;
    transition: all 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.card-content {
    color: #333;
}

.panel-heading {
    background: none;
    border-bottom: none;
    padding-bottom: 0;
}

.panel-title {
    font-size: 2rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
    margin-bottom: 5px;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    font-size: 16px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    background: rgba(255, 255, 255, 0.3);
}

.btn-success {
    background: #28a745;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 5px;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.alert {
    margin-bottom: 20px;
    padding: 10px;
    border-radius: 5px;
}

.alert-danger {
    background: rgba(255, 0, 0, 0.2);
    border: 1px solid rgba(255, 0, 0, 0.3);
    color: #fff;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    color: #0056b3;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .glass-card {
        padding: 20px;
        max-width: 90%;
    }
    .panel-title {
        font-size: 1.5rem;
    }
    .form-control {
        font-size: 14px;
    }
    .btn-success {
        padding: 10px 20px;
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .glass-card {
        padding: 15px;
    }
    .panel-title {
        font-size: 1.2rem;
    }
    .form-control {
        font-size: 12px;
    }
    .btn-success {
        padding: 8px 15px;
        font-size: 12px;
    }
}
</style>
<?php include '../includes/header.php'; ?>
<section id="content" class="section">
    <div class="hero-section">
        <div class="hero-banner"></div>
        <div class="login-container">
            <div class="global-heading">
                <h3 class="global-title">Login</h3>
            </div>
            <div class="glass-card">
                <div class="card-content">
                    <?php if (!empty($status)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php
                            if ($status == 'error') {
                                echo '<p>Your email address and/or password are incorrect.</p>';
                            }
                            if ($status == 'activate_account') {
                                echo '<p>Your account is inactive.</p>';
                            } ?>
                        </div>
                    <?php } ?>
                    <form method="post">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input name="email" type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input name="password" type="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button name="login" type="submit" id="login" class="btn btn-success" style="background: rgb(2, 60, 248);"><i
                                    class="fa fa-sign-in" style="color: #fff" aria-hidden="true"></i> Login</button>
                        </div>
                        <div class="form-group">
                            <a style="color: rgb(2, 60, 248);" href="<?php echo website_base_url; ?>authentication/forgot_password.php">Reset
                                Password</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php'; ?>