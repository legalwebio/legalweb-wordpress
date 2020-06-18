
(function($){

    $(document).ready(function(){


        $('#user-commission-history-month-filter').on('change', function() {
            document.forms['user-commission-history-filter'].submit();
        });

        $('#user-wordpress-settings-payoutType').on('change', function() {

            var val = $(this).val();

            setWordpressSetingsPayoutContainer(val);

        });

        function setWordpressSetingsPayoutContainer(val) {
            if (val== null || val == '') return;
            $('#user-wordpress-settings-bank-container').hide();
            $('#user-wordpress-settings-paypal-container').hide();


            $('#user-wordpress-settings-'+val+'-container').show();
        }


        function toggleWordpressDataSignUpContainer() {
            if ($('#woo_signup_wordpress_enabled') && $('#woo_signup_wordpress_enabled').is(':checked')) {

                $('#user-wordpress-settings-data-container').show();
            } else {
                $('#user-wordpress-settings-data-container').hide();
            }
        }

        $('#woo_signup_wordpress_enabled').on('change', function() {

            toggleWordpressDataSignUpContainer();

        });

        setWordpressSetingsPayoutContainer($('#user-wordpress-settings-payoutType').val());
        toggleWordpressDataSignUpContainer();
    });
})( jQuery );

