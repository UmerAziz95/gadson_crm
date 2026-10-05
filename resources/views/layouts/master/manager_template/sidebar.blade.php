<aside>
    <div class="logo_blk">
        <div class="logo">
            <a href="{{route('manager.dashboard')}}" style="background-image: url('{{ asset('assets/images/logo-light.png') }}');"></a>
        </div>
        <button type="button" class="toggle"><span></span></button>
    </div>
    
    <div class="inside">
        <ul>
            <li class="{{$page == 'Dashboard' ? 'active' : ''}}">
                <a href="{{route('manager.dashboard')}}">
                    <img src="{{asset('assets/images/icon-home.svg')}}" alt="">
                    <em>Dashboard</em>
                </a>
            </li>
 
            <li class="{{$page == 'Tasks' ? 'active' : ''}}">
                <a href="{{route('manager.assigned_tasks')}}">
                    <img src="{{asset('assets/images/icon-pricing.svg')}}" alt="">
                    <em>Task Manager</em>
                </a>
            </li>
            <li class="">
                <a href="{{route('manager.logout')}}">
                    <img src="{{asset('assets/images/icon-signout.svg')}}" alt="">
                    <em>Logout</em>
                </a>
            </li>
        </ul>
    </div>
</aside>
<!-- aside -->