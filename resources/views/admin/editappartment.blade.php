
@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')
<section id="listing">
    <div class="contain-fluid">
        <ul class="crumbs">
            <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li>Edit Apartment</li>
        </ul>
        <div class="top_head"></div>
        <div class="blk">
            <div class="tbl_blk">
                <div id="Inspection" class="tab-pane fade active in">
                <form method="POST" id="edit_apartment_form" enctype="multipart/form-data">
                    <input type="hidden" name="appartment_id" id="appartment_id" value="{{$appartment->id}}">
                        @csrf
                        <fieldset>
                            <div class="blk">
                            <div style="display: flex; align-items: center; margin-bottom: 10px">
                                    <a href="{{url('admin/appartments')}}" style="margin-right: 10px;">
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
                                    <h5 class="color" style="margin: 0; flex-grow: 1; text-align: center;">Edit Apartment
                                    </h5>
                                </div>
                                <div class="form_row row">
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Building<sup>*</sup></h6>
                                            <select name="building" id="building" class="form-control text_box select2">
                                                <option value="">Select Building</option>
                                                
                                                @foreach($buildings as $building)
                                                <option value="{{$building->id}}" @if($building->id == $appartment->building_id) selected @endif> {{$building->building_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Apartment No<sup>*</sup></h6>
                                            <input type="text" name="apartment_no" id="apartment_no" value="{{$appartment->apartment_no}}" class="text_box" placeholder="eg: A-101" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Apartment Name<sup>*</sup></h6>
                                            <input type="text" name="apartment_name" id="apartment_name" class="text_box" value="{{$appartment->apartment_name}}" placeholder="eg: Ocean View" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Category<sup>*</sup></h6>
                                            <select name="category" id="category" class="form-control text_box">
                                                <option value="">Select Category</option>
                                                <option value="Residential" @if($appartment->category == 'Residential') selected @endif>Residential</option>
                                                <option value="Commercial" @if($appartment->category == 'Commercial') selected @endif>Commercial</option>
                                                <option value="Mixed Use" @if($appartment->category == 'Mixed Use') selected @endif>Mixed Use</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Apartment Type<sup>*</sup></h6>
                                            <select name="apartment_type" id="apartment_type" class="form-control text_box">
                                                <option value="">Select Apartment Type</option>
                                                <option value="Penthouse"@if($appartment->apartment_type == 'Penthouse') selected @endif>Penthouse</option>
                                                <option value="Studio"@if($appartment->apartment_type == 'Studio') selected @endif>Studio</option>
                                                <option value="Appartment"@if($appartment->apartment_type == 'Appartment') selected @endif>Apartment</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6 {{ $appartment->apartment_type != 'Appartment' ? 'd-none' : '' }}" id="number_of_rooms_div">
                                        <div class="form_blk">
                                            <h6>Number of Rooms<sup>*</sup></h6>
                                            <input type="number" name="number_of_rooms" value="{{$appartment->number_of_rooms}}" id="number_of_rooms"placeholder="No. of Rooms" class="form-control text_box">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Apartment Size (sqft)<sup>*</sup></h6>
                                            <input type="number" name="apartment_size" value="{{$appartment->apartment_size}}" id="apartment_size" class="text_box" placeholder="eg: 1200.00" maxlength="10">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Status</h6>
                                            <select name="status" id="status" class="form-control text_box">
                                                <option value="">Select Status</option>
                                                <option value="Available" @if($appartment->status == 'Available') selected @endif>Available</option>
                                                <option value="Blocked" @if($appartment->status == 'Blocked') selected @endif>Blocked</option>
                                                <option value="Leased" @if($appartment->status == 'Leased') selected @endif>Leased</option>
                                                <option value="Rejected" @if($appartment->status == 'Rejected') selected @endif>Rejected</option>
                                                <option value="Rented" @if($appartment->status == 'Rented') selected @endif>Rented</option>
                                                <option value="Under Maintenance" @if($appartment->apartment_type == 'Under Maintenance') selected @endif>Under Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Unit Purchase Price<sup>*</sup></h6>
                                            <input type="number" name="unit_purchase_price" value="{{$appartment->unit_purchase_price}}"  id="unit_purchase_price" class="text_box" placeholder="eg: 500000.00" maxlength="15">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Landlord Name<sup>*</sup></h6>
                                            <input type="text" name="landlord_name" value="{{$appartment->landlord_name}}"  id="landlord_name" class="text_box" placeholder="eg: John Doe" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Landlord Contact Number<sup>*</sup></h6>
                                            <input type="text" name="landlord_contact_number"value="{{$appartment->landlord_contact_number}}"  id="landlord_contact_number" class="text_box" placeholder="eg: +1234567890" maxlength="15">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form_blk">
                                            <h6>Reference Number</h6>
                                            <input type="text" name="reference_number" value="{{$appartment->reference_number}}"  id="reference_number" class="text_box" placeholder="eg: REF12345" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form_blk">
                                            <h6>Apartment Description</h6>
                                            <textarea name="description" id="description" 
                                             class="text_area form-control" placeholder="Describe the apartment" maxlength="255" rows="5">{{$appartment->description}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="blk">
                                            <h4 class="subheading">Upload Photos</h4>
                                            <div class="form_row row">
                                                <div class="col-xs-12">
                                                    <div class="uploader_blk text_box">
                                                        <h6>Drag & Drop</h6>
                                                        <div class="or">OR</div>
                                                        <div class="btn_blk text-center">
                                                            <input type="file" id="fileInput" name="photos[]" multiple style="display:none;">
                                                            <button type="button" class="site_btn sm" onclick="document.getElementById('fileInput').click();">Browse Files</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12">
                                                    <div class="upload_lst_blk text_box">
                                                        <ul class="img_list flex" id="previewList">
                                                            <!-- Previews will be added here dynamically -->
                                                            @foreach($appartment->images as $image)
                                                            <li>
                                                                <div class="thumb">
                                                                    <img src="{{ $image->image_path }}" alt="">
                                                                    <button type="button" class="x_btn" onclick="removeExistingImage(this,'{{ $image->image_path }}')"></button>
                                                                </div>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn_blk form_btn text-center">
                                    <button type="submit" class="site_btn long saveapartmentbtn" id="saveapartmentbtn">Update</button>
                                    <a href="{{url('admin/appartments')}}" class="site_btn long btn-btn-danger" style="background-color:red">Back</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $('#apartment_type').change(function(){
        var type = $(this).val();
        if(type == 'Appartment'){
            $('#number_of_rooms_div').removeClass('d-none');
        }
        else{
            $('#number_of_rooms_div').addClass('d-none');

        }
    });
</script>
@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_edit_appartments.js') }}"></script>
@endpush
