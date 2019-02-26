<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TCMP_Utils {
    const FORMAT_DATETIME='d/m/Y H:i';
    const FORMAT_COMPACT_DATETIME='d/m H:i';
    const FORMAT_DATE='d/m/Y';
    const FORMAT_TIME='H:i';

    const FORMAT_SQL_DATETIME='Y-m-d H:i:s';
    const FORMAT_SQL_DATE='Y-m-d';
    const FORMAT_SQL_TIME='H:i:s';
    
    private $colorIndex;
    private $defaultCurrencySymbol;

    public function __construct() {
        $this->colorIndex=0;
    }

    public function  setDefaultCurrencySymbol($value) {
        $this->defaultCurrencySymbol=$value;
    }
    public function getDefaultCurrencySymbol() {
        return ($this->defaultCurrencySymbol=='' ? 'USD' : $this->defaultCurrencySymbol);
    }
    function format($message, $v1=NULL, $v2=NULL, $v3=NULL, $v4=NULL, $v5=NULL) {
        if($v1 || $v2 || $v3 || $v4 || $v5) {
            $message=sprintf($message, $v1, $v2, $v3, $v4, $v5);
        }
        return $message;
    }
    function startsWith($haystack, $needle) {
        $length=strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function endsWith($haystack, $needle) {
        $length=strlen($needle);
        $start=$length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }
    function substr($text, $start=0, $end=-1) {
        if($end<0) {
            $end=strlen($text);
        }
        $length=$end-$start;
        return substr($text, $start, $length);
    }

    function shortcodeArgs($args, $defaults) {
        $args=$this->sanitizeShortcodeKeys($args);
        $defaults=$this->sanitizeShortcodeKeys($defaults);
        $args=shortcode_atts($defaults, $args);
        return $args;
    }
    function sanitizeShortcodeKeys($array) {
        $result=array();
        foreach($array as $k=>$v) {
            if(is_string($k)) {
                $k=strtolower($k);
            }
            $result[$k]=$v;
        }
        return $result;
    }

    //WOW! $end is passed as reference due to we can change it if we found \n character after
    //substring to avoid having these characters after or before
    function substrln($text, $start=0, &$end=-1) {
        if($end<0) {
            $end=strlen($text);
        }

        do {
            $loop=FALSE;
            $c=substr($text, $end, 1);
            if($c=="\n" || $c=="\r" || $c==".") {
                $end += 1;
                $loop=TRUE;
            }
        } while($loop);

        $length=$end-$start;
        return substr($text, $start, $length);
    }

    function toCommaArray($array, $isNumeric=TRUE, $isTrim=TRUE) {
        if(is_string($array)) {
            if(trim($array)=='') {
                $array=array();
            } else {
                $array=explode(',', $array);
            }
        } elseif(is_numeric($array)) {
            $array=array($array);
        }
        if(!is_array($array)) {
            $array=array();
        }
        for($i=0; $i<count($array); $i++) {
            if($isTrim) {
                $array[$i]=trim($array[$i]);
            }
            if($isNumeric) {
                $array[$i]=floatval($array[$i]);
            }
        }
        return $array;
    }
    function inAllArray($search, $where) {
        return ($this->inArray(-1, $where) || $this->inArray($search, $where));
    }
    function inArray($search, $where) {
        $result=FALSE;
        $where=$this->toArray($where);
        $search=$this->toArray($search);
        if(count($where)==0 || count($search)==0) {
            return FALSE;
        }

        foreach($where as $v) {
            $v.='';
            foreach($search as $c) {
                $c.='';
                if($v===$c) {
                    $result=TRUE;
                    break;
                }
            }
            /*$v=intval($v);
            if ($v<0) {
                //if one element of the array have -1 value means i select "all" option
                $result=TRUE;
                break;
            }*/

            if($result) {
                break;
            }
        }
        return $result;
    }

    function is($name, $compare, $default='', $ignoreCase=TRUE) {
        $what=$this->qs($name, $default);
        $result=FALSE;
        if(is_string($compare)) {
            $compare=explode(',', $compare);
        }
        if($ignoreCase){
            $what=strtolower($what);
        }

        foreach($compare as $v) {
            if($ignoreCase){
                $v=strtolower($v);
            }
            if($what==$v) {
                $result=TRUE;
                break;
            }
        }
        return $result;
    }

    public function twitter($name) {
        ?>
        <a href="https://twitter.com/<?php echo $name?>" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @<?php echo $name?></a>
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
    <?php
    }
    
    public function sort($isAssociative, $a1, $a2=NULL, $a3=NULL, $a4=NULL, $a5=NULL) {
        $array=$this->merge($isAssociative, $a1, $a2, $a3, $a4, $a5);
        ksort($array);
        return $array;
    }
    public function merge($isAssociative, $a1, $a2=NULL, $a3=NULL, $a4=NULL, $a5=NULL) {
        $result=array();
        if($isAssociative) {
            $array=array($a1, $a2, $a3, $a4, $a5);
            foreach($array as $a) {
                if(!is_array($a)) {
                    continue;
                }

                foreach($a as $k=>$v) {
                    if(!isset($result[$k])) {
                        $result[$k]=$v;
                    }
                }
            }
        } else {
            $result=array_merge($a1, $a2, $a3, $a4, $a5);
        }
        return $result;
    }

    function bget($instance, $name, $index=-1) {
        $v=$this->get($instance, $name, FALSE, $index);
        $v=$this->isTrue($v);
        return $v;
    }
    function dget($instance, $name, $index=-1) {
        $v=$this->get($instance, $name, FALSE, $index);
        $v=$this->parseDateToTime($v);
        return $v;
    }
    function aget($instance, $name, $index=-1) {
        $v=$this->get($instance, $name, FALSE, $index);
        $v=$this->toArray($v);
        return $v;
    }
    function get($instance, $name, $default='', $index=-1) {
        if($this->isEmpty($instance)) {
            return $default;
        }
        $options=array();
        //assolutamente da non fare altrimenti succede un disastro in quanto i metodi del inputComponent
        //gli passano come name il valore...insomma un disastro!
        //$name=$this->toArray($name);
        //$name=implode('.', $name);

        $result=$default;
        if(is_array($instance) || is_object($instance)) {
            if($this->propertyReflect($instance, $name, $options)) {
                $result=$options['get'];
            }
        }
        if($index>-1) {
            $result=$this->toArray($result);
            if(isset($result[$index])) {
                $result=$result[$index];
            } else {
                $result=$default;
            }
        }
        return $result;
    }
    function has($instance, $name) {
        return $this->propertyReflect($instance, $name);
    }
    function set(&$instance, $name, $value) {
        $options=array('set'=>$value);
        $result=$this->propertyReflect($instance, $name, $options);
        if(!$result) {
        }
        return $result;
    }
    function iget($array, $name, $default='') {
        return floatval($this->get($array, $name, $default));
    }

    private function propertyReflect(&$instance, $name, &$options=array()) {
        if(!is_object($instance) && !is_array($instance)) {
            return FALSE;
        }

        if($options===FALSE || !is_array($options)) {
            $options=array();
        }
        $options['has']=FALSE;
        $options['get']=FALSE;

        $current=$instance;
        $names=explode('.', $name);
        $value=FALSE;
        $result=TRUE;
        for($i=0; $i<count($names); $i++) {
            $name=$names[$i];
            if(!is_object($current) && !is_array($current)) {
                return FALSE;
            }
            if(is_null($current)) {
                return FALSE;
            }

            if(is_object($current)) {
                if(get_class($current)=='stdClass') {
                    if(isset($current->$name)) {
                        $value=$current->$name;
                    } else {
                        $result=FALSE;
                    }
                } else {
                $r=new ReflectionClass($current);
                try {
                    if($r->getProperty($name)!==FALSE) {
                        $value=$current->$name;
                    } else {
                        $result=FALSE;
                    }
                } catch(Exception $ex) {
                        if(isset($current->$name)) {
                            $value=$current->$name;
                        } else {
                    $result=FALSE;
                        }
                    }
                }
            } elseif(is_array($current)) {
                if(isset($current[$name])) {
                    $value=$current[$name];
                } else {
                    $result=FALSE;
                }
            }

            if(!$result) {
                break;
            } elseif($i<(count($names)-1)) {
                $current=$value;
            } else {
                $options['get']=$value;
                if(isset($options['set'])) {
                    if(is_object($current)) {
                        $current->$name=$options['set'];
                    } elseif(is_array($current)) {
                        $current[$name]=$options['set'];
                    }
                }
            }
        }
        return $result;
    }
    function isTrue($value) {
        $result=FALSE;
        if(is_bool($value)) {
            $result=(bool)$value;
        } elseif(is_numeric($value)) {
            $result=floatval($value)>0;
        } else {
            $result=strtolower($value);
            if($result=='ok' || $result=='yes' || $result=='true' || $result=='on') {
                $result=TRUE;
            } else {
                $result=FALSE;
            }
        }
        return $result;
    }
    function aqs($prefix, $removePrefix=TRUE) {
        $result=array();
        $array=$this->merge(TRUE, $_POST, $_GET);
        foreach($array as $k=>$v) {
            if($this->startsWith($k, $prefix)) {
                if($removePrefix) {
                    $k=substr($k, strlen($prefix));
                }
                $result[$k]=$v;
            }
        }
        return $result;
    }
    function iqs($name, $default=0, $min=0, $max=0) {
        $result=floatval($this->qs($name, $default));
        if($min!=$max) {
            if($result<$min) {
                $result=$min;
            } elseif($result>$max) {
                $result=$max;
            }
        }
        return $result;
    }
    function dqs($name, $default=0) {
        $result=($this->qs($name, $default));
        $result=$this->parseDateToTime($result);
        if($result==0) {
            $result=$default;
        }
        return $result;
    }
    //per ottenere un campo dal $_GET oppure dal $_POST
    function qs($name, $default='') {
        $result=$default;
        if(isset($_POST[$name])) {
            $result=$_POST[$name];
        } elseif (isset($_GET[$name])) {
            $result=$_GET[$name];
        }

        if (is_string($result)) {
            //The superglobals $_GET and $_REQUEST are already decoded.
            //Using urldecode() on an element in $_GET or $_REQUEST
            //could have unexpected and dangerous results.
            //$result=urldecode($result);
            $result=trim($result);
        }
        return $result;
    }

    var $_taxonomyType;
    private function getTermLink($id) {
        if(is_array($id)) {
            foreach($id as $v) {
                $id=$v;
                break;
            }
        }
        if(is_numeric($id)) {
            $id=intval($id);
        }
        $result=get_term_link($id, $this->_taxonomyType);
        if(is_wp_error($result)) {
            $result=FALSE;
        }
        return $result;
    }
    function query($query, $options=NULL) {
        global $tcmp, $wpdb;

        $parent='';
        $defaults=array(
            'post_type' => ''
            , 'all' => FALSE
            , 'select' => FALSE
            , 'taxonomy'=>''
        );
        $options=wp_parse_args($options, $defaults);

        if(!isset($options['type'])) {
            if($options['post_type']!='') {
                $options['type']=$options['post_type'];
            } elseif($options['taxonomy']!='') {
                $options['type']=$options['taxonomy'];
            } else {
                $options['type']='';
            }
        }

        if($query==TCMP_QUERY_CONVERSION_PLUGINS) {
            $array=$tcmp->Ecommerce->getPlugins(FALSE);
            $result=array();
            foreach($array as $k=>$v) {
                $result[]=$v;
            }
        } else {
            $key=array('Query', $query.'_'.$options['type']);
            $result=$tcmp->Options->getCache($key);
            if (!is_array($result) || count($result) == 0) {
                $q=NULL;
                $id='ID';
                $name='post_title';
                $function='';
                switch ($query) {
                    case TCMP_QUERY_POSTS_OF_TYPE:
                        //$options=array('posts_per_page'=>-1, 'post_type'=>$args['post_type']);
                        //$q=get_posts($options);
                        $sql="SELECT ID, post_title FROM ".$wpdb->prefix."posts WHERE post_status='publish' AND post_type='".$options['type']."' ORDER BY post_title";
                        $q=$wpdb->get_results($sql);
                        $function='get_permalink';
                        break;
                    case TCMP_QUERY_CATEGORIES:

                        break;
                    case TCMP_QUERY_TAGS:

                        break;
                    case TCMP_QUERY_TAXONOMIES_OF_TYPE:

                        break;
                }

                $result=array();
                if ($q) {
                    if(!is_wp_error($q)) {
                        foreach ($q as $v) {
                            $item=array('id' => $v->$id, 'name' => $v->$name);
                            if($parent!='') {
                                $item['parent']=$v->$parent;
                            }
                            $result[]=$item;
                        }
                    }
                } elseif ($query==TCMP_QUERY_POST_TYPES) {
                    global $wp_post_types;
                    $result=array();
                    foreach($wp_post_types as $k=>$v) {
                        $isPublic=$tcmp->Utils->bget($v, 'public');
                        if($isPublic && $k!='attachment') {
                            $v=$tcmp->Utils->get($v, 'labels.singular_name');
                            if($k=='post' || $k=='page') {
                                $result[$k]=$v;
                            }
                        }
                    }
                    $result=$tcmp->Utils->toFormatListArrayFromListObjects($result, FALSE, '{text} ({id})');
                } elseif($query==TCMP_QUERY_TAXONOMY_TYPES) {

                }

                if($this->functionExists($function)) {
                    for($i=0; $i<count($result); $i++) {
                        $v=$result[$i];
                        $v['url']=$this->functionCall($function, array($v['id']));
                        $result[$i]=$v;
                    }
                }
                $tcmp->Options->setCache($key, $result);
            }
        }

        if ($options['all']) {
            $first=array();
            $first[]=array('id'=>-1, 'name'=>'['.$tcmp->Lang->L('All').']', 'url'=>'');
            $result=array_merge($first, $result);
        }
        if ($options['select']) {
            $first=array();
            $first[]=array('id'=>0, 'name'=>'['.$tcmp->Lang->L('Select').']', 'url'=>'');
            $result=array_merge($first, $result);
        }
        $result=$this->sortOptions($result);
        $this->_taxonomyType='';
        return $result;
    }

    //wp_parse_args with null correction
    function parseArgs($options, $defaults) {
        if (is_null($options)) {
            $options=array();
        } elseif(is_object($options)) {
            $options=(array)$options;
        } elseif(!is_array($options)) {
            $options=array();
        }
        if (is_null($defaults)) {
            $defaults=array();
        } elseif(is_object($defaults)) {
            $defaults=(array)$defaults;
        } elseif(!is_array($defaults)) {
            $defaults=array();
        }

        foreach($defaults as $k=>$v) {
            if(is_null($v)) {
                unset($defaults[$k]);
            }
        }

        foreach($options as $k=>$v) {
            if(isset($defaults[$k])) {
                if (is_null($v)) {
                    //so can take the default value
                    unset($options[$k]);
                } elseif (is_string($v) && ($v==='') && isset($defaults[$k]) && is_array($defaults[$k])) {
                    //a very strange case, i have a blank string for rappresenting an empty array
                    unset($options[$k]);
                } else {
                    unset($defaults[$k]);
                }
            }
        }
        foreach($defaults as $k=>$v) {
            $options[$k]=$v;
        }
        return $options;
    }

    function redirect($location) {
        if($location=='') {
            return;
        }
        //seems that if you have installed xdebug (or some version of it) doesnt work so js added
        if(!headers_sent()) {
            wp_redirect($location);
        }
        ?>
        <script> window.location.replace('<?php echo $location?>'); </script>
    <?php
        die();
    }

    //return the element inside array with the specified key
    function getArrayValue($key, $array, $value='') {
        $result=FALSE;
        if (isset($array[$key])) {
            $result=$array[$key];
            $result['name']=$key;
        }
        if($result!==FALSE && $value!='') {
            if(isset($result[$value])) {
                $result=$result[$value];
            }
        }
        return $result;
    }

    var $_sortField;
    var $_ignoreCase;
    function aksort(&$array, $sortField='name', $ignoreCase=TRUE) {
        $this->_sortField=$sortField;
        $this->_ignoreCase=$ignoreCase;
        usort($array, array($this, "aksortCompare"));
    }
    //not thread-safe!
    private function aksortCompare($a, $b) {
        if ($a===$b || $a==$b) {
            return 0;
        }

        $result=0;
        $a=$a[$this->_sortField];
        $b=$b[$this->_sortField];
        if(is_numeric($a) && is_numeric($b)) {
            $result=($a < $b) ? -1 : 1;
        } else {
            $a.='';
            $b.='';
            if($this->_ignoreCase) {
                $result=strcasecmp($a, $b);
            } else {
                $result=strcmp($a.'', $b);
            }
        }
        return $result;
    }

    function printScriptCss() {
        global $tcmp;
        $uri=get_bloginfo('wpurl');
        $tcmp->Tabs->enqueueScripts();
        //wp_enqueue_style('buttons', $uri.'/wp-includes/css/buttons.min.css');
        //wp_enqueue_style('editor', $uri.'/wp-includes/css/editor.min.css');
        //wp_enqueue_style('jquery-ui-dialog', $uri.'/wp-includes/css/jquery-ui-dialog.min.css');
        $styles='dashicons,admin-bar,buttons,media-views,wp-admin,wp-auth-check,wp-color-picker';
        $styles=explode(',', $styles);
        foreach($styles as $v) {
            wp_enqueue_style($v);
        }

        remove_all_actions('wp_print_scripts');
        print_head_scripts();
        print_admin_styles();
    }

    public function formatCustomDate($time, $format) {
        $time=$this->parseDateToTime($time);
        if($time>0) {
            $time=date($format, $time);
        } else {
            $time='';
        }
        return $time;
    }

    public function formatDatetime($time='now') {
        return $this->formatCustomDate($time, TCMP_Utils::FORMAT_DATETIME);
    }
    public function formatCompactDatetime($time='now') {
        return $this->formatCustomDate($time, TCMP_Utils::FORMAT_COMPACT_DATETIME);
    }
    public function formatDate($time='date') {
        return $this->formatCustomDate($time, TCMP_Utils::FORMAT_DATE);
    }
    public function formatSmartDatetime($time='now') {
        $time=$this->parseDateToTime($time);
        $result='';
        if($time>0) {
            $h=intval(date('H', $time));
            $i=intval(date('i', $time));
            $s=intval(date('s', $time));
            if($h==0 && $i==0 && $s==0) {
                $result=$this->formatDate($time);
            } else {
                $result=$this->formatDatetime($time);
            }
        }
        return $result;
    }
    public function formatTime($time='now') {
        return $this->formatCustomTime($time, TCMP_Utils::FORMAT_TIME);
    }
    public function formatSqlDatetime($time='now') {
        return $this->formatCustomDate($time, TCMP_Utils::FORMAT_SQL_DATETIME);
    }
    public function formatSqlDate($time='date') {
        return $this->formatCustomDate($time, TCMP_Utils::FORMAT_SQL_DATE);
    }
    public function formatSqlTime($time='now') {
        return $this->formatCustomTime($time, TCMP_Utils::FORMAT_SQL_TIME);
    }

    private function formatCustomTime($time, $format) {
        $time=$this->parseDateToTime($time);
        if($time>86400) {
            $h=date('H', $time);
            $i=date('i', $time);
            $s=date('s', $time);
            $time=$h*3600+$i*60+$s;
        }

        $s=$time%60;
        $time=($time-$s)/60;
        $i=$time%60;
        $h=($time-$i)/60;
        $s=str_pad($s, 2, "0", STR_PAD_LEFT);
        $i=str_pad($i, 2, "0", STR_PAD_LEFT);
        $h=str_pad($h, 2, "0", STR_PAD_LEFT);
        $format=str_replace('H', $h, $format);
        $format=str_replace('i', $i, $format);
        $format=str_replace('s', $s, $format);
        return $format;
    }

    public function parseNumber($what, $default=0) {
        $result=$default;
        if(is_array($what)) {
            if(count($what)>0) {
                $result=doubleval($what[0]);
            }
        }
        elseif(is_numeric($what)) {
            $result=doubleval($what);
        } elseif(is_string($what) || is_bool($what)) {
            $result=($this->isTrue($what) ? 1 : 0);
        }
        return $result;
    }
    public function parseDateToArray($date) {
        global $tcmp;

        $pm=FALSE;
        $date=strtoupper(trim($date));
        if($tcmp->Utils->endsWith($date, 'AM')) {
            $date=substr($date, 0, strlen($date)-2);
            $date=trim($date);
        } elseif($tcmp->Utils->endsWith($date, 'PM')) {
            $date=substr($date, 0, strlen($date)-2);
            $date=trim($date);
            $pm=TRUE;
        }

        $date=explode(' ', $date);
        if(count($date)==1) {
            $result=array();
            $date=$date[0];
            $date=str_replace("/", "-", $date);
            if(strpos($date, '-')!==FALSE) {
                $date=explode('-', $date);
                if(count($date)>=3) {
                    $d=intval($date[0]);
                    $m=intval($date[1]);
                    $y=intval($date[2]);
                    if($d>1900) {
                        $t=$d;
                        $d=$y;
                        $y=$t;
                    }
                    if($y>0 && $m>0 && $d>0) {
                        $result['y']=$y;
                        $result['m']=$m;
                        $result['d']=$d;
                    }
                }
            } elseif(strpos($date, ':')!==FALSE) {
                $date=explode(':', $date);
                if(count($date)==2) {
                    $date[]=0;
                }
                if(count($date)>=3) {
                    $h=intval($date[0]);
                    $i=intval($date[1]);
                    $s=intval($date[2]);
                    if($h>=0 && $i>=0 && $s>=0) {
                        $result['h']=$h;
                        $result['i']=$i;
                        $result['s']=$s;
                    }
                }
            }
        } else {
            $a1=$this->parseDateToArray($date[0]);
            $a2=$this->parseDateToArray($date[1]);
            $result=$tcmp->Utils->parseArgs($a1, $a2);
        }

        if($pm && isset($result['h'])) {
            $result['h']=intval($result['h'])+12;
        }
        return $result;
    }
    public function parseDateToTime($date) {
        global $tcmp;
        if(is_numeric($date) || trim($date)=='') {
            $date=intval($date);
            return $date;
        }

        $date=strtolower($date);
        if($date=='now') {
            $date=time();
            return $date;
        } elseif($date=='date') {
            $date=strtotime(date('Y-m-d', time()));
            return $date;
        } elseif($date=='time') {
            $date=date('H:i:s', time());
        }
        $result=$this->parseDateToArray($date);
        $defaults=array('y'=>0, 'm'=>0, 'd'=>0, 'h'=>0, 'i'=>0, 's'=>0);
        $a=$tcmp->Utils->parseArgs($result, $defaults);
        if($a['y']==0 && $a['m']==0 && $a['d']==0) {
            $result=$a['h']*3600+$a['i']*60+$a['s'];
        } else {
            $result=mktime($a['h'], $a['i'], $a['s'], $a['m'], $a['d'], $a['y']);
        }
        if($result<0) {
            $result=0;
        }
        return $result;
    }
    public function getIntDate($time, $separator='') {
        $time=$this->parseDateToTime($time);
        if($time>0) {
            if($separator=='') {
                $time=date('Ymd', $time);
                $time=intval($time);
            } else {
                $time=date('Y', $time).$separator.date('m', $time).$separator.date('d', $time);
            }
        }

        return $time;
    }
    public function getIntMinute($h, $m, $separator='') {
        $h=intval($h);
        $m=intval($m);
        if($m<10) {
            $m='0'.$m;
        }
        $result=$h.$separator.$m;
        if($separator=='') {
            $result=intval($result);
        }
        return $result;
    }

    //args can be a string or an associative array if you want
    public function getTextArgs($args, $defaults=array(), $excludes=array()) {
        $result=$args;
        $excludes=$this->toArray($excludes);
        if(is_array($result) && count($result)>0) {
            $result='';
            foreach($args as $k=>$v) {
                if(is_array($v) || is_object($v)) {
                    continue;
                }

                if(count($excludes)==0 || !in_array($k, $excludes)) {
                    $v=trim($v);
                    $result.=' '.$k.'="'.$v.'"';
                }
            }
        } elseif(!$args) {
            $result='';
        }
        if(is_array($defaults) && count($defaults)>0) {
            foreach($defaults as $k=>$v) {
                if(count($excludes)==0 || !in_array($k, $excludes)) {
                    if(!isset($args[$k])) {
                        $v=trim($v);
                        $result.=' '.$k.'="'.$v.'"';
                    }
                }
            }
        }
        return $result;
    }
    public function queryString($uri, $options=array()) {
        if(is_string($options)) {
            $options=explode('&', $options);
            $array=array();
            foreach($options as $v) {
                $v=explode('=', $v);
                if(count($v)>1) {
                    $array[trim($v[0])]=trim($v[1]);
                }
            }
            $options=$array;
        }
        if(!isset($options['root']) || $this->isTrue($options['root'])) {
            $uri=TCMP_BLOG_URL.$uri;
        }
        unset($options['root']);
        $uri=$this->addQueryString($options, $uri);
        return $uri;
    }
    public function iuarray($ids, $positive=FALSE) {
        $array=$this->iarray($ids, $positive);
        $array=array_unique($array);
        sort($array);
        return $array;
    }
    public function iarray($ids, $positive=FALSE) {
        if(is_string($ids)) {
            $ids=explode(',', $ids);
        } elseif(is_numeric($ids)) {
            $ids=array($ids);
        } elseif(!is_array($ids)) {
            $ids=array();
        }

        $array=array();
        foreach($ids as $v) {
            $v=trim($v);
            if($v!='') {
                $v=intval($v);
                if(!$positive || $v>0) {
                    $array[]=$v;
                }
            }
        }
        return $array;
    }
    public function dbarray($ids) {
        if(is_string($ids)) {
            $ids=explode(',', $ids);
        } elseif(is_numeric($ids)) {
            $ids=array($ids);
        } elseif(!is_array($ids)) {
            $ids=array();
        }

        $array=array();
        foreach($ids as $v) {
            $v=trim($v);
            if($v!='') {
                if(is_numeric($v)) {
                    $v=intval($v);
                }
                $array[]=$v;
            }
        }
        return $array;
    }

    function isAssociativeArray($array) {
        if(!is_array($array)) {
            return FALSE;
        }

        $isArray=TRUE;
        $i=0;
        foreach($array as $k=>$v) {
            if($k!==$i) {
                $isArray=FALSE;
                break;
            }
            ++$i;
        }
        return !$isArray;
    }
    function trim($value) {
        if(is_null($value)) {

        } elseif(is_string($value)) {
            $value=trim($value);
        } elseif(is_numeric($value)) {

        } elseif($this->isAssociativeArray($value)) {
            foreach($value as $k=>$v) {
                $value[$k]=$this->trim($v);
            }
        } elseif(is_object($value)) {
            foreach($value as $k=>$v) {
                $value->$k=$this->trim($v);
            }
        } elseif(is_array($value)) {
            for($i=0; $i<count($value); $i++) {
                $v=$value[$i];
                $this->trim($v);
                $value[$i]=$v;
            }
        }
        return $value;
    }
    function implode($open, $close, $join, $array) {
        $result='';
        foreach($array as $v) {
            if($result!='') {
                $result.=$join;
            }
            $result.=$open.$v.$close;
        }
        return $result;
    }
    function toArray($text, $index=-1, $default='') {
        if(is_array($text)) {
            if(is_string($index)) {
                $array=array();
                foreach($text as $v) {
                    $v=$this->get($v, $index, FALSE);
                    if($v!==FALSE) {
                        $array[]=$v;
                    }
                }
            } else {
                $array=$text;
            }
            return $array;
        } elseif(is_numeric($text)) {
            return array($text);
        } elseif(is_bool($text) || $text==='') {
            return array();
        }

        if(($this->startsWith($text, '[') && $this->endsWith($text, ']'))
            || ($this->startsWith($text, '{') && $this->endsWith($text, '}'))) {
            $text=substr($text, 1, strlen($text)-2);
        }
        $text=str_replace('|', ',', $text);
        $text=explode(',', $text);

        //exclude empty string
        $array=array();
        foreach($text as $t) {
            if($t!=='') {
                $array[]=$t;
            }
        }
        $text=$array;
        if($index>-1) {
            $result=$default;
            if(isset($text[$index])) {
                $result=$text[$index];
            }
            $text=$result;
        }
        return $text;
    }
    function dirToFlatArray($dir, &$output) {
        if(!isset($output['dirs'])) {
            $output['dirs']=array();
        }
        if(!isset($output['files'])) {
            $output['files']=array();
        }

        $cdir=scandir($dir);
        foreach ($cdir as $k=>$v) {
            if (!in_array($v,array(".",".."))) {
                if (is_dir($dir.DIRECTORY_SEPARATOR.$v)) {
                    $i=$dir.DIRECTORY_SEPARATOR.$v;
                    array_push($output['dirs'], $i);
                    $this->dirToFlatArray($i, $output);
                } else {
                    $i=$this->getFileInfo($dir.DIRECTORY_SEPARATOR.$v);
                    array_push($output['files'], $i);
                }
            }
        }
    }
    function dirToArray($dir) {
        $result=array();
        if(!is_string($dir)) {
            return $result;
        }

        $cdir=scandir($dir);
        foreach ($cdir as $k=>$v) {
            if (!in_array($v, array(".",".."))) {
                if (is_dir($dir.DIRECTORY_SEPARATOR.$v)) {
                    $result[$v]=$this->dirToArray($dir.DIRECTORY_SEPARATOR.$v);
                } else {
                    $result[]=$this->getFileInfo($dir.DIRECTORY_SEPARATOR.$v);
                }
            }
        }
        return $result;
    }
    function getFileInfo($source) {
        $source=$this->toDirectory($source);
        if(!file_exists($source)) {
            return FALSE;
        }

        $array=explode(DIRECTORY_SEPARATOR, $source);
        $size=filesize($source);
        $source=array_pop($array);
        $directory=implode(DIRECTORY_SEPARATOR, $array).DIRECTORY_SEPARATOR;

        $pos=strrpos($source, ".");
        $ext='';
        if($pos!==FALSE) {
            $name=substr($source, 0, $pos);
            $ext=strtolower(substr($source, $pos));
        }
        $array=array(
            'directory'=>$directory
            , 'name'=>$name
            , 'file'=>$source
            , 'size'=>$size
            , 'textSize'=>$this->getFileTextSize($size)
            , 'ext'=>$ext
            , 'textExt'=>$this->getFileTextExt($source)
        );
        return $array;
    }
    function getFileTextSize($size) {
        $units=array('B', 'KB', 'MB', 'GB');
        for ($i=0; $i<count($units); $i++) {
            if($size<1024) {
                break;
            } else {
                $size/=1024;
            }
        }
        return intval($size).' '.$units[$i];
    }
    function getFileTextExt($source) {
        $ext=strrpos($source, ".");
        if($ext!==FALSE) {
            $ext=strtolower(substr($source, $ext+1));
        } else {
            $ext=$source;
        }
        $ext=strtolower($ext);
        $text='text';
        switch ($ext) {
            case 'doc':
            case 'docx':
            case 'odt':
                $text='word';
                break;
            case 'xls':
            case 'xlsx':
            case 'ods':
                $text='excel';
                break;
            case 'ppt':
            case 'pptx':
            case 'odp':
                $text='powerpoint';
                break;
            case 'zip':
            case 'tar':
            case 'gzip':
            case 'rar':
            case '7z':
                $text='archive';
                break;
            case 'mp3':
            case 'wav':
                $text='audio';
                break;
            case 'mpeg':
            case 'mpg':
            case 'avi':
            case 'mp4':
                $text='video';
                break;
            case 'gif':
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'bmp':
                $text='image';
                break;
            case 'pdf':
                $text='pdf';
                break;
        }
        return $text;
    }
    function match($value, $array, $default='', $ignoreCase=TRUE) {
        $result=$default;
        if($ignoreCase) {
            $value=strtolower($value);
        }
        foreach($array as $k=>$v) {
            $v=$this->toArray($v);
            foreach($v as $c) {
                if($ignoreCase) {
                    $c=strtolower($c);
                }
                if($value==$c || strpos($value, $c)!==FALSE) {
                    $result=$k;
                    break;
                }
            }

            if($result!==$default) {
                break;
            }
        }
        return $result;
    }

    function pickColor() {
        $names=explode('|', 'primary|success|warning|danger|info|alert|system|dark');
        $colors=explode('|', '3498db|70ca63|f6bb42|df5640|3bafda|967adc|37bc9b|666');

        $i=($this->colorIndex%count($colors));
        $names=$names[$i];
        $colors=$colors[$i];
        ++$this->colorIndex;
        return array($names, '#'.$colors);
    }
    function upperUnderscoreCase($text) {
        $text=$this->arrayCase($text);
        $text=implode('_', $text);
        $text=strtoupper($text);
        return $text;
    }
    function lowerUnderscoreCase($text) {
        $text=$this->upperUnderscoreCase($text);
        $text=strtolower($text);
        return $text;
    }
    function toDirectory($file, $mkdirs=FALSE) {
        $file=str_replace("\\", DIRECTORY_SEPARATOR, $file);
        $file=str_replace("/", DIRECTORY_SEPARATOR, $file);
        $file=str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);

        if(is_dir($file) && !file_exists($file) && $mkdirs) {
            mkdir($file, 0777, TRUE);
        }
        return $file;
    }
    function getUploadName($name) {
        if($name=='') {
            return '';
        }

        $name=$this->toDirectory($name);
        $name=explode(DIRECTORY_SEPARATOR, $name);
        $name=$name[count($name)-1];
        $ext='';
        $pos=strpos($name, '.');
        if($pos!==FALSE) {
            $ext=substr($name, $pos);
            $name=substr($name, 0, $pos);
        }

        $buffer='';
        $name=str_split(strtolower($name));
        for($i=0; $i<count($name); $i++) {
            if($name[$i]>='a' && $name[$i]<='z') {
                $buffer.=$name[$i];
            } else {
                $buffer.=' ';
            }
        }
        while(strpos($buffer, '  ')!==FALSE) {
            $buffer=str_replace('  ', ' ', $buffer);
        }
        $buffer=trim($buffer);
        $buffer=str_replace(' ', '-', $buffer);
        $buffer.='-'.date('Ymd-His', time()).$ext;
        return $buffer;
    }
    function toListArrayFromClass($array, $id=FALSE, $value=FALSE) {
        global $tcmp;
        $result=array();
        if($array!==FALSE && count($array)>0) {
            foreach($array as $k=>$v) {
                if($id!==FALSE) {
                    $k=$tcmp->Utils->get($v, $id);
                }
                if($value!==FALSE) {
                    $v=$tcmp->Utils->get($v, $value);
                }

                if($k!='' && $v!='') {
                    $result[]=array(
                        'id'=>$k
                        , 'text'=>$v
                        , 'name'=>$v
                    );
                }
            }
        }
        return $result;
    }
    function toFormatListArrayFromListObjects($array, $idField, $textFormat) {
        global $tcmp;
        $result=array();
        if($array!==FALSE && count($array)>0) {
            foreach($array as $i=>$e) {
                $text=$textFormat;
                $idExists=FALSE;
                if(is_array($e) || is_object($e)) {
                    foreach($e as $k=>$v) {
                        if($k=='id') {
                            $idExists=TRUE;
                        }
                        if(is_array($v)) {
                            $v=implode(', ', $v);
                        }
                        $text=str_replace("{".$k."}", $v, $text);
                    }
                } else {
                    $text=str_replace("{text}", $e, $text);
                }

                $id=$i;
                if($idField!==FALSE && $idField!=='') {
                    $id=$tcmp->Utils->get($e, $idField, '');
                }

                if(!$idExists) {
                    $text=str_replace("{id}", $id, $text);
                }
                if($id!='') {
                    $result[]=array(
                        'id'=>$id
                        , 'text'=>$text
                        , 'name'=>$text
                    );
                }
            }
        }
        return $result;
    }
    function toListArrayFromListObjects($array
        , $idFrom=FALSE, $textFrom='name', $idTo='id', $textTo='text') {

        $result=array();
        foreach($array as $v) {
            $sId=$v;
            $sText=$v;
            if($idFrom!==FALSE) {
                $sId=$this->get($v, $idFrom, FALSE);
                $sText=$this->get($v, $textFrom, FALSE);
            }
            if($sId!==FALSE && $sText!='') {
                if($sId!='') {
                $result[]=array(
                    $idTo=>$sId
                    , $textTo=>$sText
                );
                }
            }
        }
        return $result;
    }
    function toColorListArrayFromListObjects($array, $colors, $id='id', $text='name') {
        global $tcmp;
        $result=array();
        foreach($array as $instance) {
            $sId=$this->get($instance, $id, FALSE);
            $sText=$this->get($instance, $text, FALSE);
            foreach($colors as $color=>$when) {
                $success=FALSE;
                foreach($when['conditions'] as $conditionKey=>$conditionValue) {
                    $conditionValue=$tcmp->Utils->toArray($conditionValue);
                    $c=$this->get($instance, $conditionKey, FALSE);
                    if($c!==FALSE) {
                        $c.='';
                        foreach($conditionValue as $v) {
                            $v.='';
                            if($c===$v) {
                                $success=TRUE;
                                break;
                            }
                        }
                    }
                    if($success) {
                        break;
                    }
                }

                if($success) {
                    $style='color:'.$color.'; ';
                    if(isset($when['bold']) && $when['bold']) {
                        $style.='font-weight:bold; ';
                    }
                    $sText='<span style="'.$style.'">'.$sText.'</span>';
                }
            }
            if($sId!='' && $sText!==FALSE) {
                $result[]=array(
                    'id'=>$sId
                    , 'text'=>$sText
                    , 'name'=>$sText
                );
            }
        }
        return $result;
    }
    function md5() {
        $array=func_get_args();
        $buffer='';
        foreach($array as $v) {
            $buffer.=':)'.$v;
        }
        $buffer=md5($buffer);
        return $buffer;
    }
    function arrayCase($text) {
        $buffer='';
        $array=array();
        $text=str_split($text);
        $prevUpper=FALSE;
        $nextUpper=FALSE;
        foreach($text as $c) {
            if($c>='a' && $c<='z') {
                if($nextUpper) {
                    if($buffer!='') {
                        $array[]=$buffer;
                        $buffer='';
                    }
                    $c=strtoupper($c);
                }
                $buffer.=$c;
                $nextUpper=FALSE;
                $prevUpper=FALSE;
            } elseif($c>='0' && $c<='9') {
                $buffer.=$c;
                $nextUpper=TRUE;
            } elseif($c>='A' && $c<='Z') {
                if(!$prevUpper) {
                    if($buffer!='') {
                        $array[]=$buffer;
                        $buffer='';
                    }
                }
                $buffer.=$c;
                $nextUpper=FALSE;
                $prevUpper=TRUE;
            } else {
                if($buffer!='') {
                    $array[]=$buffer;
                    $buffer='';
                }
                $nextUpper=TRUE;
                $prevUpper=FALSE;
            }
        }
        if($buffer!='') {
            $array[]=$buffer;
        }
        return $array;
    }
    function lowerCamelCase($text) {
        $buffer='';
        if(strpos($text, '_')!==FALSE || strpos($text, '-')!==FALSE) {
            $text=strtolower($text);
        }

        $text=str_split($text);
        $allUpper=TRUE;
        $nextUpper=FALSE;
        foreach($text as $c) {
            if($c>='a' && $c<='z') {
                $allUpper=FALSE;
                if($nextUpper) {
                    $c=strtoupper($c);
                }
                $buffer.=$c;
                $nextUpper=FALSE;
            } elseif($c>='0' && $c<='9') {
                $buffer.=$c;
                $nextUpper=TRUE;
            } elseif($c>='A' && $c<='Z') {
                $buffer.=$c;
                $nextUpper=FALSE;
            } else {
                $nextUpper=TRUE;
            }
        }
        if($allUpper) {
            $buffer=strtolower($buffer);
        } else {
            $buffer=lcfirst($buffer);
        }
        return $buffer;
    }
    function upperCamelCase($text) {
        $text=$this->lowerCamelCase($text);
        $text=ucfirst($text);
        return $text;
    }

    function castStdClass($a) {
        $a=(array)$a;
        $r=new stdClass();
        foreach($a as $k=>$v) {
            $r->$k=$v;
        }
        return $r;
    }
    function castArray($a) {
        $r=$a;
        if(is_object($a)) {
            $r=(array)$a;
        }

        if(!is_array($r)) {
            $r=array();
        }
        return $r;
    }
    public function copyArray($array) {
        $temp=array();
        foreach($array as $k=>$v) {
            $temp[$k]=$v;
        }
        return $temp;
    }
    public function isObject($v) {
        return ($v!==FALSE && !is_null($v) && is_object($v));
    }
    public function isArray($v) {
        return ($v!==FALSE && !is_null($v) && is_array($v));
    }
    public function getConstants($class, $prefix, $reverse=FALSE) {
        global $tcmp;
        if(is_object($class)) {
            $class=get_class($class);
        }
        $class=str_replace('Search', '', $class);
        $class=str_replace('Constants', '', $class);
        $class.='Constants';
        if(!class_exists($class)) {
            $class=TCMP_PLUGIN_PREFIX.$class;
        }

        $result=array();
        if(class_exists($class)) {
            $reflection=new ReflectionClass($class);
            $array=$reflection->getConstants();
            foreach($array as $k=>$v) {
                $pos=0;
                if($prefix!='') {
                $pos=stripos($k, $prefix);
                }
                if($pos===0) {
                    if($reverse) {
                        $result[$v]=$k;
                    } else {
                        $result[$k]=$v;
                    }
                }
            }
        }
        return $result;
    }
    public function getConstantValue($class, $prefix, $name, $default=FALSE) {
        /* @var $ec TCMP_Singleton */
        global $ec;
        $result=$default;
        if(is_object($class)) {
            $class=get_class($class);
        }
        $class=str_replace('Search', '', $class);
        $class=str_replace('Constants', '', $class);
        $class.='Constants';
        if(!class_exists($class)) {
            $class=TCMP_PLUGIN_PREFIX.$class;
        }

        if(class_exists($class)) {
            $name=$prefix.'_'.$name;
            $name=$ec->Utils->upperUnderscoreCase($name);
            $reflection=new ReflectionClass($class);
            $result=$reflection->getConstant($name);
        }
        return $result;
    }
    public function getConstantName($class, $prefix, $value, $default=FALSE) {
        /* @var $ec TCMP_Singleton */
        $constants=$this->getConstants($class, $prefix, TRUE);
        $result=$default;
        if(isset($constants[$value])) {
            $result=$constants[$value];
        }
        return $result;
    }
    public function daysDiff($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        $result=($dt2-$dt1)/86400;
        $result=intval($result);
        return $result;
    }
    public function getFirstLastDayOfWeek($dt) {
        $dt=$this->parseDateToTime($dt);
        // Get the day of the week: Sunday=0 to Saturday=6
        $dotw=date('w', $dt);
        if($dotw>1) {
            $dt1=$dt-(($dotw-1)*24*60*60);
            $dt2=$dt+((7-$dotw)*24*60*60);
        }
        else if($dotw==1) {
            $dt1=$dt;
            $dt2=$dt+((7-$dotw)*24*60*60);
        } else if($dotw==0) {
            $dt1=$dt -(6*24*60*60);;
            $dt2=$dt;
        }

        $result=array($dt1, $dt2);
        return $result;
    }
    public function toMap($array, $key=FALSE, $value=FALSE, $classes=FALSE) {
        $classes=$this->toArray($classes);
        $result=array();
        if(is_string($array)) {
            $array=$this->toArray($array);
            $key=FALSE;
            $value=FALSE;
        }
        if(!is_array($array)) {
            $array=array();
        }

        $assoc=$this->isAssociativeArray($array);
        foreach($array as $k=>$v) {
            if(!$assoc) {
                $k=$v;
            }

            if($classes!==FALSE && count($classes)>0 && is_object($v)) {
                $class=get_class($v);
                if(!in_array($class, $classes)) {
                    continue;
                }
            }

            if($key!==FALSE) {
                $k=$this->get($v, $key, FALSE);
            }
            if($value!==FALSE) {
                $v=$this->get($v, $value, FALSE);
            }

            if($k!==FALSE && $v!==FALSE) {
                $result[$k]=$v;
            }
        }
        return $result;
    }
    public function getText($text, $args) {
        if($args===FALSE || count($args)==0) {
            return $text;
        }

        foreach($args as $k=>$v) {
            $text=str_replace("{".$k."}", $v, $text);
        }
        return $text;
    }
    public function arrayExtends($options, $defaults) {
        global $tcmp;
        $options=$tcmp->Utils->parseArgs($options, $defaults);
        foreach ($options as $k=>$v) {
            if(is_bool($v)) {
                $v=($v ? 1 : 0);
            }
            if (isset($defaults[$k])) {
                if($this->isAssociativeArray($v)) {
                    $v=$this->arrayExtends($v, $defaults[$k]);
                } else {
                    $v=$tcmp->Utils->toArray($v);
                    $old=$defaults[$k];
                    $old=$tcmp->Utils->toArray($old);
                    if(!$this->isAssociativeArray($old)) {
                        $v=array_merge($v, $old);
                        $v=array_unique($v);
                    }
                }
            } else {
                $v=$tcmp->Utils->toArray($v);
            }
            $options[$k]=$v;
        }
        return $options;
    }
    //send remote request to our server to store tracking and feedback
    function remotePost($action, $data = '') {
        global $tcmp;

        $data['secret']='WYSIWYG';
        $response = wp_remote_post(TCMP_INTELLYWP_ENDPOINT.'?iwpm_action='.$action, array(
            'method'=>'POST'
            , 'timeout'=>20
            , 'redirection'=>5
            , 'httpversion'=>'1.1'
            , 'blocking'=>TRUE
            , 'body'=>$data
            , 'user-agent'=>TCMP_PLUGIN_NAME.'/'.TCMP_PLUGIN_VERSION.'; '.get_bloginfo('url')
        ));
        $data = json_decode(wp_remote_retrieve_body($response), TRUE);
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200
            || !isset($data['success']) || !$data['success']
        ) {
            $tcmp->Log->error('ERRORS SENDING REMOTE-POST ACTION=%s DUE TO REASON=%s', $action, $response);
            $data = FALSE;
        } else {
            $tcmp->Log->debug('SUCCESSFULLY SENT REMOTE-POST ACTION=%s RESPONSE=%s', $action, $data);
        }
        return $data;
    }
    function remoteGet($uri, $options) {
        global $tcmp;
        $result=FALSE;
        $uri=$this->addQueryString($options, $uri);
        //$uri=str_replace('https://', 'http://', $uri);

        $args=array('timeout'=>900, 'sslverify'=>false);
        $tcmp->Log->debug('REMOTEGET: URI=%s', $uri);
        $response=FALSE;//wp_remote_get($uri, $args);
        if ($response!==FALSE && !is_wp_error($response)) {
            // decode the license data
            $body=wp_remote_retrieve_body($response);
            if($body!==FALSE && $body!='') {
                    $tcmp->Log->debug('REMOTEGET: RETRIEVEBODY=%s', $body);
                    $result=json_decode($body);
                    if(!$result) {
                        $tcmp->Log->error('REMOTEGET: UNDECODABLE BODY');
                        if(strpos($body, 'debug')!==FALSE || strpos($body, 'fatal')!==FALSE || strpos($body, 'warning')!==FALSE || strpos($body, 'error')!==FALSE) {
                            $tcmp->Options->pushErrorMessage('XdebugException');
                        }
                    }
                } else {
                    $tcmp->Log->debug('REMOTEGET: RETRIEVEBODY ERROR');
                    $result=FALSE;
                }
        } else {
            $tcmp->Log->debug('REMOTEGET: RESULT=ERROR');
        }
        if($result===FALSE) {
            $tcmp->Log->debug('file_get_contents: URI=%s', $uri);
            //$body=file_get_contents($uri);
            $ch=curl_init();
            $timeout=900;
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $body=curl_exec($ch);
            curl_close($ch);

            $tcmp->Log->debug('file_get_contents: RETRIEVEBODY=%s', $body);
            if($body!==FALSE && $body!='') {
                $tcmp->Log->debug('REMOTEGET: RETRIEVEBODY=%s', $body);
                $result=json_decode($body);
                if(!$result) {
                    $tcmp->Log->error('REMOTEGET: UNDECODABLE BODY');
                    if(strpos($body, 'xdebug')!==FALSE) {
                        $tcmp->Options->pushErrorMessage('XdebugException');
                    }
                }
            } else {
                $tcmp->Log->debug('REMOTEGET: RETRIEVEBODY ERROR');
                $result=FALSE;
            }
        }
        /*if(TRUE || $result===FALSE) {
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $result=curl_exec($ch);
            curl_close($ch);
            if($result!==FALSE && $result!='') {
                $result=json_decode($result);
            } else {
                $result=FALSE;
            }
        }*/
        if(!$result) {
            $result=FALSE;
        }
        return $result;
    }

    function isAdminUser() {
        //https://wordpress.org/support/topic/how-to-check-admin-right-without-include-pluggablephp
        return TRUE;
        /*
        if (!function_exists('wp_get_current_user')) {
            require_once(ABSPATH.'wp-includes/pluggable.php');
        }
        return (is_multisite() || current_user_can('manage_options'));
        */
    }
    function isUserLogged() {
        if (!function_exists('is_user_logged_in')) {
            require_once(ABSPATH.'wp-includes/pluggable.php');
        }
        $result=is_user_logged_in();
        return $result;
    }
    function isPluginPage() {
        global $tcmp;
        $page=TCMP_SQS('page');
        $result=($this->startsWith($page, TCMP_PLUGIN_SLUG));
        return $result;
    }
    function isQsNull($v) {
        return (is_null($v) || $v===FALSE || $v==='');
    }
    function jsonToClass($json, $class) {
        global $tcmp;
        $instance=$tcmp->Dao->Utils->getClass($class);
        if($instance=='') {
            throw new Exception('CLASS ['.$class.'] DOES NOT EXIST');
        }
        $result=FALSE;
        if(is_bool($json)) {
            return $json;
        }
        if(is_string($json)) {
            $json=json_decode($json);
        }
        if($class=='stdClass') {
            $result=new stdClass();
            foreach ($json as $k=>$v) {
                $result->$k=$v;
            }
            return $result;
        }

        if($tcmp->Utils->isArray($json) && !$tcmp->Utils->isAssociativeArray($json)) {
            $match=FALSE;
            $result=array();
            foreach($json as $v) {
                $v=$this->jsonToInstance($json, $v);
                if($v!==FALSE) {
                    $result[]=$v;
                    $match=TRUE;
                }
            }
            if(!$match) {
                $result=FALSE;
            }
        } elseif($tcmp->Utils->isAssociativeArray($json) || is_object($json)) {
            $result=$this->jsonToInstance($json, $class);
        }
        return $result;
    }
    private function jsonToInstance($json, $class) {
        global $tcmp;

        $match=FALSE;
        $result=FALSE;
        $columns=$tcmp->Dao->Utils->getAllColumns($class);
        $instance=$tcmp->Dao->Utils->getClass($class);
        $instance=new $instance();
        foreach($json as $property=>$value) {
            if(isset($columns[$property])) {
                $column=$columns[$property];
                if(isset($column['ui-type']) && $column['ui-type']=='array') {
                    $array=array();
                        $rel=$column['rel'];
                        foreach ($value as $k => $v) {
                            $v=$this->jsonToInstance($v, $rel);
                            $array[$k] = $v;
                        }
                        $value=$array;
                } else {
                    $value=$tcmp->Dao->Utils->decode($class, $property, $value);
                }
            }
            if($tcmp->Utils->set($instance, $property, $value)) {
                $match=TRUE;
            }
        }
        if($match) {
            $result=$instance;
        }
        return $result;
    }
    public function classToJson($instance) {
        if(!is_object($instance)) {
            $instance=(array)$instance;
        }
        $result=wp_json_encode($instance);
            return $result;
    }

    function dateGt($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        return ($dt1>$dt2);
    }
    function dateGtEq($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        return ($dt1>=$dt2);
    }
    function dateEq($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        return ($dt1==$dt2);
    }
    function dateLt($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        return ($dt1<$dt2);
    }
    function dateLtEq($dt1, $dt2) {
        $dt1=$this->parseDateToTime($dt1);
        $dt2=$this->parseDateToTime($dt2);
        return ($dt1<=$dt2);
    }
    function absDateDiff($dt1, $dt2, $unit='d') {
        $diff=$this->dateDiff($dt1, $dt2, $unit);
        $diff=abs($diff);
        return $diff;
    }
    function dateDiff($dt1, $dt2, $unit='d') {
        $dt1=$this->formatSqlDatetime($dt1);
        $dt2=$this->formatSqlDatetime($dt2);
        $dt1=new DateTime($dt1);
        $dt2=new DateTime($dt2);
        $diff=$dt1->diff($dt2);

        $result=0;
        switch ($unit) {
            case 'Y':
            case 'y':
                $result=$diff->y;
                break;
            case 'm':
                $result=$diff->m;
                break;
            case 'd':
                $result=$diff->days;
                break;
            case 'H':
            case 'h':
                $result=$diff->h;
                break;
            case 'n':
            case 'i':
                $result=$diff->i;
                break;
            case 's':
                $result=$diff->s;
                break;
        }
        return $result;
    }
    public function arrayPush(&$array, $another) {
        if(!is_array($another)) {
            array_push($array, $another);
        } elseif(is_array($another)) {
            foreach($another as $v) {
                array_push($array, $v);
            }
        }
        return $array;
    }
    public function encodeData($data) {
        $dataType='';
        $dataClass='';
        $text=FALSE;
        if(is_object($data)) {
            $dataType='class';
            $dataClass=get_class($data);
            $text=$this->classToJson($data);
        } elseif(is_array($data)) {
            $dataType='array';
            if(count($data)>0) {
                //array of class??
                $associative=$this->isAssociativeArray($data);
                foreach($data as $k=>$v) {
                    break;
                }
                if(is_object($v)) {
                    $dataType='class';
                    $dataClass=get_class($v);
                    $text=$this->classToJson($data);
                } else {
                    $text=json_encode($data);
                }
            } else {
                $text=json_encode($data);
            }
        } else {
            $dataType='primitive';
            $text=json_encode($data);
        }

        $result=array(
            'dataType'=>$dataType
            , 'dataClass'=>$dataClass
            , 'data'=>$this->httpEncode($text)
        );
        return $result;
    }
    public function decodeData($data=array()) {
        $defaults=array(
            'dataType'=>$this->qs('dataType', '')
            , 'dataClass'=>$this->qs('dataClass', '')
            , 'data'=>$this->qs('data', '')
        );
        $data=$this->parseArgs($data, $defaults);
        $data['data']=$this->httpDecode($data['data']);

        //$data['data']=str_replace("\\\"", "\"", $data['data']);
        //$data['data']=str_replace("\\\\", "\\", $data['data']);

        $result=FALSE;
        switch (strtolower($data['dataType'])) {
            case 'array':
                $result=json_decode($data['data'], TRUE);
                break;
            case 'class':
                if(class_exists($data['dataClass']) && $this->startsWith($data['dataClass'], TCMP_PLUGIN_PREFIX)) {
                    $result=$this->jsonToClass($data['data'], $data['dataClass']);
                } else {
                    $result=json_decode($data['data'], TRUE);
                }
                break;
            default:
                $result=json_decode($data['data']);
                break;
        }
        return $result;
    }
    public function getConstantsValues($class, $prefix='', $glue=FALSE) {
        $array=$this->getConstants($class, $prefix);
        $result=array_values($array);
        if($glue!==FALSE) {
            $result=implode($glue, $result);
        }
        return $result;

    }
    public function getValue($array, $index, $default=FALSE) {
        $result=$this->getIndex($array, $index, $default);
        if($result!==$default) {
            $result=$result['v'];
        }
        return $result;
    }
    public function getKey($array, $index, $default=FALSE) {
        $result=$this->getIndex($array, $index, $default);
        if($result!==$default) {
            $result=$result['k'];
        }
        return $result;
    }
    public function getIndex($array, $index, $default=FALSE) {
        $result=$default;
        if(is_array($array) && count($array)>0) {
            if($this->isAssociativeArray($array)) {
                $i=0;
                foreach($array as $k=>$v) {
                    if($index==$i) {
                        $result=array(
                            'k'=>$k
                            , 'v'=>$v
                        );
                        break;
                    }
                    $i++;
                }
            } else {
                if($index<count($array) && $index>=0) {
                    $result=$array[$index];
                }
            }
        }
        return $result;
    }
    public function isEmpty($v) {
        if(!$v) {
            return TRUE;
        }

        $result=FALSE;
        if(is_string($v)) {
            $result=($v=='');
        } elseif(is_array($v)) {
            $result=count($v)==0;
        } elseif(is_object($v)) {
            $result=TRUE;
            foreach($v as $k=>$w) {
                if(!is_null($w) && $w!=='') {
                    $result=FALSE;
                    break;
                }
            }
        }
        return $result;
    }
    public function httpEncode($v) {
        $v=gzcompress($v);
        $v=bin2hex($v);
        return $v;
}
    public function httpDecode($v) {
        $v=hex2bin($v);
        $v=gzuncompress($v);
        return $v;
    }
    public function trimHttp($uri) {
        $uri=str_replace('http://', '', $uri);
        $uri=str_replace('https://', '', $uri);
        return $uri;
    }
    function getClientIpAddress() {
        $ipaddress='';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress=getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress=getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress=getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress=getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress=getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress=getenv('REMOTE_ADDR');
        else
            $ipaddress='UNKNOWN';
        $ipaddress=($ipaddress == '::1') ? '192.168.0.1' : $ipaddress;
        return $ipaddress;
    }
    public function isMail($mail) {
        $at=strpos($mail, '@');
        $dot=strrpos($mail, '.');
        $result=FALSE;
        if($at!==FALSE && $dot!==FALSE && $at<$dot) {
            $result=TRUE;
        }
        return $result;
    }
    public function getNameFromListArray($array, $id, $default=FALSE) {
        $result=$default;
        foreach($array as $v) {
            if($v['id']==$id) {
                if(isset($v['text'])) {
                    $result=$v['text'];
                    break;
                } elseif(isset($v['name'])) {
                    $result=$v['name'];
                    break;
                }
            }
        }
        return $result;
    }
    function bqs($name, $default=FALSE) {
        $v=$this->qs($name, '');
        $result=$default;
        if($v!='') {
            if(is_numeric($v)) {
                $v=intval($v);
                $result=($v>0);
        } else {
                $result=$this->isTrue($v);
        }
    }
        return $result;
        }
    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    function getGravatarImage($email, $s=80, $d='mm', $r='g', $img=TRUE, $atts=array()) {
        if(!is_array($atts)) {
            $atts=array();
        }
        if(!isset($atts['class'])) {
            $atts['class']='gravatar';
        }
        $url='//www.gravatar.com/avatar/';
        $url.=md5(strtolower(trim($email)));
        $url.="?s=$s&d=$d&r=$r";
        if ($img) {
            $url='<img src="'.$url.'"';
            foreach ($atts as $key => $val)
                $url .= ' '.$key.'="'.$val.'"';
            $url .= ' />';
                }
        return $url;
        }
    function getGravatarUri($email, $s=80, $d='mm', $r='g', $atts=array()) {
        $url=$this->getGravatarImage($email, $s, $d, $r, FALSE, $atts);
        return $url;
    }
    function getFunctionName($function) {
        $result=FALSE;
        if(is_string($function)) {
            $result=$function;
        } elseif(is_array($function)) {
            $result=$function[1];
        }
        return $result;
    }
    function functionExists($function) {
        $result=FALSE;
        if(is_string($function)) {
            $result=function_exists($function);
        } elseif(is_array($function)) {
            $result=method_exists($function[0], $function[1]);
        } elseif(is_callable($function)) {
            $result=TRUE;
        }
        return $result;
    }
    function functionCall() {
        $args=func_get_args();
        if($args===FALSE || count($args)==0) {
            return;
        }

        $function=array_shift($args);
        $result=NULL;
        if($this->functionExists($function)) {
            $result=call_user_func_array($function, $args);
        }
        return $result;
    }

    function passwordsEquals($p1, $p2) {
        if (!function_exists('wp_check_password')) {
            require_once(ABSPATH.'wp-includes/pluggable.php');
        }
        $result=wp_check_password($p1, $p2);
        return $result;
    }
    public function contains($v1, $v2, $ignoreCase=TRUE) {
        $result=FALSE;
        if($ignoreCase) {
            $result=stripos($v1, $v2)!==FALSE;
        } else {
            $result=strpos($v1, $v2)!==FALSE;
        }
        return $result;
    }
    public function getMailTextHtml() {
        return "text/html";
    }
    public function mail($to, $subject, $body, $options=array()) {
        $subject='[LeadsBridge] '.$subject;
        $defaults=array(
            'html'=>TRUE
            , 'footer'=>TRUE
            , 'headers'=>array()
            , 'noreply'=>TRUE
            , 'attachments'=>array()
        );
        $options=$this->parseArgs($options, $defaults);
        $to=$this->toArray($to);
        if($options['html']) {
            add_filter('wp_mail_content_type', array($this, 'getMailTextHtml'));
        }
        if($options['noreply']) {
            $options['headers'][]='From: LeadsBridge <no-reply@leadsbrige.com>';
        }
        if($options['footer'] && $options['html']) {
            $body .= '<br><hr><a href="'.TCMP_WORDPRESS_SITE.'"><img src="'.TCMP_SITE_IMAGES_URI.'logos/logo-mail.png" /></a>';
        }
        $result=FALSE;
        if(!function_exists('wp_mail')) {
            include_once('../../../../../../wp-includes/pluggable.php');
        }
        if(function_exists('wp_mail')) {
            if(count($to)>0) {
                $result=wp_mail($to, $subject, $body
                    , $options['headers'], $options['attachments']);
            }
        }
        return $result;
    }
    /*public function formSubmit($action, $method='POST', $data=array(), $options=array()) {
        $defaults=array('json'=>FALSE);
        $options=$this->parseArgs($options, $defaults);

        $postData=http_build_query($data);
        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL, $action);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, strtolower($method)=='post');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $result=curl_exec($curl);
        curl_close($curl);
        if($result!==FALSE && $result!=='' && $options['json']) {
            $result=json_decode($result, TRUE);
        }
        return $result;
    }*/

    private function getHtmlCode($value) {
        $value=str_replace('\"', '', $value);
        $value=str_replace('"', '', $value);
        return $value;
    }
    public function parseHtmlForm($html) {
        $data=array(
            'action'=>''
            , 'method'=>'GET'
            , 'fields'=>array()
        );
        $result=FALSE;

        $previous=error_reporting();
        error_reporting($previous^E_WARNING);

        $doc=new DOMDocument();
        if ($doc->loadHTML($html)) {
            $xpath=new DOMXPath($doc);
            $form=$xpath->query('//form');
            if ($form->length > 0) {
                $result=TRUE;
                $data['action']=$this->getHtmlCode($form->item(0)->getAttribute('action'));
                $data['method']=$this->getHtmlCode($form->item(0)->getAttribute('method'));
                if($data['method']===FALSE || $data['method']=='') {
                    $data['method']='GET';
                }
                $data['method']=strtoupper($data['method']);
                $inputs=$xpath->query('//input');
                foreach ($inputs as $input) {
                    $name=$this->getHtmlCode($input->getAttribute('name'));
                    $type=$this->getHtmlCode($input->getAttribute('type'));
                    $value=$this->getHtmlCode($input->getAttribute('value'));
                    if($name!='') {
                        $data['fields'][$name]=array(
                            'name'=>$name
                            , 'type'=>$type
                            , 'value'=>$value
                        );
                    }
                }
            }
        }
        error_reporting($previous);
        return ($result ? $data : FALSE);
    }
    public function dequeueScripts($array) {
        if(!function_exists('wp_scripts') || function_exists('wp_dequeue_script')) {
            return;
        }

        $array=$this->toArray($array);
        $scripts=wp_scripts();
        /* @var $v _WP_Dependency */
        foreach($scripts->registered as $k=>$v) {
            foreach($array as $pattern) {
                if($this->contains($v->src, $pattern) || $this->contains($v->handle, $pattern)) {
                    wp_dequeue_script($v->handle);
                    break;
                }
            }
        }
    }
    public function dequeueStyles($array) {
        if(!function_exists('wp_styles') || function_exists('wp_dequeue_style')) {
            return;
        }

        $array=$this->toArray($array);
        $styles=wp_styles();
        /* @var $v _WP_Dependency */
        foreach($styles->registered as $k=>$v) {
            foreach($array as $pattern) {
                if($this->contains($v->src, $pattern) || $this->contains($v->handle, $pattern)) {
                    wp_dequeue_style($v->handle);
                    break;
                }
            }
        }
    }
    public function formatSeconds($time) {
        if($time==='') {
            return '';
        }

        $time=intval($time);
        $seconds=($time%60);
        $time=(($time-$seconds)/60);
        $minutes=($time%60);
        $time=(($time-$minutes)/60);
        $hours=($time%24);
        $time=(($time-$hours)/24);
        $days=$time;

        $array=array();
        if($seconds>0) {
            $array[]=$seconds.'s';
        }
        if($minutes>0) {
            $array[]=$minutes.'m';
        }
        if($hours>0) {
            $array[]=$hours.'h';
        }
        if($days>0) {
            $array[]=$days.'d';
        }
        $array=array_reverse($array);
        $text=implode(' ', $array);
        return $text;
    }
    function logout() {
        if (!function_exists('wp_logout')) {
            require_once(ABSPATH.'wp-includes/pluggable.php');
        }
        wp_logout();
        return TRUE;
    }
    function formatPercentage($value, $options=array()) {
        if(is_bool($options)) {
            $options=array('symbol'=>$options);
        }
        $defaults=array('symbol'=>TRUE);
        $options=$this->parseArgs($options, $defaults);

        $value=floatval($value);
        $value=round($value, 3);
        $value=number_format($value, 3, ',', '');
        if($options['symbol']) {
            $value.=' %';
        }
        return $value;
    }
    function formatCurrencyMoney($value, $options=array()) {
        $defaults=array('currency'=>$this->getDefaultCurrencySymbol());
        $options=$this->parseArgs($options, $defaults);

        $value=$this->formatMoney($value, $options);
        return $value;
    }
    function formatMoney($value, $options=array()) {
        if(is_string($options)) {
            $options=array('currency'=>$options);
        }
        $defaults=array('currency'=>FALSE);
        $options=$this->parseArgs($options, $defaults);

        $value=floatval($value);
        $value=round($value, 3);
        $value=number_format($value, 3, ',', '.');
        if($options['currency']!='') {
            $symbol=$options['currency'];
            if(strlen($symbol)>1) {
                $symbol=$this->getCurrencySymbol($symbol);
            }
            $value.=' '.$symbol;
        }
        return $value;
    }
    function sortOptions(&$options) {
        if(!is_array($options)) {
            return $options;
        }

        usort($options, array($this, 'sortOptions_Compare'));
        return $options;
    }
    public function sortOptions_Compare($o1, $o2) {
        global $tcmp;
        $v1=$tcmp->Utils->get($o1, 'text', FALSE);
        if($v1===FALSE) {
            $v1=$tcmp->Utils->get($o1, 'name', FALSE);
        }
        $v2=$tcmp->Utils->get($o2, 'text', FALSE);
        if($v2===FALSE) {
            $v2=$tcmp->Utils->get($o2, 'name', FALSE);
        }

        //to order properly
        if($tcmp->Utils->startsWith($v1, '[')) {
            $v1=' '.$v1;
        }
        if($tcmp->Utils->startsWith($v2, '[')) {
            $v2=' '.$v2;
        }
        return strcasecmp($v1, $v2);
    }
    public function getVisitorIpAddress() {
        $ip='';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public function getVisitorUserAgent() {
        $result='';
        if(isset($_SERVER['HTTP_USER_AGENT'])) {
            $result=$_SERVER['HTTP_USER_AGENT'];
        }
        return $result;
    }
    public function cURL($uri, $method, $options=array()) {
        global $tcmp;
        $defaults=array(
            'body'=>''
            , 'agent'=>''
            , 'timeout'=>5
            , 'header'=>array()
        );
        $options=$this->parseArgs($options, $defaults);

        if($this->startsWith($uri, '//')) {
            $uri='https:'.$uri;
        }

        try {
            $curl=curl_init();
            if($method=='') {
                $method='GET';
            }
            $method=strtoupper($method);
            switch($method) {
                case 'GET':
                    if(is_array($options['body'])) {
                        $uri=$this->addQueryString($options['body'], $uri);
                    }
                    break;
                case 'POST':
                    if(is_array($options['body'])) {
                        curl_setopt($curl, CURLOPT_POST, TRUE);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($options['body']));
                    }
                    break;
                case 'PUT':
                case 'DELETE':
                default:
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                    if(is_array($options['body'])) {
                        curl_setopt($curl, CURLOPT_POST, TRUE);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($options['body']));
                    }
                    break;
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $header=$options['header'];
            if(is_array($header) && count($header)>0) {
                $data=array();
                foreach($header as $k=>$v) {
                    if(is_numeric($k)) {
                        $data[]=$v;
                    } else {
                        $data[]=$k.': '.$v;
                    }
                }
                curl_setopt($curl, CURLOPT_HEADER, TRUE);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $data);
            }
            curl_setopt($curl, CURLOPT_URL, $uri);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);

            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, intval($options['timeout']));
            if($options['agent']!=='' && $options['agent']!==FALSE) {
                curl_setopt($curl, CURLOPT_USERAGENT, $options['agent']);
            }

            $response=curl_exec($curl);
            $info=curl_getinfo($curl);

            $errorCode=intval(curl_errno($curl));
            $errorDescription=curl_error($curl);
            if($errorCode==0 && isset($info['http_code'])) {
                $info['http_code']=intval($info['http_code']);
                if($info['http_code']!=200 && $info['http_code']!=201) {
                    $errorCode=$info['http_code'];
                    if($errorDescription=='') {
                        $errorDescription=$response;
                    }
                } elseif(is_string($response) && $this->startsWith($response, 'No site configured at this address')) {
                    $errorCode=-1;
                    if($errorDescription=='') {
                        $errorDescription=$response;
                    }
                }
            }
            if($errorCode!=0) {
                $result=new WP_Error($errorCode, $errorDescription);
            } else {
                if(is_array($header) && count($header)>0) {
                    $text=trim(substr($response, 0, $info['header_size']));
                    $result=substr($response, $info['header_size']);
                } else {
                    $result=$response;
                }
            }
            curl_close($curl);
        } catch(Exception $ex) {
            $result=new WP_Error($ex->getCode(), $ex->getMessage());
        }
        return $result;
    }
    public function toEmailsArray($data) {
        if(!is_array($data)) {
            $data=str_replace(',', '|', $data);
            $data=str_replace(';', '|', $data);
            $data=str_replace(' ', '|', $data);
            $data=$this->toArray($data);
        }

        $result=array();
        foreach($data as $v) {
            $v=trim($v);
            if(function_exists('is_email')) {
                if(!is_email($v)) {
                    $v='';
                }
            } elseif(!filter_var($v, FILTER_VALIDATE_EMAIL)) {
                $v='';
            }
            if($v!='') {
                $result[$v]=$v;
            }
        }
        $result=array_keys($result);
        return $result;
    }
    function getCustomFields($fields) {
        $items=$this->toArray($fields);
        $result=array();
        foreach($items as $v) {
            $name=str_replace('_', ' ', $v);
            $name=str_replace('-', ' ', $name);
            $name=str_replace('$', '', $name);
            $name=ucwords($name);
            $result[$v]=array('name'=>$name, 'format'=>'text');
        }
        return $result;
    }
    function getCurrencySymbol($currency) {
        // Create a NumberFormatter
        $locale='en_US';
        $formatter=new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Figure out what 0.00 looks like with the currency symbol
        $withCurrency=$formatter->formatCurrency(0, $currency);

        // Figure out what 0.00 looks like without the currency symbol
        $formatter->setPattern(str_replace('¤', '', $formatter->getPattern()));
        $withoutCurrency=$formatter->formatCurrency(0, $currency);

        // Extract just the currency symbol from the first string
        return str_replace($withoutCurrency, '', $withCurrency);
    }
    function encodeUri($string) {
        $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D');
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "#", "[", "]");
        $result=urlencode($string);
        //$result=str_replace($replacements, $entities, $result);
        return $result;
    }
    function addQueryString($args, $uri) {
        if(!is_array($args) || count($args)==0) {
            return $uri;
        }

        $params=array();
        foreach($args as $k=>$v) {
            $params[]=$k.'='.$this->encodeUri($v);
        }
        $params=implode('&', $params);
        if($this->contains($uri, '?')) {
            $uri.='&'.$params;
        } else {
            $uri.='?'.$params;
        }
        return $uri;
    }
    function arrayCopy($fromArray, &$toArray, $options=array()) {
        $defaults=array(
            'matchFields'=>array()
            , 'otherFields'=>TRUE
            , 'key'=>''
        );
        $options=$this->parseArgs($options, $defaults);

        foreach($fromArray as $i=>$item) {
            $new=array();
            foreach($options['matchFields'] as $k=>$t) {
                if(is_bool($k)) {
                    $v=$i;
                } else {
                    $v=$this->get($item, $k, '');
                    unset($item[$k]);
                }
                $new[$t]=$v;
            }

            if($options['otherFields']) {
                foreach($item as $k=>$v) {
                    $new[$k]=$v;
                }
            }

            $k=$options['key'];
            if($k=='') {
                $toArray[]=$new;
            } else {
                $k=$this->get($new, $k, '');
                if($k!='') {
                    $toArray[$k]=$new;
                }
            }

        }
        return $toArray;
    }
    public function formatTimer($time) {
        if(!is_int($time)) {
            if(is_string($time)) {
                $time=str_replace(' ', ':', $time);
                $time=str_replace('.', ':', $time);
                $time=str_replace('/', ':', $time);
                $time=explode(':', $time);

                $length=count($time);
                $days=0;
                $hours=0;
                $minutes=0;
                $secs=intval($time[$length-1]);

                if($length>1) {
                    $minutes=intval($time[$length-2]);
                    if($length>2) {
                        $hours=intval($time[$length-3]);
                        if($length>3) {
                            $days=intval($time[$length-4]);
                        }
                    }
                }
                $time=$days*86400+$hours*3600+$minutes*60+$secs;
            } else {
                $time=0;
            }
        } else {
            $time=intval($time);
        }

        $secs=$time%60;
        $time=($time-$secs)/60;
        $minutes=$time%60;
        $time=($time-$minutes)/60;
        $hours=$time%24;
        $days=($time-$hours)/24;

        $result=array();
        $result[]=$days;
        $result[]=($hours<10 ? '0' : '').$hours;
        $result[]=($minutes<10 ? '0' : '').$minutes;
        $result[]=($secs<10 ? '0' : '').$secs;
        $result=implode(':', $result);
        return $result;
    }
    public function parseTimer($time) {
        $time=$this->formatTimer($time);
        $time=explode(':', $time);
        $result=intval($time[0])*86400+intval($time[1])*3600+intval($time[2])*60+intval($time[3]);
        return $result;
    }
    public function isTax($taxonomy='', $term='') {
        $result=FALSE;
        switch ($taxonomy) {
            case 'category':
                $result=is_category($term);
                break;
            case 'tag':
                $result=is_tag($term);
                break;
            default:
                $result=is_tax($taxonomy, $term);
                break;
        }
        return $result;
    }
 }
