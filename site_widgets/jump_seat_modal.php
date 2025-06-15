<?php
use Proxy\Api\Api;
Api::__constructStatic();

$settings = null;
$res = Api::sendSync('GET', 'v1/airline', null);
if ($res->getStatusCode() == 200) {
    $settings = json_decode($res->getBody(), false);
}
$valid = $pilot->wallet >= $settings->jumpSeatFee ? true : false;
?>
<div class="modal fade" id="jumpModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><strong>Change Location</strong></h4>
            </div>
            <div class="modal-body">
                <div class="jump-form">
                    <?php if ($valid) {?>
                    <?php if ($settings->jumpToHubOnly) {?>
                    <p>You can take a jump seat flight back to your base for
                        <u>$<?php echo number_format($settings->jumpSeatFee, 2); ?></u>
                    </p>
                    <?php } else {?>
                    <p>You can take a jump seat flight to any destination for
                        <u>$<?php echo number_format($settings->jumpSeatFee, 2); ?></u>
                    </p>
                    <p>Leave blank to return to base.</p>
                    <form method="post" class="jumpForm">
                        <div class="form-group horizontal">
                            <label>Airport ICAO:</label>
                            <input name="icao" type="text" id="icao" class="form-control" max-length="4" value=""
                                style="text-transform:uppercase" placeholder="ICAO">
                        </div>
                    </form>
                    <p class="help-text">Ensure you enter the correct airport ICAO before you confirm as fees are
                        non-refundable.</p>
                    <?php }?>
                    <?php } else {?>
                    <div class="alert alert-danger" role="alert">You do not have enough money to pay the jump seat fee
                        of <strong><u>$<?php echo number_format($settings->jumpSeatFee, 2); ?></u></strong>.</div>
                    <?php }?>
                </div>
                <div class="jump-success" style="display:none;">
                    <div class="alert alert-success" role="alert">You have successfully changed location.</div>
                </div>
                <div class="jump-error" style="display:none;">
                    <div class="alert alert-danger" role="alert">An error occured. Please try again later.</div>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($valid) {?>
                <button type="button" class="btn btn-success" id="btnConfirm" data-type="">Confirm</button>
                <?php }?>
                <button type="button" class="btn btn-default" id="btnCloseCancel" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(".jump").on('click', function(e) {
        e.preventDefault();
        $(".jump-error").hide();
        $(".jump-success").hide();
        $("#btnConfirm").show();
        $(".jump-form").show();
        $("#jumpModal").modal("show");
    });

    $("#btnConfirm").on('click', function(e) {
        e.preventDefault();

        var $form = $(".jumpForm");
        var $inputs = $form.find("input[type='text'], input[type='hidden']");
        var serializedData = $form.serialize();
        console.log(serializedData);
        request = $.ajax({
            url: "/site_widgets/jump_seat_request.php",
            type: "POST",
            data: serializedData
        });

        request.done(function(response, textStatus, jqXHR) {
            var r = JSON.parse(response);
            $(".virlocation").html(r.icao);
            $(".balance").html(r.wallet);
            $(".jump-error").hide();
            $(".jump-success").show();
            $("#btnConfirm").hide();
            $(".icao").val('');
            $(".jump-form").hide();

        });
        request.fail(function(jqXHR, textStatus, errorThrown) {
            $(".jump-error").show();
            console.error(
                "The following error occurred: " +
                textStatus, errorThrown
            );
        });
    });
});
</script>