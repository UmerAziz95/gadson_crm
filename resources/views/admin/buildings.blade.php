@extends('layouts.master.admin_template.master')

@push('css')
@endpush

@section('content')
    <style>
        table td {
            vertical-align: middle !important;
        }

        #buildings_table {
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
            max-height: 5vh;
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
                <li><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li>Buildings</li>
            </ul>
            <!-- <div class="card_row flex_row" style="justify-content:end"> -->
            <div class="card_row flex_row">
                <div class="col">
                    <div class="card_blk">
                        <div class="icon" id="total_buildings"></div>
                        <strong>Total</strong>
                    </div>
                </div>

                <div class="col">
                    <div class="card_blk">
                        <div class="icon" id="resedential_type_builings"></div>
                        <strong>
                            Residential
                        </strong>
                    </div>
                </div>
                <div class="col">
                    <div class="card_blk">
                        <div class="icon" id="commercial_type_builings"></div>
                        <strong>Commercial</strong>
                    </div>
                </div>
                <div class="col">
                    <div class="card_blk">
                        <div class="icon" id="mixed_type_buildings"></div>
                        <strong>
                            Mixed
                        </strong>
                    </div>
                </div>

                <div class="col">
                    <div class="card_blk" id="add_building_btn">
                        <div class="icon"><img src="{{ asset('assets/images/icon-plus.svg') }}" alt=""></div>
                        <strong>
                            Add Building
                        </strong>
                    </div>
                </div>
            </div>
            {{-- <div class="top_head mt-5" style="float:right; ">

            <div class="form_blk">
                <input type="text" name="" id="searchInListing" class="text_box" placeholder="Search here">
                <button type="button"><img src="{{asset('assets/images/icon-search.svg')}}" alt=""></button>
            </div>
        </div>
        <div class="br"></div> --}}
            <div class="top_head">


            </div>
            {{-- advanced filter --}}
            <div class="d-flex align-items-center advance-search-btn">
                <img style="height: 20px; width: 20px; display: none;" class="advance-plus-icon"
                    src="{{asset('assets/images/icon-plus.svg')}}" alt="">
                <svg style="height: 20px; width: 20px;" class="advance-minus-icon" xmlns="http://www.w3.org/2000/svg"
                    width="1em" height="1em" viewBox="0 0 32 32">
                    <path fill="currentColor"
                        d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3m0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5m-6 10v2h12v-2z">
                    </path>
                </svg>
                <h5 class="m-0 px-2"><u>
                        Advance Search
                    </u></h5>
            </div>
            <div class="form_row row advance-search mt-5" style="">
                
                {{-- Building Number --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>Building Number</h6>
                    <div class="form_blk">
                        <input type="text" name="building_number_filter" id="building_number_filter"
                            class="form-control text_box" placeholder="Building Number">
                    </div>
                </div>
                {{-- Building Name --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>Building Name</h6>
                    <div class="form_blk">
                        <input type="text" name="building_name_filter" id="building_name_filter"
                            class="form-control text_box" placeholder="Building Name">
                    </div>
                </div>
                {{-- Building Type --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>Building Type</h6>
                    <div class="form_blk">
                        <select name="building_type_filter" id="building_type_filter" class="form-control text_box">
                            <option value="">Choose Building Type</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Mixed Use">Mixed Use</option>
                        </select>
                    </div>
                </div>
                {{-- No. Of Apartments --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>No. Of Apartments</h6>
                    <div class="form_blk">
                        <input type="text" name="no_of_apartments_filter" id="no_of_apartments_filter"
                            class="form-control text_box" placeholder="No. Of Apartments">
                    </div>
                </div>
                {{-- No. Of Floors --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>No. Of Floors</h6>
                    <div class="form_blk">
                        <input type="text" name="no_of_floors_filter" id="no_of_floors_filter"
                            class="form-control text_box" placeholder="No. Of Floors">
                    </div>
                </div>
                {{-- Building Address --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>Building Address</h6>
                    <div class="form_blk">
                        <input type="text" name="building_address_filter" id="building_address_filter"
                            class="form-control text_box" placeholder="Building Address">
                    </div>
                </div>
                {{-- Status --}}
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <h6>Status</h6>
                    <div class="form_blk">
                        <select name="status_filter" id="status_filter" class="form-control text_box">
                            <option value="">Choose Status</option>
                            <option value="0">Available</option>
                            <option value="1">Not-Available</option>
                        </select>
                    </div>
                </div>
                {{-- Action --}}
                <div class="col-sm-12 my-5">
                    <div class="d-flex justify-content-end">
                        <div class="btn_blk mx-2">
                            <button type="button" class="site_btn sm px-2 building-advance-search-btn"
                                id="building-advance-search-btn">
                                Search
                            </button>
                        </div>
                        <div class="btn_blk mx-2">
                            <button type="button" class="site_btn sm px-2 advance-reset-btn"
                                id="advance-reset-btn">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- advanced filter end --}}
            <div class="blk" style="margin-top:50px">
                <div class="">
                    <table id="buildings_table"
                        class="table table-striped table-bordered table-hover table-responsive display"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Building Number</th>
                                <th>Building Name</th>
                                <th>Building Type</th>
                                <th>No. Of Apartments</th>
                                <th>No. Of Floors</th>
                                <th>Building Address</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody id="buildings_table_body">


                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>


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
                                data-id=""><img src="{{ asset('assets\images\check_1828640.png') }}"
                                    style="width:30px"></button>
                            <button type="button" class="btn bg-transparent rounded-pill"
                                id="close_delete_modal_btn"><img
                                    src="{{ asset('assets\images\close-button_11450177.png') }}"
                                    style="width:30px"></button>

                            <!-- <button type="button" class="btn btn-danger ">Delete</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- delete modal end  -->
    <script>
        var url = '{{ url(' / ') }}';
    </script>
@endsection

@push('script')
    <script src="{{ asset('assets_admin/customjs/script_admin_buildings.js') }}"></script>
@endpush
