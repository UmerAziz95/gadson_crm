@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')
<style>
  #managers_table, td {
    font-size: x-small;
    vertical-align: middle !important;
}

.table-responsive::-webkit-scrollbar {
    width: 5px;
    background-color: white;
    height: 5px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #e8e8e8;
    /* Color of the scrollbar thumb */
    border-radius: 6px;
    /* Rounded corners of the scrollbar thumb */
}



.table-responsive {
    max-height: 10vh;
    overflow-y: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    position: sticky;
    top: 0;
    background-color: white;
    z-index: 9;
}
</style>

<section id="listing">
    <div class="contain-fluid">
        <ul class="crumbs">
            <li><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li>Managers</li>
        </ul>
        <!-- <div class="card_row flex_row" style="justify-content:end"> -->
        <div class="card_row flex_row" >
            
        {{-- <div class="col">
                <div class="card_blk" id="">
                    <div class="icon" style="height:61px"> <img src="{{asset('assets/images/vector-user.svg')}}" alt="" ></div>
                    <strong>
                        
                    </strong>
                </div>
            </div> --}}
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="total_managers"></div>
                    <strong>Total</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="active_managers"></div>
                    <strong>
                        Active
                    </strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="inactive_managers"></div>
                    <strong>Inactive</strong>
                </div>
            </div>
            
            
            <div class="col">
                <div class="card_blk" id="add_manager_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Add Manager
                    </strong>
                </div>
            </div>
        </div>
        <div class="top_head mt-5" style="float:right; ">

        {{-- <div class="form_blk">
            <input type="text" name="" id="searchInListing" class="text_box" placeholder="Search here">
            <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
        </div> --}}
        </div>
        {{-- <div class="br"></div> --}}
        
        <div class="top_head">
            
           
        </div>
        
        <div class="blk" style="margin-top:50px">
            <div class="">
                <table id="managers_table" class="table table-striped table-bordered table-hover table-responsive display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Created Date</th>
                            <th data-center>Status</th>
                            <th data-center>Action</th>
                        </tr>
                    </thead>
                    <tbody id="manager_table_body">
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</section>


<div class="popup lg" data-popup="edit-data-popup" id="edit-data-popup">
    <div class="table_dv">
        <div class="table_cell">
            <div class="contain">
                <div class="_inner editor_blk">
                    <button type="button" class="x_btn" id="close_update_modal_default_btn"></button>
                    <div id="Inspection" class="tab-pane fade active in">
                       
                        <form  method="POST" id="edit_manager_form">
                            <input type="hidden" name="manager_id_edit" id="manager_id_edit">
                            @csrf
                            <fieldset>
                                <div class="blk">
                                    <h5 class="color">Edit Manager</h5>
                                    <div class="form_row row">
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>First Name<sup>*</sup></h6>
                                                <input type="text" name="first_name_edit" id="first_name_edit" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Middle Name</h6>
                                                <input type="text" name="middle_name_edit" id="middle_name_edit" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Last Name<sup>*</sup></h6>
                                                <input type="text" name="last_name_edit" id="last_name_edit" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Contact Number<sup>*</sup></h6>
                                                <input type="number" name="contact_number_edit" id="contact_number_edit" class="text_box" placeholder="eg: +92300 0000 000"maxlength="15">
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form_blk">
                                                <h6>Email<sup>*</sup></h6>
                                                <p id="email_edit" class="text_box">
                                            </div>
                                        </div>
                                    </div>
                                   
                                <div class="btn_blk form_btn text-center">
                                  
                                    <button type="submit" class="site_btn long savemanagerbtn" id="savemanagerbtn">Update</button>
                                    <button type="button" class="site_btn long"  style="background-color:red !important;"id="closeupdatedmodalbtn">Close</button>
                                </div>
                            </fieldset>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- modal to add manager start  -->
<div class="popup lg" id="add-manager-popup">
    <div class="table_dv">
        <div class="table_cell">
            <div class="contain">
                <div class="_inner editor_blk">
                    <button type="button" class="x_btn" id="close_add_modal_btn"></button>
                    <div id="Inspection" class="tab-pane fade active in">
                       
                        <form  method="POST" id="add_manager_form">
                            @csrf
                            <fieldset>
                                <div class="blk">
                                    <h5 class="color">Add Manager</h5>
                                    <div class="form_row row">
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>First Name<sup>*</sup></h6>
                                                <input type="text" name="first_name" id="first_name" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Middle Name</h6>
                                                <input type="text" name="middle_name" id="middle_name" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Last Name<sup>*</sup></h6>
                                                <input type="text" name="last_name" id="last_name" class="text_box" placeholder="eg: John Wick" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form_blk">
                                                <h6>Contact Number<sup>*</sup></h6>
                                                <input type="number" name="contact_number" id="contact_number" class="text_box" placeholder="eg: 1234567890"maxlength="15">
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form_blk">
                                                <h6>Email<sup>*</sup></h6>
                                                <input type="text" name="email" id="email" class="text_box" placeholder="eg: someone@example.com" maxlength="50">
                                            </div>
                                        </div>
                                    </div>
                                   
                                <div class="btn_blk form_btn text-center">
                                  
                                    <button type="submit" class="site_btn long savemanagerbtn" id="savemanagerbtn">Save</button>
                                    <button type="button" class="site_btn long"  style="background-color:red !important;"id="closeaddmodalbtn">Close</button>
                                </div>
                            </fieldset>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal to add manager end  -->


<!-- delete modal start  -->
<div class="popup sm" data-popup="delete-data-popup" id="delete_modal">
    <div class="table_dv">
        <div class="table_cell">
            <div class="contain">
                <div class="_inner editor_blk">
                    <button type="button" class="hidden x_btn clode_delete_modal_default_btn"></button>
                    <h3 class="text-center">Are You Sure to Delete?</h3>
                    <!-- <p>Are You Sure to Delete?</p> -->
                    <div class="text-center row">
                    <button type="button" class="btn bg-transparent rounded-pill" id="delete_confirmed_btn" data-id=""><img src="{{asset('assets\images\check_1828640.png')}}" style="width:30px"></button>
                    <button type="button" class="btn bg-transparent rounded-pill" id="close_delete_modal_btn" ><img src="{{asset('assets\images\close-button_11450177.png')}}" style="width:30px"></button>
                    
                    <!-- <button type="button" class="btn btn-danger ">Delete</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- delete modal end  -->








@endsection

@push('script')
    
<script src="{{ asset('assets_admin/customjs/script_adminmanagers.js') }}"></script>
    
@endpush
