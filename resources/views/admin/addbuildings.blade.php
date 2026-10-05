@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')

<section id="listing">
    <div class="contain-fluid">
        <ul class="crumbs">
            <li><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li>Add Building</li>
        </ul>
        <!-- <div class="card_row flex_row" style="justify-content:end"> -->


        <div class="top_head">


        </div>
        <div class="blk">
            <div class="tbl_blk">
                <div id="Inspection" class="tab-pane fade active in">

                    <form method="POST" id="add_building_form">
                        @csrf
                        <fieldset>
                            <div class="blk">
                                <div style="display: flex; align-items: center; margin-bottom: 10px">
                                    <a href="{{url('admin/buildings')}}" style="margin-right: 10px;">
                                        <svg id="Icons" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                            style="width: 30px;">
                                            <defs>
                                                <style>
                                                .cls-1 {
                                                    fill: #0078b9;
                                                }
                                                </style>
                                            </defs>
                                            <path class="cls-1"
                                                d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm6,12H8.414l2.293,2.293a1,1,0,1,1-1.414,1.414l-4-4a1,1,0,0,1,0-1.414l4-4a1,1,0,1,1,1.414,1.414L8.414,11H18a1,1,0,0,1,0,2Z">
                                            </path>
                                        </svg>
                                    </a>
                                    <h5 class="color" style="margin: 0; flex-grow: 1; text-align: center;">Add Building
                                    </h5>
                                </div>

                                <div class="form_row row">
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Name<sup>*</sup></h6>
                                            <input type="text" name="building_name" id="building_name" class="text_box"
                                                placeholder="eg: Empire State" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Number<sup>*</sup></h6>
                                            <input type="text" name="building_number" id="building_number"
                                                class="text_box" placeholder="eg: 101" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Type<sup>*</sup></h6>
                                            <select name="building_type" id="building_type"
                                                class="form-control text_box">
                                                <option value="">Select Building Type</option>
                                                <option value="Residential">Residential</option>
                                                <option value="Commercial">Commercial</option>
                                                <option value="Mixed Use">Mixed Use</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Address<sup>*</sup></h6>
                                            <input type="text" name="building_address" id="building_address"
                                                class="text_box" placeholder="eg: 123 Main St" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Number of Apartments<sup>*</sup></h6>
                                            <input type="number" name="number_of_apartments" id="number_of_apartments"
                                                class="text_box" placeholder="eg: 100" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Number of Floors<sup>*</sup></h6>
                                            <input type="number" name="number_of_floors" id="number_of_floors"
                                                class="text_box" placeholder="eg: 20" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Country<sup>*</sup></h6>
                                            <!-- <input type="text" name="country" id="country" class="text_box" placeholder="eg: USA" maxlength="255"> -->
                                            <select name="country" id="country" class="form-control text_box select2">
                                            <option value="233" selected>United States</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>State<sup>*</sup></h6>
                                            <!-- <input type="text" name="state" id="state" class="text_box" placeholder="eg: New York" maxlength="255"> -->
                                            <select name="state" id="state" class="form-control text_box">
                                            <option value="">Select State</option>
                                                @foreach($states as $state)
                                                <option value="{{$state->id}}">{{$state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>City</h6>
                                            <!-- <input type="text" name="city" id="city" class="text_box" placeholder="eg: New York City" maxlength="255"> -->
                                            <select name="city" id="city" class="form-control text_box">
                                                <option value="">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Total Parkings<sup>*</sup></h6>
                                            <input type="number" name="total_parkings" id="total_parkings"
                                                class="text_box" placeholder="eg: 50" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Owner Name<sup>*</sup></h6>
                                            <input type="text" name="owner_name" id="owner_name" class="text_box"
                                                placeholder="eg: John Doe" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Size (sqft)<sup>*</sup></h6>
                                            <input type="number" name="building_size" id="building_size"
                                                class="text_box" placeholder="eg: 1500.50" maxlength="10">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form_blk">
                                            <h6>Building Description<sup>*</sup></h6>
                                            <textarea name="building_description" id="building_description"
                                                class="text_area form-control" placeholder="Describe the building"
                                                maxlength="255" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <!-- <div class="form_blk">
                                <h6>Building Image</h6>
                                <p class="form-control text_box " style="padding-top:14px" id="building_image_name_container">No File Chosen</p>
                                <input type="file" name="building_image" id="building_image" class="form-control text_box hidden">
                            </div>  -->
                                        <div class="blk">
                                            <h4 class="subheading">Upload Photos</h4>
                                            <div class="form_row row">
                                                <div class="col-xs-12">
                                                    <div class="uploader_blk text_box">

                                                        <h6>Drag & Drop</h6>
                                                        <div class="or">OR</div>
                                                        <div class="btn_blk text-center">
                                                            <input type="file" id="fileInput" name="photos[]" multiple
                                                                style="display:none;">
                                                            <button type="button" class="site_btn sm"
                                                                onclick="document.getElementById('fileInput').click();">Browse
                                                                Files</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 d-none" id="image_preview_div">
                                                    <div class="upload_lst_blk text_box">
                                                        <ul class="img_list flex" id="previewList">
                                                            <!-- Previews will be added here dynamically -->
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-xs-12 hidden">
                                        <div class="form_blk">
                                            <h6>Status<sup>*</sup></h6>
                                            <!-- <input type="text" name="status" id="status" class="text_box" placeholder="eg: Active" maxlength="50"> -->
                                            <!-- available,blocked,cancelled, draft, sale out -->
                                            <select name="status" id="status" class="form-control text_box">
                                                <option value="">Select Status</option>
                                                <option value="Available">Available</option>
                                                <option value="Blocked">Blocked</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Sold Out">Sold Out</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="btn_blk form_btn text-center">
                                    <button type="submit" class="site_btn long savebuildingbtn"
                                        id="savebuildingbtn">Save</button>
                                    <a href="{{url('admin/buildings')}}" class="site_btn long btn-btn-danger"
                                        style="background-color:red">Back</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>

                </div>
            </div>
        </div>

    </div>
</section>


<!-- <script>
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const files = event.target.files;
        const previewList = document.getElementById('previewList');
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const li = document.createElement('li');
                li.innerHTML += `
                    <div class="thumb">
                        <img src="${e.target.result}" alt="">
                        <button type="button" class="x_btn" onclick="removeFile(this)">&times;</button>
                    </div>
                `;
                previewList.appendChild(li);
            };
            reader.readAsDataURL(file);
        });
    });
    function removeFile(btn) {
        const li = btn.parentElement.parentElement;
        li.remove();
    }
</script> -->


<script>
let selectedFiles = [];

document.getElementById('fileInput').addEventListener('change', function(event) {
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

    if(fileInput.files!='' || fileInput.files!=[]){
        $('#image_preview_div').removeClass('d-none');
    }
    else{
        $('#image_preview_div').addClass('d-none');
    }
}
</script>



@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_buildings.js') }}"></script>

@endpush