<?php
function tcmp_notice_pro_features() {
    global $tcmp;
    ?>
    <br/>
    <div class="message updated below-h2 iwp">
        <div style="height:10px;"></div>
        <?php
        $i=1;
        while($tcmp->Lang->H('Notice.ProHeader'.$i)) {
            $tcmp->Lang->P('Notice.ProHeader'.$i);
            echo '<br/>';
            ++$i;
        }
        $i=1;
        ?>
        <br/>
        <?php

        /*$options = array('public' => TRUE, '_builtin' => FALSE);
        $q=get_post_types($options, 'names');
        if(is_array($q) && count($q)>0) {
            sort($q);
            $q=implode(', ', $q);
            $q='(<b>'.$q.'</b>)';
        } else {
            $q='';
        }*/
        $q='';
        while($tcmp->Lang->H('Notice.ProFeature'.$i)) { ?>
            <div style="clear:both; margin-top: 2px;"></div>
            <div style="float:left; vertical-align:middle; height:24px; margin-right:5px; margin-top:-5px;">
                <img src="<?php echo TCMP_PLUGIN_IMAGES_URI?>tick.png" />
            </div>
            <div style="float:left; vertical-align:middle; height:24px;">
                <?php $tcmp->Lang->P('Notice.ProFeature'.$i, $q)?>
            </div>
            <?php ++$i;
        }
        ?>
        <div style="clear:both;"></div>
        <div style="height:10px;"></div>
        <div style="float:right;">
            <?php
            $url=TCMP_PAGE_PREMIUM.'?utm_source=free-users&utm_medium=wp-cta&utm_campaign=wp-plugin';
            ?>
            <a href="<?php echo $url?>" target="_blank">
                <b><?php $tcmp->Lang->P('Notice.ProCTA')?></b>
            </a>
        </div>
        <div style="height:10px; clear:both;"></div>
    </div>
    <br/>
<?php }
function tcmp_ui_editor_check($snippet) {
    global $tcmp;

    $snippet['trackMode']=intval($snippet['trackMode']);
    $snippet['trackPage']=intval($snippet['trackPage']);

    $snippet['includeEverywhereActive']=0;
    if($snippet['trackPage']==TCMP_TRACK_PAGE_ALL) {
        $snippet['includeEverywhereActive']=1;
    }
    $snippet=$tcmp->Manager->sanitize($snippet['id'], $snippet);

    if ($snippet['name'] == '') {
        $tcmp->Options->pushErrorMessage('Please enter a unique name');
    } else {
        $exist=$tcmp->Manager->exists($snippet['name']);
        if ($exist && $exist['id'] != $snippet['id']) {
            //nonostante il tutto il nome deve essee univoco
            $tcmp->Options->pushErrorMessage('You have entered a name that already exists. IDs are NOT case-sensitive');
        }
    }
    if ($snippet['code'] == '') {
        $tcmp->Options->pushErrorMessage('Paste your HTML Tracking Code into the textarea');
    }

    if($snippet['trackMode']==TCMP_TRACK_MODE_CODE) {

        $types=$tcmp->Utils->query(TCMP_QUERY_POST_TYPES);
        if($snippet['trackPage']==TCMP_TRACK_PAGE_SPECIFIC) {
            foreach ($types as $v) {
                $includeActiveKey='includePostsOfType_'.$v['id'].'_Active';
                $includeArrayKey='includePostsOfType_'.$v['id'];
                $exceptActiveKey='exceptPostsOfType_'.$v['id'].'_Active';
                $exceptArrayKey='exceptPostsOfType_'.$v['id'];

                if ($snippet[$includeActiveKey] == 1 && $snippet[$exceptActiveKey] == 1) {
                    if (in_array(-1, $snippet[$includeArrayKey]) && in_array(-1, $snippet[$exceptArrayKey])) {
                        $tcmp->Options->pushErrorMessage('Error.IncludeExcludeAll', $v['name']);
                    }
                }
                if ($snippet[$includeActiveKey] == 1 && count($snippet[$includeArrayKey]) == 0) {
                    $tcmp->Options->pushErrorMessage('Error.IncludeSelectAtLeastOne', $v['name']);
                }
            }

            //second loop to respect the display order
            foreach ($types as $v) {
                $includeActiveKey='includePostsOfType_'.$v['id'].'_Active';
                $includeArrayKey='includePostsOfType_'.$v['id'];
                $exceptActiveKey='exceptPostsOfType_'.$v['id'].'_Active';
                $exceptArrayKey='exceptPostsOfType_'.$v['id'];

                if ($snippet[$includeActiveKey] == 1 && in_array(-1, $snippet[$includeArrayKey])) {
                    if ($snippet[$exceptActiveKey] == 1 && count($snippet[$exceptArrayKey]) == 0) {
                        $tcmp->Options->pushErrorMessage('Error.ExcludeSelectAtLeastOne', $v['name']);
                    }
                }
            }
        } else {
            foreach($types as $v) {
                $exceptActiveKey='exceptPostsOfType_'.$v['id'].'_Active';
                $exceptArrayKey='exceptPostsOfType_'.$v['id'];

                if(isset($snippet[$exceptActiveKey])
                    && $snippet[$exceptActiveKey]==1
                    && count($snippet[$exceptArrayKey])==0) {
                    $tcmp->Options->pushErrorMessage('Error.ExcludeSelectAtLeastOne', $v['name']);
                }
            }
        }
    }
}
function tcmp_ui_editor() {
    global $tcmp;

    $tcmp->Form->prefix='Editor';
    $id=TCMP_ISQS('id', 0);
    if($id==0 && $tcmp->Manager->isLimitReached(FALSE)) {
        $tcmp->Utils->redirect(TCMP_TAB_MANAGER_URI);
    }

    $snippet=$tcmp->Manager->get($id, TRUE);
    if (wp_verify_nonce(TCMP_QS('tcmp_nonce'), 'tcmp_nonce')) {
        foreach ($snippet as $k=>$v) {
            $snippet[$k]=TCMP_QS($k);
            if (is_string($snippet[$k])) {
                $snippet[$k]=stripslashes($snippet[$k]);
            }
        }

	    tcmp_ui_editor_check($snippet);
        if (!$tcmp->Options->hasErrorMessages()) {
            $snippet=$tcmp->Manager->put($snippet['id'], $snippet);
            /*if ($id <= 0) {
                $tcmp->Options->pushSuccessMessage('Editor.Add', $snippet['id'], $snippet['name']);
                $snippet=$tcmp->Manager->get('', TRUE);
            } else {
                $tcmp->Utils->redirect(TCMP_PAGE_MANAGER.'&id='.$id);
                exit();
            }*/
            $id=$snippet['id'];
            $tcmp->Utils->redirect(TCMP_PAGE_MANAGER.'&id='.$id);        }
    }
    $tcmp->Options->writeMessages()
    ?>
    <script>
        jQuery(function(){
            //enable/disable some part of except creating coherence
            function tcmCheckVisible() {
                var $mode=jQuery('[name=trackMode]:checked');
                var showTrackCode=false;
                var showTrackConversion=false;
                if($mode.length>0) {
                    if(parseInt($mode.val())!=<?php echo TCMP_TRACK_MODE_CODE ?>) {
                        showTrackConversion=true;
                        jQuery('#position-box').hide();

                        tcmShowHide('.box-track-conversion', false);
                        tcmShowHide('#box-track-conversion-'+$mode.val(), true);
                    } else {
                        showTrackCode=true;
                        jQuery('#position-box').show();
                    }
                }
                tcmShowHide('#box-track-conversion', showTrackConversion);
                tcmShowHide('#box-track-code', showTrackCode);

                var $all=jQuery('[name=trackPage]:checked');
                if($all.length>0 && parseInt($all.val())==<?php echo TCMP_TRACK_PAGE_SPECIFIC ?>) {
                    showExcept=false;
                    jQuery('[type=checkbox]').each(function() {
                        var $check=jQuery(this);
                        var id=TCMP.attr($check, 'id', '');
                        if(TCMP.startsWith(id, 'include')) {
                            var $select=id.replace('_Active', '');
                            $select=TCMP.jQuery($select);

                            isCheck=$check.is(':checked');
                            selection=$select.select2('val');
                            found=false;
                            for(i=0; i<selection.length; i++) {
                                if(parseInt(selection[i])==-1){
                                    found=true;
                                }
                            }

                            var $except=id.replace('_Active', '');
                            $except=$except.replace('Active', '')+'Box';
                            $except=$except.substr('include'.length);
                            $except='except'+$except;
                            $except=jQuery('[id='+$except+']');

                            if(found) {
                                showExcept=true;
                                if($except.length>0) {
                                    $except.show();
                                }
                            } else {
                                if($except.length>0) {
                                    $except.hide();
                                }
                            }
                        }
                    });
                }

                showInclude=false;
                if($all.length==0) {
                    showExcept=false;
                } else {
                    if(parseInt($all.val())==<?php echo TCMP_TRACK_PAGE_ALL ?>) {
                        showExcept=true;
                    } else {
                        showInclude=true;
                    }
                }
                tcmShowHide('#tcmp-except-div', showExcept);
                tcmShowHide('#tcmp-include-div', showInclude);
            }
            function tcmShowHide(selector, show) {
                $selector=jQuery(selector);
                if(show) {
                    $selector.show();
                } else {
                    $selector.hide();
                }
            }

            /*jQuery(".tcmTags").select2({
                placeholder: "Type here..."
                , theme: "classic"
            }).on('change', function() {
                tcmCheckVisible();
            });*/
            jQuery('.tcmLineTags,.tcmp-dropdown').select2({
                placeholder: "Type here..."
                , theme: "classic"
                , width: '550px'
            });

            jQuery('.tcmp-hideShow').click(function() {
                tcmCheckVisible();
            });
            jQuery('.tcmp-hideShow, input[type=checkbox], input[type=radio]').change(function() {
                tcmCheckVisible();
            });
            jQuery('.tcmLineTags').on('change', function() {
                tcmCheckVisible();
            });
            tcmCheckVisible();
        });
    </script>
    <?php

    $tcmp->Form->formStarts();
    $tcmp->Form->hidden('id', $snippet);
    $tcmp->Form->hidden('order', $snippet);

    $tcmp->Form->checkbox('active', $snippet);
    $tcmp->Form->text('name', $snippet);
    $tcmp->Form->editor('code', $snippet);

    $values=array(TCMP_POSITION_HEAD, TCMP_POSITION_BODY, TCMP_POSITION_FOOTER);
    $tcmp->Form->dropdown('position', $snippet, $values, FALSE);
    $values=array(TCMP_DEVICE_TYPE_ALL, TCMP_DEVICE_TYPE_DESKTOP, TCMP_DEVICE_TYPE_MOBILE, TCMP_DEVICE_TYPE_TABLET);
    $tcmp->Form->dropdown('deviceType', $snippet, $values, TRUE);

    $args=array('id'=>'box-track-mode');
    $tcmp->Form->divStarts($args);
    {
        $tcmp->Form->p('Where do you want to add this code?');
        $tcmp->Form->radio('trackMode', $snippet['trackMode'], TCMP_TRACK_MODE_CODE);
        $plugins=$tcmp->Ecommerce->getActivePlugins();
        if(count($plugins)==0) {
            $plugins=array('Ecommerce'=>array(
                'name'=>'Ecommerce'
                , 'id'=>TCMP_PLUGINS_NO_PLUGINS
                , 'version'=>'')
            );
        }
        $tcmp->Form->tagNew=TRUE;
        foreach($plugins as $k=>$v) {
            $ecommerce=$v['name'];
            if(isset($v['version']) && $v['version']!='') {
                $ecommerce.=' (v.'.$v['version'].')';
            }
            $args=array('label'=>$tcmp->Lang->L('Editor.trackMode_1', $ecommerce));
            $tcmp->Form->radio('trackMode', $snippet['trackMode'], $v['id'], $args);
        }
        $tcmp->Form->tagNew=FALSE;

    }
    $tcmp->Form->divEnds();

    $args=array('id'=>'box-track-conversion');
    $tcmp->Form->divStarts($args);
    {
        $tcmp->Form->p('ConversionProductQuestion');
        ?>
        <p style="font-style: italic;"><?php $tcmp->Lang->P('Editor.PositionBlocked') ?></p>
        <?php
        foreach($plugins as $k=>$v) {
            $args=array('id'=>'box-track-conversion-'.$v['id'], 'class'=>'box-track-conversion');
            $tcmp->Form->divStarts($args);
            {
                if($v['id']==TCMP_PLUGINS_NO_PLUGINS) {
                    $plugins=$tcmp->Ecommerce->getPlugins(FALSE);
                    $ecommerce='';
                    foreach($plugins as $k=>$v) {
                        if($ecommerce!='') {
                            $ecommerce.=', ';
                        }
                        $ecommerce.=$k;
                    }
                    $tcmp->Options->pushErrorMessage('Editor.NoEcommerceFound', $ecommerce);
                    $tcmp->Options->writeMessages();
                } else {
                    $postType=$tcmp->Ecommerce->getCustomPostType($v['id']);
                    $keyActive='CTC_'.$v['id'].'_Active';
                    $label=$tcmp->Lang->L('Editor.EcommerceCheck', $v['name'], $v['version']);

                    if($postType!='') {
                        $args=array('post_type'=>$postType, 'all'=>TRUE);
                        $values=$tcmp->Utils->query(TCMP_QUERY_POSTS_OF_TYPE, $args);
                        $keyArray='CTC_'.$v['id'].'_ProductsIds';
                        if(count($snippet[$keyArray])==0) {
                            //when enabled default selected -1
                            $snippet[$keyArray]=array(-1);
                        }

                        $args=array('label'=>$label, 'class'=>'tcmp-select tcmLineTags');
                        $tcmp->Form->labels=FALSE;
                        $tcmp->Form->dropdown($keyArray, $snippet[$keyArray], $values, TRUE, $args);
                        $tcmp->Form->labels=TRUE;
                    } else {
                        $args=array('label'=>$label);
                        $tcmp->Form->checkbox($keyActive, $snippet[$keyActive], 1, $args);
                    }
                }
            }
            $tcmp->Form->divEnds();

            $tcmp->Form->br();
            $tcmp->Form->i('ConversionDynamicFields');
            $tcmp->Form->br();
            $tcmp->Form->br();
        }
    }
    $tcmp->Form->divEnds();

    $args=array('id'=>'box-track-code');
    $tcmp->Form->divStarts($args);
    {
        $tcmp->Form->p('In which page do you want to insert this code?');
        $tcmp->Form->radio('trackPage', $snippet['trackPage'], TCMP_TRACK_PAGE_ALL);
        $tcmp->Form->radio('trackPage', $snippet['trackPage'], TCMP_TRACK_PAGE_SPECIFIC);

        //, 'style'=>'margin-top:10px;'
        $args=array('id'=>'tcmp-include-div');
        $tcmp->Form->divStarts($args);
        {
            $tcmp->Form->p('Include tracking code in which pages?');
            tcmp_formOptions('include', $snippet);
        }
        $tcmp->Form->divEnds();

        $args=array('id'=>'tcmp-except-div');
        $tcmp->Form->divStarts($args);
        {
            $tcmp->Form->p('Do you want to exclude some specific pages?');
            tcmp_formOptions('except', $snippet);
        }
        $tcmp->Form->divEnds();
    }
    $tcmp->Form->divEnds();

    $tcmp->Form->nonce('tcmp_nonce', 'tcmp_nonce');
    tcmp_notice_pro_features();
    $tcmp->Form->submit('Save');
    $tcmp->Form->formEnds();
}

function tcmp_formOptions($prefix, $snippet) {
    global $tcmp;

    $types=$tcmp->Utils->query(TCMP_QUERY_POST_TYPES);
    foreach($types as $v) {
        $args=array('post_type'=>$v['id'], 'all'=>TRUE);
        $values=$tcmp->Utils->query(TCMP_QUERY_POSTS_OF_TYPE, $args);
        //$tcmp->Form->premium=!in_array($v['name'], array('post', 'page'));

        $keyActive=$prefix.'PostsOfType_'.$v['id'].'_Active';
        $keyArray=$prefix.'PostsOfType_'.$v['id'];
        if($snippet[$keyActive]==0 && count($snippet[$keyArray])==0 && $prefix!='except') {
            //when enabled default selected -1
            $snippet[$keyArray]=array(-1);
        }
        $tcmp->Form->checkSelect($keyActive, $keyArray, $snippet, $values);
    }
}
