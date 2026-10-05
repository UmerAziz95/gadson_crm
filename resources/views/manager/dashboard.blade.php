@extends('layouts.master.manager_template.master')

@push('css')
@endpush

@section('content')
	
    <!-- Dashboard Section -->
    <section id="dash">
        <div class="contain-fluid">
            <ul class="crumbs">
                <li>Dashboard</li>
            </ul>
            <h4>Tasks Statistics</h4>
            
            
            <div class="block_row flex_row">
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
                <div class="col">
                    <div class="inner">
                        <strong>{{@$assigned_tasks}}</strong>
                        <p>Assigned Tasks</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$stuck_tasks}}</strong>
                        <p>Stuck Tasks</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$working_on_tasks}}</strong>
                        <p>Wokring On Tasks</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$hold_tasks}}</strong>
                        <p>Hold Tasks</p>
                    </div>
                </div>
                <div class="col">
                    <div class="inner">
                        <strong>{{@$cancelled_tasks}}</strong>
                        <p>Cancelled Tasks</p>
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
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <script src="{{ asset('assets_manager\customjs\dashboard.js') }}"></script>
    
@endpush
