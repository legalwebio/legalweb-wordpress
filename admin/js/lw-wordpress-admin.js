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

        lwWordpressMarkActiveAdminSubmenu(lwWordpressActiveAdminSubmenu);

        $('.lw-wordpress-admin-message').on('click tap', function() {

            $.post( args.ajaxUrl, {
                action: 'lw-notice-action',
                id: (this).getAttribute('data-msgId')
            });

        });

    });
})( jQuery );


function lwCopyToClipboard(elementId) {
    /* Get the text field */
    var copyText = document.getElementById(elementId);

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    //alert("Copied the text: " + copyText.value);
}

