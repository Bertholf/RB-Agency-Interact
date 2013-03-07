// JavaScript Document
jQuery(document).ready(function(){
	
	/*
	 *	
	 */
	jQuery("#ProfileGender").change(function() {

		if(jQuery(this).val() == '1'){

			jQuery(".male_filter").show();
			jQuery(".female_filter").hide();
			clear_filter(".female_filter");

		} else if (jQuery(this).val() == '2'){

			jQuery(".male_filter").hide();
			jQuery(".female_filter").show();
			clear_filter(".male_filter");

		} else {

			jQuery(".male_filter").show();
			jQuery(".female_filter").show();

		}

	});
	
	/*
	 *	Clear fields to set value to null
	 */
	 function clear_filter(filter){

	 	jQuery(filter).each(function(){
			
				jQuery(this).val('');

		});	

	 }
});