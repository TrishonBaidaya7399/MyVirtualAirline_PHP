function display_c(){
        var refresh=1000; // Refresh rate in milli seconds (one second)
        mytime=setTimeout('display_ct()',refresh)
     }

function display_ct() {
        var x = new Date()
        var hour=x.getUTCHours();
        var minute=x.getUTCMinutes();
        var second=x.getUTCSeconds();
        if(hour <10 ){hour='0'+hour;}
        if(minute <10 ) {minute='0' + minute; }
        if(second<10){second='0' + second;}
        var x1 = hour+":"+minute+":"+second+" Z";
        document.getElementById('ct').innerHTML = x1;
        display_c();
}

$(window).bind("pageshow", function(event) {
    Loader.stop();
});

jQuery(document).ready(function($) {
    
    display_ct();

    /* ======= Fixed header when scrolled ======= */
    
    $(window).bind('scroll', function() {
       if ($(window).scrollTop() > 50) {
           $('#header').addClass('navbar-fixed-top');
           //$('#logo-image').attr("src", "../assets/images/logo-dark.png"); //Uncomment for logo image instead of text 
       }
       else {
           $('#header').removeClass('navbar-fixed-top');
           //$('#logo-image').attr("src", "../assets/images/logo.png"); //Uncomment for logo image instead of text 
       }
   });

   if (localStorage.getItem("cookieAccepted") == null) {
    $(".cookie-banner").delay(200).fadeIn();
};
$('#cookieAccept').click(function () {
    $('.cookie-banner').fadeOut();
    localStorage.setItem("cookieAccepted", "accepted");
});

   /* ======= Page Loaders ======= */
$(document).on('click', '.js_showloader', function (e) {
    Loader.start();
});

$(document).on('submit', 'form', function (e) {
    Loader.start();
});

});