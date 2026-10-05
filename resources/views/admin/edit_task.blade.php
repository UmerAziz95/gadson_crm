@extends('layouts.master.admin_template.master')

@push('css')
@endpush
<style>
    .disabledclass:hover{
        cursor: none !important;
    }
</style>
@section('content')
    <form method="POST" action="" enctype="multipart/form-data" id="edit_task_form">
        <input type="hidden" name="task_id" value="{{ $task->id }}">
        @csrf
        <fieldset>
            <div class="blk">
                <div style="display: flex; align-items: center; margin-bottom: 10px">
                    <a href="{{ url('admin/tasks') }}" style="margin-right: 10px;">
                        <svg id="Icons" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 30px;">
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
                    <h5 class="color" style="margin: 0; flex-grow: 1; text-align: center;">Edit Task
                    </h5>
                </div>
                <div class="form_row row">

                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Task Title<sup>*</sup></h6>
                            <input type="text" name="task_title" id="task_title" class="text_box"
                                placeholder="Task Title" maxlength="255" required value="{{ $task->task_title }}">
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Priority<sup>*</sup></h6>
                            <select name="priority" id="priority" class="form-control text_box" required>
                                <option value="">Select Priority</option>
                                <option value="0" @if ($task->priority == 0) selected @endif>Low</option>
                                <option value="1" @if ($task->priority == 1) selected @endif>Medium</option>
                                <option value="2" @if ($task->priority == 2) selected @endif>Urgent</option>
                            </select>

                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Document Type<sup>*</sup></h6>
                            <select name="document_type" id="document_type" class="form-control text_box" required>
                                <option value="">Select Document Type</option>
                                <option value="0" @if ($task->document_type == 0) selected @endif>Section 8</option>
                                <option value="1" @if ($task->document_type == 1) selected @endif>HPD</option>
                                <option value="2" @if ($task->document_type == 2) selected @endif>Work Order</option>
                                <option value="3" @if ($task->document_type == 3) selected @endif>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Building<sup>*</sup></h6>
                            <select name="building" id="building" class="form-control text_box select2" required>
                                <option value="">Select Building</option>
                                @foreach ($buildings_list as $building)
                                    <option value="{{ $building->id }}" @if ($task->building == $building->id) selected @endif>
                                        {{ $building->building_name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Apartment<sup>*</sup></h6>
                            <select name="appartment" id="appartment" class="form-control text_box select2" required>
                                <option value="">Select Apartment</option>
                                @foreach ($appartments_list as $appartment)
                                    <option value="{{ $appartment->id }}" @if ($task->apartment == $appartment->id) selected @endif>
                                        {{ $appartment->apartment_name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Manager<sup>*</sup></h6>
                            <select name="manager" id="manager" class="form-control text_box select2" required>
                                <option value="">Select Manager</option>
                                @foreach ($managers_list as $manager)
                                    <option value="{{ $manager->id }}" @if ($task->manager == $manager->id) selected @endif>
                                        {{ $manager->first_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form_blk">
                            <h6>Description</h6>
                            <textarea name="description" id="description" class="text_area form-control" placeholder="Describe the task"
                                maxlength="255" rows="5">{{ $task->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <!-- <div class="blk">
                                <h4 class="subheading">Edit File Attachment<sup>*</sup></h4>
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
                        <div class="form_blk">
                            <h6>File Attachment<sup>*</sup> </h6>
                            <div class="form-control text_box" style="padding-top:14px" id="fileuploadnamecontainer">
                                <span id="fileName">
                                    @php
                                        // echo str_replace(url('/public/uploads/tasks_attachments/'.$task->id.'/'), '', $task->document);
                                        echo basename($task->document);
                                    @endphp
                                </span>

                                @if (!empty($task->document))
            <a title="Download" id="downloadButton" href="{{$task->document}}" download="attachment" style="font-style: italic; color:#0078b9; margin-left: 10px;">
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="square" stroke-linejoin="arcs"><path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5 5-5-5M12 12.8V2.5"></path></svg>
            </a>
        @endif





                                
        <svg title="Upload" style="margin-left: 25px" id="fileSelectDiv" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="square" stroke-linejoin="arcs">
            <path d="M3 15v4c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2v-4M17 9l-5-5-5 5M12 2.5v10.3"></path>
        </svg>
        
                                    
    </div>
                            <input type="file" name="attachment" id="fileInput" style="display: none;">
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Status<sup></sup></h6>
                            <select name="status" id="status" class="form-control text_box" required>
                                <option value="">Select Status</option>
                                <option value="1"@if ($task->status == 1) selected @endif>Assign</option>
                                <option value="2"@if ($task->status == 2) selected @endif>Working on it</option>
                                <option value="3"@if ($task->status == 3) selected @endif>Hold</option>
                                <option value="4"@if ($task->status == 4) selected @endif>Stuck</option>
                                <option value="5"@if ($task->status == 5) selected @endif>Done</option>
                                <option value="6"@if ($task->status == 6) selected @endif>Cancel</option>
                                
                              

                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 " id="edit_reason_div">
                        <div class="form_blk">
                            <h6>Edit Reason<sup>*</sup></h6>
                            <textarea name="edit_reason" id="edit_reason"style="height: 89px;" class="text_box form-control"></textarea>

                        </div>
                    </div>
                    {{-- createdBy Name --}}
                    <div class="col-xs-6">
                        <div class="form_blk">
                            <h6>Created By: <sup>{{ $task->createdBy->first_name ?? "N/A" }}&nbsp;{{ $task->createdBy->last_name ?? "N/A" }}</sup></h6>
                        </div>
                    </div>
                    {{-- updatedBy --}}
                    <div class="col-xs-6" style="text-align: right;">
                        <div class="form_blk">
                            <h6>Updated By: <sup>{{ $task->updatedBy->first_name ?? "N/A"}}&nbsp;{{ $task->updatedBy->last_name ?? "N/A" }}</sup></h6>
                        </div>
                    </div>
                </div>
                
                
                <div class="btn_blk form_btn text-center">
                    <button type="submit" class="site_btn long updatetaskbtn" id="updatetaskbtn">Update</button>
                    <a href="{{ url('admin/tasks') }}" class="site_btn long btn-btn-danger"
                        style="background-color:red">Back</a>
                </div>
            </div>
        </fieldset>
    </form>
@endsection

@push('script')
    <script src="{{ asset('assets_admin/customjs/script_admin_edit_tasks.js') }}"></script>
@endpush
