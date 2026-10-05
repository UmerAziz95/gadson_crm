<header>
    <div class="contain-fluid">
        <div id="nav">
            <nav class="ease"></nav>
           
            <!-- <ul id="icon_btn">
                <li id="noti">
                    <a href="javascript:;">
                        <img src="{{asset('assets/images/icon-bell.svg')}}" alt="">
                    </a>
                </li>
            </ul> -->
            <div id="pro_btn" class="drop_down">
                <div class="drop_btn">
                    <div class="ico">
                        <img src="{{Auth::user()->profile_image!=null ? Auth::user()->profile_image : asset('assets/images/users/dfault_user.png')}}" alt="">
                    </div>
                    <div class="name">{{Auth::user()->first_name." ".Auth::user()->last_name}} 
                        <small>
                            @if(Auth::user()->type == 'admin')
                            Admin
                            @endif
                            @if(Auth::user()->type == 'manager')
                            Manager
                            @endif
                        </small></div>
                </div>
                <div class="drop_cnt">
                    <ul class="drop_lst">
                        <li><a href="{{route('manager.profile')}}">Profile</a></li>
                        <li><a href="{{route('manager.logout')}}">Logout</a></li>

                        <!-- <form action="{{route('admin.logout')}}">
                            
                            <li><button type="submit" class="nav-link bg-transparent">Logout</button></li>
                        </form> -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header -->