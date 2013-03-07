// JavaScript Document
jQuery(document).ready(function(){
	jQuery("#ProfileGender").change(function() {
		if(jQuery(this).val() == '1'){
			jQuery(".male_filter").show();
			jQuery(".female_filter").hide();
		} else if (jQuery(this).val() == '2'){
			jQuery(".male_filter").hide();
			jQuery(".female_filter").show();
		} else {
			jQuery(".male_filter").show();
			jQuery(".female_filter").show();
		}
	});
});