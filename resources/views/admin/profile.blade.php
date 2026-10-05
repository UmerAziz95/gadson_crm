<?php use Carbon\Carbon; ?>

@extends('layouts.master.admin_template.master')

@push('css')

@endpush

@section('content')


<style>
.profile {
    padding: 2rem;
}

.edit-profile .card {
    border-radius: 5px;
    padding: 15px;
}

.card-user .card-image img {
    height: 110px !important;
}

.card-user .card-body {
    min-height: 210px;
    padding: 15px 15px 10px;
}

.card-user .author {
    text-align: center;
    text-transform: none;
    font-size: 12px;
    margin-top: -70px;
    font-weight: 600;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.card-user .avatar {
    width: 124px;
    height: 124px;
    border: 5px solid #fff;
    border-color: #eee;
    position: relative;
    margin-bottom: 15px;
    overflow: hidden;
    border-radius: 50%;
    margin-right: 5px;
}

.card-user .title {
    line-height: 24px;
}

.btn-simple.btn-icon {
    padding: 8px;
}

.button-container img {
    width: 18px;
}

.edit-profile-icon {
    position: absolute;
    height: 20px;
    transform: translate(-50%, -50%);
    left: 50%;
    top: 33%;
}

.card {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, .125);
    border-radius: .25rem;
}

.profile-container {
    position: relative;
    display: inline-block;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 95%;
    height: 88%;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-container:hover .overlay {
    opacity: 1;
}

.overlay svg {
    width: 40px;
    height: 40px;
    fill: white;
}
</style>
<section>

    <div class="profile">
        <div class="row">
            <div class="col-md-8">
                <div class="edit-profile">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Profile</h4>
                        </div>
                        <div class="card-body">
                            <form id="profileform">
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <p class="form-control" id="email" title="Email can not be changed"></p>

                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>First Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="First Name" value=""
                                                id="first_name" name="first_name">
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" placeholder="Middle Name" value=""
                                                id="middle_name" name="middle_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>Last Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Last Name" value=""
                                                id="last_name" name="last_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-1">
                                        <div class="form-group">
                                            <label>Contact Number<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" placeholder="Contact Number"
                                                value="" id="contact_number" name="contact_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group" style="display: flex;           align-items: center;">
                                            <input type="checkbox" id="passwordchange_check" name="passwordchange_check"
                                                style="margin-right: 10px;">
                                            <label for="passwordchange_check">Want to change password?</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 passworddiv">
                                        <div class="form-group">
                                            <label>Old Password<span class="text-danger">*</span></label>
                                            <input style="position:relative" type="password" class="form-control"
                                                placeholder="Old Password" value="" id="old_password"
                                                name="old_password" autofill="off">
                                            <svg style="position:absolute; right:10%; top:50%"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye oldpasswordeye"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5c-2.8 0-5.2-2-6.7-4.5C2.8 5.5 5.2 3.5 8 3.5c2.8 0 5.2 2 6.7 4.5-1.5 2.5-3.9 4.5-6.7 4.5z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm0 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                                <div class="row passworddiv">
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <label>New Password<span class="text-danger">*</span></label>
                                            <input type="password" style="position:relative" class="form-control"
                                                placeholder="New Password" value="" id="password" name="password">
                                            <svg style="position:absolute; right:10%; top:50%"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye newpasswordeye"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5c-2.8 0-5.2-2-6.7-4.5C2.8 5.5 5.2 3.5 8 3.5c2.8 0 5.2 2 6.7 4.5-1.5 2.5-3.9 4.5-6.7 4.5z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm0 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                            </svg>

                                        </div>
                                    </div>
                                    <div class="col-md-6 px-1">
                                        <div class="form-group">
                                            <label>Confirm New Password<span class="text-danger">*</span></label>
                                            <input type="password" style="position:relative" class="form-control"
                                                placeholder="Confirm New Password" value="" id="password_confirmation"
                                                name="password_confirmation">
                                            <svg style="position:absolute; right:10%; top:50%"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-eye confirmnewpasswordeye"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5c-2.8 0-5.2-2-6.7-4.5C2.8 5.5 5.2 3.5 8 3.5c2.8 0 5.2 2 6.7 4.5-1.5 2.5-3.9 4.5-6.7 4.5z" />
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm0 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                                <input type="file" class="hidden" name="profile_image" id="profile_image">
                                <div class="text-center " style="margin-top:12px">
                                    <button type="submit" class="site_btn  btn  btn-fill text-center"
                                        id="update_btn">Update</button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-user">
                    <div class="card-image">
                        <img src="https://ununsplash.imgix.net/photo-1431578500526-4d9613015464?fit=crop&amp;fm=jpg&amp;h=300&amp;q=75&amp;w=400"
                            alt="...">
                    </div>
                    <div class="card-body">
                        <div class="author" id="profile_image_div">
                            <a href="#" id="profile_image_input_select" class="profile-container">
                                <img class="avatar border-gray"
                                    src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541"
                                    alt="...">
                                <div class="overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-camera"
                                        viewBox="0 0 16 16">
                                        <path d="M10.5 9.5A1.5 1.5 0 1 1 9 8a1.5 1.5 0 0 1 1.5 1.5z" />
                                        <path
                                            d="M4.75 2a.75.75 0 0 1 .7-.5h5.1a.75.75 0 0 1 .7.5l.5 1h2.5A1.5 1.5 0 0 1 16 4.5v8A1.5 1.5 0 0 1 14.5 14h-13A1.5 1.5 0 0 1 0 12.5v-8A1.5 1.5 0 0 1 1.5 3.5h2.5l.5-1zM3 4a1 1 0 1 0 0 2A1 1 0 0 0 3 4zm8 5.5a3.5 3.5 0 1 0-7 0A3.5 3.5 0 0 0 11 9.5z" />
                                    </svg>
                                </div>
                            </a>
                            <h5 class="title" id="user_name_container"></h5>
                        </div>
                        <p class="description text-center" id="userdetailscontainer">

                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>



</section>



@endsection

@push('script')
<script src="{{ asset('assets_admin/customjs/script_admin_profile.js') }}"></script>
@endpush