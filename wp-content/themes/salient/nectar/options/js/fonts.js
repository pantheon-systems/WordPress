jQuery(document).ready(function ($) {
	
	
	//only begin if we're sure it's the right page
	if($('h3:contains("Typography")').length > 0 && $('#redux-opts-form-wrapper').length  > 0){
		
		//add font loading gif
		$('.typography-table tbody tr:nth-child(7n+0) td').append('<span class="font-attrs-loading"></span>');
		
		//turn selects into chosen
		$('.font-family select').chosen();
		
	

		$('.typography-table .font-family select').change(function(){

			updateFontAttrs($(this),'false');
			
			//auto select first option in visible list
			$(this).closest('tr').next('tr').next('tr').next('tr').find('select option').attr('selected','');
			$(this).closest('tr').next('tr').next('tr').next('tr').find('select option:visible:first').attr('selected','selected');
			
			$(this).closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('select option').attr('selected','');
			$(this).closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('select option:visible:first').attr('selected','selected');

		});//change event
		
		
		
		
		//on load only show the corresponding weights
		//if($('input[name="salient[use-custom-fonts]"]').is(':checked')){
		$('.typography-table .font-family select').each(function(){
			updateFontAttrs($(this),'true');
		});
		//}

		//line height text
		$('.typography-table .font-option select[id*=_line_height]').each(function(){
			$(this).find('option[value="-"]').text('Line Height');
		});

		
	
	}//if typography h3
	
	
	
	
	function updateFontAttrs(element,firstLoad){
		
		//unhide all
		element.closest('tr').next('tr').next('tr').next('tr').find('select option').show();
		

		var $that = element;
		
		//check what weights are available for font
		var $dataToPass = {
			action: 'nectar_check_font_attrs', 
			font_family: element.val(), 
		}
		
		//show loading
		$that.closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('.font-attrs-loading').stop().animate({'opacity':'1'},350);
		
		$.post(fontData.ajaxurl, $dataToPass, function(data){
			
			//hide loading
			$('.font-attrs-loading').stop().animate({'opacity':'0'},250);
			
			//parse returned JSON
			$json = $.parseJSON(data);
			
			//hide all options except the placeholder
			$that.closest('tr').next('tr').next('tr').next('tr').find('select option:not(:first)').hide().attr("disabled", "true");
			$that.closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('select option:not(:first)').hide().attr("disabled", "true");
			
			if(firstLoad == 'false'){		
				$that.closest('tr').next('tr').next('tr').next('tr').find('select').val('-');
				$that.closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('select').val('subset');
			}
			
			//loop through the json obj and show the applicable attrs
			$.each($json,function(i,v){
				
				 $.each(v.subsets,function(i,v){
				 	$that.closest('tr').next('tr').next('tr').next('tr').next('tr').next('tr').next('tr').find('td option[value='+v+']').show().removeAttr("disabled"); 
				 });
				 
				 $.each(v.weights,function(i,v){
				 	$that.closest('tr').next('tr').next('tr').next('tr').find('td option[value='+v+']').show().removeAttr("disabled");
				 });
				 
			});
			
		});
		

		
	}
	
	
	
	
	
})
