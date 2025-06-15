var Loader = Loader || {
    init: function () {
        if ($("#loader").length === 0) {
            $('<div id="loader"></div>').appendTo("body");
        }
    },
    start: function () {
        $("#loader").show();
    },
    stop: function () {
        $("#loader").hide();
    },
    isActive: function () {
        return $("#loader").is(":visible");
    }
};

$(function () {
    Loader.init();
});
