</div>
</div>

                                    <div id="styleSelector">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fixed-button">
                    <a href="https://codedthemes.com/item/guru-able-admin-template/" target="_blank" class="btn btn-md btn-primary">
                      <i class="fa fa-shopping-cart" aria-hidden="true"></i> Upgrade To Pro
                    </a>
                </div>
            </div>
        </div>

<!-- Required Jquery -->
<script type="text/javascript" src="{{URL::asset('assets/js/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/popper.js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="{{URL::asset('assets/js/jquery-slimscroll/jquery.slimscroll.js')}}"></script>
<!-- modernizr js -->
<script type="text/javascript" src="{{URL::asset('assets/js/modernizr/modernizr.js')}}"></script>
<!-- am chart -->
<script src="{{URL::asset('assets/pages/widget/amchart/amcharts.min.js')}}"></script>
<script src="{{URL::asset('assets/pages/widget/amchart/serial.min.js')}}"></script>
<!-- Todo js -->
<script type="text/javascript " src="{{URL::asset('assets/pages/todo/todo.js')}}"></script>
<!-- Custom js -->
<script type="text/javascript" src="{{URL::asset('assets/pages/dashboard/custom-dashboard.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('assets/js/script.js')}}"></script>
<script type="text/javascript " src="{{URL::asset('assets/js/SmoothScroll.js')}}"></script>
<script src="{{URL::asset('assets/js/pcoded.min.js')}}"></script>
<script src="{{URL::asset('assets/js/demo-12.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script>
var $window = $(window);
var nav = $('.fixed-button');
    $window.scroll(function(){
        if ($window.scrollTop() >= 200) {
         nav.addClass('active');
     }
     else {
         nav.removeClass('active');
     }
 });
</script>
</body>

</html>