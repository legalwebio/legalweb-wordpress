

    window.addEventListener('lw-beforeshowpopup', function (e) {
        window.wp_consent_type = 'optin';
        var event = new CustomEvent('wp_consent_type_defined');
        document.dispatchEvent( event );
        //console.log('lw-beforeshowpopup', e.detail);
    });

    window.addEventListener('lw-popup-closed', function (e) {

        //console.log('closed', e.detail);
        wp_set_consent('statistics', window.lwIsIntegrationCategoryEnabled('analyse_statistic') ? 'allow' : 'deny');
        wp_set_consent('statistics-anonymous', window.lwIsIntegrationCategoryEnabled('analyse_statistic') ? 'allow' : 'deny');
        wp_set_consent('marketing', window.lwIsIntegrationCategoryEnabled('targeting_profiling') ? 'allow' : 'deny');
        wp_set_consent('functional', 'allow');
       // wp_set_consent('preferences', 'allow');
    });
