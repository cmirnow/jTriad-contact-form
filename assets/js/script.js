jQuery(document).ready(function(){
	jQuery('#contact-form').jqTransform();

	jQuery('button').click(function(){
		jQuery('.formError').hide();
	});

	var use_ajax = true;
	jQuery.validationEngine.settings={};

	jQuery('#contact-form').validationEngine({
		inlineValidation: false,
		promptPosition: 'centerRight',
		success: function(){ use_ajax = true; },
		failure: function(){ use_ajax = false; }
	 })

	jQuery('#contact-form').submit(function(e){
		if(use_ajax){
			jQuery('#loading').css('visibility','visible');
			jQuery.post(
				window.location
				,jQuery(this).serialize() + '&ajax=1',
				function(data){
					if(data.Error){
						jQuery('#errorMessage').html(data.Data.strErr);
						jQuery('#errorMessage').slideDown();

						/* Always reinit recaptcha element to update code and check */
						jQuery('#dynamic_recaptcha_1').replaceWith(data.recaptchaHTML);
						window.JoomlaInitReCaptcha2();
					}
					else{
						jQuery('#contact-form').hide('slow').after('<h1>Thank you!</h1>');
					}

					jQuery('#loading').css('visibility','hidden');
				}
				,'json'
			);
		}
		e.preventDefault();
	})
});
