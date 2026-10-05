@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')
	
    <!-- Dashboard Section -->
    <section id="dash">
        <div class="contain-fluid">
            <ul class="crumbs">
                <li>Dashboard</li>
            </ul>
            
            <div class="block_row flex_row">
            <div class="col">
                <div class="card_blk" id="add_manager_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Add Manager
                    </strong>
                </div>
            </div>
            <div class="col">
                <div class="card_blk" id="add_building_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Add Building
                    </strong>
                </div>
            </div>
                <div class="col">
                <div class="card_blk" id="add_appartment_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Add Apartment
                    </strong>
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
                <div class="card_blk" id="assign_tasks_btn">
                    <div class="icon"><img src="{{asset('assets/images/icon-plus.svg')}}" alt=""></div>
                    <strong>
                        Reassign Task
                    </strong>
                </div>
            </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$total_managers}}</strong>
                        <p>Total Managers</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$total_buildings}}</strong>
                        <p>Total Buildings</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$total_appartments}}</strong>
                        <p>Total Apartments</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$total_tasks}}</strong>
                        <p>Total Tasks</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$completed_tasks}}</strong>
                        <p>Completed Tasks</p>
                    </div>
                </div>
                
                
            </div>
            
            
            
           
            <div id="taskChart" style="width: 100%; height: 350px; margin-top:70px;"></div>
            
        </div>
    </section>
        
    <script>
        var taskData = {!! json_encode($results) !!};
    </script>
@endsection

@push('script')
    
<script src="{{ asset('assets_admin/customjs/admin_dashboard.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    
@endpush
