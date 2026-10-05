@extends('layouts.master.manager_template.master')

@push('css')

@endpush

@section('content')
<style>
    .to_do_item_row {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.to_do_item_row input {
    flex: 1;
    margin-right: 10px;
}

.to_do_item_row button {
    margin-left: 5px;
}
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

.timeline > li {
    position: relative;
    min-height: 100px;
    margin-bottom: 20px;
}

.timeline > li:before,
.timeline > li:after {
    content: " ";
    display: table;
}

.timeline > li:after {
    clear: both;
}

.timeline > li .timeline-panel {
    width: 46%;
    float: left;
    border: 1px solid #e9ecef;
    border-radius: 2px;
    padding: 15px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    text-align: right;  /* Default text alignment */
}

.timeline > li.timeline-inverted .timeline-panel {
    float: right;
    text-align: left;  /* Right align text for inverted panels */
}

.timeline > li .timeline-badge {
    color: #fff;
    width: 50px;
    height: 50px;
    line-height: 40px;
    font-size: 1.4em;
    text-align: center;
    position: absolute;
    top: 16px;
    left: 50%;
    margin-left: -20px;
    background-color: white;
    z-index: 100;
    border-radius: 50%;
    
}

.timeline > li.timeline-inverted .timeline-badge {
    left: auto;
    right: 50%;
    margin-right: -20px;
}

.timeline > li .timeline-panel:before {
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

.timeline > li.timeline-inverted .timeline-panel:before {
    right: auto;
    left: -15px;
    border-left-width: 0;
    border-right-width: 15px;
}

.timeline > li .timeline-body > p,
.timeline > li .timeline-body > ul {
    margin-bottom: 0;
}

.timeline > li .timeline-body > p + p {
    margin-top: 5px;
}

.timeline > li .timeline-heading h4 {
    margin-top: 0;
    color: inherit;
}

#_active_task_table{
    font-size:x-small;
}
#done_task_table{
    font-size:x-small;
}
#_cancelled_task_table{
    font-size:x-small;
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
            <li><a href="{{url('manager/dashboard')}}">Dashboard</a></li>
            <li>Task Manager</li>
        </ul>
        
        <div class="card_row flex_row hidden">
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
            <div class="col hidden">
                <div class="card_blk">
                    <div class="icon" id="draft_tasks"></div>
                    <strong>Draft</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk" id="add_task_btn">
                    <div class="icon" id="done_tasks"></div>
                    <strong>Done</strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk" id="add_task_btn">
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
        <div class="tab-content">
            <div id="active_tasks" class="tab-pane fade in active">
                <div class="br"></div>
                <div class="d-flex align-items-center advance-search-btn">
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
            <h4>All Assigned Tasks</h4>
            <div class="form_blk">
                <input type="text" name="" id="searchInListing" class="text_box" placeholder="Search here">
                <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
            </div>
        </div>
                <div class="top_head"></div>
                <div class="blk">
                    <div class="tbl_blk">
                        <div id="Inspection" class="tab-pane fade active in">
                            <div class="table-responsive">
                                <table id="_active_task_table">
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
                                    <tbody id="active_tasks_table_body">

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
                        @foreach($appartments as $appartment)
                        <option value="{{$appartment->id}}">{{$appartment->apartment_name}}</option>
                        @endforeach
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
            <div class="form_blk">
                <input type="text" name="" id="searchInListing2" class="text_box" placeholder="Search here">
                <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
            </div>
        </div>
                <div class="top_head"></div>
                <div class="blk">
                    <div class="tbl_blk">
                        <div id="Inspection" class="tab-pane fade active in">
                            <div class="table-responsive">
                                <table id="_cancelled_task_table">
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
                <select name="document_status_filter1" id="document_status_filter1" class="form-control text_box">
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
            
            </form>
            <div class="col-sm-12 my-5">
                <div class="d-flex justify-content-end">
                    <div class="btn_blk mx-2">
                        <button type="button" class="site_btn sm px-2 advance-search-btn" 
                        id ="advance_search_done_tasks_btn">
                            Search
                        </button>
                    </div>
                    <div class="btn_blk mx-2">
                        <button type="button" class="site_btn sm px-2 advance-search-btn"
                        id ="advance_search_done_tasks_reset_btn">
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
                            <div class="table-responsive">
                                <table id="done_task_table">
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


   <!-- modals  -->
    <div class="popup sm" data-popup="status-update-popup" id="status_update_modal">
        <div class="table_dv">
            <div class="table_cell">
                <div class="contain">
                    <div class="_inner editor_blk">
                        <button type="button" class="hidden x_btn close_status_update_modal_default_btn"></button>
                        <h3 class="text-center">Update Task Status</h3>
                        <form id="status_update_form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="task_id" id="task_id">

                            <div class="form-group">
                                <label for="status">Status<sup style="color:red">*</sup></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <!-- <option value="0">Draft</option> -->
                                    <option value="2">Working on it</option>
                                    <option value="3">Hold</option>
                                    <option value="4">Stuck</option>
                                    <option value="5">Done</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="comment">Comment<sup style="color:red">*</sup></label>
                                <textarea name="comment" id="comment" class="form-control" placeholder="Add a comment"
                                    rows="4"></textarea>
                            </div>

                            <div class="form-group" style="display: flex; align-items: center;">
    <p class="form-control" id="attachment_upload_btn" style="display: flex; align-items: center; margin: 0; padding: 0.5rem; cursor: pointer;">
    Add Attachment
    <svg style="margin-left:14px;"height="17" viewBox="0 0 1792 1792" width="17" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1472q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h427q21 56 70.5 92t110.5 36h256q61 0 110.5-36t70.5-92h427q40 0 68 28t28 68zm-325-648q-17 40-59 40h-256v448q0 26-19 45t-45 19h-256q-26 0-45-19t-19-45v-448h-256q-42 0-59-40-17-39 14-69l448-448q18-19 45-19t45 19l448 448q31 30 14 69z"/></svg>
       
    </p>
</div>


                            <input type="file" name="attachment_upload" id="attachment_upload" class="hidden">

                            <div class="text-center">
                                <button type="submit" id="update_task_status_btn"
                                    class="site_btn btn update_task_status_btn"
                                    style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px;">Update</button>
                                <button type="button" class="btn site_btn" id="close_update_status_modal_btn"
                                    style="padding: unset;height:32px;padding-left: 10px; padding-right: 10px; background-color:red;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- update status modal end  -->

    <!-- add to do list modal  -->

    <div class="popup sm" data-popup="add_todo_list_modal" id="add_todo_list_modal">
    <div class="table_dv">
        <div class="table_cell">
            <div class="contain">
                <div class="_inner editor_blk">
                    <button type="button" class="hidden x_btn close_status_update_modal_default_btn"></button>
                    <h3 class="text-center">Add to do list</h3>
                    <form id="add_todo_list_form" method="POST">
                        @csrf
                        <input type="hidden" name="task_id" id="task_id_to_do">
                        <div class="" id="todolistcontainer">
                        
                        </div>
                        <div class="text-center">
                            <button type="submit" id="add_todo_btn" class="site_btn btn update_task_status_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px;">Add</button>
                            <button type="button" class="btn site_btn" id="close_update_status_modal_btn" style="padding: unset; height: 32px; padding-left: 10px; padding-right: 10px; background-color: red;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- viewdetails modal  -->

    <div class="popup lg" data-popup="viewdetailspopup" id="viewdetailspopup">
    <div class="table_dv">
        <div class="table_cell">
            <div class="contain">
                <div class="_inner editor_blk">
                    <button type="button" class=" x_btn close_status_update_modal_default_btn"></button>
            <ul class="tab_list">
            <!-- <li class="active"><a href="#to_do_details" data-toggle="tab">To Do List</a></li> -->
            <li class="active hidden"><a href="#task_status_timeline" data-toggle="tab">Task Status Timeline</a></li>
            </ul>

            <div class="tab-content">
             
                <div id="task_status_timeline" class="tab-pane fade in active">
                <!-- <h6 >Status timeline</h6> -->
                <div id="status_timeline_detailsdiv"class="text-center mt-4 bg-grey"></div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>


</section>


@endsection

@push('script')
<script src="{{ asset('assets_manager/customjs/script_manager_assigned_tasks.js') }}"></script>
@endpush