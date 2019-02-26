<?php

register_activation_hook(TCMP_PLUGIN_FILE, 'tcmp_install');
function tcmp_install($networkwide=NULL) {
	global $wpdb, $tcmp;

    $time=$tcmp->Options->getPluginInstallDate();
    if($time==0) {
        $tcmp->Options->setPluginInstallDate(time());
    }
    $tcmp->Options->setPluginUpdateDate(time());
    $tcmp->Options->setShowWhatsNew(TRUE);
    $tcmp->Options->setPluginFirstInstall(TRUE);
}

add_action('admin_init', 'tcmp_first_redirect');
function tcmp_first_redirect() {
    global $tcmp;
    $v=$tcmp->Options->getShowWhatsNewSeenVersion();
    if($v>=0 && $v!=TCMP_WHATSNEW_VERSION) {
        $tcmp->Options->setShowWhatsNewSeenVersion(-1);
        tcmp_install();
    }

    if ($tcmp->Options->isPluginFirstInstall()) {
        $tcmp->Options->setPluginFirstInstall(FALSE);
        $tcmp->Options->setShowActivationNotice(TRUE);
        $tcmp->Utils->redirect(TCMP_PAGE_MANAGER);
    }
}
