$(document).ready(function () {
    $(".user-panel > .user-info").click(function () {
        $(".user-panel").find(".user-panel-action").toggle();
        $(".user-panel").find(".arrow-icon").toggleClass("open");
    });
});
