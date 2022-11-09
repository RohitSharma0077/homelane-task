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
                    @if(getLoggedInUserRole() != 5 && getLoggedInUserRole() != 4)
                        <a class="btn btn-success" href="{{ route('edit_menu_master_view') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                    @endif
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <table id="menu_datatable_table" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" value="1" class="select_all_checkbox" id="select-all-checkbox"></th>
                            <th>Menu Name</th>
                            <th>Menu URL</th>
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
		oTable = $('#menu_datatable_table').DataTable({
			responsive:true,
			"order": [[1, 'desc']], //By default, order by descending 1st column
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	        	url: "{{ route('menu_ajax_list') }}",
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
	        	{ "data": "menu_name"},
                { "data": "menu_URL"},
                { "data": "log_details", orderable: false, searchable: false},
	            { "data": "action", orderable: false, searchable: false  }
	        ]
		});
		
		// $(document).on('change','#tracker_type',function() {
		// 	oTable.ajax.reload();
		// });
		// Handle click on checkbox
        $('#menu_datatable_table tbody').on('click', 'input[type="checkbox"]', function(e){
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
        $('#menu_datatable_table').on('click', 'tbody td, thead th:first-child', function(e){
          // $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead input[name="select_all"]', oTable.table().container()).on('click', function(e){
          if(this.checked){
             $('#menu_datatable_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
          } else {
             $('#menu_datatable_table tbody input[type="checkbox"]:checked').trigger('click');
          }

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });
});	


//Delete user
$(document).on('click', '.delete_menu', function() {
    var u_id = $(this).attr("data-uid");
        if(confirm("Menu may assigned to some role(s). Do you really want to delete this menu?")){
            $this = $(this);
            $this.prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('delete_menu') }}",
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
                           setTimeout(function(){ 
                                window.location.reload();
                                //$('#pg_load_success_menu').load(location.href + " #pg_load_success_menu"); 
							 },1500);
                      

                    
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
//Delete user close
</script>

@endsection