$(document).ready(function () {
    function checkElement(selector) {
        return $(selector).length > 0;
    }

    // Functions
    function modalDeleteUser() {
        $(".delete-modal").click(function (event) {
            var action = $(this).attr("data_action_url");
            $("#delete_form").attr("action", action);
        });
    }

    // Active functions
    if (checkElement(".delete-modal")) {
        modalDeleteUser();
    }

    if (checkElement("#summernote")) {
        $("#summernote").summernote();
    }

    if (checkElement("#customFile")) {
        $(function () {
            bsCustomFileInput.init();
        });
    }
    //change image
    $("#image").on("change", function () {
        const fileName = $(this).val().split("\\").pop();
        $(this).next(".custom-file-label").html(fileName);
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#my_img").attr("src", e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
});
