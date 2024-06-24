$(function () {
    $.validator.setDefaults({
        submitHandler: function (form) {
            form.submit();
        },
    });

    // List of validation rules
    const rulesConstant = {
        name: {
            required: true,
            maxlength: 255,
        },
        email: {
            required: true,
            email: true,
        },
        password: {
            required: true,
            minlength: 6,
        },
        password_confirmation: {
            required: true,
            equalTo: '[name="password"]',
        },
        old_password: {
            required: true,
        },
        new_password: {
            required: true,
            minlength: 6,
        },
        new_password_confirmation: {
            required: true,
            equalTo: '[name="new_password"]',
        },
        category_id: {
            required: true,
        },
        quantity: {
            required: true,
            number: true,
            min: 0,
        },
        price: {
            required: true,
            number: true,
            min: 0,
        },
        discount: {
            number: true,
            min: 0,
            max: function () {
                return parseInt($("#price").val());
            },
        },
        status: {
            number: true,
            min: 0,
        },
        image: {
            fileExtension: "png,jpg,jpeg",
        },
        additional_images: {
            fileExtension: "png,jpg,jpeg",
        },
    };

    // List of rule messages
    const messagesConstant = {
        name: {
            required: "Please enter name",
            maxlength: "Your name must be less than 255 characters",
        },
        email: {
            required: "Please enter a email address",
            email: "Please enter a valid email address",
        },
        password: {
            required: "Please enter password",
            minlength: "Password must be at least 6 characters long",
        },
        password_confirmation: {
            required: "Please confirm new password",
            equalTo: "The confirmation password does not match the password",
        },
        old_password: {
            required: "Please enter your current password",
        },
        new_password: {
            required: "Please enter a new password",
            minlength: "Your new password must be at least 6 characters long",
        },
        new_password_confirmation: {
            required: "Please confirm new password",
            equalTo:
                "The confirmation password does not match the new password",
        },
        category_id: {
            required: "Please select a category",
        },
        quantity: {
            required: "Please enter a quantity",
            number: "Please enter a valid number",
            min: "Quantity cannot be less than 0",
        },
        price: {
            required: "Please enter price",
            number: "Please enter a valid number",
            min: "Price cannot be less than 0",
        },
        discount: {
            number: "Please enter a valid number",
            min: "Discount cannot be less than 0",
            max: "Discount cannot be greater than price",
        },
        status: {
            number: "Please enter a valid number",
            min: "Status cannot be less than 0",
        },
        image: {
            required: "Please select an image",
            fileExtension:
                "Please upload an image file with a valid extension (png, jpg, jpeg)",
        },
        additional_images: {
            required: "Please select an image",
            fileExtension:
                "Please upload an image file with a valid extension (png, jpg, jpeg)",
        },
    };

    // Admin Update Profile
    if ($("#update_profile_form").length) {
        $("#update_profile_form").validate({
            rules: {
                name: rulesConstant.name,
                email: rulesConstant.email,
            },
            messages: {
                name: messagesConstant.name,
                email: messagesConstant.email,
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    if ($("#update_passWord_form").length) {
        $("#update_passWord_form").validate({
            rules: {
                old_password: rulesConstant.old_password,
                new_password: rulesConstant.new_password,
                new_password_confirmation:
                    rulesConstant.new_password_confirmation,
            },
            messages: {
                old_password: messagesConstant.old_password,
                new_password: messagesConstant.new_password,
                new_password_confirmation:
                    messagesConstant.new_password_confirmation,
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    // Admin Management
    if ($("#new_admin_form").length) {
        $("#new_admin_form").validate({
            rules: {
                name: rulesConstant.name,
                email: rulesConstant.email,
                password: rulesConstant.password,
                password_confirmation: rulesConstant.password_confirmation,
            },
            messages: {
                name: messagesConstant.name,
                email: messagesConstant.email,
                password: messagesConstant.password,
                password_confirmation: messagesConstant.password_confirmation,
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    if ($("#edit_admin_form").length) {
        $("#edit_admin_form").validate({
            rules: {
                name: rulesConstant.name,
                email: rulesConstant.email,
            },
            messages: {
                name: messagesConstant.name,
                email: messagesConstant.email,
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    // Custom method to check file extension
    function addFileExtensionMethod() {
        $.validator.addMethod(
            "fileExtension",
            function (value, element, param) {
                param =
                    typeof param === "string"
                        ? param.replace(/,/g, "|")
                        : "png|jpe?g";

                var files = element.files;
                if (this.optional(element) || !files.length) {
                    return true;
                }

                for (var i = 0; i < files.length; i++) {
                    if (
                        !files[i].name.match(
                            new RegExp(".(" + param + ")$", "i")
                        )
                    ) {
                        return false;
                    }
                }
                return true;
            }
        );
    }
    // create category validate form
    function categoryValidationForm(formSelector, requireImage) {
        $(formSelector).validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255,
                },
                image: requireImage
                    ? { required: true, fileExtension: "png,jpg,jpeg" }
                    : { fileExtension: "png,jpg,jpeg" },
            },
            messages: {
                name: {
                    required: "Please enter your name",
                    maxlength: "Your name must be less than 255 characters",
                },
                image: requireImage
                    ? {
                          required: "Please select an image",
                          fileExtension:
                              "Please upload an image file with a valid extension (png, jpg, jpeg)",
                      }
                    : {
                          fileExtension:
                              "Please upload an image file with a valid extension (png, jpg, jpeg)",
                      },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    // Setup validation for create category form
    if ($("#create_category_form")) {
        addFileExtensionMethod();
        categoryValidationForm("#create_category_form", true);
    }

    // Setup validation for update category form
    if ($("#update_category_form")) {
        addFileExtensionMethod();
        categoryValidationForm("#update_category_form", false);
    }

    // Create Product Form
    function productValidationForm(formSelector, requireImage) {
        $(formSelector).validate({
            rules: {
                name: rulesConstant.name,
                category_id: rulesConstant.category_id,
                quantity: rulesConstant.quantity,
                price: rulesConstant.price,
                discount: rulesConstant.discount,
                status: rulesConstant.status,
                image: requireImage
                    ? { required: true, fileExtension: "png,jpg,jpeg" }
                    : { fileExtension: "png,jpg,jpeg" },
                "additional_images[]": { fileExtension: "png,jpg,jpeg" },
            },
            messages: {
                name: messagesConstant.name,
                category_id: messagesConstant.category_id,
                quantity: messagesConstant.quantity,
                price: messagesConstant.price,
                discount: messagesConstant.discount,
                status: messagesConstant.status,
                image: messagesConstant.image,
                "additional_images[]": messagesConstant.additional_images,
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }

    // Validate create product form
    if ($("#create_product_form").length) {
        addFileExtensionMethod();
        productValidationForm("#create_product_form", true);
    }

    // Validate create product form
    if ($("#update_product_form").length) {
        addFileExtensionMethod();
        productValidationForm("#update_product_form", false);
    }

    // create users validate form
    function regexPhoneValidationMethod() {
        $.validator.addMethod(
            "regexPhone",
            function (value, element) {
                return this.optional(element) || /^[0-9]{10,11}$/.test(value);
            },
            "Please enter a valid phone number"
        );
    }
    if ($("#user_validation_form")) {
        addFileExtensionMethod();
        regexPhoneValidationMethod();
        $("#user_validation_form").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 255,
                },
                email: {
                    required: true,
                    maxlength: 255,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                address: {
                    required: true,
                },
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 11,
                    regexPhone: true,
                },
                image: { fileExtension: "png,jpg,jpeg" },
            },
            messages: {
                name: {
                    required: "Please enter your name",
                    maxlength: "Your name must be less than 255 characters",
                },
                email: {
                    required: "Please enter your email",
                    maxlength: "Your email must be less than 255 characters",
                    email: "Email is not the correct format",
                },
                password: {
                    required: "Please enter your password",
                    minlength: "Password cannot be less than 6 characters",
                },
                address: {
                    required: "Please enter your address",
                },
                phone: {
                    required: "Please enter your phone",
                    minlength: "Phone cannot be less than 10 characters",
                    maxlength: "Phone cannot be greater than 11 characters",
                    regexPhone: "Phone is not in the correct format",
                },
                image: {
                    fileExtension:
                        "Please upload an image file with a valid extension (png, jpg, jpeg)",
                },
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("text-danger");
                element.closest(".form-group").append(error);
            },
        });
    }
});
