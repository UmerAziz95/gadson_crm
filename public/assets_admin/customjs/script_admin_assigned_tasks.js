$('#add_task_btn').click(function(){
    window.location = '/admin/tasks/add';
});

$(document).on('click','.delete_btn', function(){
    $('#delete_confirmed_btn').attr('data-id','');
    var del_id = $(this).attr('data-id');
  $('#delete_confirmed_btn').attr('data-id',del_id);
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
// close delete modal 

    $(document).on('click','#close_delete_modal_btn', function(){
    $('#delete_confirmed_btn').attr('data-id','');
    $('.clode_delete_modal_default_btn').click(); 
});

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
        $('#appartment').html('<option value="">Select State</option>'); 
        $.each(appartments, function(index, appartment) {
            $('#appartment').append('<option value="' + appartment.id + '">' + appartment.apartment_name + '</option>');
        });
    }
    
}

$('#task_form').submit(function(e){
    e.preventDefault();

    let form = document.getElementById('task_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/storeTask';
    SendAjaxRequestToServer(type, url, data, '', storeTaskResponse, '', 'savebuildingbtn');


});

function storeTaskResponse(response){
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

function getTaskList(){
    let type = 'GET';
    let url = '/admin/getTasksList';
    SendAjaxRequestToServer(type, url, '', '', getTaskListResponse, '', '');
}
function getTaskListResponse(response){

    var taskTableBody = $('#tasks_table_body');
    var donetaskTableBody = $('#done_tasks_table_body');
    taskTableBody.empty();
    donetaskTableBody.empty();
    var tasks = response.tasks_list.tasks_list;
    var done_tasks_list = response.tasks_list.done_tasks_list;
    var total_tasks = response.tasks_list.total_tasks;
    var assigned_tasks = response.tasks_list.assigned_tasks;
    var hold_tasks = response.tasks_list.hold_tasks;
    var done_tasks = response.tasks_list.done_tasks;
    $('#total_tasks').text(total_tasks);
    $('#assigned_tasks').text(assigned_tasks);
    $('#hold_tasks').text(hold_tasks);
    $('#done_tasks').text(done_tasks);

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
                               
                                <td class="nowrap" >${task.document_status == 1 ? 'Viewed' : 'Uploaded'}</td>
                                <td class="nowrap" data-center>${document_typeTxt}</td>
                                <td class="nowrap" data-center>${task.manager.first_name}</td>
                                <td class="nowrap">${task.building.building_name}</td>
                                <td class="nowrap">${task.appartment.apartment_name}</td>
                                <td class="nowrap">${statusTxt}</td>
                                
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                   
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${task.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                </td>
                            </tr>`;
                            taskTableBody.append(taskRow);
                           


    });
    $.each(done_tasks_list, function (index, task) {
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
                               
                                <td class="nowrap" >${task.document_status == 1 ? 'Viewed' : 'Uploaded'}</td>
                                <td class="nowrap" data-center>${document_typeTxt}</td>
                                <td class="nowrap" data-center>${task.manager.first_name}</td>
                                <td class="nowrap">${task.building.building_name}</td>
                                <td class="nowrap">${task.appartment.apartment_name}</td>
                                <td class="nowrap">${statusTxt}</td>
                                
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                    <button type="button" class="pop_btn viewdetailsbtn" title="View" data-id = "${task.id}" data-popup="viewdetailspopup"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3 3 1.346 3 3zm9-.449s-4.252 8.449-11.985 8.449c-7.18 0-12.015-8.449-12.015-8.449s4.446-7.551 12.015-7.551c7.694 0 11.985 7.551 11.985 7.551zm-7 .449c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5 5-2.243 5-5z"/></svg></button>
                                    <a href="/admin/tasks/${task.id}/edit" class="edit  edit_btn" title="Edit"></a>
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${task.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                </td>
                            </tr>`;
                            donetaskTableBody.append(taskRow);
                           


    });

}


$(document).on('click','#delete_confirmed_btn', function(){
    var del_id = $(this).attr('data-id');
    let url = '/admin/deleteTask';
    let type = 'POST';
    let data = new FormData();
    data.append('del_id', del_id);
    SendAjaxRequestToServer(type, url, data, '', deleteTaskResponse, '', '');
});

function deleteTaskResponse(response){
    if (response.status == 200) {
        
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#uiBlocker').hide();
        
        getTaskList();
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



// to view to do list and status timeline

$(document).on('click', '.viewdetailsbtn', function(){
    var task_id = $(this).attr('data-id');
    let data = new FormData();
    data.append('task_id', task_id);
    let type = 'POST';
    let url = '/admin/gettimelinesdetail';
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
           
            
            timelineHTML += `<li>
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
        
            html += `<li>
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
});