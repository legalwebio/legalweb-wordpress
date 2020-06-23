var lwWordpressActiveAdminSubmenu = '';

(function($){

    $(document).ready(function(){


        function lwWordpressMarkActiveAdminSubmenu(activeSlug)
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

        lwWordpressMarkActiveAdminSubmenu(lwAffActiveAdminSubmenu);


    });
})( jQuery );

