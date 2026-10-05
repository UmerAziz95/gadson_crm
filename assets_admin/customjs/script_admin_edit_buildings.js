const removedImageIds = [];
let  selectedFiles =[];

function removeExistingImage(button, imageId) {
    removedImageIds.push(imageId);

    // Remove the image preview
    const li = button.closest('li');
    li.remove();
}

document.getElementById('fileInput').addEventListener('change', function(event) {
    const files = event.target.files;
    const previewList = document.getElementById('previewList');

    Array.from(files).forEach((file) => {
        selectedFiles.push(file);
        const reader = new FileReader();
        reader.onload = function(e) {
            const li = document.createElement('li');
            li.innerHTML = `
                <div class="thumb">
                    <img src="${e.target.result}" alt="">
                    <button type="button" class="x_btn" onclick="removeNewImage(this)"></button>
                </div>
            `;
            previewList.appendChild(li);
        };
        reader.readAsDataURL(file);
    });
    updateFileInput();
});
function updateFileInput() {
            const fileInput = document.getElementById('fileInput');
            const dataTransfer = new DataTransfer();
            
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            fileInput.files = dataTransfer.files;
        }

function removeNewImage(button) {
    button.closest('li').remove();
}

document.getElementById('edit_building_form').addEventListener('submit', function(event) {
    // Append removed image IDs as hidden inputs to the form
    removedImageIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'removed_image_ids[]';
        input.value = id;
        this.appendChild(input);
    });
});

$('#country').change(function(){
    var country = $(this).val();
    let type = 'POST';
    let url = '/admin/getStates';
    let data = new FormData();
    data.append('country', country);
    SendAjaxRequestToServer(type, url, data, '', getStateResponse, '', '');

});

function getStateResponse(response){
    
    if (response.status == 200) {
        var states = response.states_list.states_list;
        $('#state').html('<option value="">Select State</option>'); 
        $.each(states, function(index, state) {
            $('#state').append('<option value="' + state.id + '">' + state.name + '</option>');
        });
    }
    
    
    
}

$('#state').change(function(){
    var state = $(this).val();
    let type = 'POST';
    let url = '/admin/getCities';
    let data = new FormData();
    data.append('state', state);
    SendAjaxRequestToServer(type, url, data, '', getCitiesResponse, '', '');

});

function getCitiesResponse(response){
    
    if (response.status == 200) {
        var cities = response.cities_list.cities_list;
        $('#city').html('<option value="">Select City</option>');
        $.each(cities, function(index, city) {
            $('#city').append('<option value="' + city.id + '">' + city.name + '</option>');
        });
    }
    
}


$('#edit_building_form').submit(function (e) {
    e.preventDefault();
    let form = document.getElementById('edit_building_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/updateBuilding';
    SendAjaxRequestToServer(type, url, data, '', updateBuildingResponse, '', '');

});

function updateBuildingResponse(response) {
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

        setTimeout(function () {
            window.location = '/admin/buildings'
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

