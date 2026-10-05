$('#add_appartment_btn').click(function () {
    window.location = '/admin/addAppartment';
});

function getappartmentsList() {
    let url = '/admin/getappartments';
    let type = 'GET';
    SendAjaxRequestToServer(type, url, '', '', getappartmentsListResponse, '', '');
}


function getappartmentsListResponse(response) {
    var appartmentsTableBody = $('#appartments_table_body');
    appartmentsTableBody.empty();
    var appartments = response.appartments_list.appartments_list;
    var pent_house_appartments = response.appartments_list.pent_house_appartments_list;
    var studio_appartments = response.appartments_list.studio_appartments_list;
    var appartmemt_appartments = response.appartments_list.appartment_appartments_list;
    var total_appartments = response.appartments_list.total_appartments_list;
    $('#penthouse_type_appartments').text(pent_house_appartments);
    $('#studio_type_appartments').text(studio_appartments);
    $('#appartment_type_appartments').text(appartmemt_appartments);
    $('#total_appartments').text(total_appartments);
    if (appartments.length > 0) {
        $.each(appartments, function (index, appartment) {

            if(appartment.images.length == 0){
               var appartmentImage_src = base_url +'/assets/images/building-icon.png'; 
            }
            else{
                var appartmentImage_src = appartment.images[0].image_path; 
            }
            var appartmentRow = `<tr style="align-items-center">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap"><img style="width:50px; height:50px; border-radius:500px;" src="${appartmentImage_src}"></td>
                                <td>${appartment.apartment_no}</td>
                                <td class="nowrap" >${appartment.apartment_name}</td>
                                <td class="nowrap" data-center>${appartment.building_id}</td>
                                <td class="nowrap" data-center>${appartment.category}</td>
                                <td class="nowrap">${appartment.apartment_type}</td>
                                <td class="nowrap">${appartment.number_of_rooms}</td>
                                <td class="nowrap">${appartment.apartment_size}</td>
                                <td class="nowrap">${appartment.status}</td>
                               
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                    <a href="/admin/appartments/${appartment.id}/edit" class="edit  edit_btn" title="Edit"></a>
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${appartment.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                </td>
                            </tr>`;
            appartmentsTableBody.append(appartmentRow);



        });
    }
    else {
        appartmentRow = `<tr class="col-12">
                        <td data-center colspan="7">No Data Available</td>
                        </tr>
                        `;
        appartmentsTableBody.append(appartmentRow);
    }
}

$('#add_apartment_form').submit(function (e) {
    e.preventDefault();
    let form = document.getElementById('add_apartment_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/storeAppartment';
    SendAjaxRequestToServer(type, url, data, '', storeAppartmentResponse, '', 'saveapartmentbtn');

});

function storeAppartmentResponse(response) {
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

$(document).on('click', '.delete_btn', function () {
    $('#delete_confirmed_btn').attr('data-id', '');
    var del_id = $(this).attr('data-id');
    $('#delete_confirmed_btn').attr('data-id', del_id);
});

$(document).on('click', '#close_delete_modal_btn', function () {
    $('#delete_confirmed_btn').attr('data-id', '');
    $('.clode_delete_modal_default_btn').click();
});


$(document).on('click', '#delete_confirmed_btn', function () {
    var del_id = $(this).attr('data-id');
    let url = '/admin/deleteappartment';
    let type = 'POST';
    let data = new FormData();
    data.append('del_id', del_id);
    SendAjaxRequestToServer(type, url, data, '', deleteappartmentResponse, '', '');
});

function deleteappartmentResponse(response) {
    if (response.status == 200) {

        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#uiBlocker').hide();

        getappartmentsList();
        $('#close_delete_modal_btn').click();
    }

    if (response.status == 402) {
        $('#close_delete_modal_btn').click();

        error = response.message;

    } else {
        $('#close_delete_modal_btn').click();

        error = response.responseJSON.message;
    }
    toastr.error(error, '', {
        timeOut: 3000
    });
}

let selectedFiles = [];

$('#fileInput').change(function(event) {
    const files = event.target.files;
    const previewList = document.getElementById('previewList');

    Array.from(files).forEach((file) => {
        selectedFiles.push(file);

        const reader = new FileReader();
        reader.onload = function(e) {
            const li = document.createElement('li');
            li.innerHTML += `
                <div class="thumb">
                    <img src="${e.target.result}" alt="">
                    <button type="button" class="x_btn" onclick="removeFile(${selectedFiles.length - 1})">&times;</button>
                </div>
            `;
            previewList.appendChild(li);
        };
        reader.readAsDataURL(file);
    });

    updateFileInput();
});

function removeFile(index) {
    selectedFiles.splice(index, 1);
    const previewList = document.getElementById('previewList');
    previewList.innerHTML = '';
    selectedFiles.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const li = document.createElement('li');
            li.innerHTML += `
                <div class="thumb">
                    <img src="${e.target.result}" alt="">
                    <button type="button" class="x_btn" onclick="removeFile(${idx})">&times;</button>
                </div>
            `;
            previewList.appendChild(li);
        };
        reader.readAsDataURL(file);
    });

    updateFileInput();
}

function updateFileInput() {
    const fileInput = document.getElementById('fileInput');
    const dataTransfer = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    fileInput.files = dataTransfer.files;
}

$(document).ready(function () {
    getappartmentsList();
});