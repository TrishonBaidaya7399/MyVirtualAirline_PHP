<?php if (enable_cookie_banner) {?>
<div class="cookie-banner text-center" style="display: none">
    <p>
        This site uses cookies. By continuing to browse this site, you accept the use of cookies. <button
            id="cookieAccept" class="btn btn-cta-primary">Ok</button>
    </p>
</div>
<?php } ?>
<style>

.footer {
    background: rgba(74, 128, 145, 0.8);
    background: linear-gradient(360deg,rgba(74, 128, 145, 0.8) 18%, rgba(139, 232, 192, 0.8) 46%, rgba(169, 218, 250, 1) 100%);
    padding: 40px 0 20px;
    border-top: 1px solid #e9ecef;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.footer .copyright {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 5px;
}

.footer a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #0056b3;
    text-decoration: underline;
}

.social-icons {
    display: inline-block;
    margin: 0 12px;
    font-size: 28px;
    color: #007bff !important;
    transition: all 0.3s ease;
    text-decoration: none;
    padding: 8px;
    border-radius: 50%;
    background: rgba(0, 123, 255, 0.1);
    width: 50px;
    height: 50px;
    text-align: center;
    line-height: 34px;
}

.social-icons:hover {
    color: #ffffff !important;
    background: #007bff;
    transform: translateY(-3px);
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}


.cookie-banner {
    background: #333;
    color: white;
    padding: 15px 0;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.cookie-banner p {
    margin: 0;
    font-size: 14px;
}

.cookie-banner .btn-cta-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 20px;
    margin-left: 15px;
    border-radius: 4px;
    font-size: 14px;
    transition: background 0.3s ease;
}

.cookie-banner .btn-cta-primary:hover {
    background: #0056b3;
}


@media (min-width: 768px) {
    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .footer-left {
        flex: 1;
        text-align: left;
    }
    
    .footer-right {
        flex-shrink: 0;
        text-align: right;
    }
}


@media (max-width: 767px) {
    .footer {
        padding: 30px 0 20px;
        text-align: center;
    }
    
    .footer-left {
        margin-bottom: 20px;
    }
    
    .footer-right {
        margin-top: 15px;
    }
    
    .social-icons {
        margin: 0 8px;
        font-size: 24px;
        width: 45px;
        height: 45px;
        line-height: 29px;
    }
    
    .copyright {
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    .footer {
        padding: 25px 0 15px;
    }
    
    .footer .container {
        padding: 0 15px;
    }
    
    .copyright {
        font-size: 12px;
        line-height: 1.4;
    }
    
    .social-icons {
        margin: 0 6px;
        font-size: 22px;
        width: 42px;
        height: 42px;
        line-height: 26px;
    }
    
    .cookie-banner {
        padding: 12px 15px;
    }
    
    .cookie-banner p {
        font-size: 13px;
    }
    
    .cookie-banner .btn-cta-primary {
        margin-left: 10px;
        margin-top: 10px;
        display: block;
        width: 100px;
        margin: 10px auto 0;
    }
}
</style>

<?php if (enable_cookie_banner) {?>
<div class="cookie-banner text-center" style="display: none">
    <p>
        This site uses cookies. By continuing to browse this site, you accept the use of cookies. <button
            id="cookieAccept" class="btn btn-cta-primary">Ok</button>
    </p>
</div>
<?php } ?>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="footer-content">
                    <div class="footer-left">
                        <small class="copyright">Copyright &copy; <?php echo date("Y");?>
                            MyVirtualAirline</small><br />
                        <small class="copyright">Powered & Designed by <a href="https://www.vabase.com"
                                target="_blank">vaBase.com</a></small><br />
                        <small class="copyright"><a
                                href="<?php echo website_base_url;?>privacy.php">Privacy
                                Policy</a> | <a
                                href="<?php echo website_base_url;?>staff.php">Contact
                                Us</a></small>
                    </div>
                    <div class="footer-right">
                        <?php if (!empty(facebook_footer_link)) {?>
                        <a href="<?php echo facebook_footer_link?>"
                            class="social-icons" target="_blank"><i class="fa fa-facebook-official" aria-hidden="true" style="margin-left: 0px"></i></a>
                        <?php } ?>
                        <?php if (!empty(twitter_footer_link)) {?>
                        <a href="<?php echo twitter_footer_link?>"
                            class="social-icons" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true" style="margin-left: 0px"></i></a>
                        <?php } ?>
                        <?php if (!empty(youtube_footer_link)) {?>
                        <a href="<?php echo youtube_footer_link?>"
                            class="social-icons" target="_blank"><i class="fa fa-brands fa-youtube" aria-hidden="true" style="margin-left: 0px"></i></a>
                        <?php } ?>
                        <?php if (!empty(discord_footer_link)) {?>
                        <a href="<?php echo discord_footer_link?>"
                            class="social-icons" target="_blank"><i class="fa fa-brands fa-discord" aria-hidden="true" style="margin-left: 0px"></i></a>
                        <?php } ?>
                        <?php if (!empty(instagram_footer_link)) {?>
                        <a href="<?php echo instagram_footer_link?>"
                            class="social-icons" target="_blank"><i class="fa fa-brands fa-instagram"
                                aria-hidden="true" style="margin-left: 0px"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<script type="text/javascript"
    src="<?php echo website_base_url;?>assets/plugins/bootstrap/js/bootstrap.min.js">
</script>
<script
    src="<?php echo website_base_url;?>assets/plugins/jquery.waypoints.min.js">
</script>
<script type="text/javascript"
    src="<?php echo website_base_url;?>assets/plugins/jquery.counterup.min.js">
</script>

<script type="text/javascript"
    src="<?php echo website_base_url;?>assets/js/main.js"></script>
<script type="text/javascript"
    src="<?php echo website_base_url;?>assets/js/loader.js"></script>
</body>

</html>