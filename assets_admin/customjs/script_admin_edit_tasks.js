
$('#building').change(function(){
    var building_id = $(this).val();
    let type = 'POST';
    let url = '/admin/getAppartmentsList';
    let data = new FormData();
    data.append('building_id', building_id);
    SendAjaxRequestToServer(type, url, data, '', getAppartmentsListResponse, '', '');

});


function getAppartmentsListResponse(response){
    if (response.status == 200) {
        var appartments = response.appartment_list.appartment_list;
        $('#appartment').html('<option value="">Select Appartment</option>'); 
        $.each(appartments, function(index, appartment) {
            $('#appartment').append('<option value="' + appartment.id + '">' + appartment.apartment_name + '</option>');
        });
    }
    
}

$('#edit_task_form').submit(function(e){
    e.preventDefault();

    let form = document.getElementById('edit_task_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/updateTask';
    SendAjaxRequestToServer(type, url, data, '', updateTaskResponse, '', 'updatetaskbtn');


});

function updateTaskResponse(response){
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

    setTimeout(function(){
        window.location = '/admin/tasks'
    }, 1000);
       
    }

    if (response.status == 402) {

        error = response.message;

    } else {

        error = response.responseJSON.message;
        var is_invalid = response.responseJSON.errors;

        var offset = 100; 
        var firstInvalidFound = false;
        $.each(is_invalid, function (key) {
            var inputField = $('[name="' + key + '"]');
            inputField.addClass('is-invalid');
            if (!firstInvalidFound) {

                $('html, body').animate({ scrollTop: inputField.offset().top - offset }, 1000);
                firstInvalidFound = true;
            }
            
        });
    }
    toastr.error(error, '', {
        timeOut: 3000
    });
}


// document.getElementById('fileSelectDiv').addEventListener('click', function() {
//     document.getElementById('fileInput').click();
// });

// document.getElementById('fileInput').addEventListener('change', function() {
//     const fileInput = document.getElementById('fileInput');
//     const fileSelectDiv = document.getElementById('fileuploadnamecontainer');

//     if (fileInput.files.length > 0) {
//         fileSelectDiv.textContent = fileInput.files[0].name;
//     } else {
//         fileSelectDiv.textContent = 'Select a file';
//     }
// });


document.getElementById('fileSelectDiv').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function(event) {
    // Get the new file name
    var newFileName = event.target.files[0].name;
    
    // Update the file name displayed
    document.getElementById('fileName').textContent = newFileName;
    
    // Hide the download button
    var downloadButton = document.getElementById('downloadButton');
    if (downloadButton) {
        downloadButton.style.display = 'none';
    }
});


$(document).ready(function(){
    $('#updatetaskbtn').prop('disabled',true);
    $('#updatetaskbtn').attr('title','No changes made');
    $('#updatetaskbtn').addClass('disabledclass');

    $('input').on('keyup', function(){
    $('#updatetaskbtn').prop('disabled',false);
    $('#updatetaskbtn').attr('title','');
    $('#updatetaskbtn').removeClass('disabledclass');
    });
    
    $('textarea').on('keyup', function(){
    $('#updatetaskbtn').prop('disabled',false);
    $('#updatetaskbtn').attr('title','');
    $('#updatetaskbtn').removeClass('disabledclass');
    });
    
    $('select').on('change', function(){
    $('#updatetaskbtn').prop('disabled',false);
    $('#updatetaskbtn').attr('title','');
    $('#updatetaskbtn').removeClass('disabledclass');
    });
});