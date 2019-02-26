<?php
//per agganciarsi ogni volta che viene scritto un contenuto
add_filter('wp_head', 'tcmp_head');
function tcmp_head(){
    global $post, $tcmp;

    $tcmp->Options->setPostShown(NULL);
    if($post && isset($post->ID) && (is_page($post->ID) || is_single($post->ID))) {
        $tcmp->Options->setPostShown($post);
        $tcmp->Log->info('POST ID=%s IS SHOWN', $post->ID);
    }

    //future development
    //is_archive();
    //is_post_type_archive();
    //is_post_type_hierarchical();
    //is_attachment();
    $tcmp->Manager->writeCodes(TCMP_POSITION_HEAD);
}
add_action('wp_footer', 'tcmp_footer');
function tcmp_footer() {
    global $tcmp;
    //there isn't a hook when <BODY> starts
    $tcmp->Manager->writeCodes(TCMP_POSITION_BODY);
    $tcmp->Manager->writeCodes(TCMP_POSITION_CONVERSION);
    $tcmp->Manager->writeCodes(TCMP_POSITION_FOOTER);
}

//volendo funziona anche con gli shortcode
add_shortcode('tcmp', 'tcmp_shortcode');
add_shortcode('tcm', 'tcmp_shortcode');
function tcmp_shortcode($atts, $content='') {
    global $tcmp;
    extract(shortcode_atts(array('id' => false), $atts));

    if (!isset($id) || !$id) {
        return '';
    }

    $snippet=$tcmp->Manager->get($id, true);
    return $snippet['code'];
}

function tcmp_ui_first_time() {
    global $tcmp;
    if($tcmp->Options->isShowActivationNotice()) {
        //$tcmp->Options->pushSuccessMessage('FirstTimeActivation');
        //$tcmp->Options->writeMessages();
        $tcmp->Options->setShowActivationNotice(FALSE);
    }
}
function tcmp_admin_footer() {
    global $tcmp;
    if($tcmp->Lang->bundle->autoPush && TCMP_AUTOSAVE_LANG) {
        $tcmp->Lang->bundle->store(TCMP_PLUGIN_DIR.'languages/Lang.txt');
    }
}
add_filter('admin_footer', 'tcmp_admin_footer');