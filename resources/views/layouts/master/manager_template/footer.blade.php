 <!-- footer -->
 <footer>
    <div class="contain-fluid">
        <div class="top_blk">
            <div class="logo">
                <a href="{{url('admin/dashboard')}}" style="background-image: url('{{asset('assets/images/logo-light.png')}}');"></a>
            </div>
            <ul class="social_links">
                <li><a href="#" target="_blank"><img src="{{asset('assets/images/social-facebook.svg')}}"></a></li>
                <li><a href="#" target="_blank"><img src="{{asset('assets/images/social-twitter.svg')}}"></a></li>
                <li><a href="#" target="_blank"><img src="{{asset('assets/images/social-instagram.svg')}}"></a></li>
                <li><a href="#" target="_blank"><img src="{{asset('assets/images/social-youtube.svg')}}"></a></li>
            </ul>
        </div>
        <div class="copyright relative">
            <div class="inner">
                {{-- <ul class="smLst flex">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul> --}}
                <p>Copyright © {{date('Y')}} <a href="{{url('manager/dashboard')}}">Galaxy CRM</a> All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<!-- footer -->


 
<!-- JS Files -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/js/select.min.js') }}"></script>
<script src="{{ asset('assets/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.rateyo.min.js') }}"></script>
<script src="{{ asset('assets/js/multi-animated-counter.js') }}"></script>
<script src="{{ asset('assets/js/datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script src="{{ asset('assets/customjs/common.js') }}"></script>
<script src="{{ asset('assets/customjs/echarts.min.js') }}"></script>

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@stack('script')