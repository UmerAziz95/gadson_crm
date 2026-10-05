$('#add_manager_btn').click(function () {
    $('#add-manager-popup').show();
    $('#add_manager_form')[0].reset();
    $('#add_manager_form .is-invalid').removeClass('is-invalid');
});

$('#edit_btn').click(function () {
    $('#edit-manager-popup').show();
});
$('#closeupdatedmodalbtn').click(function () {
    $('#close_update_modal_default_btn').click();
});
$('#closeaddmodalbtn').click(function () {
    $('#close_add_modal_btn').click();
});
function formatDate(dateString) {
    const months = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
}


function loadManagerList() {
    let url = '/admin/getmanagers';
    let type = 'GET';
    SendAjaxRequestToServer(type, url, '', '', loadManagerListResponse, '', '');
}


function loadManagerListResponse(response) {

    var managerTableBody = $('#manager_table_body');
    managerTableBody.empty();
    var managers = response.managers_list.managers_list;
    var inactive_managers = response.managers_list.inactive_managers;
    var active_managers = response.managers_list.active_managers;
    var totalManagers = managers.length;
    $('#total_managers').text(totalManagers);
    $('#inactive_managers').text(inactive_managers);
    $('#active_managers').text(active_managers);
    $.each(managers, function (index, manager) {
        

        var managerRow = `<tr class="identify">
                                <td class="nowrap">${index + 1}</td>
                                <td class="grid-p-searchby">${manager.first_name} ${manager.middle_name?manager.middle_name:''} ${manager.last_name?manager.last_name:''}</td>
                                <td class="grid-p-searchby">${manager.email}</td>
                                <td class="nowrap" >${manager.contact_number?manager.contact_number:''}</td>
                                <td class="nowrap">${formatDate(manager.created_at)}</td>
                                <td data-center>
                                    <div class="switch" >
                                        <input type="checkbox" onclick="changestatus(${manager.id})" name="status" id="status" ${manager.status == '1' ? 'checked' : ''}>
                                        <em></em>
                                    </div>
                                </td>
                               
                                
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                        <button type="button" class="edit pop_btn edit_btn"title="Edit"  data-popup="edit-data-popup" data-id = "${manager.id}"></button>
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${manager.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                    </div>
                                </td>
                            </tr>`;
        managerTableBody.append(managerRow);


    });
    // managers if length then add Datatable
    if (totalManagers > 0) {
        if ($.fn.DataTable.isDataTable('#managers_table')) {
            $('#managers_table').DataTable().destroy();
        }
        $('#managers_table').DataTable({
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


}

$('#add_manager_form').submit(function (e) {
    e.preventDefault();

    let form = document.getElementById('add_manager_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/addManager';
    SendAjaxRequestToServer(type, url, data, '', addManagerResponse, '', '#savemanagerbtn');


});

function addManagerResponse(response) {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

        let form = $('#add_manager_form');
        $('#uiBlocker').hide();
        form.trigger("reset");
        loadManagerList();
        $('#close_add_modal_btn').click();
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


$(document).on('click', '.delete_btn', function(){
    var del_id = $(this).attr('data-id');
    $('#delete_confirmed_btn').attr('disabled',false);
    $('#delete_confirmed_btn').attr('data-id', del_id);
});

$('#close_delete_modal_btn').click(function(){
    $('.clode_delete_modal_default_btn').click();
    $('#delete_confirmed_btn').attr('data-id', '');
});

$('#delete_confirmed_btn').click(function(){
    var del_id = $(this).attr('data-id');
    let url = '/admin/deletemanager';
    let type = 'POST';
    let data = new FormData();
    data.append('del_id', del_id);
    SendAjaxRequestToServer(type, url, data, '', deletemanagerResponse, '', '#delete_confirmed_btn');

});


function deletemanagerResponse(response){
    if (response.status == 200) {
        
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#uiBlocker').hide();
        
        loadManagerList();
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

function changestatus(id){
    
    let url = '/admin/changestatus';
    let type = 'POST';
    let data = new FormData();
    data.append('id', id);
    SendAjaxRequestToServer(type, url, data, '', changeStatusResponse, '', '');


}


function changeStatusResponse(response){
    if (response.status == 200) {
        
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#uiBlocker').hide();
        
        loadManagerList();
    }

    if (response.status == 402) {
        error = response.message;

    } else {
        error = response.responseJSON.message;
    }
    toastr.error(error, '', {
        timeOut: 3000
    });
}   

$(document).on('click','.edit_btn', function(){
    var id = $(this).attr('data-id');

    let url = '/admin/getmanagerdata';
    let type = 'POST';
    let data = new FormData();
    data.append('id', id);
    SendAjaxRequestToServer(type, url, data, '', getmanagerdataResponse, '', '');

    
});


function getmanagerdataResponse(response){
    if (response.status == 200) {
        var manager = response.data;
       
        $('#uiBlocker').hide();
        $('#manager_id_edit').val(manager[0].id);
        $('#first_name_edit').val(manager[0].first_name);
        $('#middle_name_edit').val(manager[0].middle_name);
        $('#last_name_edit').val(manager[0].last_name);
        $('#contact_number_edit').val(manager[0].contact_number);
        $('#email_edit').text(manager[0].email);
        
    }

    if (response.status == 402) {
        var error = response.message;
        toastr.error(error, '', {
            timeOut: 3000
        });
    } 
    
}


$('#edit_manager_form').submit(function(e){
    e.preventDefault();

    let form = document.getElementById('edit_manager_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/updateManager';
    SendAjaxRequestToServer(type, url, data, '', updateManagerResponse, '', 'savemanagerbtn');
});

function updateManagerResponse(response){
    const inputs = document.querySelectorAll('input');
    inputs.forEach(function(input) {
        input.classList.remove('is-invalid');
    });
    $('#uiBlocker').hide();
    if (response.status == 200) {
        
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        
        
        loadManagerList();
        $('#close_update_modal_default_btn').click();
    }

    if (response.status == 402) {
        // $('#close_update_modal_default_btn').click();

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

    loadManagerList();
    
});



