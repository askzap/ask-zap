 (function(_, $) {
    $(document).submit('submit', function(e) {
    	if ($('form.cm-vendor-changes-confirm').formIsChanged()) { 
			if (confirm(_.tr('text_vendor_profile_changes_notice')) == false) {
	        	return false;
	      	}
      	}
    });
    $(document).ready(function(){
        if (_.vendor_pre == 'Y') {
            $('form#company_update_form').addClass('cm-vendor-changes-confirm');
        }
    });
}(Tygh, Tygh.$));