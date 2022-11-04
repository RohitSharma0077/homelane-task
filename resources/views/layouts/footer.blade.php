<!-- jQuery -->
<!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script> -->
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<!-- <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script> -->
<!-- JQVMap -->
<!-- <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> -->
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script> -->

<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
    function showSweetAlertMessage(status, message, url){
		// console.log(url);
          if(status == true){
              type = 'success';
          }
          else{
              type = 'error';
          }
          
          if(url){
              swal({
                  title: message,
                  text: "",
                  type: type,
                  showCancelButton: false,
                  closeOnConfirm: true,
              },
              function(){
                  window.location.href = url;
              });
          }
          else{
              swal(message, " ", type)   
          }
    }


//check whether menu url exist, if exist then redirect to route, if not exist then redirect to dynamic created page
$(document).on('click', '.url_check', function() {
    var s_url = $(this).attr("s-url");
    var s_name = $(this).attr("s-name");
    $this = $(this);
    $this.prop("disabled", true);
    console.log(s_url);
            $.ajax({
                type: "POST",
                url: "{{ route('check_url_exist_in_routes') }}",
                data: {'s_url': s_url, 's_name': s_name },
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                // dataType: 'json',
                success: function (result) {
                    $this.prop("disabled", false);
                    if(result.status == true){
                        if(result.third_party){
                            window.open(result.url, '_blank');
                        }
                        else{
                            window.open(result.url, '_self');
                        }
                    }
                    else{
                            showSweetAlertMessage(result.status,result.message);
                    }       
                },
                error: function (data) {
                    // console.log('Error:', data);
                    $this.prop("disabled", false);
                    var parse_error = JSON.parse(data.responseText);
                    // console.log('parse_error:'+ parse_error.error);
                    if(typeof parse_error.error != 'undefined' && parse_error.error == 'Unauthenticated.'){
                        showSweetAlertMessage(false,'Your session has expired. Please login again.');
                        $(".close, .modal").click(function(){
                            window.location.reload();
                        });
                    }
                }
            });
    
});
//Delete user close

//
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
   	var $table             = table.table().node();
   	var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   	var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   	var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   	if($chkbox_checked.length === 0){
      	chkbox_select_all.checked = false;
      	if('indeterminate' in chkbox_select_all){
         	chkbox_select_all.indeterminate = false;
      	}

   // If all of the checkboxes are checked
   	} else if ($chkbox_checked.length === $chkbox_all.length){
      	chkbox_select_all.checked = true;
      	if('indeterminate' in chkbox_select_all){
         	chkbox_select_all.indeterminate = false;
      	}

   	// If some of the checkboxes are checked
   	} else {
      	chkbox_select_all.checked = true;
      	if('indeterminate' in chkbox_select_all){
         	chkbox_select_all.indeterminate = true;
      	}
   	}
}

</script>