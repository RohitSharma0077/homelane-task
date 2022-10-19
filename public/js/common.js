//$(".pre-loader").show();
$(document).ready(function() {
	// $(".pre-loader").hide();
	// $(".pre-loader").css('display','none');
	setTimeout(function(){
		$(".pre-loader").hide();
	},1500);

	// AutoComplete Off for all forms
    $(document).on('focus', ':input', function(){
        $(this).attr('autocomplete', 'off');
    });

});

function showFlashModal(status, message){

	$("#myModalLabel").html('');
	$("#modal_msg").html('');
	$("#flash_msg_modal .modal-header").removeClass('alert-success');
	$("#flash_msg_modal .modal-header").removeClass('alert-danger');
	if(status==true){
		$("#myModalLabel").html('Success alert');
		$("#modal_msg").html(message);
		$("#flash_msg_modal .modal-header").addClass('alert-success');
	}
	else{
		$("#myModalLabel").html('Failure alert');
		$("#modal_msg").html(message);
		$("#flash_msg_modal .modal-header").addClass('alert-danger');
	}
    setTimeout(function(){
        $("#btn_flash_msg").trigger("click");
    },1000);
}
    

    
    // Displaying sweet alert popup
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
function ValidateExcelSheet(field, rules, i, options){
	var error_msg = "";
	var fuData = field[0];
	var FileUploadPath = fuData.value;
	// console.log(fuData);
	if (FileUploadPath == '') {
	}
	else {
		var Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
		var EXCEL_EXTENSIONS = new Array('jpeg', 'jpg', 'png', 'gif');
		var arr = EXCEL_EXTENSIONS;
		if( $.inArray(Extension, EXCEL_EXTENSIONS) != -1){
			if (fuData.files && fuData.files[0]) {
				var size = fuData.files[0].size;
				// console.log(fuData.files);
				// console.log(size);
				if(size > 5452595.2){
					return "Sorry, File size cant be greater than 5MB";
				}else{
					// console.log(field);
					readURL(fuData);
				}
			}
		} 
		else {
			var array = arr.join(", ");
			return "Sorry, only "+array+" files are allowed. ";
		} 
	}
	return true;
}
$(document).on('change','#profile_pic',function(){
	var valid_excel = ValidateExcelSheet($(this));
	// console.log(valid_excel);
	if(valid_excel!=true){
		$(this).val("");
		//alert(valid_excel);
		showSweetAlertMessage(0,valid_excel);
	}
});
function readURL(input) {
	// console.log(input);
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#profile_img_display')
				.attr('src', e.target.result);
		};

		reader.readAsDataURL(input.files[0]);
	}
}