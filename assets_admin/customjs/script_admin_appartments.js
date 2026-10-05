$('#add_appartment_btn').click(function () {
    window.location = '/admin/addAppartment';
});

function getappartmentsList() {
    let url = '/admin/getappartments';
    let type = 'GET';
    let apartment_no = $('#apartment_no_filter').val();
    let apartment_name = $('#apartment_name_filter').val();
    let building_name = $('#building_name_filter').val();
    let category = $('#category_filter').val();
    let apartment_type = $('#apartment_type_filter').val();
    let no_of_rooms = $('#no_of_rooms_filter').val();
    let apartment_size = $('#apartment_size_filter').val();
    let status = $('#status_filter').val();
    url += '?apartment_no=' + apartment_no + '&apartment_name=' + apartment_name + '&building_name=' + building_name + '&category=' + category + '&apartment_type=' + apartment_type + '&no_of_rooms=' + no_of_rooms + '&apartment_size=' + apartment_size + '&status=' + status;
    SendAjaxRequestToServer(type, url, '', '', getappartmentsListResponse, '', '');
}

$(".advance-search").hide();
$(".advance-minus-icon").hide();
$(".advance-plus-icon").show();
$(".advance-search-btn").click(function () {
    $(".advance-search").toggle();
    if ($(".advance-search").is(":visible")) {
        $(".advance-minus-icon").show();
        $(".advance-plus-icon").hide();
    } else {
        $(".advance-minus-icon").hide();
        $(".advance-plus-icon").show();
    }
});
// building-advance-search-btn
$("#building-advance-search-btn").on("click", function () {
    getappartmentsList();
});
// advance-reset-btn
$(".advance-reset-btn").click(function () {
    // apartment_no_filter
    $('#apartment_no_filter').val('');
    $('#apartment_name_filter').val('');
    $('#building_name_filter').val('');
    $('#category_filter').val('');
    $('#apartment_type_filter').val('');
    $('#no_of_rooms_filter').val('');
    $('#apartment_size_filter').val('');
    $('#status_filter').val('');
    getappartmentsList();
});

function getappartmentsListResponse(response) {
    // apartment_table
    if ($.fn.DataTable.isDataTable('#apartment_table')) {
        $('#apartment_table').DataTable().destroy();
    }
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
            var appartmentRow = `<tr class="align-items-center identify">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap"><img style="width:50px; height:50px; border-radius:500px;" src="${appartmentImage_src}"></td>
                                <td class=" grid-p-searchby">${appartment.apartment_no}</td>
                                <td class="nowrap grid-p-searchby" >${appartment.apartment_name}</td>
                                <td class="nowrap grid-p-searchby" >${appartment.building && appartment.building.building_name ? appartment.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby" >${appartment.category}</td>
                                <td class="nowrap grid-p-searchby">${appartment.apartment_type}</td>
                                <td class="nowrap grid-p-searchby">${appartment.number_of_rooms!=null ? appartment.number_of_rooms : 'N/A'}</td>
                                <td class="nowrap grid-p-searchby">${appartment.apartment_size}</td>
                                <td class="nowrap grid-p-searchby">${appartment.status}</td>
                               
                               
                                <td class="nowrap" >
                                    <div class="act_btn">
                                    <a href="/admin/appartments/${appartment.id}/edit" class="edit  edit_btn" title="Edit"></a>
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${appartment.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                </td>
                            </tr>`;
            appartmentsTableBody.append(appartmentRow);



        });
        
        $('#apartment_table').DataTable({
            destroy: true, // Ensures reinitialization
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            dom: '<"row"<"col-md-12"f>>rtip' // Ensures the search bar gets col-md-12
        });
    }
    else {
        appartmentRow = `<tr class="col-12">
                        <td data-center colspan="9">No Data Available</td>
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

$(document).on('click', '.delete_btn', function () {
    $('#delete_confirmed_btn').attr('data-id', '');
    $('#delete_confirmed_btn').attr('disabled',false);
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
    SendAjaxRequestToServer(type, url, data, '', deleteappartmentResponse, '', '#delete_confirmed_btn');
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
                    <button type="button" class="x_btn" onclick="removeFile(${selectedFiles.length - 1})"></button>
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
                    <button type="button" class="x_btn" onclick="removeFile(${idx})"></button>
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


$('#searchInListing').on("keyup", function (e)  {   
   
    var tr = $('.identify');
    
    if ($(this).val().length >= 1) {//character limit in search box.
        var noElem = true;
        var val = $.trim(this.value).toLowerCase();
        el = tr.filter(function() {
            return $(this).find('.grid-p-searchby').text().toLowerCase().match(val);
        });
        if (el.length >= 1) {
            noElem = false;
        }
        tr.not(el).hide();
		el.fadeIn().show();
	} else {
		tr.fadeIn().show();
    }
});

$(document).ready(function () {
    getappartmentsList();
});