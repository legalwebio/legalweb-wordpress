var lwAffActiveAdminSubmenu = '';

(function($){

    $(document).ready(function(){


        function lwAffMarkActiveAdminSubmenu(activeSlug)
        {
            $('a[href^="admin.php?page=lw-wordpress&tab=info"]').css('color','#28a745');
            $('a[href^="admin.php?page=lw-wordpress&tab=info"]').css('font-weight','500');

            if(document.URL.indexOf("admin.php?page=lw-wordpress") < 0){
                return;
            }

            //alert(activeSlug);
            $('a[href*="admin.php?page=lw-wordpress"]').each(function() {
                $(this).parent().removeClass('current');
            });
            if(activeSlug == 'common-settings')
            {
                $('a[href$="admin.php?page=lw-wordpress"]').parent().addClass('current');
            } else {
                $('a[href^="admin.php?page=lw-wordpress&tab=' + activeSlug + '"]').parent().addClass('current');
            }
        }

        lwAffMarkActiveAdminSubmenu(lwAffActiveAdminSubmenu);

        $('#user-commission-history-month-filter').on('change', function() {
            document.forms['user-commission-history-filter'].submit();
        });
        $('#admin-commission-history-month-filter').on('change', function() {
            document.forms['admin-commission-history-filter'].submit();
        });

        $('#user-wordpress-settings-payoutType').on('change', function() {

            var val = $(this).val();

            setWordpressSetingsPayoutContainer(val);

        });

        function setWordpressSetingsPayoutContainer(val) {
            $('#user-wordpress-settings-bank-container').hide();
            $('#user-wordpress-settings-paypal-container').hide();


            $('#user-wordpress-settings-'+val+'-container').show();
        }
        setWordpressSetingsPayoutContainer($('#user-wordpress-settings-payoutType').val());

    });
})( jQuery );

