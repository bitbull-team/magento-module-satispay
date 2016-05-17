var Satispay_Payment = function(options) {
    var self = this;
    var interval = 4000;
    
    this.renderError = function(message) {
        jQuery('.satispay-container .errors').html(message).fadeIn();
    };
    this.hideError = function() {
        jQuery('.satispay-container .errors').fadeOut();
    };
    this.renderSuccess = function() {
        jQuery('.satispay-container .instructions, .satispay-container .phone-selection, .satispay-container #submit').fadeOut(function() {
            jQuery('.satispay-container .success').fadeIn();
        });
    };
    
    this.checkStatus = function() {
        jQuery.get(options.statusEndpoint, function(data) {
            if(!data.pending && data.redirect) {
                location.href = data.redirect;
                return;
            }
            
            setTimeout(self.checkStatus, interval);
        });
    };
    
    this.submit = function() {
        self.hideError();
        var phoneNumber = jQuery('#country-code').val() + jQuery('#phone-number').val();
        jQuery.post(options.chargeEndpoint, { phone_number: phoneNumber }, function(data) {
            if(!data.success) {
                self.renderError(data.message);
                return;
            }
            
            self.renderSuccess();
            setTimeout(self.checkStatus, interval);
        }).fail(function(response) {
            if(!response.responseJSON.message) {
                return;
            }
            
            self.renderError(response.responseJSON.message);
        });
    };
    
    jQuery('#submit').click(this.submit);
    jQuery('#phone-number').keypress(function(e) {
       if(e.keyCode == 13) {
           self.submit();
       } 
    });
}
