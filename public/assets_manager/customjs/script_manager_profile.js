$(document).on('click', '#profile_image_input_select',function(e){
    $('#profile_image').click();
});
$('#profile_image').change(function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var html = `<a href="#" id="profile_image_input_select">
            <img class="avatar border-gray"
                src="${e.target.result}"
                alt="Profile Image">
            </a>`;
            $('#profile_image_div').html(html);
            
        };
        reader.readAsDataURL(file);
    } else {
        var html = `<a href="#" id="profile_image_input_select">
        <img class="avatar border-gray"
            src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541"
            alt="Profile Image Placeholder">
        </a>`;
        $('#profile_image_div').html(html);
        
    }
});
function getprofiledata(){
    let type = 'GET';
	let url = '/manager/getUserProfile';
	let message = '';
	let form = '';
	
	
	// PASSING DATA TO FUNCTION
	SendAjaxRequestToServer(type, url, '', '', getUserProfileResponse, '', '');

}

function getUserProfileResponse(response){
    if (response.status == 200 || response.status == '200') {
        var details = response.user;
       
        $('#email').text(details.email);
        $('#first_name').val(details.first_name);
        $('#middle_name').val(details.middle_name);
        $('#last_name').val(details.last_name);
        $('#contact_number').val(details.contact_number);
        var html = `${details.first_name +" "+ details.last_name} <br> ${details.email}
        <br> ${details.contact_number}`;
        $('#userdetailscontainer').html(html);
        var html2 = `<a href="#" id="profile_image_input_select">
            <img class="avatar border-gray"
                src="${details.profile_image}"
                alt="Profile Image">
            </a>`;
            $('#profile_image_div').html(html2);

    }
    else{

    }
}

$('#update_btn').click(function(e){
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

function updateprofileResponse(response){
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


$('#personaldataform').submit(function(e){
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

function updatepersonaldataResponse(response){
    if (response.status == 200 || response.status == '200') {
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        getprofiledata();
    }
    else{
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
$(document).ready(function(){
    getprofiledata();
   

    // $('#date_of_birth').datepicker({ dateFormat: 'yyyy/mm/dd' })
    
})