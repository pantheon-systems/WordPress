jQuery(document).ready(function() {
	var selected = jQuery("#_bsf_post_type").val();
	if(selected == "0")
		hidden();
	else
		expand_default(selected);
//Function to hide all the snippet blocks
function hidden() {
	jQuery(".review").hide();	
	jQuery(".events").hide();
	jQuery(".music").hide();
	jQuery(".organization").hide();
	jQuery(".people").hide();
	jQuery(".product").hide();
	jQuery(".recipes").hide();
	jQuery(".software").hide();
	jQuery(".video").hide();
	jQuery(".article").hide();
	jQuery(".service").hide();

}
//Function to expand the updated snippet block
function expand_default(selected) {
	hidden();
	if(selected == '1')
	{
		jQuery(".review").show(500);
	} 
	else if(selected == '2')
	{
		jQuery(".events").show(500);
	}
	else if(selected == '3')
	{
		jQuery(".music").show(500);
	}
	else if(selected == '4')
	{
		jQuery(".organization").show(500);
	}
	else if(selected == '5')
	{
		jQuery(".people").show(500);
	}
	else if(selected == '6')
	{
		jQuery(".product").show(500);
	}
	else if(selected == '7')
	{
		jQuery(".recipes").show(500);
	}
	else if(selected == '8')
	{
		jQuery(".software").show(500);
	}
	else if(selected == '9')
	{
		jQuery(".video").show(500);
	}
	else if(selected == '10')
	{
		jQuery(".article").show(500);
	}
	else if(selected == '11')
	{
		jQuery(".service").show(500);
	}
}
    jQuery("#_bsf_post_type").change(function() {
		hidden();
		var type=jQuery(this).val();
		if(type == '1')
		{
			jQuery(".review").show(500);
		} 
		else if(type == '2')
		{
			jQuery(".events").show(500);
		}
		else if(type == '3')
		{
			jQuery(".music").show(500);
		}
		else if(type == '4')
		{
			jQuery(".organization").show(500);
		}
		else if(type == '5')
		{
			jQuery(".people").show(500);
		}
		else if(type == '6')
		{
			jQuery(".product").show(500);
		}
		else if(type == '7')
		{
			jQuery(".recipes").show(500);
		}
		else if(type == '8')
		{
			jQuery(".software").show(500);
		}
		else if(type == '9')
		{
			jQuery(".video").show(500);
		}
		else if(type == '10')
		{
			jQuery(".article").show(500);
		}
		else if(type == '11')
		{
			jQuery(".service").show(500);
		}
	});
});