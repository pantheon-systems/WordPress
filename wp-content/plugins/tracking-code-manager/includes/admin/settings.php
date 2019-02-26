<?php
function tcmp_ui_track() {
    global $tcmp;

    $track=TCMP_SQS('track', '');
    if($track!='') {
        $track=intval($track);
        $tcmp->Options->setTrackingEnable($track);
        $tcmp->Tracking->sendTracking(TRUE);
    }

    $uri=TCMP_TAB_SETTINGS_URI.'&track=';
    if($tcmp->Options->isTrackingEnable()) {
        $uri.='0';
        $tcmp->Options->pushSuccessMessage('EnableAllowTrackingNotice', $uri);
    } else {
        $uri.='1';
        $tcmp->Options->pushErrorMessage('DisableAllowTrackingNotice', $uri);
    }
    $tcmp->Options->writeMessages();
}
function tcmp_ui_settings() {
    global $tcmp;

    $tcmp->Form->prefix='License';
    if($tcmp->Check->nonce('tcmp_license')) {
        $options=$tcmp->Options->getMetaboxPostTypes();
        foreach ($options as $k => $v) {
            $v=TCMP_ISQS('metabox_' . $k, 0);
            $options[$k]=$v;
        }
        $tcmp->Options->setMetaboxPostTypes($options);
    }

    $tcmp->Form->formStarts();
    $tcmp->Form->p('MetaboxSection');
    $metaboxes=$tcmp->Options->getMetaboxPostTypes();

    $types=$tcmp->Utils->query(TCMP_QUERY_POST_TYPES);
    foreach ($types as $v) {
        $v=$v['id'];
        //$tcmp->Form->tags=TRUE;
        //$tcmp->Form->premium=!in_array($v, array('post', 'page'));
        $tcmp->Form->checkbox('metabox_'.$v, $metaboxes[$v]);
    }
    $tcmp->Form->nonce('tcmp_license');
    $tcmp->Form->br();
    $tcmp->Form->submit('Save');
    $tcmp->Form->formEnds();
}