/**
 * plugin admin area javascript
 */
(function($){$(function () {
	if ( ! $('body.pmxi_plugin').length) return; // do not execute any code if we are not on plugin page

	$('.product_data_tabs').find('a').click(function(){
		$('.product_data_tabs').find('li').removeClass('active');
		$(this).parent('li').addClass('active');
		$('.panel').hide();
		$('#' + $(this).attr('rel')).show();
	});

	var change_depencies = function (){

		var is_variable = ($('#product-type').val() == 'variable');
		var is_grouped = ($('#product-type').val() == 'grouped');
		var is_simple = ($('#product-type').val() == 'simple');
		var is_external = ($('#product-type').val() == 'external');
		var is_downloadable = ($('#_downloadable').is(':checked'));
		var is_virtual = ($('#_virtual').is(':checked'));			
		var is_multiple_product_type = ($('input[name=is_multiple_product_type]:checked').val() == 'yes');		

		if (!is_multiple_product_type) $('.product_data_tabs li, .options_group').show();

		$('.product_data_tabs li, .options_group').each(function(){

			if (($(this).hasClass('hide_if_grouped') || 				
				$(this).hasClass('hide_if_external')) && is_multiple_product_type)				
			{
	 			if ($(this).hasClass('hide_if_grouped') && is_grouped) { $(this).hide(); return true; } else if ( $(this).hasClass('hide_if_grouped') && !is_grouped )  $(this).show(); 	 			
	 			if ($(this).hasClass('hide_if_external') && is_external) { $(this).hide(); return true; } else if ( $(this).hasClass('hide_if_external') && !is_external )  $(this).show();	 			
	 		}

	 		if (($(this).hasClass('show_if_simple') || $(this).hasClass('show_if_variable') || $(this).hasClass('show_if_grouped') || $(this).hasClass('show_if_external')) && is_multiple_product_type){
	 			if ($(this).hasClass('show_if_simple') && is_simple) $(this).show(); else if ( ! is_simple ){  
	 				$(this).hide();
	 				if ($(this).hasClass('show_if_variable') && is_variable) $(this).show(); else if ( ! is_variable ){  
	 					$(this).hide();
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) $(this).show(); else if ( ! is_grouped ) { 
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) $(this).show(); else if ( ! is_external ) $(this).hide();
	 					}
	 				}
	 			}
	 			else if( !$(this).hasClass('show_if_simple') ){	 				
	 				if ($(this).hasClass('show_if_variable') && is_variable) $(this).show(); else if ( ! is_variable ){  
	 					$(this).hide();
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) $(this).show(); else if ( ! is_grouped ) { 
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) $(this).show(); else if ( ! is_external ) $(this).hide();
	 					}
	 				}
	 				else if ( !$(this).hasClass('show_if_variable') ){	 					
	 					if ($(this).hasClass('show_if_grouped') && is_grouped) $(this).show(); else if ( ! is_grouped ) { 
	 						$(this).hide();
	 						if ($(this).hasClass('show_if_external') && is_external) $(this).show(); else if ( ! is_external ) $(this).hide();
	 					}
	 					else if ( !$(this).hasClass('show_if_grouped') ){
	 						if ($(this).hasClass('show_if_external') && is_external) $(this).show(); else if ( ! is_external ) $(this).hide();
	 					}
	 				}
	 			}
	 		}

	 		if ($(this).hasClass('hide_if_virtual') || 
				$(this).hasClass('show_if_virtual') || 
				$(this).hasClass('show_if_downloadable'))
	 		{
	 			if ($(this).hasClass('hide_if_virtual') && is_virtual) $(this).hide(); else if ( $(this).hasClass('hide_if_virtual') && !is_virtual )  $(this).show();
	 			if ($(this).hasClass('show_if_virtual') && is_virtual) $(this).show(); else if ( $(this).hasClass('show_if_virtual') && !is_virtual )  $(this).hide();
	 			if ($(this).hasClass('show_if_downloadable') && is_downloadable) $(this).show(); else if ( $(this).hasClass('show_if_downloadable') && !is_downloadable )  $(this).hide();
	 		}
		});

		if ($('input[name=is_product_manage_stock]:checked').val() == 'no') $('.stock_fields').hide(); else $('.stock_fields').show(); 
		
		if ($('#link_all_variations').is(':checked')) $('.variations_tab').hide(); else if (is_variable) $('.variations_tab').show();	

		if ( ! is_simple ) {
			$('.woocommerce_options_panel').find('input, select').attr('disabled','disabled'); 
			$('.upgrade_template').show();
		} 
		else { 
			$('.woocommerce_options_panel').find('input, select').removeAttr('disabled');
			$('.upgrade_template').hide();
		}
		
		if ($('#xml_matching_parent').is(':checked')) $('#variations_tag').show(); else $('#variations_tag').hide();
	}

	$('input[name=matching_parent]').click(function(){

		if ($(this).val() == "xml") $('#variations_tag').show(); else $('#variations_tag').hide();

	});
	
	change_depencies();

	$('#product-type').change(function(){
		change_depencies();
		$('.wc-tabs').find('li:visible:first').find('a').click();
	});
	$('#_virtual, #_downloadable, input[name=is_product_manage_stock]').click(change_depencies);
	$('input[name=is_multiple_product_type]').click(function(){
		change_depencies();
		$('.wc-tabs').find('li:visible:first').find('a').click();
	});
	$('#link_all_variations').change(function(){
		if ($(this).is(':checked'))
			$('.variations_tab').hide();		
		else
			$('.variations_tab').show();
	});
	$('#regular_price_shedule').click(function(){
		$('#sale_price_range').show();
		$('input[name=is_regular_price_shedule]').val('1');
		$(this).hide();
	});

	$('#cancel_regular_price_shedule').click(function(){
		$('#sale_price_range').hide();
		$('input[name=is_regular_price_shedule]').val('0');
		$('#regular_price_shedule').show();
	});

	$('#variable_sale_price_shedule').click(function(){
		$('#variable_sale_price_range').show();
		$('input[name=is_variable_sale_price_shedule]').val('1');		
		$(this).hide();
	});

	$('#cancel_variable_regular_price_shedule').click(function(){
		$('#variable_sale_price_range').hide();
		$('input[name=is_variable_sale_price_shedule]').val('0');		
		$('#variable_sale_price_shedule').show();
	});

	$('#_variable_virtual').click(function(){
		if ($(this).is(':checked')){
			$('#variable_virtual').show();
			$('#variable_dimensions').hide();
		}
		else{
			$('#variable_virtual').hide();
			$('#variable_dimensions').show();
		}
	});

	$('#_variable_downloadable').click(function(){
		if ($(this).is(':checked')) $('#variable_downloadable').show(); else $('#variable_downloadable').hide();
	});	
	
	$('.variation_attributes, #woocommerce_attributes').find('label').live({
        mouseenter:
           function()
           {           	
           	if ( "" == $(this).attr('for')){
				var counter = $('.variation_attributes').find('.form-field').length;
				$(this).parents('span:first').find('input').attr('id', $(this).parents('span:first').find('input').attr('name') + '_' + counter);
				$(this).attr('for', $(this).parents('span:first').find('input').attr('id'));
			}
           },
        mouseleave:
           function()
           {

           }
    });

	$('#variations_tag').insertAfter('.xpath_help');

});})(jQuery);
