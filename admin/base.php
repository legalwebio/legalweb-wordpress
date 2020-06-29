<div class="wrap"></div>

<div class="lw-aff" style="padding-right: 15px">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-2 text-white">
        <a class="navbar-brand" href="#">
            <img src="<?php echo LegalWebCloud::pluginURI('public\images\legalwebio-logo-icon-white.svg'); ?>" width="30" height="30" class="d-inline-block align-top" alt="">
            <a class="navbar-brand"><?php _e('LegalWeb Cloud by legalweb.io', 'legalweb-cloud'); ?></a>
        </a>
    </nav>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><?php _e('LegalWeb Cloud', 'legalweb-cloud'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= __($tabs[$tab]->getTabTitle(),'legalweb-cloud');;?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 container-fluid lw-aff-content-container">
            <?php

            if (isset($tabs[$tab])) {
                $tabs[$tab]->page();
                ?>
                <script>
                    var lwWordpressActiveAdminSubmenu = '<?= $tabs[$tab]->slug ?>';

                </script>
                <?php
            } else {
                $tabs['common-settings']->page();
            }

            ?>
        </div>
    </div>

</div>
