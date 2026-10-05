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

document.getElementById('edit_apartment_form').addEventListener('submit', function(event) {
    // Append removed image IDs as hidden inputs to the form
    removedImageIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'removed_image_ids[]';
        input.value = id;
        this.appendChild(input);
    });
});

$('#edit_apartment_form').submit(function (e) {
    e.preventDefault();
    let form = document.getElementById('edit_apartment_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/updateAppartment';
    SendAjaxRequestToServer(type, url, data, '', updateAppartmentResponse, '', 'saveapartmentbtn');

});

function updateAppartmentResponse(response) {
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

        setTimeout(function () {
            window.location = '/admin/appartments'
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
