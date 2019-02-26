<?php
//column renderer
function tcmp_ui_manager_column($active, $values=NULL, $hide=FALSE) {
    global $tcmp;
    ?>
    <td style="text-align:center;">
        <?php
        if($hide) {
            $text='-';
        } else {
            if($active) {
                $text='<span style="font-weight:bold; color:green">'.$tcmp->Lang->L('Yes').'</span>';
            } else {
                $text='<span style="font-weight:bold; color:red">'.$tcmp->Lang->L('No').'</span>';
            }
            if($active && $values) {
                if(!is_array($values)) {
                    $text.='&nbsp;{'.$values.'}';
                } elseif(count($values)>0) {
                    $what=implode(',', $values);
                    if($what!='') {
                        $text.='&nbsp;['.$what.']';
                    }
                }
            }
        }
        echo $text;
        ?>
    </td>
<?php
}

function tcmp_ui_manager() {
    global $tcmp;

    $id=TCMP_ISQS('id', 0);
    if (TCMP_SQS('action')=='delete' && $id>0 && wp_verify_nonce(TCMP_QS('tcmp_nonce'), 'tcmp_delete')) {
        $snippet=$tcmp->Manager->get($id);
        if ($tcmp->Manager->remove($id)) {
            $tcmp->Options->pushSuccessMessage('CodeDeleteNotice', $id, $snippet['name']);
        }
    } else if($id!='') {
        $snippet=$tcmp->Manager->get($id);
	if($tcmp->Utils->is('action', 'toggle') && $id>0 && wp_verify_nonce(TCMP_QS('tcmp_nonce'), 'tcmp_toggle')) {
            $snippet['active']=($snippet['active']==0 ? 1 : 0);
            $tcmp->Manager->put($snippet['id'], $snippet);
        }
        $tcmp->Options->pushSuccessMessage('CodeUpdateNotice', $id, $snippet['name']);
    }

    $tcmp->Manager->isLimitReached(TRUE);
    $tcmp->Options->writeMessages();

    //controllo che faccio per essere retrocompatibile con la prima versione
    //dove non avevo un id e salvavo tutto con il con il nome quindi una stringa
    $snippets=$tcmp->Manager->keys();
    foreach($snippets as $v) {
        $snippet=$tcmp->Manager->get($v, FALSE, TRUE);
        if(!$snippet) {
            $tcmp->Manager->remove($v);
        } elseif(!is_numeric($v)) {
            $tcmp->Manager->remove($v);
            $tcmp->Manager->put('', $snippet);
        }
    }
    $snippets=$tcmp->Manager->values();
    if (count($snippets)>0) { ?>
        <div style="float:left;">
            <form method="get" action="" style="margin:5px; float:left;">
                <input type="hidden" name="page" value="<?php echo TCMP_PLUGIN_SLUG?>" />
                <input type="hidden" name="tab" value="<?php echo TCMP_TAB_EDITOR?>" />
                <input type="submit" class="button-primary" value="<?php $tcmp->Lang->P('Button.Add')?>" />
            </form>
        </div>
        <div style="clear:both;"></div>

        <style>
            .widefat th {
                font-weight: bold!important;
            }
            table input {
                font-size: 13px;
            }
            .widefat thead td, .widefat thead th {
                border-bottom: 0px!important;
            }
        </style>
        <table class="widefat fixed" style="width:100%" id="tblSortable">
            <thead>
                <tr>
                    <th style="width:30px;">#N</th>
                    <th style="width:50px; text-align:center;"><?php $tcmp->Lang->P('Active?')?></th>
                    <th><?php $tcmp->Lang->P('Name')?></th>
                    <th><?php $tcmp->Lang->P('Where?')?></th>
                    <th style="text-align:center;"><?php $tcmp->Lang->P('Shortcode')?></th>
                    <th style="text-align:center;"><?php $tcmp->Lang->P('Actions')?></th>
                </tr>
            </thead>
            <tbody class="table-body">
            <?php
            $i=1;
            foreach ($snippets as $snippet) {
                $bClass=(($i%2)==1 ? 'odd' : 'even');
                ?>
                <tr class="<?php echo $bClass?>" id="row_<?php echo $snippet['id']?>">
                    <td>#<?php echo $i++ ?></td>
                    <td style="text-align:center;">
                        <?php
                        $color='red';
                        $text='No';
                        $question='QuestionActiveOn';
                        if($snippet['active']==1) {
                            $color='green';
                            $text='Yes';
                            $question='QuestionActiveOff';
                        }
                        $text='<span style="font-weight:bold; color:'.$color.'">'.$tcmp->Lang->L($text).'</span>';
                        ?>
                        <a onclick="return confirm('<?php echo $tcmp->Lang->L($question)?>');" href="<?php echo TCMP_TAB_MANAGER_URI?>&tcmp_nonce=<?php echo esc_attr(wp_create_nonce('tcmp_toggle')); ?>&action=toggle&id=<?php echo $snippet['id'] ?>">
                            <?php echo $text?>
                        </a>
                    </td>
                    <td><?php echo $snippet['name']?></td>
		            <td>
                        <?php
                        if($tcmp->Manager->isModeScript($snippet)) {
                            if($tcmp->Manager->isPageEverywhere($snippet)) {
                                $text='Everywhere';
                            } else {
                                $text='Specific Pages';
                            }
                        } else {
                            $text='Conversion';
                        }
                        $tcmp->Lang->P($text);
                        ?>
                    </td>
                    <td style="text-align:center;">
                        <input type="text" style="width:110px; text-align:center;" value='[tcm id="<?php echo esc_html($snippet['id']); ?>"]' readonly="readonly" class="tcmp-select-onfocus" />
                    </td>
                    <td style="text-align:center;">
                        <input type="button" class="button button-secondary" value="<?php $tcmp->Lang->P('Edit')?>" onclick="location.href='<?php echo TCMP_TAB_EDITOR_URI?>&id=<?php echo $snippet['id'] ?>';"/>
                        <input type="button" class="button button-secondary" value="<?php $tcmp->Lang->P('Delete?')?>" onclick="TCMP_btnDeleteClick(<?php echo $snippet['id'] ?>)"/>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <script>
            function TCMP_btnDeleteClick(id) {
                var success=confirm('<?php echo $tcmp->Lang->L('Question.DeleteQuestion')?>');
                if(success) {
                    var href='<?php echo TCMP_TAB_MANAGER_URI?>&tcmp_nonce=<?php echo esc_attr(wp_create_nonce('tcmp_delete')); ?>&action=delete&id=';
                    location.href=href+id;
                }
            }
        </script>
    <?php
        tcmp_notice_pro_features();
        if(count($snippets)>1) {
            tcmp_manager_sortable_scripts();
        }
    } else { ?>
        <h2><?php $tcmp->Lang->P('EmptyTrackingList', TCMP_TAB_EDITOR_URI)?></h2>
    <?php }
}
function tcmp_manager_sortable_scripts() {
    ?>
    <style>
        .ui-state-highlight {
            border: 1px dotted red!important;
            background-color: #F4E449!important;
        }
        #tblSortable tbody tr:hover {
            cursor: move!important;
        }
        #tblSortable tbody tr a:hover {
            cursor: hand!important;
        }
    </style>
    <script>
        jQuery(function() {
            var $sortable=jQuery("#tblSortable .table-body");
            $sortable.sortable({
                tolerance:'intersect'
                , cursor:'move'
                , items:'tr'
                , placeholder:'ui-state-highlight'
                , nested: 'tbody'
                , update: function(event, ui) {
                    var orders=$sortable.sortable('serialize');
                    var data={action: 'TCMP_changeOrder', order: orders};
                    jQuery.post(ajaxurl, data, function(result) {
                        console.log(result);
                    });
                }
            });
            $sortable.disableSelection();
        });
    </script>
<?php
}