<!DOCTYPE html>
<html lang="en">

<head>
    <title>Change Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Css Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/toastr.min.css') }}" />
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/customjs/common.js') }}"></script>
</head>

<body data-page="logon">
    @if ($errors->any())
    <script>
    toastr.error("{{ $errors->first() }}", "", {
        timeOut: 3000
    });
    </script>
    @endif

    <main>
        <section id="logon m-auto" style="justify-content:center; display:flex;">


            <div class="table_dv" style="width:50%">
                <div class="table_cell">
                    <div class="contain">
                        <div class="_inner editor_blk">

                            <div id="Inspection" class="tab-pane fade active in">

                                <form method="POST" id="change_default_password_form"
                                    action="{{route('manager.changedefaultpassword')}}">
                                    @csrf
                                    <fieldset>
                                        <div class="blk">
                                            <h5 class="color">Change Your Password First</h5>
                                            <div class="form_row row">

                                                <div class="col-xs-6">
                                                    <div class="form_blk">
                                                        <h6>New Password<sup>*</sup></h6>
                                                        <input type="password" name="password" id="password"
                                                            class="text_box">
                                                        <svg style="position:absolute; right:6%; top:55%"
                                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-eye " id="passwordeye"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5c-2.8 0-5.2-2-6.7-4.5C2.8 5.5 5.2 3.5 8 3.5c2.8 0 5.2 2 6.7 4.5-1.5 2.5-3.9 4.5-6.7 4.5z" />
                                                            <path
                                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm0 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form_blk">
                                                        <h6>Confirm Password<sup>*</sup></h6>
                                                        <input type="password" name="password_confirmation"
                                                            id="password_confirmation" class="text_box">
                                                        <svg style="position:absolute; right:6%; top:55%"
                                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-eye " id="newpasswordeye"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5c-2.8 0-5.2-2-6.7-4.5C2.8 5.5 5.2 3.5 8 3.5c2.8 0 5.2 2 6.7 4.5-1.5 2.5-3.9 4.5-6.7 4.5z" />
                                                            <path
                                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm0 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                                                        </svg>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="btn_blk form_btn text-center">

                                                <button type="submit" class="site_btn long savemanagerbtn"
                                                    id="savemanagerbtn">Update</button>

                                            </div>
                                    </fieldset>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </section>
        <!-- logon -->
        
        <script>
        let passwordField = document.getElementById('password');
        let passwordIcon = document.getElementById('passwordeye');

        passwordIcon.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });
        let newpasswordField = document.getElementById('password_confirmation');
        let newpasswordIcon = document.getElementById('newpasswordeye');

        newpasswordIcon.addEventListener('click', function() {
            if (newpasswordField.type === 'password') {
                newpasswordField.type = 'text';
            } else {
                newpasswordField.type = 'password';
            }
        });
        </script>
    </main>

</body>

</html>