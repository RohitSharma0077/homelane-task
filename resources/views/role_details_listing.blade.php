@extends('layouts.dynamic_pg')
@section('content_dynamic')
<div class="content-wrapper">
     <!-- breadcrumbs -->
     @include('layouts.common_breadcrumbs')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header text-right">
                     <a class="btn btn-success" href="{{ route('edit_role_master_view') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <table id="role_datatable_table" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" value="1" class="select_all_checkbox" id="select-all-checkbox"></th>
                            <th>Role Name</th>
                            <th>Assigned Menu(s)</th>
                            <th>Log Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<script type="text/javascript">
	var oTable;
	$(function() {
		var rows_selected = [];
		oTable = $('#role_datatable_table').DataTable({
			responsive:true,
			"order": [[1, 'desc']], //By default, order by descending 1st column
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	        	url: "{{ route('role_ajax_list') }}",
	        	"dataType":"json",
				
	    	},
	    	"fnDrawCallback": function( oSettings ) {
	    		
		      	$("#select-all-schools").on("click", function(){
					var rows = oTable.rows({ "search": "applied" }).nodes();
					$("input[type='checkbox']", rows).prop("checked", this.checked);
				});
		    },
	        "columns": [
	        	{ "data": "checkbox", orderable: false, searchable: false},
	        	{ "data": "name"},
                { "data": "role_values", orderable: false, searchable: false },
                { "data": "log_details", orderable: false, searchable: false},
	            { "data": "action", orderable: false, searchable: false  }
	        ]
		});
		
		// $(document).on('change','#tracker_type',function() {
		// 	oTable.ajax.reload();
		// });
		// Handle click on checkbox
        $('#role_datatable_table tbody').on('click', 'input[type="checkbox"]', function(e){
          var $row = $(this).closest('tr');
          
          // Get row data
          var data = oTable.row($row).data();
          
          // Get row ID
          var rowId = data[0];
          
          
          // Determine whether row ID is in the list of selected row IDs
          var index = $.inArray(rowId, rows_selected);

          // If checkbox is checked and row ID is not in list of selected row IDs
          if(this.checked && index === -1){
             rows_selected.push(rowId);

          // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
          } else if (!this.checked && index !== -1){
             rows_selected.splice(index, 1);
          }

          if(this.checked){
             $row.addClass('selected');
          } else {
             $row.removeClass('selected');
          }

          // Update state of "Select all" control
          updateDataTableSelectAllCtrl(oTable);

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });

        // Handle click on table cells with checkboxes
        $('#role_datatable_table').on('click', 'tbody td, thead th:first-child', function(e){
          // $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead input[name="select_all"]', oTable.table().container()).on('click', function(e){
          if(this.checked){
             $('#role_datatable_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
          } else {
             $('#role_datatable_table tbody input[type="checkbox"]:checked').trigger('click');
          }

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });
});	


//Delete role
$(document).on('click', '.delete_role', function() {
    var u_id = $(this).attr("data-uid");

        if(confirm("This role may have users assigned to it. Do you really want to delete this role?")){
            $this = $(this);
            $this.prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('delete_role') }}",
                data: {'u_id': u_id },
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                // dataType: 'json',
                success: function (result) {
                    $this.prop("disabled", false);
                    if(result.status == true){
                        oTable.draw();
                        showSweetAlertMessage(result.status,result.message);

                    
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
        }

    
});
//Delete role close

$(document).on('click', '.view_assign_menus', function() {
    var role_nm = $(this).attr("role-name");
    var menu_nm = $(this).attr("menu-name");
    //var menu_url = $(this).attr("menu-url");
    if(role_nm == null || menu_nm == null || role_nm == '' || menu_nm == ''){
        var not_assign = "No menu assigned yet !!!"
        //var text ='<h5><b>Menu Name(s) - '+role_nm+'</b> <br></h5><h7>'+not_assign+'</h7>';
        var text ='<h5><b>Menu Name(s) -   </b><span class="right badge badge-danger">'+role_nm+'</span></h5><br><h7>'+not_assign+'</h7>';
    }
    else{
        var text ='<h5><b>Menu Name(s) -   </b><span class="right badge badge-danger">'+role_nm+'</span></h5><br><h7>'+menu_nm+'</h7>';
        // var text ='<h5><b>Menu Name(s) - '+role_nm+'</b> <br></h5><h7>'+menu_nm+'</h7>';
    }
   
    $( "#menu_dataid" ).append( text );
});

$(document).on('click', '.cls_modal', function() {
    $("#menu_dataid").val('');
    $("#menu_dataid").text('');
    $("#menu_dataid").empty();
    $("#menu_dataid").val('False');
});

//assigned menu details
$(document).on('click', '.view_assign_menus_ajax', function() {
    var u_id = $(this).attr("data-uid");
    var role_nm = $(this).attr("role-name");
            $this = $(this);
            $this.prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('view_assign_menus_ajax') }}",
                data: {'u_id': u_id, 'role_nm': role_nm},
                headers: {
                    // 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                // dataType: 'json',
                success: function (result) {
                    $this.prop("disabled", false);
                    if(result.status == true){
                                $("#menu_dataid").html(result.data);
                                $("#exampleModalCenter").modal('show');
                                $("#menu_dataid").show();
                    
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
//assigned menu details close

</script>


 <!-- Modal -->
 <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Assigned Menu(s)</h5>
            <button type="button" class="close cls_modal" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div id="menu_dataid"> </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary cls_modal" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

@endsection