$('.passworddiv').hide();
$('#passwordchange_check').click(function () {
    if ($('#passwordchange_check').prop('checked')) {
        $('.passworddiv').show();
    } else {
        $('.passworddiv').hide();
    }
});

let oldpasswordField = document.getElementById('old_password');
let old_passwordIcon = document.getElementsByClassName('oldpasswordeye')[0];
old_passwordIcon.addEventListener('click', function () {
    if (oldpasswordField.type === 'password') {
        oldpasswordField.type = 'text';
    } else {
        oldpasswordField.type = 'password';
    }
});

let passwordField = document.getElementById('password');
let passwordIcon = document.getElementsByClassName('newpasswordeye')[0];
passwordIcon.addEventListener('click', function () {
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
    } else {
        passwordField.type = 'password';
    }
});

let password_confirmationField = document.getElementById('password_confirmation');
let password_confirmationFieldIcon = document.getElementsByClassName('confirmnewpasswordeye')[0];
password_confirmationFieldIcon.addEventListener('click', function () {
    if (password_confirmationField.type === 'password') {
        password_confirmationField.type = 'text';
    } else {
        password_confirmationField.type = 'password';
    }
});

$(document).on('click', '#profile_image_input_select', function (e) {
    $('#profile_image').click();
});
$('#profile_image').change(function (e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var html = `<a href="#" id="profile_image_input_select" class="profile-container">
            <img class="avatar border-gray"
                src="${e.target.result}"
                alt="">
                <div class="overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-camera"
                                        viewBox="0 0 16 16">
                                        <path d="M10.5 9.5A1.5 1.5 0 1 1 9 8a1.5 1.5 0 0 1 1.5 1.5z" />
                                        <path
                                            d="M4.75 2a.75.75 0 0 1 .7-.5h5.1a.75.75 0 0 1 .7.5l.5 1h2.5A1.5 1.5 0 0 1 16 4.5v8A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-8A1.5 1.5 0 0 1 1.5 3.5h2.5l.5-1zM3 4a1 1 0 1 0 0 2A1 1 0 0 0 3 4zm8 5.5a3.5 3.5 0 1 0-7 0A3.5 3.5 0 0 0 11 9.5z" />
                                    </svg>
                                </div>
            </a>`;
            $('#profile_image_div').html(html);

        };
        reader.readAsDataURL(file);
    } else {
        var html = `<a href="#" id="profile_image_input_select" class="profile-container">
        <img class="avatar border-gray"
            src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541"
            alt="Profile Image Placeholder">
            <div class="overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-camera"
                                        viewBox="0 0 16 16">
                                        <path d="M10.5 9.5A1.5 1.5 0 1 1 9 8a1.5 1.5 0 0 1 1.5 1.5z" />
                                        <path
                                            d="M4.75 2a.75.75 0 0 1 .7-.5h5.1a.75.75 0 0 1 .7.5l.5 1h2.5A1.5 1.5 0 0 1 16 4.5v8A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-8A1.5 1.5 0 0 1 1.5 3.5h2.5l.5-1zM3 4a1 1 0 1 0 0 2A1 1 0 0 0 3 4zm8 5.5a3.5 3.5 0 1 0-7 0A3.5 3.5 0 0 0 11 9.5z" />
                                    </svg>
                                </div>
        </a>`;
        $('#profile_image_div').html(html);

    }
});
function getprofiledata() {
    let type = 'GET';
    let url = '/manager/getUserProfile';
    let message = '';
    let form = '';


    // PASSING DATA TO FUNCTION
    SendAjaxRequestToServer(type, url, '', '', getUserProfileResponse, '', '');

}

function getUserProfileResponse(response) {
    if (response.status == 200 || response.status == '200') {
        var details = response.user;

        $('#email').text(details.email);
        $('#first_name').val(details.first_name);
        $('#middle_name').val(details.middle_name);
        $('#last_name').val(details.last_name);
        $('#contact_number').val(details.contact_number);
        var html = `${details.first_name + " " + details.last_name} <br> ${details.email}
        <br> ${details.contact_number}`;
        $('#userdetailscontainer').html(html);
        var html2 = `<a href="#" id="profile_image_input_select" class="profile-container">
            <img class="avatar border-gray"
                src="${details.profile_image}"
                alt="">
                <div class="overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-camera"
                                        viewBox="0 0 16 16">
                                        <path d="M10.5 9.5A1.5 1.5 0 1 1 9 8a1.5 1.5 0 0 1 1.5 1.5z" />
                                        <path
                                            d="M4.75 2a.75.75 0 0 1 .7-.5h5.1a.75.75 0 0 1 .7.5l.5 1h2.5A1.5 1.5 0 0 1 16 4.5v8A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-8A1.5 1.5 0 0 1 1.5 3.5h2.5l.5-1zM3 4a1 1 0 1 0 0 2A1 1 0 0 0 3 4zm8 5.5a3.5 3.5 0 1 0-7 0A3.5 3.5 0 0 0 11 9.5z" />
                                    </svg>
                                </div>
            </a>`;
        $('#profile_image_div').html(html2);

    }
    else {

    }
}

$('#update_btn').click(function (e) {
    e.preventDefault();
    let type = 'POST';
    let url = '/manager/updateprofile';
    let message = '';
    let form = $("#profileform");
    let data = new FormData(form[0]);

    // PASSING DATA TO FUNCTION
    $('[name]').removeClass('is-invalid');
    SendAjaxRequestToServer(type, url, data, '', updateprofileResponse, '', '');
});

function updateprofileResponse(response) {
    if (response.status == 200 || response.status == '200') {
        $("#profileform")[0].reset();
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        getprofiledata();
    }
    if (response.status == 402) {

        error = response.message;

    } else {

        error = response.responseJSON.message;
        var is_invalid = response.responseJSON.errors;

        $.each(is_invalid, function (key) {
            // Assuming 'key' corresponds to the form field name
            var inputField = $('[name="' + key + '"]');
            // Add the 'is-invalid' class to the input field's parent or any desired container
            inputField.addClass('is-invalid');

        });
    }
    toastr.error(error, '', {
        timeOut: 3000
    });
}


$('#personaldataform').submit(function (e) {
    e.preventDefault();
    let type = 'POST';
    let url = '/customer/updatepersonaldata';
    let message = '';
    let form = $("#personaldataform");
    let data = new FormData(form[0]);

    // PASSING DATA TO FUNCTION
    $('[name]').removeClass('is-invalid');
    SendAjaxRequestToServer(type, url, data, '', updatepersonaldataResponse, '', '');
});

function updatepersonaldataResponse(response) {
    if (response.status == 200 || response.status == '200') {
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        getprofiledata();
    }
    else {
        if (response.status == 402 || response.status == '402') {

            error = response.message;

        } else {

            error = response.responseJSON.message;
            var is_invalid = response.responseJSON.errors;

            $.each(is_invalid, function (key) {
                // Assuming 'key' corresponds to the form field name
                var inputField = $('[name="' + key + '"]');
                // Add the 'is-invalid' class to the input field's parent or any desired container
                inputField.addClass('is-invalid');

            });
        }

        toastr.error(error, '', {
            timeOut: 3000
        });
    }
}
$(document).ready(function () {
    getprofiledata();


    // $('#date_of_birth').datepicker({ dateFormat: 'yyyy/mm/dd' })

})