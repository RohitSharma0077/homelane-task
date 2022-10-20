@extends('layouts.dynamic_pg')
@section('content_dynamic')

<?php

	$req_cat_name     = 'required';
    $req_cat_desc     = 'required';

	if (empty($cat_details)){
        $category_name   = old('category_name');
        $category_desc    = old('category_desc');

    }
    else{
    
        $category_name    = !empty($cat_details->category_name)?$cat_details->category_name: '';
        $category_desc     = !empty($cat_details->category_desc)?$cat_details->category_desc: '';
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
                                <form class="form-horizontal bv-form" role="form" id="add_cat_details_form" enctype="multipart/form-data">
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
                                                            <label class="control-label">Category Name*</label>
                                                            <input type="text" id="category_name" name="category_name" class="form-control" placeholder=" Category Name" {{ $req_cat_name }} value = "{{ $category_name }}" >
                                                            @if ($errors->has('category_name'))
                                                                <small class="form-control-feedback">{{ $errors->first('category_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Category Description *</label>
                                                            <!-- <input type="textarea" id="category_desc" name="category_desc" class="form-control" placeholder=" Category Description" {{ $req_cat_desc }} value = "{{ $category_desc }}" > -->

                                                            <textarea placeholder=" Category Description" {{ $req_cat_desc }} id="category_desc" name="category_desc" rows="4" cols="50" maxlength="200">{{ $category_desc }}</textarea>
                                                            @if ($errors->has('category_desc'))
                                                                <small class="form-control-feedback">{{ $errors->first('category_desc') }}</small>
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
		$('#add_cat_details_form')
        .bootstrapValidator({
            excluded: ':disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {

                category_name: {
                    validators: {
                        notEmpty :{
                            message: 'Enter Name'
                        }
                    }
                },

                category_desc: {
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
			var formData = new FormData($("#add_cat_details_form")[0]);
			// console.log(formData);
			$(".pre-loader").show();
            var type = "POST"; //for creating new resource
            $.ajax({
                type: type,
                url:  "{{ route('save_cat_details') }}",
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
                        showSweetAlertMessage(result.status,result.message, "{{ route('cat_view') }}");
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

        $('#add_cat_details_form').data('bootstrapValidator').resetForm();

		// $('#add_cat_details_form').validator('update');
		$("#submit_button_user").attr('disabled',false);

	});
</script>

@endsection