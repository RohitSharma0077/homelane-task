@extends('layouts.dynamic_pg')
@section('content_dynamic')

<?php

    $req_role_name     = 'required';
    $req_assigned_menus 		= 'required';

	if (empty($role_details)){
		$name        = old('name');
        $role_values   = old('role_values');
    }
    else{
    
    	$name        = !empty($role_details->name)?$role_details->name: '';
        $role_values    = !empty($role_details->role_values)?$role_details->role_values: '';
    }

    if(!empty($get_menu_list)){
        $get_menu_list = $get_menu_list;
    }
    else{
        $get_menu_list = '';
    }
    //dd($get_menu_list);
?>
<div class="content-wrapper">
    <!-- breadcrumbs -->
    @include('layouts.common_breadcrumbs')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title"></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-lg-12 responsive-md-100">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="m-b-0">{{ $heading }}</h4>
                            </div>
                            <div class="card-body m-t-20">
                                <form class="form-horizontal bv-form" role="form" id="add_pd_details_form" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-actions">
                                            <div class="form-body">
                                                <div class="row">
                                                    @if(!empty($row_id))
                                                        <input type="hidden" name="row_id" value="{{ $row_id }}">
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Role Name *</label>
                                                            <input type="name" id="name" name="name" class="form-control" placeholder="Enter name" {{$req_role_name}} value = "{{$name}}" >
                                                            @if ($errors->has('name'))
                                                                <small class="form-control-feedback">{{ $errors->first('name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Menu List *</label>
                                                            <select multiple id="menu_ids" name="menu_ids[]" class="form-control">
                                                                <option value="">Select</option>

                                                                <?php
                                                                 foreach($get_menu_list as $menu_detail){ 
                                                                    if(!empty($role_values)) { ?>
                                                                    <option value="{{ $menu_detail->id }}">
                                                                        {{ $menu_detail->menu_name }}
                                                                    </option>    
                                                                      <?php } 
                                                                      else {  ?>

                                                                      <option value="{{ $menu_detail->id }}" >
                                                                        {{ $menu_detail->menu_name }}
                                                                    </option>   
                                                                    <?php } ?>
                                                                 <?php } ?>
                                                            </select>
                                                            <small class="form-control-feedback">Note: Press ctrl and select multiple menus</small>
                                                            <span></span>
                                                            @if ($errors->has('role_values'))
                                                                <small class="form-control-feedback">{{ $errors->first('role_values') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="row">
                                                   
                                                </div> -->
                                            </div>
                                        </div>   
                                        <div class="form-actions m-t-5">
                                            <div class="pull-right">
                                                <div class="pre-loader" style="display:none;"></div>
                                                <button type="submit" class="btn btn-success" id="submit_button_user"> <i class="fa fa-check"></i> Save</button>
                                                <a class="btn btn-danger" href="{{ $go_back_url }}"> <i class="fa fa-times"></i> Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

<script>
	$(function(){
		$('#add_pd_details_form')
        .bootstrapValidator({
            excluded: ':disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: {
                    validators: {
                        notEmpty :{
                            message: 'Enter Name'
                        }
                    }
                },

                role_values: {
                    validators: {
                        notEmpty :{
                            message: 'Please select menu from list'
                        }
                    }
                },

            }

        })
        .on('success.form.bv', function(e,data) {
			
			// data.bv.disableSubmitButtons(false);
        	// console.log('Saving Details');
            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
            // Use Ajax to submit form data
			var formData = new FormData($("#add_pd_details_form")[0]);
			// console.log(formData);
			$(".pre-loader").show();
            var type = "POST"; //for creating new resource
            $.ajax({
                type: type,
                url:  "{{ route('save_role_details') }}",
                data: formData,
				contentType: false,
				dataType: "json",	
				cache : false,
				processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (result) {
                    // Showing flash modal on success
                    if(result.status){
                            showSweetAlertMessage(result.status,result.message, "{{ route('role_view') }}");
					}else{
						showSweetAlertMessage(result.status,result.message);
						$("#submit_button_user").attr('disabled',false);
					}
					$(".pre-loader").hide();                   
                },
                error: function (data) {
                    // Showing Flash modal if error occurs    
                    var parse_error = JSON.parse(data.responseText);
                    if(parse_error.error !== undefined && parse_error.error == 'Unauthenticated.'){
                        showSweetAlertMessage(false,'Your session has expired. Please login again.');
                    }
                    else if(data.status == 401){
                        showSweetAlertMessage(false,"{{ trans('custom.unauthenticated') }}", window.location.href );   
                    }
					$(".pre-loader").hide();
                }
            });
        })

        $('#add_pd_details_form').data('bootstrapValidator').resetForm();

		// $('#add_pd_details_form').validator('update');
		$("#submit_button_user").attr('disabled',false);

	});
</script>

@endsection