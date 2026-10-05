<aside>
    <div class="logo_blk">
        <div class="logo">
            <a href="{{route('admin.dashboard')}}" style="background-image: url('{{ asset('assets/images/logo-light.png') }}');"></a>
        </div>
        <button type="button" class="toggle"><span></span></button>
    </div>
    <!-- <div class="mini_btn">
        <a href="?"><img src="{{asset('assets/images/symbol-comments.svg')}}" alt="">Live Chat</a>
        <a href="?"><img src="{{asset('assets/images/symbol-envelope.svg')}}" alt="">Email</a>
        <a href="?"><img src="{{asset('assets/images/symbol-headphone.svg')}}" alt="">Phone</a>
    </div> -->
    <div class="inside">
        <ul>
            <li class="{{$page == 'Dashboard' ? 'active' : ''}}">
                <a href="{{route('admin.dashboard')}}">
                    <img src="{{asset('assets/images/icon-home.svg')}}" alt="">
                    <em>Dashboard</em>
                </a>
            </li>
            <!-- <li class="{{$page == 'Subscription' ? 'active' : ''}}">
                <a href="{{route('admin.subscription')}}">
                    <img src="{{asset('assets/images/icon-pricing.svg')}}" alt="">
                    <em>Subscriptions</em>
                </a>
            </li>
            
            <li class="">
                <a href="{{route('admin.logout')}}">
                    <img src="{{asset('assets/images/icon-signout.svg')}}" alt="">
                    <em>Logout</em>
                </a>
            </li> -->

            <li class="{{$page == 'Managers' ? 'active' : ''}}">
                <a href="{{route('admin.managers')}}">
                    <img src="{{asset('assets/images/vector-user.svg')}}" alt="">
                    <em>Managers</em>
                </a>
            </li>

            <li class="{{$page == 'Buildings' ? 'active' : ''}}">
                <a href="{{route('admin.buildings')}}">
                    <img src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2048%2048%22%3E%3Cg%20fill%3D%22%23010101%22%3E%3Cpath%20d%3D%22M35.8%2048H1c-.6%200-1-.4-1-1V23c0-.4.2-.7.6-.9l22.8-10.3c.2-.1.5-.1.7%200l12%204.2c.4.1.7.5.7.9V47c0%20.6-.4%201-1%201zM2%2046h32.8V17.6l-11-3.8L2%2023.6V46z%22%2F%3E%3Cpath%20d%3D%22M23.7%2048c-.6%200-1-.4-1-1V13.5c0-.6.4-1%201-1s1%20.4%201%201V47c0%20.6-.4%201-1%201zM4.5%2028c-.4%200-.8-.3-1-.7-.2-.5.1-1.1.7-1.3l14.2-4.4c.5-.2%201.1.1%201.3.7.2.5-.1%201.1-.7%201.3L4.8%2028h-.3zM4.5%2033.3c-.5%200-.9-.3-1-.8-.1-.5.2-1.1.8-1.2l14.2-3c.5-.1%201.1.2%201.2.8.1.5-.2%201.1-.8%201.2l-14.2%203h-.2zM4.5%2038.5c-.5%200-.9-.4-1-.9-.1-.5.3-1%20.9-1.1L18.6%2035c.5-.1%201%20.3%201.1.9.1.5-.3%201-.9%201.1L4.6%2038.5h-.1zM18.7%2043.8H4.5c-.6%200-1-.4-1-1s.4-1%201-1h14.2c.6%200%201%20.4%201%201s-.4%201-1%201z%22%2F%3E%3Cpath%20d%3D%22M47%2048H31.7c-.6%200-1-.4-1-1s.4-1%201-1H46V6.5L32.4%202.1l-13%205.8v6.7c0%20.6-.4%201-1%201s-1-.4-1-1V7.2c0-.4.2-.8.6-.9L31.9.1c.2-.1.5-.1.7-.1l14.7%204.8c.4.2.7.6.7%201V47c0%20.6-.4%201-1%201z%22%2F%3E%3Cpath%20d%3D%22M41%2013.7c-.6%200-1-.4-1-1V8.8c0-.6.4-1%201-1s1%20.4%201%201v3.9c0%20.6-.4%201-1%201zM41%2020.7c-.6%200-1-.4-1-1v-3.9c0-.6.4-1%201-1s1%20.4%201%201v3.9c0%20.6-.4%201-1%201zM41%2027.8c-.6%200-1-.4-1-1v-4c0-.6.4-1%201-1s1%20.4%201%201v4c0%20.6-.4%201-1%201zM41%2034.8c-.6%200-1-.4-1-1v-3.9c0-.6.4-1%201-1s1%20.4%201%201v3.9c0%20.6-.4%201-1%201zM41%2041.8c-.6%200-1-.4-1-1v-3.9c0-.6.4-1%201-1s1%20.4%201%201v3.9c0%20.6-.4%201-1%201z%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="">
                    <em>Buildings</em>
                </a>
            </li>
            <li class="{{$page == 'Appartments' ? 'active' : ''}}">
                <a href="{{route('admin.appartments')}}">
                    <img src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20data-name%3D%22Layer%201%22%20viewBox%3D%220%200%2064%2064%22%3E%3Cpath%20d%3D%22M47.7%2C12.556H16.3a.75.75%2C0%2C0%2C0-.75.75V49.944h-3.51a.75.75%2C0%2C0%2C0%2C0%2C1.5H51.959a.75.75%2C0%2C0%2C0%2C0-1.5H48.45V13.306A.75.75%2C0%2C0%2C0%2C47.7%2C12.556ZM27.083%2C49.944V40.014H31.25v9.931Zm5.667%2C0V40.014h4.166v9.931Zm5.666%2C0V39.264a.75.75%2C0%2C0%2C0-.75-.75H26.333a.75.75%2C0%2C0%2C0-.75.75V49.944H17.05V14.056h29.9V49.944Z%22%2F%3E%3Cpath%20d%3D%22M25.367%2019.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM34.125%2019.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM42.883%2019.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM25.367%2025.1h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM34.125%2025.1h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM42.883%2025.1h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM25.367%2030.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM34.125%2030.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5zM42.883%2030.6h-4.25a.75.75%200%200%200%200%201.5h4.25a.75.75%200%200%200%200-1.5z%22%2F%3E%3C%2Fsvg%3E" alt="">
                    <em>Apartments</em>
                </a>
            </li>
            <li class="{{$page == 'Tasks' ? 'active' : ''}}">
                <a href="{{route('admin.assigned_tasks')}}">
                    <img src="{{asset('assets/images/icon-pricing.svg')}}" alt="">
                    <em>Task Manager</em>
                </a>
            </li>
            <li class="">
                <a href="{{route('admin.logout')}}">
                    <img src="{{asset('assets/images/icon-signout.svg')}}" alt="">
                    <em>Logout</em>
                </a>
            </li>
        </ul>
    </div>
</aside>
<!-- aside -->