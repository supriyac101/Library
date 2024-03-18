/**
* Grid Live Edit
* 
* @copyright Anowave
* @author Angel Kostadinov
*/
jQuery.noConflict(); 

var Price = {}

jQuery(document).ready(function($)
{
	/* Create Anowave Object */
	Price = (function() 
	{
		return {
			bind: function()
			{
				return this;
			},
			choose: function(element)
			{
				var url = Anowave.Config.url;
				
				new Ajax.Request(url, 
				{
					parameters: 
					{
						isAjax: 	1, 
						method: 	'POST',
						form_key: 	window.FORM_KEY
					},
		            onSuccess: function(transport) 
		            {	
		                var response = transport.responseText;

		               	document.getElementById('chooseGrid').innerHTML = response;
		               	
       					chooseGridJsObject = new varienGrid('chooseGrid', url, 'page', 'sort', 'dir', 'filter');
						chooseGridJsObject.useAjax 	= '1';
						
						chooseGridJsObject.rowClickCallback = function(grid, event)
						{
							var target = $(event.target);
							
							target = target.is('tr') ? target : target.parents('tr:first');

							var radio = target.find(':input[type=radio]'), entity = radio.val();
							
							radio.prop('checked', true);
							
							$(':hidden[name=price_product_id]').val(entity);
							
							return true;
						};
						
						chooseGridJsObject.checkboxCheckCallback = function(grid, element, checked)
						{
							return true;
						};
						
						chooseGridJsObject.initRowCallback = function(grid, row)
						{
							return true;
						};
		            }.bind(this)
				})
			},
			create: function(element)
			{
				var fields = $(element).parents('.fieldset:first').find(':input').serialize();
				
				
				new Ajax.Request(Anowave.Config.save, 
				{
					parameters: fields,
		            onSuccess: function(transport) 
		            {
		            	priceGridJsObject.doFilter();
		            }
				})
			}
		}
	})().bind();
});