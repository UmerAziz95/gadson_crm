@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')

<form method="POST" action="" enctype="multipart/form-data" id="task_form">
        @csrf
        <fieldset>
            <div class="blk">
            <div style="display: flex; align-items: center; margin-bottom: 10px">
                                    <a href="{{url('admin/tasks')}}" style="margin-right: 10px;">
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
                                    <h5 class="color" style="margin: 0; flex-grow: 1; text-align: center;">Add Task
                                    </h5>
                                </div>
                <div class="form_row row">
                    
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Task Title<sup>*</sup></h6>
                            <input type="text" name="task_title" id="task_title" class="text_box" placeholder="Task Title" maxlength="255" required>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Priority<sup>*</sup></h6>
                            <select name="priority" id="priority" class="form-control text_box" required>
                                <option value="">Select Priority</option>
                                <option value="0">Low</option>
                                <option value="1">Medium</option>
                                <option value="2">Urgent</option>
                            </select>
                        </div>
                    </div>
                   
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Document Type<sup>*</sup></h6>
                            <select name="document_type" id="document_type" class="form-control text_box" required>
                                <option value="">Select Document Type</option>
                                <option value="0">Section 8</option>
                                <option value="1">HPD</option>
                                <option value="2">Work Order</option>
                                <option value="3">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Building<sup>*</sup></h6>
                            <select name="building" id="building" class="form-control text_box select2" required>
                                <option value="">Select Building</option>
                                @foreach($buildings_list as $building)
                                <option value="{{$building->id}}">{{$building->building_name}}</option>
                                @endforeach
                               
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Apartment<sup>*</sup></h6>
                            <select name="appartment" id="appartment" class="form-control text_box select2" required>
                                <option value="">Select Apartment</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Manager<sup>*</sup></h6>
                            <select name="manager" id="manager" class="form-control text_box select2" required>
                                <option value="">Select Manager</option>
                                @foreach($managers_list as $manager)
                                <option value="{{$manager->id}}">{{$manager->first_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form_blk">
                            <h6>Description</h6>
                            <textarea name="description" id="description" class="text_area form-control" placeholder="Describe the task" maxlength="255" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <!-- <div class="blk">
                            <h4 class="subheading">Add File Attachment<sup>*</sup></h4>
                            <div class="form_row row">
                                <div class="col-xs-12">
                                    <div class="uploader_blk text_box">
                                        
                                        <div class="btn_blk text-center">
                                            <input type="file" id="fileInput" name="attachment" style="display:none;">
                                            <button type="button" class="site_btn sm" onclick="document.getElementById('fileInput').click();">Browse Files</button>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div> -->
        <div class="form-group">
            <h6 class="subheading">Add File Attachment<sup>*</sup></h6>
                <div id="fileSelectDiv" class="file-select-div form-control text_box" style="padding-top:14px" >Select a file <svg style="margin-left:14px;"height="17" viewBox="0 0 1792 1792" width="17" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1472q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h427q21 56 70.5 92t110.5 36h256q61 0 110.5-36t70.5-92h427q40 0 68 28t28 68zm-325-648q-17 40-59 40h-256v448q0 26-19 45t-45 19h-256q-26 0-45-19t-19-45v-448h-256q-42 0-59-40-17-39 14-69l448-448q18-19 45-19t45 19l448 448q31 30 14 69z"/></svg></div>
                    <input type="file" name="attachment" id="fileInput" style="display: none;">
                </div>
        </div>
                    
                    <!-- <div class="col-xs-12">
                        <div class="form_blk">
                            <h6>Status<sup>*</sup></h6>
                            <select name="status" id="status" class="form-control text_box" required>
                                <option value="">Select Status</option>
                                <option value="0">Draft</option>
                                <option value="1">Assigned</option>
                                <option value="2">Working on it</option>
                                <option value="3">Hold</option>
                                <option value="4">Stuck</option>
                                <option value="5">Done</option>
                            </select>
                        </div>
                    </div> -->
                </div>
                <div class="btn_blk form_btn text-center">
                    <button type="submit" class="site_btn long">Assign</button>
                    <a href="{{url('admin/tasks')}}" class="site_btn long btn-btn-danger" style="background-color:red">Back</a>
                </div>
            </div>
        </fieldset>
    </form>

@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_assign_tasks.js') }}"></script>
@endpush