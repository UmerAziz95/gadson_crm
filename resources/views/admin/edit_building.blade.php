@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')
<section id="listing">
    <div class="contain-fluid">
        <ul class="crumbs">
            <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li>Edit Building</li>
        </ul>
        <div class="top_head"></div>
        <div class="blk">
            <div class="tbl_blk">
                <div id="Inspection" class="tab-pane fade active in">
                    <form method="POST" id="edit_building_form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="building_id" value="{{ $building->id }}">
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
                                    <h5 class="color" style="margin: 0; flex-grow: 1; text-align: center;">Edit Building
                                    </h5>
                                </div>
                                <div class="form_row row">
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Name<sup>*</sup></h6>
                                            <input type="text" value="{{ $building->building_name }}" name="building_name" id="building_name" class="text_box" placeholder="eg: Empire State" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Number<sup>*</sup></h6>
                                            <input type="text" value="{{ $building->building_number }}" name="building_number" id="building_number" class="text_box" placeholder="eg: 101" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Type<sup>*</sup></h6>
                                            <select name="building_type" id="building_type" class="form-control text_box">
                                                <option value="">Select Building Type</option>
                                                <option value="Residential" @if($building->building_type == 'Residential') selected @endif>Residential</option>
                                                <option value="Commercial" @if($building->building_type == 'Commercial') selected @endif>Commercial</option>
                                                <option value="Mixed Use" @if($building->building_type == 'Mixed Use') selected @endif>Mixed Use</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Address<sup>*</sup></h6>
                                            <input type="text" value="{{ $building->building_address }}" name="building_address" id="building_address" class="text_box" placeholder="eg: 123 Main St" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Number of Apartments<sup>*</sup></h6>
                                            <input type="number" value="{{ $building->number_of_apartments }}" name="number_of_apartments" id="number_of_apartments" class="text_box" placeholder="eg: 100" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Number of Floors<sup>*</sup></h6>
                                            <input type="number" value="{{ $building->number_of_floors }}" name="number_of_floors" id="number_of_floors" class="text_box" placeholder="eg: 20" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Country<sup>*</sup></h6>
                                            <select name="country" id="country" class="form-control text_box">
                                                <option value="233" selected>United States</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>State<sup>*</sup></h6>
                                            <select name="state" id="state" class="form-control text_box">
                                                <option value="">Select State</option>
                                                <!-- <option value="Residential" @if($building->state == 'Residential') selected @endif>Residential</option>
                                                <option value="Commercial" @if($building->state == 'Commercial') selected @endif>Commercial</option>
                                                <option value="Mixed Use" @if($building->state == 'Mixed Use') selected @endif>Mixed Use</option> -->
                                                @foreach($states as $state)
                                                <option value="{{$state->id}}" {{$state->id == $building->state ? 'selected' :'' }}>{{$state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>City</h6>
                                            <select name="city" id="city" class="form-control text_box">
                                                <option value="">Select City</option>
                                                <!-- <option value="Residential" @if($building->city == 'Residential') selected @endif>Residential</option>
                                                <option value="Commercial" @if($building->city == 'Commercial') selected @endif>Commercial</option>
                                                <option value="Mixed Use" @if($building->city == 'Mixed Use') selected @endif>Mixed Use</option> -->
                                                @foreach($cities as $city)
                                                <option value="{{$city->id}}" {{$city->id == $building->city ? 'selected' :'' }}>{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Total Parkings<sup>*</sup></h6>
                                            <input type="number" value="{{ $building->total_parkings }}" name="total_parkings" id="total_parkings" class="text_box" placeholder="eg: 50" maxlength="11">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Owner Name<sup>*</sup></h6>
                                            <input type="text" value="{{ $building->owner_name }}" name="owner_name" id="owner_name" class="text_box" placeholder="eg: John Doe" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building Size<sup>*</sup></h6>
                                            <input type="text" value="{{ $building->building_size }}" name="building_size" id="building_size" class="text_box" placeholder="eg: 1500.50" maxlength="10">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form_blk">
                                            <h6>Building Description<sup>*</sup></h6>
                                            <textarea name="building_description" id="building_description" class="text_area form-control" placeholder="Describe the building" maxlength="255" rows="5">{{ $building->building_description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <!-- <div class="form_blk">
                                            <h6>Building Image</h6>
                                            <p class="form-control text_box" style="padding-top:14px" id="building_image_name_container">No File Chosen</p>
                                            <input type="file" name="building_image" id="building_image" class="form-control text_box hidden">
                                        </div> -->
                                        <div class="blk">
                                                <h4 class="subheading">Upload Photos</h4>
                                                <div class="form_row row">
                                                    <div class="col-xs-12">
                                                        <div class="uploader_blk text_box">
                                                        
                                                            <!-- <h6>Drag & Drop</h6>
                                                            <div class="or">OR</div> -->
                                                            <div class="btn_blk text-center">
                                                            <input type="hidden" id="removedImages" name="removed_images">
                                                            <input type="hidden" id="imagesData" name="images_data">
                                                                <input type="file" id="fileInput" name="photos[]"
                                                                    multiple style="display:none;">
                                                                <button type="button" class="site_btn sm"
                                                                    onclick="document.getElementById('fileInput').click();">Browse
                                                                    Files</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12">
                                                        <div class="upload_lst_blk text_box">
                                                            <ul class="img_list flex" id="previewList">
                                                                <!-- Previews will be added here dynamically -->
                                                                @foreach($building->images as $image)
                                                                <li data-id="{{ $image->id }}">
                                                                <div class="thumb">
                                                                <img src="{{$image->image_path}}" alt="">
                                                                <button type="button" class="x_btn" onclick="removeExistingImage(this, '{{ $image->image_path }}')"></button>

                                                            </div>
                                                            </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form_blk">
                                            <h6>Status<sup>*</sup></h6>
                                            <select name="status" id="status" class="form-control text_box">
                                                <option value="">Select Status</option>
                                                <option value="Available" @if($building->status == 'Available') selected @endif>Available</option>
                                                <option value="Blocked" @if($building->status == 'Blocked') selected @endif>Blocked</option>
                                                <option value="Cancelled" @if($building->status == 'Cancelled') selected @endif>Cancelled</option>
                                                <option value="Draft" @if($building->status == 'Draft') selected @endif>Draft</option>
                                                <option value="Sold Out" @if($building->status == 'Sold Out') selected @endif>Sold Out</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn_blk form_btn text-center">
                                    <button type="submit" class="site_btn long savebuildingbtn" id="savebuildingbtn">Update</button>
                                    <a href="{{url('admin/buildings')}}" class="site_btn long btn-btn-danger" style="background-color:red">Back</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_edit_buildings.js') }}"></script>
@endpush
