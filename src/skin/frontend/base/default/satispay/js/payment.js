var Satispay_Payment = function(options) {
    var self = this;
    var interval = 5000;
    
    this.renderError = function(message) {
        alert(message);
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
    
    jQuery('#submit').click(function() {
        var phoneNumber = jQuery('#phone_number').val();
        jQuery.post(options.chargeEndpoint, { phone_number: phoneNumber }, function(data) {
            if(!data.success) {
                self.renderError(data.message);
                return;
            }
            
            setTimeout(self.checkStatus, interval);
        }).fail(function(response) {
            if(!response.responseJSON.message) {
                return;
            }
            
            self.renderError(response.responseJSON.message);
        });
    });
}
