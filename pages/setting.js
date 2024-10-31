function _ri_submit() {
	if(document.ri_form.ri_image.value == "") {
		alert(rilb_adminscripts.rilb_image);
		document.ri_form.ri_image.focus();
		return false;
	}
	else if(document.ri_form.ri_group.value == "" && document.ri_form.ri_group_txt.value == "") {
		alert(rilb_adminscripts.rilb_group);
		document.ri_form.ri_group_txt.focus();
		return false;
	}
	else if(document.ri_form.ri_width.value=="" && isNaN(document.ri_form.ri_width.value)) {
		alert(rilb_adminscripts.rilb_width);
		document.ri_form.ri_width.focus();
		document.ri_form.ri_width.select();
		return false;
	}
}

function _ri_delete(id) {
	if(confirm(rilb_adminscripts.rilb_delete)) {
		document.frm_ri_display.action="options-general.php?page=random-image-light-box&ac=del&did="+id;
		document.frm_ri_display.submit();
	}
}	

function _ri_redirect() {
	window.location = "options-general.php?page=random-image-light-box";
}

function _ri_help() {
	window.open("http://www.gopiplus.com/work/2020/10/11/wordpress-plugin-random-image-light-box/");
}

function _ri_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('ri_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(rilb_adminscripts.rilb_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('ri_group_txt').value = newinputtxt;
		return false;  
	}  
}