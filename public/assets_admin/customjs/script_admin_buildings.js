$('#add_building_btn').click(function(){
    window.location = '/admin/addBuilding';
});
$('#building_image_name_container').click(function(){
    $('#building_image').click();
});

$('#building_image').change(function() {

    var fileName = this.files[0] ? this.files[0].name : 'No jhj Chosen';
    document.getElementById('building_image_name_container').innerText = fileName;
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



$('#add_building_form').submit(function(e){
    e.preventDefault();

    let form = document.getElementById('add_building_form');
    let data = new FormData(form);
    let type = 'POST';
    let url = '/admin/storeBuilding';
    SendAjaxRequestToServer(type, url, data, '', storeBuildingResponse, '', 'savebuildingbtn');


});

function storeBuildingResponse(response){
    if (response.status == 200) {
        toastr.success(response.message, '', {
            timeOut: 3000
        });

    setTimeout(function(){
        window.location = '/admin/buildings'
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

function getBuildingsList(){
    let url = '/admin/getbuildings';
    let type = 'GET';
    SendAjaxRequestToServer(type, url, '', '', getBuildingsListResponse, '', '');
}


function getBuildingsListResponse(response){
    var buildingTableBody = $('#buildings_table_body');
    buildingTableBody.empty();
    var buildings = response.buildings_list.buildings_list;
    var residential_buildings = response.buildings_list.residential_list;
    var commercial_buildings = response.buildings_list.commercial_list;
    var mixed_use_buildings = response.buildings_list.mixed_list;
    var total_buildings = response.buildings_list.total_buildings_list;
    $('#resedential_type_builings').text(residential_buildings);
    $('#commercial_type_builings').text(commercial_buildings);
    $('#mixed_type_buildings').text(mixed_use_buildings);
    $('#total_buildings').text(total_buildings);
    if(buildings.length > 0){
    $.each(buildings, function (index, building) {
        if(building.images.length == 0){
            var buildingImage_src = base_url +'/assets/images/building-icon.png'; 
         }
         else{
             var buildingImage_src = building.images[0].image_path; 
         }

        var buildingRow = `<tr style="align-items-center">
                                <td class="nowrap">${index + 1}</td>
                                <td class="nowrap"><img style="width:50px; height:50px; border-radius:500px;" src="${buildingImage_src}"></td>
                                <td>${building.building_name}</td>
                               
                                <td class="nowrap" >${building.building_type}</td>
                                <td class="nowrap" data-center>${building.number_of_apartments}</td>
                                <td class="nowrap" data-center>${building.number_of_floors}</td>
                                <td class="nowrap">${building.building_address}</td>
                                
                               
                                <td class="nowrap" data-center>
                                    <div class="act_btn">
                                    <a href="/admin/buildings/${building.id}/edit" class="edit  edit_btn" title="Edit"></a>
                                        <button type="button" class="del pop_btn delete_btn" title="Delete" data-id = "${building.id}" data-popup="delete-data-popup"></button>
                                    </div>
                                </td>
                            </tr>`;
                            buildingTableBody.append(buildingRow);
                           


    });}
    else{
        buildingRow= `<tr class="col-12">
                        <td data-center colspan="7">No Data Available</td>
                        </tr>
                        `;
        buildingTableBody.append(buildingRow);
    }
}
// click on delete button and assign its data id to delete confirm button

    $(document).on('click','.delete_btn', function(){
    $('#delete_confirmed_btn').attr('data-id','');
    var del_id = $(this).attr('data-id');
  $('#delete_confirmed_btn').attr('data-id',del_id);
});
// close delete modal 

    $(document).on('click','#close_delete_modal_btn', function(){
    $('#delete_confirmed_btn').attr('data-id','');
    $('.clode_delete_modal_default_btn').click(); 
});

    $(document).on('click','#delete_confirmed_btn', function(){
    var del_id = $(this).attr('data-id');
    let url = '/admin/deletebuilding';
    let type = 'POST';
    let data = new FormData();
    data.append('del_id', del_id);
    SendAjaxRequestToServer(type, url, data, '', deletebuildingResponse, '', '');
});

function deletebuildingResponse(response){
    if (response.status == 200) {
        
        toastr.success(response.message, '', {
            timeOut: 3000
        });
        $('#uiBlocker').hide();
        
        getBuildingsList();
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




$(document).ready(function(){
    getBuildingsList();
});