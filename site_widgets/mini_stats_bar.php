<div class="row">
    <div class="item col-md-3 col-sm-6 col-12">
        <div class="content">
            <h3 class="sub-title">Total flights</h3>
            <p class="stat-numbers"><span class="counter"><span class="total-flights"><i
                            class="fa fa-circle-o-notch fa-spin"></i></span></span>
            </p>
            <p class="stat-mini">
                <strong><span class="month-flights"><i class="fa fa-circle-o-notch fa-spin"></i></span></strong>
                flights this month
            </p>
        </div>
    </div>
    <div class="item col-md-3 col-sm-6 col-12">
        <div class="content">
            <h3 class="sub-title">Total distance</h3>
            <p class="stat-numbers"><span class="counter"><span class="total-miles"><i
                            class="fa fa-circle-o-notch fa-spin"></i></span></span>
            </p>
            <p class="stat-mini">
                <strong><span class="month-miles"><i class="fa fa-circle-o-notch fa-spin"></i></span></strong>
                miles this month
            </p>
        </div>
    </div>
    <div class="item col-md-3 col-sm-6 col-12">
        <div class="content">
            <h3 class="sub-title">Total hours</h3>
            <p class="stat-numbers"><span class="counter"><span class="total-hours"><i
                            class="fa fa-circle-o-notch fa-spin"></i></span></span>
            </p>
            <p class="stat-mini">
                <strong><span class="month-hours"><i class="fa fa-circle-o-notch fa-spin"></i></span></strong>
                hours this month
            </p>
        </div>
    </div>
    <div class="item col-md-3 col-sm-6 col-12">
        <div class="content">
            <h3 class="sub-title">Total pilots</h3>
            <p class="stat-numbers"><span class="counter"><span class="total-pilots"><i
                            class="fa fa-circle-o-notch fa-spin"></i></span></span></p>
            <p class="stat-mini">
                <strong><span class="active-pilots"><i class="fa fa-circle-o-notch fa-spin"></i></span></strong>
                active this month
            </p>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {

    request = $.ajax({
        url: "<?php echo website_base_url;?>includes/mini_stats_data.php",
        type: "GET"
    });

    request.done(function(response, textStatus, jqXHR) {
        var r = JSON.parse(response);
        $(".total-flights").html(r.totalAllFlights);
        $(".month-flights").html(r.monthFlights);
        $(".total-miles").html(r.totalAllMiles);
        $(".month-miles").html(r.monthMiles);
        $(".total-hours").html(r.totalAllHours);
        $(".month-hours").html(r.monthAllHours);
        $(".total-pilots").html(r.totalPilots);
        $(".active-pilots").html(r.totalActivePilots);
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    });

    request.fail(function(jqXHR, textStatus, errorThrown) {
        $(".jump-error").show();
        console.error(
            "The following error occurred: " +
            textStatus, errorThrown
        );
    });
});
</script>