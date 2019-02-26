<?php
class TCMP_Tabs {
    private $tabs = array();

    function __construct() {
    }
    public function init() {
        global $tcmp;
        if($tcmp->Utils->isAdminUser()) {
            add_action('admin_menu', array(&$this, 'attachMenu'));
            add_filter('plugin_action_links', array(&$this, 'pluginActions'), 10, 2);
            if($tcmp->Utils->isPluginPage()) {
                add_action('admin_enqueue_scripts', array(&$this, 'enqueueScripts'));
            }
        }
    }

    function attachMenu() {
        add_submenu_page('options-general.php'
            , TCMP_PLUGIN_NAME, TCMP_PLUGIN_NAME
            , 'manage_options', TCMP_PLUGIN_SLUG, array(&$this, 'showTabPage'));
    }
    function pluginActions($links, $file) {
        global $tcmp;
        if($file==TCMP_PLUGIN_SLUG.'/index.php'){
            $settings=array();
            $settings[]="<a href='".TCMP_TAB_MANAGER_URI."'>".$tcmp->Lang->L('Settings').'</a>';
            $settings[]="<a href='".TCMP_PAGE_PREMIUM."'>".$tcmp->Lang->L('PREMIUM').'</a>';
            $links=array_merge($settings, $links);
        }
        return $links;
    }
    function enqueueScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jQuery');
        wp_enqueue_script('jquery-ui-sortable');

        $this->wpEnqueueStyle('assets/css/style.css');
        $this->wpEnqueueStyle('assets/deps/select2-3.5.2/select2.css');
        $this->wpEnqueueScript('assets/deps/select2-3.5.2/select2.min.js');
        $this->wpEnqueueScript('assets/deps/starrr/starrr.js');

        $this->wpEnqueueScript('assets/js/library.js');
        $this->wpEnqueueScript('assets/js/plugin.js');
    }
    function wpEnqueueStyle($uri, $name='') {
        if($name=='') {
            $name=explode('/', $uri);
            $name=$name[count($name)-1];
            $dot=strrpos($name, '.');
            if($dot!==FALSE) {
                $name=substr($name, 0, $dot);
            }
            $name=TCMP_PLUGIN_PREFIX.'_'.$name;
        }

        $v='?v='.TCMP_PLUGIN_VERSION;
        wp_enqueue_style($name, TCMP_PLUGIN_URI.$uri.$v);
    }
    function wpEnqueueScript($uri, $name='', $version=FALSE) {
        if($name=='') {
            $name=explode('/', $uri);
            $name=$name[count($name)-1];
            $dot=strrpos($name, '.');
            if($dot!==FALSE) {
                $name=substr($name, 0, $dot);
            }
            $name=TCMP_PLUGIN_PREFIX.'_'.$name;
        }

        $v='?v='.TCMP_PLUGIN_VERSION;
        $deps=array();
        wp_enqueue_script($name, TCMP_PLUGIN_URI.$uri.$v, $deps, $version, FALSE);
    }

    function showTabPage() {
        global $tcmp;

        $v=$tcmp->Options->getShowWhatsNewSeenVersion();
        if($v!=TCMP_WHATSNEW_VERSION) {
            $tcmp->Options->setShowWhatsNew(TRUE);
        }

        $hwb=TCMP_ISQS('hwb', '');
        if($hwb!='') {
            $tcmp->Options->setShowWhatsNew(FALSE);
        }

        $id=TCMP_ISQS('id', 0);
        $defaultTab=TCMP_TAB_MANAGER;
        $tab=TCMP_SQS('tab', $defaultTab);

        if($tcmp->Options->isShowWhatsNew()) {
            $tab=TCMP_TAB_WHATS_NEW;
            $defaultTab=$tab;
            $this->tabs[TCMP_TAB_WHATS_NEW]=$tcmp->Lang->L('What\'s New');
            //$this->tabs[TCMP_TAB_MANAGER]=$tcmp->Lang->L('Start using the plugin!');
        } else {
            if($id>0 || !$tcmp->Manager->isLimitReached(FALSE)) {
                $this->tabs[TCMP_TAB_EDITOR]=$tcmp->Lang->L($id>0 && $tab==TCMP_TAB_EDITOR ? 'Edit Script' : 'Add New Script');
            } elseif($tab==TCMP_TAB_EDITOR) {
                $tab = TCMP_TAB_MANAGER;
            }

            $this->tabs[TCMP_TAB_MANAGER]=$tcmp->Lang->L('Manager');
            $this->tabs[TCMP_TAB_SETTINGS]=$tcmp->Lang->L('Settings');
            $this->tabs[TCMP_TAB_DOCS]=$tcmp->Lang->L('Docs & FAQ');
        }

        ?>

        <div class="wrap" style="margin: 5px;">
            <?php
            $this->showTabs($defaultTab);
            $header='';
            switch ($tab) {
                case TCMP_TAB_EDITOR:
                    $header=($id>0 ? 'Edit' : 'Add');
                    break;
                case TCMP_TAB_WHATS_NEW:
                    $header='';
                    break;
                case TCMP_TAB_MANAGER:
                    $header='Manager';
                    break;
                case TCMP_TAB_SETTINGS:
                    $header='Settings';
                    break;
            }

            if($tcmp->Lang->H($header.'Title')) { ?>
                <h2><?php $tcmp->Lang->P($header . 'Title', TCMP_PLUGIN_VERSION) ?></h2>
                <?php if ($tcmp->Lang->H($header . 'Subtitle')) { ?>
                    <div><?php $tcmp->Lang->P($header . 'Subtitle') ?></div>
                <?php } ?>
                <br/>
            <?php }

            tcmp_ui_first_time();
            ?>
            <div style="float:left; margin:5px;">
                <?php
                $styles=array();
                $styles[]='float:left';
                $styles[]='margin-right:20px';
                if($tab!=TCMP_TAB_WHATS_NEW) {
                    $styles[]='max-width:750px';
                }
                $styles=implode('; ', $styles);
                ?>
                <div id="tcmp-page" style="<?php echo $styles?>">
                    <?php switch ($tab) {
                        case TCMP_TAB_WHATS_NEW:
                            tcmp_ui_whats_new();
                            break;
                        case TCMP_TAB_EDITOR:
                            tcmp_ui_editor();
                            break;
                        case TCMP_TAB_MANAGER:
                            tcmp_ui_manager();
                            break;
                        case TCMP_TAB_SETTINGS:
                            tcmp_ui_track();
                            tcmp_ui_settings();
                            break;
                    } ?>
                </div>
                <?php if($tab!=TCMP_TAB_WHATS_NEW) { ?>
                    <div id="tcmp-sidebar" style="float:left; max-width: 250px;">
                        <?php
                        $count=$this->getPluginsCount();
                        $plugins=array();
                        while(count($plugins)<2) {
                            $id=rand(1, $count);
                            if(!isset($plugins[$id])) {
                                $plugins[$id]=$id;
                            }
                        }

                        $this->drawContactUsWidget();
                        foreach($plugins as $id) {
                            $this->drawPluginWidget($id);
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div style="clear:both"></div>
    <?php }
    function getPluginsCount() {
        global $tcmp;
        $index=1;
        while($tcmp->Lang->H('Plugin'.$index.'.Name')) {
            $index++;
        }
        return $index-1;
    }
    function drawPluginWidget($id) {
        global $tcmp;
        ?>
        <div class="tcmp-plugin-widget">
            <b><?php $tcmp->Lang->P('Plugin'.$id.'.Name') ?></b>
            <br>
            <i><?php $tcmp->Lang->P('Plugin'.$id.'.Subtitle') ?></i>
            <br>
            <ul style="list-style: circle;">
                <?php
                $index=1;
                while($tcmp->Lang->H('Plugin'.$id.'.Feature'.$index)) { ?>
                    <li><?php $tcmp->Lang->P('Plugin'.$id.'.Feature'.$index) ?></li>
                    <?php $index++;
                } ?>
            </ul>
            <a style="float:right;" class="button-primary" href="<?php $tcmp->Lang->P('Plugin'.$id.'.Permalink') ?>" target="_blank">
                <?php $tcmp->Lang->P('PluginCTA')?>
            </a>
            <div style="clear:both"></div>
        </div>
        <br>
    <?php }
    function drawContactUsWidget() {
        global $tcmp;
        ?>
        <b><?php $tcmp->Lang->P('Sidebar.Title') ?></b>
        <ul style="list-style: circle;">
            <?php
            $index=1;
            while($tcmp->Lang->H('Sidebar'.$index.'.Name')) { ?>
                <li>
                    <a href="<?php $tcmp->Lang->P('Sidebar'.$index.'.Url')?>" target="_blank">
                        <?php $tcmp->Lang->P('Sidebar'.$index.'.Name')?>
                    </a>
                </li>
                <?php $index++;
            } ?>
        </ul>
    <?php }
    function showTabs($defaultTab) {
        global $tcmp;
        $tab=$tcmp->Check->of('tab', $defaultTab);
        if($tab==TCMP_TAB_DOCS) {
            $tcmp->Utils->redirect(TCMP_TAB_DOCS_URI);
        }
        if($tcmp->Options->isShowWhatsNew()) {
            $tab=TCMP_TAB_WHATS_NEW;
        }

        ?>
        <h2 class="nav-tab-wrapper" style="float:left; width:97%;">
            <?php
            foreach ($this->tabs as $k=>$v) {
                $active = ($tab==$k ? 'nav-tab-active' : '');
                $style='';
                $target='_self';
                if($tcmp->Options->isShowWhatsNew() && $k==TCMP_TAB_MANAGER) {
                    $active='';
                    $style='background-color:#F2E49B';
                }
                if($k==TCMP_TAB_DOCS) {
                    $target='_blank';
                    $style='background-color:#F2E49B';
                }
                ?>
                <a style="float:left; margin-left:10px; <?php echo $style?>" class="nav-tab <?php echo $active?>" target="<?php echo $target ?>" href="?page=<?php echo TCMP_PLUGIN_SLUG?>&tab=<?php echo $k?>"><?php echo $v?></a>
            <?php
            }
            ?>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.2.0/css/font-awesome.min.css">
            <style>
                .starrr {display:inline-block}
                .starrr i{font-size:16px;padding:0 1px;cursor:pointer;color:#2ea2cc;}
            </style>
            <div style="float:right; display:none;" id="rate-box">
                <span style="font-weight:700; font-size:13px; color:#555;"><?php $tcmp->Lang->P('Rate us')?></span>
                <div id="tcmp-rate" class="starrr" data-connected-input="tcmp-rate-rank"></div>
                <input type="hidden" id="tcmp-rate-rank" name="tcmp-rate-rank" value="5" />
                <?php  $tcmp->Utils->twitter('intellywp') ?>
            </div>
            <script>
                jQuery(function() {
                    jQuery(".starrr").starrr();
                    jQuery('#tcmp-rate').on('starrr:change', function(e, value){
                        var url='https://wordpress.org/support/view/plugin-reviews/tracking-code-manager?rate=5#postform';
                        window.open(url);
                    });
                    jQuery('#rate-box').show();
                });
            </script>
        </h2>
        <div style="clear:both;"></div>
    <?php }
}

