@extends('layouts.dynamic_pg')
@section('content_dynamic')

<?php

    $req_product_name     = 'required';
    $req_product_desc 		= 'required';
    $req_product_price 		= 'required';
    $req_product_img 		= 'required';

	if (empty($pd_details)){
		$product_name        = old('product_name');
        $product_desc   = old('product_desc');
        $product_price    = old('product_price');

    }
    else{
    
    	$product_name        = !empty($pd_details->product_name)?$pd_details->product_name: '';
        $product_desc    = !empty($pd_details->product_desc)?$pd_details->product_desc: '';
        $product_price     = !empty($pd_details->product_price)?$pd_details->product_price: '';
        $category_id     = !empty($pd_details->category_id)?$pd_details->category_id: '';

        if(!empty($pd_details->product_img)){
	        $product_img = "/uploads/".$pd_details->product_img;
            $req_product_img 		= ' ';
	    }
    }

    if(!empty($get_cat_list)){
        $get_cat_list = $get_cat_list;
    }
    else{
        $get_cat_list = '';
    }
    //dd($get_cat_list);
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
                                                            <label class="control-label">Category *</label>
                                                            <select id="category_id" name="category_id" class="form-control">
                                                                <option value="">Select</option>

                                                                <?php
                                                                 foreach($get_cat_list as $cat){ 
                                                                    if(!empty($category_id)) { ?>
                                                                    <option value="{{ $cat->id }}" {{ ($cat->id == $category_id)?    'selected': ' ' }} >
                                                                        {{ $cat->category_name }}
                                                                    </option>    
                                                                      <?php } 
                                                                      else {  ?>

                                                                      <option value="{{ $cat->id }}" >
                                                                        {{ $cat->category_name }}
                                                                    </option>   
                                                                    <?php } ?>
                                                                 <?php } ?>
                                                            </select>
                                                            @if ($errors->has('category_id'))
                                                                <small class="form-control-feedback">{{ $errors->first('category_id') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Product Name *</label>
                                                            <input type="product_name" id="product_name" name="product_name" class="form-control" placeholder=" product_name" {{$req_product_name}} value = "{{$product_name}}" >
                                                            @if ($errors->has('product_name'))
                                                                <small class="form-control-feedback">{{ $errors->first('product_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Description*</label>
                                                            <input type="text" id="product_desc" name="product_desc" class="form-control" placeholder=" Description" {{ $req_product_desc }} value = "{{ $product_desc }}" >
                                                            @if ($errors->has('product_desc'))
                                                                <small class="form-control-feedback">{{ $errors->first('product_desc') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Price*</label>
                                                            <input type="number" id="product_price" name="product_price" class="form-control" placeholder=" Price" {{ $req_product_price }} value = "{{ $product_price }}" >
                                                            @if ($errors->has('product_price'))
                                                                <small class="form-control-feedback">{{ $errors->first('product_price') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                    <div class="col-md-6">
                                                        <div class="form-group ">
                                                            <label>Image</label>
                                                            <input name="product_img" {{$req_product_img }} id="product_img" type="file" class='form-control'  accept="image/png,image/jpg,image/jpeg" data-max-size="2048">
                                                            @if ($errors->has('product_img'))
                                                                <small class="form-control-feedback">{{ $errors->first('product_img') }}</small>
                                                            @endif  
                                                        </div>
                                                    </div> 
                                                    <?php if(!empty($product_img)) { ?>
                                                        <div class="col-md-6">
                                                            <div class="form-group ">
                                                                <img src="{{ asset($product_img) }}" id="profile_img_display" width='80' height='80'>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
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
                product_name: {
                    validators: {
                        notEmpty :{
                            message: 'Enter Name'
                        }
                    }
                },

                product_desc: {
                    validators: {
                        notEmpty :{
                            message: 'Enter description'
                        }
                    }
                },
                product_price: {
                    validators: {
                        notEmpty :{
                            message: 'Enter price'
                        }
                    }
                },
                product_img: {
                    validators: {
                        notEmpty :{
                            message: 'Please select image file'
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
                url:  "{{ route('save_pd_details') }}",
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
                            showSweetAlertMessage(result.status,result.message, "{{ route('pd_view') }}");
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