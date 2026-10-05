$(document).on('click', '.edit_btn', function () {
    $('#status_update_form')[0].reset();
    $('#attachment_upload_btn').html('Add Attachment<svg style="margin-left:14px;"height="17" viewBox="0 0 1792 1792" width="17" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1472q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h427q21 56 70.5 92t110.5 36h256q61 0 110.5-36t70.5-92h427q40 0 68 28t28 68zm-325-648q-17 40-59 40h-256v448q0 26-19 45t-45 19h-256q-26 0-45-19t-19-45v-448h-256q-42 0-59-40-17-39 14-69l448-448q18-19 45-19t45 19l448 448q31 30 14 69z"/></svg></p>')
    $('#update_task_status_btn').prop('disabled', false);
    $('#task_id').val('');
    var task_id = $(this).attr('data-id');
    $('#task_id').val(task_id);
});
// close delete modal 

$(document).on('click', '#close_update_status_modal_btn', function () {
    $('#delete_confirmed_btn').attr('data-id', '');
    $('.close_status_update_modal_default_btn').click();
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

function getTaskList() {
    let type = 'GET';
    let url = '/manager/getTasksList';
    SendAjaxRequestToServer(type, url, '', '', getTaskListResponse, '', '');
}
function getTaskListResponse(response) {

    var activeTaskTableBody = $('#active_tasks_table_body');
    activeTaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var draft_tasks = response.tasks_list.draft_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    var cancelled_tasks = response.tasks_list.cancelled_tasks;
    var stuck_tasks = response.tasks_list.stuck_tasks;
    var working_on_tasks = response.tasks_list.working_on_tasks;
    $('#stuck_tasks').text(stuck_tasks);
    $('#working_on_tasks').text(working_on_tasks);
    if(cancelled_tasks < 1 || cancelled_tasks == null || cancelled_tasks == ''){
        $('#cancelled_tab_btn').addClass('d-none');
    }
    else{
        $('#cancelled_tab_btn').removeClass('d-none');
    }
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#draft_tasks').text(draft_tasks);
    $('#done_tasks').text(done_tasks);
    $('#cancelled_tasks').text(cancelled_tasks);
    if (tasks.length == 0) {
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        activeTaskTableBody.append(taskRow);
    }
    else {
        $.each(tasks, function (index, task) {
            var priority = task.priority;
            if (priority == '0' || priority == 0) {
                priority = 'Low';
            }
            if (priority == '1' || priority == 1) {
                priority = 'Medium';
            }
            if (priority == '2' || priority == 2) {
                priority = 'Urgent';
            }
            var document_typeTxt = '';
            var document_type = task.document_type;
            if (document_type == 0 || document_type == '0') {
                document_typeTxt = 'Section 8';
            }
            if (document_type == 1 || document_type == '1') {
                document_typeTxt = 'HPD';
            }
            if (document_type == 2 || document_type == '2') {
                document_typeTxt = 'Work Order'
            }
            if (document_type == 3 || document_type == '3') {
                document_typeTxt = 'Other';
            }

            var statusTxt = '';
            var notification_dot_color = '';
            var status = task.status;
            if (status == 0 || status == '0') {
                statusTxt = 'Draft';
                notification_dot_color = 'transparent';
            }
            if (status == 1 || status == '1') {
                statusTxt = 'Assigned';
                notification_dot_color = '#89BE3A';

            }
            if (status == 2 || status == '2') {
                statusTxt = 'Working On it';
                notification_dot_color = '#FFC300';
            }
            if (status == 3 || status == '3') {
                statusTxt = 'Hold';
                notification_dot_color = '#FF5733';
            }
            if (status == 4 || status == '4') {
                statusTxt = 'Stuck';
                notification_dot_color = '#9B59B6';
            }
            if (status == 5 || status == '5') {
                statusTxt = 'Done';
                notification_dot_color = '#2ECC71';
            }
            if (status == 6 || status == '6') {
                statusTxt = 'Cancelled';
                notification_dot_color = '#FF0000';
            }
            var taskRow = `<tr class="align-items-center identify">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap grid-p-searchby"><div title="${statusTxt}" style="position: relative; width: 20px; height: 20px; display:inline;margin-right:15px;">
                                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: ${notification_dot_color}; border-radius: 50%; display: inline-block;"></span>
                            </div>${formatDate(task.created_at)}</td>
                                <td class="nowrap grid-p-searchby">${task.task_title}</td>
                                <td class="nowrap grid-p-searchby">${task.building && task.building.building_name ? task.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${task.appartment && task.appartment.apartment_name ? task.appartment.apartment_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${priority}</td>
                                <td class="nowrap grid-p-searchby">
                                ${task.document_status == 1
                    ? ` <a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                    : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                                    </td>
                                

                                    <td class="nowrap grid-p-searchby" data-center>${document_typeTxt}</td>
                                    <td class="nowrap grid-p-searchby" data-center>${task.document_status == 0 ? 'Uploaded' : 'Viewed'}</td>
                               
                                    <td class="nowrap grid-p-searchby"><span style="background-color:${notification_dot_color} !important;color: #fff; padding: 3px;border-radius: 5px;">${statusTxt}</span></td>
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                   
                                    
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View Timeline" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                    ${task.status !== 6 ? `
            <button type="button" class="pop_btn add_to_to_modal_btn" title="Add To do list" 
                    data-id="${task.id}" data-popup="add_todo_list_modal">
                <img src="${base_url + '/assets/images/icon-plus.svg'}" alt="">
            </button>
            <button type="button" class="pop_btn edit edit_btn" title="Update Status" 
                    data-id="${task.id}" data-popup="status-update-popup">
            </button>
        ` : ''}
                                    
                                       
                                       
                                    </div>
                                </td>
                            </tr>`;

            activeTaskTableBody.append(taskRow);



        });
    }

}

function getDoneTaskList() {
    let type = 'GET';
    let url = '/manager/getDoneTasksList';
    SendAjaxRequestToServer(type, url, '', '', getDoneTasksListResponse, '', '');
}
function getDoneTasksListResponse(response) {

    var activeTaskTableBody = $('#done_tasks_table_body');
    activeTaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var draft_tasks = response.tasks_list.draft_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    var cancelled_tasks = response.tasks_list.cancelled_tasks;
    var stuck_tasks = response.tasks_list.stuck_tasks;
    var working_on_tasks = response.tasks_list.working_on_tasks;
    $('#stuck_tasks').text(stuck_tasks);
    $('#working_on_tasks').text(working_on_tasks);
    if(cancelled_tasks < 1 || cancelled_tasks == null || cancelled_tasks == ''){
        $('#cancelled_tab_btn').addClass('d-none');
    }
    else{
        $('#cancelled_tab_btn').removeClass('d-none');
    }
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#draft_tasks').text(draft_tasks);
    $('#done_tasks').text(done_tasks);
    $('#cancelled_tasks').text(cancelled_tasks);
    if (tasks.length == 0) {
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        activeTaskTableBody.append(taskRow);
    }
    else {
        $.each(tasks, function (index, task) {
            var priority = task.priority;
            if (priority == '0' || priority == 0) {
                priority = 'Low';
            }
            if (priority == '1' || priority == 1) {
                priority = 'Medium';
            }
            if (priority == '2' || priority == 2) {
                priority = 'Urgent';
            }
            var document_typeTxt = '';
            var document_type = task.document_type;
            if (document_type == 0 || document_type == '0') {
                document_typeTxt = 'Section 8';
            }
            if (document_type == 1 || document_type == '1') {
                document_typeTxt = 'HPD';
            }
            if (document_type == 2 || document_type == '2') {
                document_typeTxt = 'Work Order'
            }
            if (document_type == 3 || document_type == '3') {
                document_typeTxt = 'Other';
            }

            var statusTxt = '';
            var notification_dot_color = '';
            var status = task.status;
            if (status == 0 || status == '0') {
                statusTxt = 'Draft';
                notification_dot_color = 'transparent';
            }
            if (status == 1 || status == '1') {
                statusTxt = 'Assigned';
                notification_dot_color = '#89BE3A';

            }
            if (status == 2 || status == '2') {
                statusTxt = 'Working On it';
                notification_dot_color = '#FFC300';
            }
            if (status == 3 || status == '3') {
                statusTxt = 'Hold';
                notification_dot_color = '#FF5733';
            }
            if (status == 4 || status == '4') {
                statusTxt = 'Stuck';
                notification_dot_color = '#9B59B6';
            }
            if (status == 5 || status == '5') {
                statusTxt = 'Done';
                notification_dot_color = '#2ECC71';
            }
            var taskRow = `<tr class="align-items-center identify1">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap grid-p-searchby"><div title="${statusTxt}" style="position: relative; width: 20px; height: 20px; display:inline;margin-right:15px;">
                                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: ${notification_dot_color}; border-radius: 50%; display: inline-block;"></span>
                            </div>${formatDate(task.created_at)}</td>
                                <td class="nowrap grid-p-searchby">${task.task_title}</td>
                                <td class="nowrap grid-p-searchby">${task.building && task.building.building_name ? task.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${task.appartment && task.appartment.apartment_name ? task.appartment.apartment_name : 'Deleted'}</td>
                                <td>${priority}</td>
                               
                                <td class="nowrap grid-p-searchby">
                                ${task.document_status == 1
                    ? ` <a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                    : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                                    </td>

                                <td class="nowrap grid-p-searchby" data-center>${document_typeTxt}</td>
                                <td class="nowrap grid-p-searchby" data-center>${task.document_status == 0 ? 'Uploaded' : 'Viewed'}</td>
                                <td class="nowrap grid-p-searchby"><span style="background-color:${notification_dot_color} !important;color: #fff; padding: 3px;border-radius: 5px;">${statusTxt}</span></td>
                                <td class="nowrap">
                                <div class="act_btn">
                                <button type="button" class="pop_btn viewdetailsbtn" title="View Timeline" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                </div>
                                </td>
                            </tr>`;

            activeTaskTableBody.append(taskRow);



        });
    }

}


// canceled task start 
function getCancelledTaskList() {
    let type = 'GET';
    let url = '/manager/getCancelledTasksList';
    SendAjaxRequestToServer(type, url, '', '', getCancelledTasksListResponse, '', '');
}
function getCancelledTasksListResponse(response) {

    var cancelledTaskTableBody = $('#cancelled_tasks_table_body');
    cancelledTaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var draft_tasks = response.tasks_list.draft_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    var cancelled_tasks = response.tasks_list.cancelled_tasks;
    var stuck_tasks = response.tasks_list.stuck_tasks;
    var working_on_tasks = response.tasks_list.working_on_tasks;
    $('#stuck_tasks').text(stuck_tasks);
    $('#working_on_tasks').text(working_on_tasks);
    if(cancelled_tasks < 1 || cancelled_tasks == null || cancelled_tasks == ''){
        $('#cancelled_tab_btn').addClass('d-none');
    }
    else{
        $('#cancelled_tab_btn').removeClass('d-none');
    }
    $('#cancelled_tasks').text(cancelled_tasks);
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#draft_tasks').text(draft_tasks);
    $('#done_tasks').text(done_tasks);
    
    if (tasks.length == 0) {
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        cancelledTaskTableBody.append(taskRow);
    }
    else {
        $.each(tasks, function (index, task) {
            var priority = task.priority;
            if (priority == '0' || priority == 0) {
                priority = 'Low';
            }
            if (priority == '1' || priority == 1) {
                priority = 'Medium';
            }
            if (priority == '2' || priority == 2) {
                priority = 'Urgent';
            }
            var document_typeTxt = '';
            var document_type = task.document_type;
            if (document_type == 0 || document_type == '0') {
                document_typeTxt = 'Section 8';
            }
            if (document_type == 1 || document_type == '1') {
                document_typeTxt = 'HPD';
            }
            if (document_type == 2 || document_type == '2') {
                document_typeTxt = 'Work Order'
            }
            if (document_type == 3 || document_type == '3') {
                document_typeTxt = 'Other';
            }

            var statusTxt = '';
            var notification_dot_color = '';
            var status = task.status;
            if (status == 0 || status == '0') {
                statusTxt = 'Draft';
                notification_dot_color = 'transparent';
            }
            if (status == 1 || status == '1') {
                statusTxt = 'Assigned';
                notification_dot_color = '#89BE3A';

            }
            if (status == 2 || status == '2') {
                statusTxt = 'Working On it';
                notification_dot_color = '#FFC300';
            }
            if (status == 3 || status == '3') {
                statusTxt = 'Hold';
                notification_dot_color = '#FF5733';
            }
            if (status == 4 || status == '4') {
                statusTxt = 'Stuck';
                notification_dot_color = '#9B59B6';
            }
            if (status == 5 || status == '5') {
                statusTxt = 'Done';
                notification_dot_color = '#2ECC71';
            }
            if (status == 6 || status == '6') {
                statusTxt = 'Cancelled';
                notification_dot_color = '#FF0000';
            }
            var taskRow = `<tr class="align-items-center identify2">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap grid-p-searchby2"><div title="${statusTxt}" style="position: relative; width: 20px; height: 20px; display:inline;margin-right:15px;">
                                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: ${notification_dot_color}; border-radius: 50%; display: inline-block;"></span>
                            </div>${formatDate(task.created_at)}</td>
                                <td class="nowrap grid-p-searchby2">${task.task_title}</td>
                                <td class="nowrap grid-p-searchby2">${task.building && task.building.building_name ? task.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby2">${task.appartment && task.appartment.apartment_name ? task.appartment.apartment_name : 'Deleted'}</td>
                                <td>${priority}</td>
                               
                                <td class="nowrap grid-p-searchby2">
                                ${task.document_status == 1
                    ? ` <a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                    : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                                    </td>

                                <td class="nowrap grid-p-searchby2" data-center>${document_typeTxt}</td>
                                <td class="nowrap grid-p-searchby2" data-center>${task.document_status == 0 ? 'Uploaded' : 'Viewed'}</td>
                                <td class="nowrap grid-p-searchby2"><span style="background-color:${notification_dot_color} !important;color: #fff; padding: 3px;border-radius: 5px;">${statusTxt}</span></td>
                                <td class="nowrap">
                                <div class="act_btn">
                                <button type="button" class="pop_btn viewdetailsbtn" title="View Timeline" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                </div>
                                </td>
                            </tr>`;

            cancelledTaskTableBody.append(taskRow);



        });
    }

}
// canceled task end 




$(document).on('click', '#downloadDocumentBtn', function () {
    var task_id = $(this).attr('data-id');
    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/manager/changeDocumentStatus';
    SendAjaxRequestToServer(type, url, data, '', changeDocumentStatusResponse, '', 'downloadDocumentBtn');
});



document.getElementById('attachment_upload_btn').addEventListener('click', function () {
    document.getElementById('attachment_upload').click();
});

document.getElementById('attachment_upload').addEventListener('change', function () {
    const fileInput = document.getElementById('attachment_upload');
    const fileSelectDiv = document.getElementById('attachment_upload_btn');

    if (fileInput.files.length > 0) {
        fileSelectDiv.textContent = fileInput.files[0].name;
    } else {
        fileSelectDiv.textContent = 'Select a file';
    }
});

function changeDocumentStatusResponse(response) {
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

        getTaskList();


    }
    else {
        getTaskList();
    }
}


$('#status_update_form').submit(function (e) {
    e.preventDefault();
    let form = document.getElementById('status_update_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/manager/changeTaskStatus';
    SendAjaxRequestToServer(type, url, data, '', changeTaskStatusResponse, '', '#update_task_status_btn');
});

function changeTaskStatusResponse(response) {
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#status_update_form')[0].reset();
        $('#close_update_status_modal_btn').click();
        getTaskList();
        getDoneTaskList();

    }
    else {

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
}


// task to do list start
function addTodoItem(btn) {
    var container = btn.closest('.form_blk').querySelector('.to_do_list_container');
    var newItem = document.createElement('div');
    newItem.classList.add('to_do_item_row');
    newItem.innerHTML = `
        <input type="text" name="to_do_item[]" class="form-control text_box">
        <button type="button" class="add_to_do_item_btn site_btn btn update_task_status_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px;" onclick="addTodoItem(this)">+</button>
        <button type="button" class="remove_to_do_item_btn site_btn btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px; background-color: red;" onclick="removeTodoItem(this)">-</button>
    `;
    container.appendChild(newItem);
    updateButtons(container);
}

function removeTodoItem(btn) {
    var container = btn.closest('.form_blk').querySelector('.to_do_list_container');
    container.removeChild(btn.parentElement);
    updateButtons(container);
}

function updateButtons(container) {
    var rows = container.getElementsByClassName('to_do_item_row');
    for (var i = 0; i < rows.length; i++) {
        var removeBtn = rows[i].getElementsByClassName('remove_to_do_item_btn')[0];
        if (i === 0) {
            removeBtn.style.display = 'none';
        } else {
            removeBtn.style.display = 'inline-block';
        }
    }
}



// task to do list end

$(document).on('click', '.add_to_to_modal_btn', function () {
    $('#task_id_to_do').val('');
    var task_id = $(this).attr('data-id');
    $('#task_id_to_do').val(task_id);

    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/manager/getTaskTodoList';
    SendAjaxRequestToServer(type, url, data, '', getTaskTodoListResponse, '', 'add_to_to_modal_btn');
});

function getTaskTodoListResponse(response) {
    if (response.status == 200) {
        if (response.todolist.length > 0) {
            var todolist = response.todolist;
            $('#todolistcontainer').empty(); // Clear the container once before the loop
            $.each(todolist, function (index, todoitem) {
                var html = `<div class="col-xs-12">
                                <div class="form_blk">
                                    
                                    <div class="to_do_list_container">
                                        <div class="to_do_item_row">
                                            <input type="text" name="to_do_item[]" class="form-control text_box" value="${todoitem.to_do_item}">
                                            <button type="button" class="add_to_do_item_btn site_btn btn update_task_status_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px;" onclick="addTodoItem(this)">+</button>
                                            <button type="button" class="remove_to_do_item_btn site_btn btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px; background-color: red;" onclick="removeTodoItem(this)">-</button>
                                            <button type="button" class="remove_to_do_item_btn site_btn btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px; background-color: red;" onclick="removeTodoItem(this)">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                $('#todolistcontainer').append(html); // Append each item
            });
            $('.to_do_list_container').each(function () {
                updateButtons(this); // Ensure buttons are updated after appending items
            });
        }

        else {
            $('#todolistcontainer').empty();
            var html = `<div class="col-xs-12">
                                <div class="form_blk">
                                    
                                    <div class="to_do_list_container">
                                        <div class="to_do_item_row">
                                            <input type="text" name="to_do_item[]" class="form-control text_box" value="">
                                            <button type="button" class="add_to_do_item_btn site_btn btn update_task_status_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px;" onclick="addTodoItem(this)">+</button>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>`;
            $('#todolistcontainer').append(html);
        }

    }
    else {
        $('#todolistcontainer').empty();
        var html = `<div class="col-xs-12">
                                <div class="form_blk">
                                    
                                    <div class="to_do_list_container">
                                        <div class="to_do_item_row">
                                            <input type="text" name="to_do_item[]" class="form-control text_box" value="">
                                            <button type="button" class="add_to_do_item_btn site_btn btn update_task_status_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px;" onclick="addTodoItem(this)">+</button>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>`;
        $('#todolistcontainer').append(html);
    }
}

$('#add_todo_list_form').submit(function (e) {
    e.preventDefault();

    let form = document.getElementById('add_todo_list_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/manager/addTaskTodoList';
    SendAjaxRequestToServer(type, url, data, '', addTaskTodoListResponse, '', 'add_todo_btn');
});

function addTaskTodoListResponse(response) {
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#add_todo_list_form')[0].reset();
        $('#close_update_status_modal_btn').click();
        getTaskList();
        getDoneTaskList();

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

// to view to do list and status timeline

$(document).on('click', '.viewdetailsbtn', function () {
    var task_id = $(this).attr('data-id');
    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/manager/gettimelinesdetail';
    SendAjaxRequestToServer(type, url, data, '', gettimelinesdetailResponse, '', '');
});

function gettimelinesdetailResponse(response) {

    var status_timeline_details = response.data.status_timeline_details;

    $('#status_timeline_detailsdiv').empty();


    if (status_timeline_details.length < 1) {
        $('#status_timeline_detailsdiv').text('No Data Available');
    }
    else {


        var status_timeline_details_div = document.getElementById('status_timeline_detailsdiv');
        var html = '';

        $.each(status_timeline_details, function (index, item) {
            var isEven = index % 2 === 0;
            var alignmentClass = isEven ? '' : 'timeline-inverted';
            var statusText = getStatusText(item.task_status);

            html += `${
                item.created_by == 1
                    ? `<li>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img cd-picture">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="#fff" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M10 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h2m10 1a3 3 0 0 1-3 3m3-3a3 3 0 0 0-3-3m3 3h1m-4 3a3 3 0 0 1-3-3m3 3v1m-3-4a3 3 0 0 1 3-3m-3 3h-1m4-3v-1m-2.121 1.879l-.707-.707m5.656 5.656l-.707-.707m-4.242 0l-.707.707m5.656-5.656l-.707.707M12 8a3 3 0 1 1-6 0a3 3 0 0 1 6 0Z"/></svg>
                    </div>
                    <div class="cd-timeline-content">
                        <p>
                            <strong>Action:</strong> ${item.action}
                            ${
                                item.task_status == 1 && (item.comment=='Task Assigned to' || item.comment =='Manager Changed to ')
                                    ? `<button class="btn btn-sm bg-transparent" style="background:transparent;" data-toggle="tooltip" data-placement="top" title="${
                                          item.comment
                                      } ${
                                          item.manager.first_name +
                                          " " +
                                          item.manager.last_name
                                      }">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="#0078b9" d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2.033 16.01c.564-1.789 1.632-3.932 1.821-4.474.273-.787-.211-1.136-1.74.209l-.34-.64c1.744-1.897 5.335-2.326 4.113.613-.763 1.835-1.309 3.074-1.621 4.03-.455 1.393.694.828 1.819-.211.153.25.203.331.356.619-2.498 2.378-5.271 2.588-4.408-.146zm4.742-8.169c-.532.453-1.32.443-1.761-.022-.441-.465-.367-1.208.164-1.661.532-.453 1.32-.442 1.761.022.439.466.367 1.209-.164 1.661z"/>
                                    </svg>
                                </button>`
                                    : `<button class="btn btn-sm bg-transparent" style="background:transparent;" data-toggle="tooltip" data-placement="top" title="${item.comment}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="#0078b9" d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2.033 16.01c.564-1.789 1.632-3.932 1.821-4.474.273-.787-.211-1.136-1.74.209l-.34-.64c1.744-1.897 5.335-2.326 4.113.613-.763 1.835-1.309 3.074-1.621 4.03-.455 1.393.694.828 1.819-.211.153.25.203.331.356.619-2.498 2.378-5.271 2.588-4.408-.146zm4.742-8.169c-.532.453-1.32.443-1.761-.022-.441-.465-.367-1.208.164-1.661.532-.453 1.32-.442 1.761.022.439.466.367 1.209-.164 1.661z"/>
                                    </svg>
                                </button>`
                            }
                        </p>
                        <p><strong>Status:</strong> ${statusText}</p>
                        ${
                            item.attachment != null
                                ? `<a href="${item.attachment}" download="attachment" class="btn btn-primary btn-sm" style="border-radius: 5px; font-size: 9px;" title="download attachment">Download</a>`
                                : ""
                        }
                        <span class="cd-date-right">${formatDate(
                            item.created_at
                        )}</span>
                    </div>
                </div>
            </li>`
                    :
                     `<li>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img cd-picture">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><circle cx="12" cy="7.5" r="3"/><path d="M19.5 20.5c-.475-9.333-14.525-9.333-15 0"/><path d="M11.192 17.565c.394-.21.591-.315.808-.315c.217 0 .414.105.808.315l.134.072c.394.21.591.315.7.488c.108.173.108.383.108.804v.142c0 .42 0 .63-.108.804c-.109.173-.306.278-.7.488l-.134.072c-.394.21-.591.315-.808.315c-.217 0-.414-.105-.808-.315l-.134-.072c-.394-.21-.591-.315-.7-.488c-.108-.173-.108-.383-.108-.804v-.142c0-.42 0-.63.108-.804c.109-.173.306-.278.7-.488z"/></g></svg>
                    </div>
                    <div class="cd-timeline-content">
                    <div class="cd-date">
                        <p>
                            <strong>Action:</strong> ${item.action}
                            ${
                                item.task_status == 1
                                    ? `<button class="btn btn-sm bg-transparent" style="background:transparent;" data-toggle="tooltip" data-placement="top" title="${
                                          item.comment
                                      } ${
                                          item.manager.first_name +
                                          " " +
                                          item.manager.last_name
                                      }">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="#0078b9" d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2.033 16.01c.564-1.789 1.632-3.932 1.821-4.474.273-.787-.211-1.136-1.74.209l-.34-.64c1.744-1.897 5.335-2.326 4.113.613-.763 1.835-1.309 3.074-1.621 4.03-.455 1.393.694.828 1.819-.211.153.25.203.331.356.619-2.498 2.378-5.271 2.588-4.408-.146zm4.742-8.169c-.532.453-1.32.443-1.761-.022-.441-.465-.367-1.208.164-1.661.532-.453 1.32-.442 1.761.022.439.466.367 1.209-.164 1.661z"/>
                                    </svg>
                                </button>`
                                    : `<button class="btn btn-sm bg-transparent" style="background:transparent;" data-toggle="tooltip" data-placement="top" title="${item.comment}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="#0078b9" d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2.033 16.01c.564-1.789 1.632-3.932 1.821-4.474.273-.787-.211-1.136-1.74.209l-.34-.64c1.744-1.897 5.335-2.326 4.113.613-.763 1.835-1.309 3.074-1.621 4.03-.455 1.393.694.828 1.819-.211.153.25.203.331.356.619-2.498 2.378-5.271 2.588-4.408-.146zm4.742-8.169c-.532.453-1.32.443-1.761-.022-.441-.465-.367-1.208.164-1.661.532-.453 1.32-.442 1.761.022.439.466.367 1.209-.164 1.661z"/>
                                    </svg>
                                </button>`
                            }
                        </p>
                        <p><strong>Status:</strong> ${statusText}</p>
                        ${
                            item.attachment != null
                                ? `<a href="${item.attachment}" download="attachment" class="btn btn-primary btn-sm" style="border-radius: 5px; font-size: 9px;" title="download attachment">Download</a>`
                                : ""
                        }
                        </div>
                        <span class="cd-date-left">${formatDate(item.created_at)}</span>
                    </div>
                </div>
            </li>`
            }`;

        });

        status_timeline_details_div.innerHTML = `<ul class="timeline">${html}</ul>`;

        function getStatusText(status) {
            switch (status) {
                case 0: return 'Draft';
                case 1: return 'Assigned';
                case 2: return 'Working on it';
                case 3: return 'On hold';
                case 4: return 'Stuck';
                case 5: return 'Done';
                case 6: return 'Cancelled';
                case 11: return 'Reassigned';
                case 22: return 'Reopened';
                default: return 'Unknown status';
            }
        }


    }
}

$(".advance-search").hide();
$(".advance-minus-icon").hide();
$(".advance-plus-icon").show();
$(".advance-search-btn").click(function () {
    console.log('SDF');
    $(".advance-search").toggle();
    if ($(".advance-search").is(":visible")) {
        $(".advance-minus-icon").show();
        $(".advance-plus-icon").hide();
    } else {
        $(".advance-minus-icon").hide();
        $(".advance-plus-icon").show();
    }
});

$('#searchInListing').on("keyup", function (e) {

    var tr = $('.identify');

    if ($(this).val().length >= 1) {//character limit in search box.
        var noElem = true;
        var val = $.trim(this.value).toLowerCase();
        el = tr.filter(function () {
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
$('#searchInListing1').on("keyup", function (e) {

    var tr = $('.identify1');

    if ($(this).val().length >= 1) {//character limit in search box.
        var noElem = true;
        var val = $.trim(this.value).toLowerCase();
        el = tr.filter(function () {
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
$('#searchInListing2').on("keyup", function (e) {
    // console.log('SDF');
    var tr = $('.identify2');
    if ($(this).val().length >= 1) {//character limit in search box.
        var noElem = true;
        var val = $.trim(this.value).toLowerCase();
        el = tr.filter(function () {
            return $(this).find('.grid-p-searchby2').text().toLowerCase().match(val);
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

// filters code 
$('#building_filter').change(function(){
    var building_id = $(this).val();
    let type = 'POST';
    let url = '/manager/getAppartmentsList';
    let data = new FormData();
    data.append('building_id', building_id);
    SendAjaxRequestToServer(type, url, data, '', getAppartmentsListResponse, '', '');

});

function getAppartmentsListResponse(response){
    if (response.status == 200) {
        var appartments = response.appartment_list.appartment_list;
        $('#appartment_filter1').html('<option value="">Select Appartment</option>'); 
        $.each(appartments, function(index, appartment) {
            $('#appartment_filter').append('<option value="' + appartment.id + '">' + appartment.apartment_name + '</option>');
        });
    }
    
}

$('#advance_search_active_tasks_btn').click(function () {
    var building_filter = $('#building_filter').val();
    var appartment_filter = $('#appartment_filter').val();
    var priority_filter = $('#priority_filter').val();
    var doc_status_filter = $('#document_status_filter').val();
    var document_type_filter = $('#document_type_filter').val();
    var task_status_filter = $('#task_status_filter').val();
    let data = new FormData();
    data.append('building_filter', building_filter);
    data.append('appartment_filter', appartment_filter);
    data.append('priority_filter', priority_filter);
    data.append('doc_status_filter', doc_status_filter);
    data.append('document_type_filter', document_type_filter);

    data.append('task_status_filter', task_status_filter);
    let type = 'POST';
    let url = '/manager/getfilteredtasks';
    SendAjaxRequestToServer(type, url, data, '', get_filtered_tasksResponse, '', '');
});

function get_filtered_tasksResponse(response) {
    if (response.status == 200) {
        var tasks = response.tasks;
        if (tasks.length > 0) {
            $('#active_tasks_table_body').empty();

            $.each(tasks, function (index, task) {
                var priority = task.priority;
                if (priority == '0' || priority == 0) {
                    priority = 'Low';
                }
                if (priority == '1' || priority == 1) {
                    priority = 'Medium';
                }
                if (priority == '2' || priority == 2) {
                    priority = 'Urgent';
                }
                var document_typeTxt = '';
                var document_type = task.document_type;
                if (document_type == 0 || document_type == '0') {
                    document_typeTxt = 'Section 8';
                }
                if (document_type == 1 || document_type == '1') {
                    document_typeTxt = 'HPD';
                }
                if (document_type == 2 || document_type == '2') {
                    document_typeTxt = 'Work Order'
                }
                if (document_type == 3 || document_type == '3') {
                    document_typeTxt = 'Other';
                }

                var statusTxt = '';
                var notification_dot_color = '';
                var status = task.status;
                if (status == 0 || status == '0') {
                    statusTxt = 'Draft';
                    notification_dot_color = 'transparent';
                }
                if (status == 1 || status == '1') {
                    statusTxt = 'Assigned';
                    notification_dot_color = '#89BE3A';

                }
                if (status == 2 || status == '2') {
                    statusTxt = 'Working On it';
                    notification_dot_color = '#FFC300';
                }
                if (status == 3 || status == '3') {
                    statusTxt = 'Hold';
                    notification_dot_color = '#FF5733';
                }
                if (status == 4 || status == '4') {
                    statusTxt = 'Stuck';
                    notification_dot_color = '#9B59B6';
                }
                if (status == 5 || status == '5') {
                    statusTxt = 'Done';
                    notification_dot_color = '#2ECC71';
                }
                var taskRow = `<tr class="align-items-center identify">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap grid-p-searchby"><div title="${statusTxt}" style="position: relative; width: 20px; height: 20px; display:inline;margin-right:15px;">
                                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: ${notification_dot_color}; border-radius: 50%; display: inline-block;"></span>
                            </div>${formatDate(task.created_at)}</td>
                                <td class="nowrap grid-p-searchby">${task.task_title}</td>
                                <td class="nowrap grid-p-searchby">${task.building && task.building.building_name ? task.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${task.appartment && task.appartment.apartment_name ? task.appartment.apartment_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${priority}</td>
                                <td class="nowrap grid-p-searchby">
                                ${task.document_status == 1
                        ? ` <a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                        : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                                    </td>
                                

                                    <td class="nowrap grid-p-searchby" data-center>${document_typeTxt}</td>
                                    <td class="nowrap grid-p-searchby" data-center>${task.document_status == 0 ? 'Uploaded' : 'Viewed'}</td>
                               
                                    <td class="nowrap grid-p-searchby"><span style="background-color:${notification_dot_color} !important;color: #fff; padding: 3px;border-radius: 5px;">${statusTxt}</span></td>
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                   
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>

                                    <button type="button" class="pop_btn add_to_to_modal_btn" title="Add To do list" data-id = "${task.id}" data-popup="add_todo_list_modal"><img src="${base_url + '/assets/images/icon-plus.svg'}" alt=""></button>

                                    <button type="button" class="pop_btn edit  edit_btn" title="Update Status" data-id = "${task.id}" data-popup="status-update-popup"></button>
                                       
                                       
                                    </div>
                                </td>
                            </tr>`;

                $('#active_tasks_table_body').append(taskRow);



            });
        }
        else {
            $('#active_tasks_table_body').empty();
            var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
            $('#active_tasks_table_body').append(taskRow);
        }
    }
    else {
        $('#active_tasks_table_body').empty();
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        $('#active_tasks_table_body').append(taskRow);
    }
}

$('#advance_search_active_tasks_reset_btn').click(function () {
    $('#search_all_tasks_form')[0].reset();
    getTaskList();
});

$('#building_filter1').change(function(){
    var building_id = $(this).val();
    let type = 'POST';
    let url = '/manager/getAppartmentsList';
    let data = new FormData();
    data.append('building_id', building_id);
    SendAjaxRequestToServer(type, url, data, '', getAppartmentsListResponse1, '', '');

});

function getAppartmentsListResponse1(response){
    if (response.status == 200) {
        var appartments = response.appartment_list.appartment_list;
        $('#appartment_filter1').html('<option value="">Select Appartment</option>'); 
        $.each(appartments, function(index, appartment) {
            $('#appartment_filter1').append('<option value="' + appartment.id + '">' + appartment.apartment_name + '</option>');
        });
    }
    
}

// filter for done tasks 

$('#advance_search_done_tasks_btn').click(function () {
    var building_filter = $('#building_filter1').val();
    var appartment_filter = $('#appartment_filter1').val();
    var priority_filter = $('#priority_filter1').val();
    var doc_status_filter = $('#document_status_filter1').val();
    var document_type_filter1 = $('#document_type_filter1').val();

    let data = new FormData();
    data.append('building_filter', building_filter);
    data.append('appartment_filter', appartment_filter);
    data.append('priority_filter', priority_filter);
    data.append('doc_status_filter', doc_status_filter);
    data.append('document_type_filter1', document_type_filter1);

    let type = 'POST';
    let url = '/manager/getfilteredcompletedtasks';
    SendAjaxRequestToServer(type, url, data, '', getfilteredcompletedtasksResponse, '', '');
});

function getfilteredcompletedtasksResponse(response) {
    if (response.status == 200) {
        var tasks = response.tasks;
        if (tasks.length > 0) {
            $('#done_tasks_table_body').empty();

            $.each(tasks, function (index, task) {
                var priority = task.priority;
                if (priority == '0' || priority == 0) {
                    priority = 'Low';
                }
                if (priority == '1' || priority == 1) {
                    priority = 'Medium';
                }
                if (priority == '2' || priority == 2) {
                    priority = 'Urgent';
                }
                var document_typeTxt = '';
                var document_type = task.document_type;
                if (document_type == 0 || document_type == '0') {
                    document_typeTxt = 'Section 8';
                }
                if (document_type == 1 || document_type == '1') {
                    document_typeTxt = 'HPD';
                }
                if (document_type == 2 || document_type == '2') {
                    document_typeTxt = 'Work Order'
                }
                if (document_type == 3 || document_type == '3') {
                    document_typeTxt = 'Other';
                }

                var statusTxt = '';
                var notification_dot_color = '';
                var status = task.status;
                if (status == 0 || status == '0') {
                    statusTxt = 'Draft';
                    notification_dot_color = 'transparent';
                }
                if (status == 1 || status == '1') {
                    statusTxt = 'Assigned';
                    notification_dot_color = '#89BE3A';

                }
                if (status == 2 || status == '2') {
                    statusTxt = 'Working On it';
                    notification_dot_color = '#FFC300';
                }
                if (status == 3 || status == '3') {
                    statusTxt = 'Hold';
                    notification_dot_color = '#FF5733';
                }
                if (status == 4 || status == '4') {
                    statusTxt = 'Stuck';
                    notification_dot_color = '#9B59B6';
                }
                if (status == 5 || status == '5') {
                    statusTxt = 'Done';
                    notification_dot_color = '#2ECC71';
                }
                // console.log(statusTxt);
                var taskRow = `<tr class="align-items-center identify">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap grid-p-searchby"><div title="${statusTxt}" style="position: relative; width: 20px; height: 20px; display:inline;margin-right:15px;">
                                <span style="position: absolute; top: 0; right: 0; width: 10px; height: 10px; background-color: ${notification_dot_color}; border-radius: 50%; display: inline-block;"></span>
                            </div>s${formatDate(task.created_at)}</td>
                                <td class="nowrap grid-p-searchby">${task.task_title}</td>
                                <td class="nowrap grid-p-searchby">${task.building && task.building.building_name ? task.building.building_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${task.appartment && task.appartment.apartment_name ? task.appartment.apartment_name : 'Deleted'}</td>
                                <td class="nowrap grid-p-searchby">${priority}</td>
                                <td class="nowrap grid-p-searchby">
                                ${task.document_status == 1
                        ? ` <a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                        : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                                    </td>
                                

                                    <td class="nowrap grid-p-searchby" data-center>${document_typeTxt}</td>
                                    <td class="nowrap grid-p-searchby" data-center>${task.document_status == 0 ? 'Uploaded' : 'Viewed'}</td>
                               
                                    <td class="nowrap grid-p-searchby">${statusTxt}</td>
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                   
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>

                                   
                                       
                                       
                                    </div>
                                </td>
                            </tr>`;

                $('#done_tasks_table_body').append(taskRow);



            });
        } else {
            $('#done_tasks_table_body').empty();
            var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
            $('#done_tasks_table_body').append(taskRow);
        }
    }
    else {
        $('#done_tasks_table_body').empty();
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        $('#done_tasks_table_body').append(taskRow);
    }
}

$('#advance_search_done_tasks_reset_btn').click(function () {
    $('#search_done_tasks_form')[0].reset();
    getDoneTaskList();
});


$(document).ready(function () {
    getTaskList();
    getDoneTaskList();
    getCancelledTaskList();
});