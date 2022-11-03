@extends('layouts.dynamic_pg')
@section('content_dynamic')

<?php

	$req_menu_name     = 'required';
    $req_menu_URL     = 'required';

	if (empty($menu_details)){
        $menu_name   = old('menu_name');
        $menu_URL    = old('menu_URL');

    }
    else{
    
        $menu_name    = !empty($menu_details->menu_name)?$menu_details->menu_name: '';
        $menu_URL     = !empty($menu_details->menu_URL)?$menu_details->menu_URL: '';
    }
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
                                <form class="form-horizontal bv-form" role="form" id="add_menu_details_form" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-actions">
                                            <div class="form-body">
                                                <div class="row">
                                                    @if(!empty($row_id))
                                                        <input type="hidden" name="row_id" value="{{ $row_id }}">
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Menu Name*</label>
                                                            <input type="text" id="menu_name" name="menu_name" class="form-control" placeholder=" Menu Name" {{ $req_menu_name }} value = "{{ $menu_name }}" >
                                                            @if ($errors->has('menu_name'))
                                                                <small class="form-control-feedback">{{ $errors->first('menu_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Menu URL *</label>
                                                            <input type="url" id="menu_URL" name="menu_URL" class="form-control" placeholder=" Menu URL" {{ $req_menu_URL }} value = "{{ $menu_URL }}" >
                                                            @if ($errors->has('menu_URL'))
                                                                <small class="form-control-feedback">{{ $errors->first('menu_URL') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
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
		$('#add_menu_details_form')
        .bootstrapValidator({
            excluded: ':disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {

                menu_name: {
                    validators: {
                        notEmpty :{
                            message: 'Enter Name'
                        }
                    }
                },

                menu_URL: {
                    validators: {
                        notEmpty :{
                            message: 'Enter description'
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
			var formData = new FormData($("#add_menu_details_form")[0]);
			// console.log(formData);
			$(".pre-loader").show();
            var type = "POST"; //for creating new resource
            $.ajax({
                type: type,
                url:  "{{ route('save_menu_details') }}",
                data: formData,
				contentType: false,
				dataType: "json",	
				cache : false,
				processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (result) {
                    if(result.status){
                        showSweetAlertMessage(result.status,result.message, "{{ route('menu_view') }}");
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

        $('#add_menu_details_form').data('bootstrapValidator').resetForm();

		// $('#add_menu_details_form').validator('update');
		$("#submit_button_user").attr('disabled',false);

	});
</script>

@endsection