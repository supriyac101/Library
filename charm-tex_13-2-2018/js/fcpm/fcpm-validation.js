jQuery(document).ready(function($){

	var regExp = /[^\d]/;

	var fcpmValidate = function(){

		var thisVal = parseInt($(this).val());
		var maxQty = parseInt($(this).attr('data-max-qty'));

		if(maxQty != 'false'){
			if(!regExp.test($(this).val())){
				if(thisVal > maxQty) { 
					$(this).addClass('validation-failed');
					var message = '<div class="validation-advice">Maximum available stock is - ' + maxQty + '</div>';
					if(!$(this).next('.validation-advice:visible').size() > 0) {
						$(this).after(message).fadeIn(600);
					}
					$('.btn-cart').attr('disabled','disabled');
				} else {
					$(this).removeClass('validation-failed');
					$(this).next('.validation-advice').replaceWith('').fadeOut(600);
					if(!$('.fcpm-validate.matrix-qty').hasClass('validation-failed')) {
						$('.btn-cart').removeAttr('disabled');
					}
				}
			} else {
				var message = '<div class="validation-advice">Please use numbers only in this field.</div>'
				$(this).addClass('validation-failed');
				if(!$(this).next('.validation-advice:visible').size() > 0) {
					$(this).after(message).fadeIn(600);
				}
				$('.btn-cart').attr('disabled','disabled');
			}
		}
		
	}

	$('.fcpm-validate.matrix-qty').bind('keyup blur', fcpmValidate);

});