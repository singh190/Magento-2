require([
         'jquery',
         'jquery/ui',
         'jquery/validate',
         'mage/translate'
         ], function($){
        
        $.validator.addMethod(
                              'validate-camping-email', function (v) {
                              return $.mage.isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v);
        },'Invalid user name');


$.validator.addMethod(
                      'required-email',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter an email address'));


$.validator.addMethod(
                      'required-password',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter a password'));



$.validator.addMethod(
                      'validate-reg-password',function (value) {
                      if (value == null) {
                      return false;
                      }
                      /*strip leading and trailing spaces*/
                      var pass = $.trim(value);
                      if (!pass.length) {
                      return true;
                      }
                      return !(pass.length > 0 && pass.length < 6);
                      }, $.mage.__('Please choose a password between 8 and 20 characters'));


$.validator.addMethod(
                      'validate-register-password', function (v) {
                      return /^(?=\D*\d)(?=[^a-z]*[a-z])[0-9a-z]+$/i.test(v);
                      },'Password should contain at least one letter and one number');


$.validator.addMethod(
                      'required-reg-zip',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter a ZIP/Postal code'));


$.validator.addMethod(
                      'validate-reg-zip-us',function (v) {
                      return $.mage.isEmptyNoTrim(v) || /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(v);
                      }, $.mage.__('Please enter a valid zip code'));



$.validator.addMethod(
                      'required-firstname',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter your first name'));


$.validator.addMethod(
                      'required-lastname',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter your last name'));

$.validator.addMethod(
                      'validate-register-firstname-alpha',function (v) {
                      return $.mage.isEmptyNoTrim(v) || /^[a-zA-Z0-9 ]+$/.test(v);
                      }, $.mage.__('First name should have at least one character'));


$.validator.addMethod(
                      'validate-register-lastname-alpha',function (v) {
                      return $.mage.isEmptyNoTrim(v) || /^[a-zA-Z0-9 ]+$/.test(v);
                      }, $.mage.__('Last name should have at least one character'));

$.validator.addMethod(
		'required-member-number',function (value) {
	         return !$.mage.isEmpty(value);
	     }, $.mage.__('Member Number field is required.'));

		 
		
$.validator.addMethod(
		'required-web-number',function (value) {
	         return !$.mage.isEmpty(value);
	     }, $.mage.__('Please enter a Web Order Number')); 
		 
$.validator.addMethod(
		'required-member-lastname',function (value) {
	         return !$.mage.isEmpty(value);
	     }, $.mage.__('Last Name field is required.'));

$.validator.addMethod(
		'required-member-zip',function (value) {
	         return !$.mage.isEmpty(value);
	     }, $.mage.__('Zip/Postal Code field is required.'));

	$.validator.addMethod(
		'validate-reg-zip-us',function (v) {
			return $.mage.isEmptyNoTrim(v) || /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(v);
	     }, $.mage.__('Please enter a valid zip code'));

// Rv Profile //
$.validator.addMethod(
                      'required-rvtype',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please choose a RV Type'));

$.validator.addMethod(
                      'required-rvyear',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please choose a year'));

$.validator.addMethod(
                      'required-rvmake',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please select the Make of the RV'));

$.validator.addMethod(
                      'required-rvmodel',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please select the Model of the RV'));

$.validator.addMethod(
                      'required-rvpurchaseyear',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please choose a Year of purchase'));

$.validator.addMethod(
                      'required-odometer',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please provide your current odometer reading'));

// Rv Profile //

// Contact Us //
$.validator.addMethod(
					  'validate-contactus-email', function (v) {
					  return $.mage.isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v);
        },'Invalid email address');
		
$.validator.addMethod(
                      'required-comments',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please enter some comments'));
					  
$.validator.addMethod(
                      'required-subject',function (value) {
                      return !$.mage.isEmpty(value);
                      }, $.mage.__('Please select a subject'));
// Contact Us //
$.validator.addMethod(
'checkreview',function (value) {
if(value.length>100) return true;
}, $.mage.__('Review is too short. Please type in atleast 100 characters.'));

});