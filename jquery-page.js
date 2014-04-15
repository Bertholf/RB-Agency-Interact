// JavaScript Document
jQuery(document).ready(function(){
	
	/*
	 *	
	 */
	jQuery("#ProfileGender").change(function() {

		if(jQuery("#ProfileGender option:selected").text() == 'Male'){

			jQuery(".male_filter").show();
			jQuery(".female_filter").hide();
			clear_filter(".female_filter");

		} else if (jQuery("#ProfileGender option:selected").text() == 'Female'){

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

//Populate state options for selected  country
		function populateStates(countryId,stateId){
			var url=jQuery("#url").val();
			if(jQuery("#"+countryId).val()!=""){
					jQuery("#"+stateId).show();
					jQuery("#"+stateId).find("option:gt(0)").remove();
					jQuery("#"+stateId).find("option:first").text("Loading...");
					jQuery.getJSON(url+"/get-state/"+ jQuery("#"+countryId).val(), function (data) {
					jQuery("<option/>").attr("value", "").text("Select State").appendTo(jQuery("#"+stateId));	
		                        for (var i = 0; i < data.length; i++) {
						jQuery("<option/>").attr("value", data[i].StateID).text(data[i].StateTitle).appendTo(jQuery("#"+stateId));
					}
					jQuery("#"+stateId).find("option:eq(0)").remove();
				
				});
			 }
		}