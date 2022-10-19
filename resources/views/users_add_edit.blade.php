@extends('layouts.dynamic_pg')
@section('content_dynamic')

<?php

	$req_user_role     = 'required';
    $req_email     = 'required';
    $req_first_name 		= 'required';
    $req_last_name 		= 'required';

    if(empty($row_id)){
    	$req_password  = 'required';
    	$req_c_password         = 'required';
    }
    else{
    	$req_password  = '';
    	$req_c_password  = '';
    }

	if (empty($user_details)){
		$user_role 	  = old('user_role');
		$email        = old('email');
        $first_name   = old('first_name');
        $last_name    = old('last_name');

    }
    else{
    
    	$user_role 	   = !empty($user_details->user_role)?$user_details->user_role: '';
    	$email        = !empty($user_details->email)?$user_details->email: '';
        $first_name    = !empty($user_details->first_name)?$user_details->first_name: '';
        $last_name     = !empty($user_details->last_name)?$user_details->last_name: '';

        // if(!empty($user_details->profile_pic)){
	    //     $profile_pic = "/uploads/".$user_details->profile_pic;
	    // }
    }
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @if(isset($breadcrumbs) && !empty($breadcrumbs))
                    @for($i = 0; $i < count($breadcrumbs); $i++)

                    @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                        <li class="breadcrumb-item"><a href="{{ $breadcrumbs[$i]['url'] }}">
                    @endif
                        {{ $breadcrumbs[$i]['name'] }}

                        @if(isset($breadcrumbs[$i]['url']) && !empty($breadcrumbs[$i]['url']))
                        </a> </li> > 
                        @endif
                    @endfor
                @endif
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

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
                                <form class="form-horizontal bv-form" role="form" id="add_users_details_form" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-actions">
                                            <div class="form-body">
                                                <div class="row">
                                                    @if(!empty($row_id))
                                                        <input type="hidden" name="row_id" value="{{ $row_id }}">
                                                    @endif

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">User Role *</label>
                                                            <select id="user_role" name="user_role" class="form-control">
                                                                <option value="">Select</option>
                                                            
                                                                <option value="{{ config('constants.ROLES.SUPER')}}" {{ (config('constants.ROLES.SUPER') == $user_role)? 'selected': ' ' }} >
                                                                    {{ config('constants.REVERSAL_ROLES.SUPER') }}
                                                                </option>
                                                                <option value="{{ config('constants.ROLES.ADMIN')}}" {{ (config('constants.ROLES.ADMIN') == $user_role)? 'selected': ' ' }} >
                                                                    {{ config('constants.REVERSAL_ROLES.ADMIN') }}
                                                                </option>
                                                                <option value="{{ config('constants.ROLES.SALES')}}" {{ (config('constants.ROLES.SALES') == $user_role)? 'selected': ' ' }} >
                                                                    {{ config('constants.REVERSAL_ROLES.SALES') }}
                                                                </option>
                                                            </select>
                                                            @if ($errors->has('user_role'))
                                                                <small class="form-control-feedback">{{ $errors->first('user_role') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Email *</label>
                                                            <input type="email" id="email" name="email" class="form-control" placeholder=" Email" {{$req_email}} value = "{{$email}}" >
                                                            @if ($errors->has('email'))
                                                                <small class="form-control-feedback">{{ $errors->first('email') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">First Name*</label>
                                                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder=" First Name" {{ $req_first_name }} value = "{{ $first_name }}" >
                                                            @if ($errors->has('first_name'))
                                                                <small class="form-control-feedback">{{ $errors->first('first_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="hv_field admin_field display_none form-group">
                                                            <label class="control-label">Last Name *</label>
                                                            <input type="text" id="last_name" name="last_name" class="form-control" placeholder=" Last Name" {{ $req_last_name }} value = "{{ $last_name }}" >
                                                            @if ($errors->has('last_name'))
                                                                <small class="form-control-feedback">{{ $errors->first('last_name') }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Password *</label>
                                                            <input type="password" value = "" name="password" id="password" class='form-control' placeholder="Password" {{ $req_password }}>
                                                            <small class="form-control-feedback">{{ $errors->first('password') }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Confirm Password *</label>
                                                            <input type="password" value = "" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm-Password" {{ $req_c_password }} >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>   
                                        <div class="form-actions m-t-5">
                                            <div class="pull-right">
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
		$('#add_users_details_form')
        .bootstrapValidator({
            excluded: ':disabled',
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                password: {
                    validators: {
                        identical: {
                            field: 'password_confirmation',
                            message: 'The password and its confirm are not the same'
                        }
                    }
                },
                password_confirmation: {
                    validators: {
                        identical: {
                            field: 'password',
                            message: 'The password and its confirm are not the same'
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
			var formData = new FormData($("#add_users_details_form")[0]);
			// console.log(formData);
			$(".pre-loader").show();
            var type = "POST"; //for creating new resource
            $.ajax({
                type: type,
                url:  "{{ route('save_users_details') }}",
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
                        // console.log(user_role);
                        if(user_role != 8){
                            showSweetAlertMessage(result.status,result.message, "{{ route('users_view') }}");
                        }
                        else{
                            showSweetAlertMessage(result.status,result.message, "{{ route('users_view') }}");
                        }

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

        $('#add_users_details_form').data('bootstrapValidator').resetForm();

		// $('#add_users_details_form').validator('update');
		$("#submit_button_user").attr('disabled',false);

	});
</script>

@endsection