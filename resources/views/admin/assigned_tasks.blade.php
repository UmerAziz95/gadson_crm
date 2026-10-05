@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')

<style>
.timeline {
    list-style: none;
    padding: 0;
    position: relative;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
    left: 50%;
    margin-left: -1px;
}

.timeline>li {
    position: relative;
    min-height: 100px;
    margin-bottom: 20px;
}

.timeline>li:before,
.timeline>li:after {
    content: " ";
    display: table;
}

.timeline>li:after {
    clear: both;
}

.timeline>li .timeline-panel {
    width: 46%;
    float: left;
    border: 1px solid #e9ecef;
    border-radius: 2px;
    padding: 15px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    text-align: right;
    /* Default text alignment */
}

.timeline>li.timeline-inverted .timeline-panel {
    float: right;
    text-align: left;
    /* Right align text for inverted panels */
}

.timeline>li .timeline-badge {
    color: #fff;
    width: 40px;
    height: 40px;
    line-height: 40px;
    font-size: 1.4em;
    text-align: center;
    position: absolute;
    top: 16px;
    left: 50%;
    margin-left: -20px;
    background-color: #0078b9;
    z-index: 100;
    border-radius: 50%;
    background-image: url('{{asset('assets/images/vector-dashboard.svg')}}')
}

.timeline>li.timeline-inverted .timeline-badge {
    left: auto;
    right: 50%;
    margin-right: -20px;
}

.timeline>li .timeline-panel:before {
    position: absolute;
    top: 26px;
    right: -15px;
    display: inline-block;
    border-top: 15px solid #0078b9;
    border-left: 15px solid #e9ecef;
    border-right: 0 solid #e9ecef;
    border-bottom: 15px solid transparent;
    content: "";
}

.timeline>li.timeline-inverted .timeline-panel:before {
    right: auto;
    left: -15px;
    border-left-width: 0;
    border-right-width: 15px;
}

.timeline>li .timeline-body>p,
.timeline>li .timeline-body>ul {
    margin-bottom: 0;
}

.timeline>li .timeline-body>p+p {
    margin-top: 5px;
}

.timeline>li .timeline-heading h4 {
    margin-top: 0;
    color: inherit;
}

#task_table {
    font-size: x-small;
}

#done_task_table {
    font-size: x-small;
}
#_cancelled_task_table {
    font-size: x-small;
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
    max-height: 70vh;
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
            <li>Task Manager</li>
        </ul>
        @if(session()->has('error'))
        @push('script')
        <script>
        toastr.error("{{ session('error') }}", '', {
            timeOut: 3000
        });
        </script>
        @endpush
        @endif


        



        <div class="card_row flex_row">


            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="total_tasks"></div>
                    <strong>Total</strong>

                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="assigned_tasks"></div>
                    <strong>Assigned</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="hold_tasks"></div>
                    <strong>Hold</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="done_tasks"></div>
                    <strong>Done</strong>
                </div>
            </div>
            
            <div class="col">
                <div class="card_blk" id="add_task_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Add Task
                    </strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="cancelled_tasks"></div>
                    <strong>Cancelled</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="stuck_tasks"></div>
                    <strong>Stuck</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk">
                    <div class="icon" id="working_on_tasks"></div>
                    <strong>Working On</strong>
                </div>
            </div>
        </div>

        <ul class="tab_list" style="margin-top: 20px">
            <li class="active"><a href="#active_tasks" data-toggle="tab">Assigned Tasks</a></li>
            <li><a href="#done_tasks_tab" data-toggle="tab">Completed Tasks</a></li>
            <li id="cancelled_tab_btn" class="d-none"><a href="#cancelled_tasks_tab" data-toggle="tab">Cancelled Tasks</a></li>

        </ul>



        <div class="tab-content" style="margin-top:20px">
            <div id="active_tasks" class="tab-pane fade in active">
                <div class="br"></div>
                <div class="d-flex align-items-center advance-search-btn">
                    <img style="height: 20px; width:20px;" class="advance-plus-icon"
                        src="{{asset('assets/images/icon-plus.svg')}}" alt="">
                    <svg style="height: 20px; width:20px;" class="advance-minus-icon" xmlns="http://www.w3.org/2000/svg"
                        width="1em" height="1em" viewBox="0 0 32 32">
                        <path fill="currentColor"
                            d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3m0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5m-6 10v2h12v-2z" />
                    </svg>
                    <h5 class="m-0 px-2"><u>
                            Advance Search
                        </u></h5>
                </div>
                <div class="form_row row advance-search mt-5">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Building</h6>
                        <div class="form_blk">
                            <form id="search_all_tasks_form">
                                <select name="building_filter" id="building_filter" class="form-control text_box">
                                    <option value="">Choose Building</option>
                                    @foreach($buildings as $building)
                                    <option value="{{$building->id}}">{{$building->building_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Apartment</h6>
                        <div class="form_blk upload_blk">
                            <select name="appartment_filter" id="appartment_filter" class="form-control text_box">
                                <option value="">Choose Apartment</option>
                                {{-- @foreach($appartments as $appartment)
                                <option value="{{$appartment->id}}">{{$appartment->apartment_name}}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Priority</h6>
                        <div class="form_blk">
                            <select name="priority_filter" id="priority_filter" class="form-control text_box">
                                <option value="">Choose Priority</option>
                                <option value="0">Low</option>
                                <option value="1">Medium</option>
                                <option value="2">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Document Status</h6>
                        <div class="form_blk">
                            <select name="document_status_filter" id="document_status_filter"
                                class="form-control text_box">
                                <option value="">Choose Document Status</option>
                                <option value="0">Uploaded</option>
                                <option value="1">Viewed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Document Type</h6>
                        <div class="form_blk">
                            <select name="document_type_filter" id="document_type_filter" class="form-control text_box">
                                <option value="">Select Document Type</option>
                                <option value="0">Section 8</option>
                                <option value="1">HPD</option>
                                <option value="2">Work Order</option>
                                <option value="3">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Task Status</h6>
                        <div class="form_blk">
                            <select name="task_status_filter" id="task_status_filter" class="form-control text_box">
                                <option value="">Choose Task Status</option>
                                <option value="0">Draft</option>
                                <option value="1">Assigned</option>
                                <option value="2">Working on it</option>
                                <option value="3">Hold</option>
                                <option value="4">Stuck</option>
                                <option value="5">Done</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Manager</h6>
                        <div class="form_blk">
                            <select name="manager_filter" id="manager_filter" class="form-control text_box">
                                <option value="">Choose Manager</option>
                                @foreach($managers as $manager)
                                <option value="{{$manager->id}}">{{$manager->first_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </form>

                    <div class="col-sm-12 my-5">
                        <div class="d-flex justify-content-end">
                            <div class="btn_blk mx-2">
                                <button type="button" class="site_btn sm px-2 advance-search-btn"
                                    id="advance_search_active_tasks_btn">
                                    Search
                                </button>
                            </div>
                            <div class="btn_blk mx-2">
                                <button type="button" class="site_btn sm px-2 advance-search-btn"
                                    id="advance_search_active_tasks_reset_btn">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="top_head mt-5">
                    <h4>All Assigned Tasks</h4>
                    <div class="form_blk">
                        <input type="text" name="" id="searchInListing" class="text_box" placeholder="Search here">
                        <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
                    </div>
                </div> --}}

                <div class="top_head"></div>
                <div class="blk">
                    <div class="">
                        <div id="Inspection" class="tab-pane fade active in">
                            <div class="">
                                <table id="task_table" class="table table-striped table-bordered table-hover table-responsive display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th data-center>Task Date</th>
                                            <th>Task Title</th>
                                            <th>Building</th>
                                            <th>Apartment</th>
                                            <th>Assigned To</th>
                                            <th>Priority</th>
                                            <th>Document Type</th>
                                            <th>Document Status</th>
                                            <th>Task Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tasks_table_body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="cancelled_tasks_tab" class="tab-pane fade in ">
                <div class="br"></div>
                <div class="d-flex align-items-center advance-search-btn d-none">
            <img style="height: 20px; width:20px;" class="advance-plus-icon" src="{{asset('assets/images/icon-plus.svg')}}" alt="">
            <svg style="height: 20px; width:20px;" class="advance-minus-icon" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32"><path fill="currentColor" d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3m0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5m-6 10v2h12v-2z"/></svg>
            <h5 class="m-0 px-2"><u>
                    Advance Search
                </u></h5>
        </div>
        <div class="form_row row advance-search mt-5">
            <div class="col-sm-6 col-md-4 col-lg-3">
                <h6>Building</h6>
                <div class="form_blk">
                    <form id="search_all_tasks_form">
                    <select name="building_filter" id="building_filter" class="form-control text_box">
                        <option value="">Choose Building</option>
                        @foreach($buildings as $building)
                        <option value="{{$building->id}}">{{$building->building_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <h6>Apartment</h6>
                <div class="form_blk upload_blk">
                <select name="appartment_filter" id="appartment_filter" class="form-control text_box">
                        <option value="">Choose Apartment</option>
                        {{-- @foreach($appartments as $appartment)
                        <option value="{{$appartment->id}}">{{$appartment->apartment_name}}</option>
                        @endforeach --}}
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <h6>Priority</h6>
                <div class="form_blk">
                <select name="priority_filter" id="priority_filter" class="form-control text_box">
                        <option value="">Choose Priority</option>
                        <option value="0">Low</option>
                        <option value="1">Medium</option>
                        <option value="2">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <h6>Document Status</h6>
                <div class="form_blk">
                <select name="document_status_filter" id="document_status_filter" class="form-control text_box">
                        <option value="">Choose Document Status</option>
                        <option value="0">Uploaded</option>
                        <option value="1">Viewed</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Document Type</h6>
                        <div class="form_blk">
                            <select name="document_type_filter" id="document_type_filter" class="form-control text_box">
                                <option value="">Select Document Type</option>
                                <option value="0">Section 8</option>
                                <option value="1">HPD</option>
                                <option value="2">Work Order</option>
                                <option value="3">Other</option>
                            </select>
                        </div>
                    </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <h6>Task Status</h6>
                <div class="form_blk">
                <select name="task_status_filter" id="task_status_filter" class="form-control text_box">
                        <option value="">Choose Task Status</option>
                        <option value="0">Draft</option>
                        <option value="1">Assigned</option>
                        <option value="2">Working on it</option>
                        <option value="3">Hold</option>
                        <option value="4">Stuck</option>
                        <option value="5">Done</option>
                    </select>
                </div>
            </div>
            </form>
          
            <div class="col-sm-12 my-5">
                <div class="d-flex justify-content-end">
                    <div class="btn_blk mx-2">
                        <button type="button" class="site_btn sm px-2 advance-search-btn"
                        id ="advance_search_active_tasks_btn">
                            Search
                        </button>
                    </div>
                    <div class="btn_blk mx-2">
                        <button type="button" class="site_btn sm px-2 advance-search-btn"
                        id ="advance_search_active_tasks_reset_btn">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="top_head mt-5">
            <h4>All Cancelled Tasks</h4>
            {{-- <div class="form_blk">
                <input type="text" name="" id="searchInListing" class="text_box" placeholder="Search here">
                <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
            </div> --}}
        </div>
                <div class="top_head"></div>
                <div class="blk">
                    <div class="">
                        <div id="Inspection" class="tab-pane fade active in">
                            <div class="table-responsive">
                                <table id="_cancelled_task_table" class="table table-striped table-bordered table-hover table-responsive display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Assigned Date</th>
                                            <th>Task Title</th>
                                            <th>Building</th>
                                            <th>Apartment</th>
                                            <th class="col-2">Priority</th>
                                            <th>Document</th>
                                            <th>Document Type</th>
                                            <th>Document Status</th>
                                            <th>Status</th>
                                            <th data-center>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cancelled_tasks_table_body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="done_tasks_tab" class="tab-pane fade in ">
                <div class="br"></div>
                <div class="d-flex align-items-center advance-search-btn">
                    <img style="height: 20px; width:20px;" class="advance-plus-icon"
                        src="{{asset('assets/images/icon-plus.svg')}}" alt="">
                    <svg style="height: 20px; width:20px;" class="advance-minus-icon" xmlns="http://www.w3.org/2000/svg"
                        width="1em" height="1em" viewBox="0 0 32 32">
                        <path fill="currentColor"
                            d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3m0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5m-6 10v2h12v-2z" />
                    </svg>
                    <h5 class="m-0 px-2"><u>
                            Advance Search
                        </u></h5>
                </div>
                <div class="form_row row advance-search mt-5">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Building</h6>
                        <div class="form_blk">
                            <form id="search_done_tasks_form">
                                <select name="building_filter1" id="building_filter1" class="form-control text_box">
                                    <option value="">Choose Building</option>
                                    @foreach($buildings as $building)
                                    <option value="{{$building->id}}">{{$building->building_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Apartment</h6>
                        <div class="form_blk upload_blk">
                            <select name="appartment_filter1" id="appartment_filter1" class="form-control text_box">
                                <option value="">Choose Appartment</option>
                                {{-- @foreach($appartments as $appartment)
                                <option value="{{$appartment->id}}">{{$appartment->apartment_name}}</option>
                                @endforeach --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Priority</h6>
                        <div class="form_blk">
                            <select name="priority_filter1" id="priority_filter1" class="form-control text_box">
                                <option value="">Choose Priority</option>
                                <option value="0">Low</option>
                                <option value="1">Medium</option>
                                <option value="2">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Document Status</h6>
                        <div class="form_blk">
                            <select name="document_status_filter1" id="document_status_filter1"
                                class="form-control text_box">
                                <option value="">Choose Document Status</option>
                                <option value="0">Uploaded</option>
                                <option value="1">Viewed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Document Type</h6>
                        <div class="form_blk">
                            <select name="document_type_filter1" id="document_type_filter1" class="form-control text_box">
                                <option value="">Select Document Type</option>
                                <option value="0">Section 8</option>
                                <option value="1">HPD</option>
                                <option value="2">Work Order</option>
                                <option value="3">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <h6>Manager</h6>
                        <div class="form_blk">
                            <select name="manager_filter1" id="manager_filter1" class="form-control text_box">
                                <option value="">Choose Manager</option>
                                @foreach($managers as $manager)
                                <option value="{{$manager->id}}">{{$manager->first_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    </form>
                    <div class="col-sm-12 my-5">
                        <div class="d-flex justify-content-end">
                            <div class="btn_blk mx-2">
                                <button type="button" class="site_btn sm px-2 advance-search-btn"
                                    id="advance_search_done_tasks_btn">
                                    Search
                                </button>
                            </div>
                            <div class="btn_blk mx-2">
                                <button type="button" class="site_btn sm px-2 advance-search-btn"
                                    id="advance_search_done_tasks_reset_btn">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="top_head mt-5">
                    <h4>All Completed Tasks</h4>
                    <div class="form_blk">
                        <input type="text" name="" id="searchInListing1" class="text_box" placeholder="Search here">
                        <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
                    </div>
                </div>
                <div class="top_head"></div>
                <div class="blk">
                    <div class="tbl_blk">
                        <div id="Inspection" class="tab-pane fade active in">
                            <div class="">
                                <table id="done_task_table" class="table table-striped table-bordered table-hover table-responsive display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Task Date</th>
                                            <th>Task Title</th>
                                            <th>Building</th>
                                            <th>Apartment</th>
                                            <th>Assigned To</th>
                                            <th>Priority</th>
                                            <th>Document Type</th>
                                            <th>Document Status</th>
                                            <th>Task Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="done_tasks_table_body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


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
                            <button type="button" class="btn bg-transparent rounded-pill" id="delete_confirmed_btn"
                                data-id=""><img src="{{asset('assets\images\check_1828640.png')}}"
                                    style="width:30px"></button>
                            <button type="button" class="btn bg-transparent rounded-pill"
                                id="close_delete_modal_btn"><img
                                    src="{{asset('assets\images\close-button_11450177.png')}}"
                                    style="width:30px"></button>

                            <!-- <button type="button" class="btn btn-danger ">Delete</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- delete modal end  -->
    {{-- detailsPopup --}}
    <div class="popup sm" data-popup="detailsPopup" id="detailsPopup">
        <div class="table_dv">
            <div class="table_cell">
                <div class="contain">
                    <div class="_inner editor_blk">
                        <button type="button" class=" x_btn close_details_modal_default_btn"></button>
                        <h3 class="text-center">Task Details</h3>
                        <div class="mt-4" id="task_details_div">

                            {{-- Task Title --}}
                            <div class="form_blk">
                                <label for="details_task_title">Task Title</label>
                                <input type="text" name="details_task_title" id="details_task_title" class="text_box" readonly>
                            </div>
                            {{-- Priority --}}
                            <div class="form_blk">
                                <label for="details_priority">Priority</label>
                                <input type="text" name="details_priority" id="details_priority" class="text_box" readonly>
                            </div>
                           
                            {{-- Edit Reason --}}
                            <div class="form_blk">
                                <label for="details_edit_reason">Edit Reason</label>
                                <input type="text" name="details_edit_reason" id="details_edit_reason" class="text_box" readonly>
                            </div>
                            {{-- Status --}}
                            <div class="form_blk">
                                <label for="details_status">Status</label>
                                <input type="text" name="details_status" id="details_status" class="text_box" readonly>
                            </div>
                             {{-- File Attachment preview --}}
                             <div class="form_blk">
                                <label for="details_file_attachment_preview">File Attachment</label>
                                <div id="details_file_attachment_preview">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- detailsPopup --}}
    {{-- updateStatusPopup --}}
    <!-- viewdetails modal  -->

    <div class="popup lg" data-popup="viewdetailspopup" id="viewdetailspopup">
        <div class="table_dv">
            <div class="table_cell">
                <div class="contain">
                    <div class="_inner editor_blk">
                        <button type="button" class=" x_btn close_status_update_modal_default_btn"></button>
                        <ul class="tab_list">

                            <li class="active hidden"><a href="#task_status_timeline" data-toggle="tab">Task Status
                                    Timeline</a></li>
                        </ul>

                        <div class="tab-content">

                            <div id="task_status_timeline" class="tab-pane fade in active">
                                <h1 style="text-align: center;">Timeline</h1>
                                <div id="status_timeline_detailsdiv" class="text-center mt-4"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>


@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_assigned_tasks.js') }}"></script>
@endpush