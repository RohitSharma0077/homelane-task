@extends('layouts.dynamic_pg')
@section('content_dynamic')
<style>

    #users_datatable_table_length{
        display:none;
    }
    #users_datatable_table_filter{
        margin-left: 61%;
    }

</style>
<div id= "example2_wrapper" class="content-wrapper">
    <div class="card">
        <div class="card-header">
            <div class='row'>
                <div class='col-6 col-md-6'>
           		 <h4 class="m-b-0">Users List</h4>
        	</div>
			<div class='col-6 col-md-6 text-right'>
					<a class="btn btn-success" href="#"><i class="fa fa-plus-circle"></i> Add New</a>
				</div>
            </div>
        </div>

        	<div class="d-block mt-3">
                <table id="users_datatable_table" class="table table-bordered table-hover dataTable dtr-inline">
                	<thead>
                		<tr>
	                		<th><input type="checkbox" name="select_all" value="1" class="select_all_checkbox" id="select-all-checkbox"></th>
	                		<th>First Name</th>
                            <th>Last Name</th>
	                		<th>User Email</th>
	                		<th>Role</th>
							<th>Action</th>
	                	</tr>
                	</thead>
                	<tbody>
                		
                	</tbody>
                </table>
            </div>
     	</div>

    </div>
</div>
<script type="text/javascript">
	var oTable;
	$(function() {
		var rows_selected = [];
		oTable = $('#users_datatable_table').DataTable({
			responsive:true,
			"order": [[1, 'desc']], //By default, order by descending 1st column
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	        	url: "{{ route('users_ajax_list') }}",
	        	"dataType":"json",
				data: function(d){
					d.tracker_type = $('#tracker_type').val();
				}
	    	},
	    	"fnDrawCallback": function( oSettings ) {
	    		
		      	$("#select-all-schools").on("click", function(){
					var rows = oTable.rows({ "search": "applied" }).nodes();
					$("input[type='checkbox']", rows).prop("checked", this.checked);
				});
		    },
	        "columns": [
	        	{ "data": "checkbox", orderable: false, searchable: false},
	        	{ "data": "first_name"},
                { "data": "last_name"},
				{ "data": "email"},
				{ "data": "user_role"},
	            { "data": "action", orderable: false, searchable: false  }
	        ]
		});
		
		// $(document).on('change','#tracker_type',function() {
		// 	oTable.ajax.reload();
		// });
		// Handle click on checkbox
        $('#users_datatable_table tbody').on('click', 'input[type="checkbox"]', function(e){
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
        $('#users_datatable_table').on('click', 'tbody td, thead th:first-child', function(e){
          // $(this).parent().find('input[type="checkbox"]').trigger('click');
        });

        // Handle click on "Select all" control
        $('thead input[name="select_all"]', oTable.table().container()).on('click', function(e){
          if(this.checked){
             $('#users_datatable_table tbody input[type="checkbox"]:not(:checked)').trigger('click');
          } else {
             $('#users_datatable_table tbody input[type="checkbox"]:checked').trigger('click');
          }

          // Prevent click event from propagating to parent
          e.stopPropagation();
        });
});	


//Delete user
$(document).on('click', '.delete_user', function() {
    var u_id = $(this).attr("data-uid");
    var u_role = $(this).attr("u-role");
    if(u_role == 1){ // super admin cannot be deleted by anyone
        alert('Super Admin cannot be delete. Contact Administrator !!!');
    }
    else{
        if(confirm("Do you really want to delete this user?")){
            $this = $(this);
            $this.prop("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{ route('delete_user') }}",
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
    }
    
});
//Delete user close
</script>
@endsection