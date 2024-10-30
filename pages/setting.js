function _mislider_submit() {
	if(document.mislider_form.mislider_img.value == "") {
		alert(mislider_adminscripts.mislider_image);
		document.mislider_form.mislider_img.focus();
		return false;
	}
	else if(document.mislider_form.mislider_title.value == "") {
		alert(mislider_adminscripts.mislider_title);
		document.mislider_form.mislider_title.focus();
		return false;
	}
//	else if(document.mislider_form.mislider_desc.value == "") {
//		alert(mislider_adminscripts.mislider_desc);
//		document.mislider_form.mislider_desc.focus();
//		return false;
//	}
	else if(document.mislider_form.mislider_slide.value == "") {
		alert(mislider_adminscripts.mislider_slide);
		document.mislider_form.mislider_slide.focus();
		return false;
	}
	else if(document.mislider_form.mislider_group.value == "" && document.mislider_form.mislider_group_txt.value == "") {
		alert(mislider_adminscripts.mislider_group);
		document.mislider_form.mislider_group.focus();
		return false;
	}
}

function _mislider_delete(id) {
	if(confirm(mislider_adminscripts.mislider_delete)) {
		document.frm_mislider_display.action="options-general.php?page=moving-image-slider&ac=del&did="+id;
		document.frm_mislider_display.submit();
	}
}	

function _mislider_redirect() {
	window.location = "options-general.php?page=moving-image-slider";
}

function _mislider_help() {
	window.open("http://www.gopiplus.com/work/2021/05/26/moving-image-slider-wordpress-plugin/");
}

function _mislider_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('mislider_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(mislider_adminscripts.mislider_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('mislider_group_txt').value = newinputtxt;
		return false;  
	}  
}