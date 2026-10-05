$(document).on('click','.edit_btn', function(){
    $('#task_id').val('');
    var task_id = $(this).attr('data-id');
    $('#task_id').val(task_id);
});
// close delete modal 

    $(document).on('click','#close_update_status_modal_btn', function(){
    $('#delete_confirmed_btn').attr('data-id','');
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

function getTaskList(){
    let type = 'GET';
    let url = '/manager/getTasksList';
    SendAjaxRequestToServer(type, url, '', '', getTaskListResponse, '', '');
}
function getTaskListResponse(response){

    var activeTaskTableBody = $('#active_tasks_table_body');
    activeTaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var draft_tasks = response.tasks_list.draft_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#draft_tasks').text(draft_tasks);
    $('#done_tasks').text(done_tasks);
    if(tasks.length == 0){
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        activeTaskTableBody.append(taskRow);
    }
    else{
    $.each(tasks, function (index, task) {
        var priority = task.priority;
        if(priority == '0' || priority == 0){
            priority = 'Low';
        }
        if(priority == '1' || priority == 1){
            priority = 'Medium';
        }
        if(priority == '2' || priority == 2){
            priority = 'Urgent';
        }
        var document_typeTxt = '';
        var document_type = task.document_type;
        if(document_type == 0 || document_type == '0'){
            document_typeTxt = 'Section 8';
        }
        if(document_type == 1 || document_type == '1'){
            document_typeTxt = 'HPD';
        }
        if(document_type == 2 || document_type == '2'){
            document_typeTxt = 'Work Order'
        }
        if(document_type == 3 || document_type == '3'){
            document_typeTxt = 'Other';
        }

        var statusTxt = '';
        var status = task.status;
        if(status == 0 || status == '0'){
            statusTxt = 'Draft';
        }
        if(status == 1 || status == '1'){
            statusTxt =   'Assigned';
        }
        if(status == 2 || status == '2'){
            statusTxt = 'Working On';
        }
        if(status == 3 || status == '3'){
            statusTxt = 'Hold';
        }
        if(status == 4 || status == '4'){
            statusTxt = 'Stuck';
        }
        if(status == 5 || status == '5'){
            statusTxt = 'Done';
        }
        var taskRow = `<tr style="align-items-center">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap">${task.task_title}</td>
                                <td>${priority}</td>
                               
                                

                                <td class="nowrap" data-center>${document_typeTxt}</td>
                                <td class="nowrap">${task.building.building_name}</td>
                                <td class="nowrap">${task.appartment.apartment_name}</td>
                                <td class="nowrap">${formatDate(task.created_at)}</td>
                                <td class="nowrap">${statusTxt}</td>
                                <td class="nowrap" data-center>${task.document_status == 0 ? 'Uploaded': 'Viewed'}</td>
                                <td class="nowrap">
                                ${task.document_status == 1 
                                    ? `<a href="${task.document}" id="" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`
                                    : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                            </td>
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                   
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>

                                    <button type="button" class="pop_btn add_to_to_modal_btn" title="Add To do list" data-id = "${task.id}" data-popup="add_todo_list_modal"><img src="${base_url +'/assets/images/icon-plus.svg'}" alt=""></button>

                                    <button type="button" class="pop_btn edit  edit_btn" title="Update Status" data-id = "${task.id}" data-popup="status-update-popup"></button>
                                       
                                       
                                    </div>
                                </td>
                            </tr>`;
                                
                            activeTaskTableBody.append(taskRow);
                           


    });
}

}

function getDoneTaskList(){
    let type = 'GET';
    let url = '/manager/getDoneTasksList';
    SendAjaxRequestToServer(type, url, '', '', getDoneTasksListResponse, '', '');
}
function getDoneTasksListResponse(response){

    var activeTaskTableBody = $('#done_tasks_table_body');
    activeTaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var draft_tasks = response.tasks_list.draft_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#draft_tasks').text(draft_tasks);
    $('#done_tasks').text(done_tasks);
    if(tasks.length == 0){
        var taskRow = ` <tr colspan="10" data-center><td class="nowrap" data-center colspan="10">No Data Available</td></tr>`;
        activeTaskTableBody.append(taskRow);
    }
    else{
    $.each(tasks, function (index, task) {
        var priority = task.priority;
        if(priority == '0' || priority == 0){
            priority = 'Low';
        }
        if(priority == '1' || priority == 1){
            priority = 'Medium';
        }
        if(priority == '2' || priority == 2){
            priority = 'Urgent';
        }
        var document_typeTxt = '';
        var document_type = task.document_type;
        if(document_type == 0 || document_type == '0'){
            document_typeTxt = 'Section 8';
        }
        if(document_type == 1 || document_type == '1'){
            document_typeTxt = 'HPD';
        }
        if(document_type == 2 || document_type == '2'){
            document_typeTxt = 'Work Order'
        }
        if(document_type == 3 || document_type == '3'){
            document_typeTxt = 'Other';
        }

        var statusTxt = '';
        var status = task.status;
        if(status == 0 || status == '0'){
            statusTxt = 'Draft';
        }
        if(status == 1 || status == '1'){
            statusTxt =   'Assigned';
        }
        if(status == 2 || status == '2'){
            statusTxt = 'Working On';
        }
        if(status == 3 || status == '3'){
            statusTxt = 'Hold';
        }
        if(status == 4 || status == '4'){
            statusTxt = 'Stuck';
        }
        if(status == 5 || status == '5'){
            statusTxt = 'Done';
        }
        var taskRow = `<tr style="align-items-center">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap">${task.task_title}</td>
                                <td>${priority}</td>
                               
                                <td class="nowrap">
                                ${task.document_status == 1 
                                    ? '<button class="btn"  style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" disabled>Downloaded</button>' 
                                    : `<a href="${task.document}" id="downloadDocumentBtn" class="site_btn" data-id="${task.id}" style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;" download>Download</a>`}
                            </td>

                                <td class="nowrap" data-center>${document_typeTxt}</td>
                                <td class="nowrap">${task.building.building_name}</td>
                                <td class="nowrap">${task.appartment.apartment_name}</td>
                                <td class="nowrap">${formatDate(task.created_at)}</td>
                                <td class="nowrap">${statusTxt}</td>
                                <td class="nowrap">
                                <div class="act_btn">
                                <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                </div>
                                </td>
                            </tr>`;
                                
                            activeTaskTableBody.append(taskRow);
                           


    });
}

}







$(document).on('click', '#downloadDocumentBtn', function(){
    var task_id = $(this).attr('data-id');
    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/manager/changeDocumentStatus';
    SendAjaxRequestToServer(type, url, data, '', changeDocumentStatusResponse, '', 'downloadDocumentBtn');
});

function changeDocumentStatusResponse(response){
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

        getTaskList();
        

    }
    else{
        getTaskList();
    }
}


$('#status_update_form').submit(function(e){
    e.preventDefault();
    let form = document.getElementById('status_update_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/manager/changeTaskStatus';
    SendAjaxRequestToServer(type, url, data, '', changeTaskStatusResponse, '', 'update_task_status_btn');
});

function changeTaskStatusResponse(response){
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#status_update_form')[0].reset();
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


// task to do list start
function addTodoItem(btn) {
    var container = document.getElementById('to_do_list_container');
    var newItem = document.createElement('div');
    newItem.classList.add('to_do_item_row');
    newItem.innerHTML = `
        <input type="text" name="to_do_item[]" class="form-control text_box">
        <button type="button"  class="remove_to_do_item_btn  site_btn btn " style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px; background-color:red;" onclick="removeTodoItem(this)">-</button>
    `;
    container.appendChild(newItem);
    updateButtons();
}

function removeTodoItem(btn) {
    var container = document.getElementById('to_do_list_container');
    container.removeChild(btn.parentElement);
    updateButtons();
}

function updateButtons() {
    var container = document.getElementById('to_do_list_container');
    var rows = container.getElementsByClassName('to_do_item_row');
    for (var i = 0; i < rows.length; i++) {
        var addBtn = rows[i].getElementsByClassName('add_to_do_item_btn')[0];
        var removeBtn = rows[i].getElementsByClassName('remove_to_do_item_btn')[0];
        if (i === 0) {
            removeBtn.style.display = 'none';
        } else {
            removeBtn.style.display = 'inline-block';
        }
    }
}

// task to do list end

$(document).on('click','.add_to_to_modal_btn',function(){
    $('#task_id_to_do').val('');
    var task_id = $(this).attr('data-id');
    $('#task_id_to_do').val(task_id);
});

$('#add_todo_list_form').submit(function(e){
    e.preventDefault();

    let form = document.getElementById('add_todo_list_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/manager/addTaskTodoList';
    SendAjaxRequestToServer(type, url, data, '', addTaskTodoListResponse, '', 'add_todo_btn');
});

function addTaskTodoListResponse(response){
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

$(document).on('click', '.viewdetailsbtn', function(){
    var task_id = $(this).attr('data-id');
    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/manager/gettimelinesdetail';
    SendAjaxRequestToServer(type, url, data, '', gettimelinesdetailResponse, '', '');
});

function gettimelinesdetailResponse(response){
    var to_do_details = response.data.to_do_details;
    var status_timeline_details = response.data.status_timeline_details;
    $('#to_do_detailsdiv').empty();
    $('#status_timeline_detailsdiv').empty();

    if(to_do_details.length < 1){
        $('#to_do_detailsdiv').text('No Data Available');
    }
    else{
        var timelineHTML = '<ul class="timeline">';
        $.each(to_do_details, function(index, detail) {
            var date = formatDate(detail.created_at);
           
            var invertedClass = index % 2 !== 0 ? 'timeline-inverted' : '';
            timelineHTML += `
            <li>
            <div class="cd-timeline-block">
            <div class="cd-timeline-img cd-picture">
                <img src="${base_url+'/assets/images/vector-dashboard.svg'}" alt="Picture">
            </div> 

            <div class="cd-timeline-content">
                <p>${detail.to_do_item}</p>
                <span class="cd-date">${date}</span>
            </div> 
        </div></li>`;
        });
        timelineHTML += '</ul>';
        $('#to_do_detailsdiv').html(timelineHTML);
    
    }
    if(status_timeline_details.length < 1){
        $('#status_timeline_detailsdiv').text('No Data Available');
    }
    else{
        
        
        var status_timeline_details_div = document.getElementById('status_timeline_detailsdiv');
        var html = '';
        
        $.each(status_timeline_details, function(index, item ){
            var isEven = index % 2 === 0;
            var alignmentClass = isEven ? '' : 'timeline-inverted';
            var statusText = getStatusText(item.task_status); 
        
            html += `
            <li>
            <div class="cd-timeline-block">
            <div class="cd-timeline-img cd-picture">
                <img src="${base_url+'/assets/images/vector-dashboard.svg'}" alt="Picture">
            </div> 

            <div class="cd-timeline-content">
                <p><strong>Comment:</strong> ${item.comment}</p>
                <p><strong>Status:</strong> ${statusText}</p>
                <span class="cd-date">${formatDate(item.created_at)}</span>
            </div> 
        </div></li>
            `;
        });
        
        status_timeline_details_div.innerHTML = `<ul class="timeline">${html}</ul>`;
        
        function getStatusText(status) {
            switch(status) {
                case 0: return 'Draft';
                case 1: return 'Assigned';
                case 2: return 'Working on it';
                case 3: return 'On hold';
                case 4: return 'Stuck';
                case 5: return 'Done';
                default: return 'Unknown status';
            }
        }
        

    }
}



$(document).ready(function(){
    getTaskList(); 
    getDoneTaskList(); 
});