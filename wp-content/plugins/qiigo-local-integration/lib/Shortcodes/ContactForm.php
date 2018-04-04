<?php

namespace Qiigo\Plugin\LocalIntegration\Shortcodes {
	class ContactForm extends Base {
		protected $form_id;
		protected $site_url;
		protected $cc;
		protected $mr_referer;
		protected $mr_page;
		
		protected $_intlDomains = array(
		);
		protected $formControls = array(
			'1684291' => array('site_url' => 'control18429145', 'country_code' => null, 'mr_referer' => 'control24845738', 'mr_page' => 'control24852158'),
			'1684275' => array('site_url' => 'control18429214', 'country_code' => null, 'mr_referer' => 'control24845831', 'mr_page' => 'control24852240'),
			'1684264' => array('site_url' => 'control18429274', 'country_code' => null, 'mr_referer' => 'control24845878', 'mr_page' => 'control24852287'),
			'1684246' => array('site_url' => 'control18429450', 'country_code' => null, 'mr_referer' => 'control24846005', 'mr_page' => 'control24852315'),
			'1684202' => array('site_url' => 'control18429485', 'country_code' => null, 'mr_referer' => 'control24846028', 'mr_page' => 'control24852361'),
			'1684198' => array('site_url' => 'control18429511', 'country_code' => null, 'mr_referer' => 'control24846086', 'mr_page' => 'control24852463'),
			'1684164' => array('site_url' => 'control18429524', 'country_code' => null, 'mr_referer' => 'control24846119', 'mr_page' => 'control24852516'),
			'1684103' => array('site_url' => 'control18429676', 'country_code' => null, 'mr_referer' => 'control24846172', 'mr_page' => 'control24852575'),
			'1684096' => array('site_url' => 'control18430918', 'country_code' => null, 'mr_referer' => 'control24846201', 'mr_page' => 'control24852616'),
			'1684055' => array('site_url' => 'control18431072', 'country_code' => null, 'mr_referer' => 'control24846219', 'mr_page' => 'control24852706'),
			'1683995' => array('site_url' => 'control18431113', 'country_code' => null, 'mr_referer' => 'control24846276', 'mr_page' => 'control24852800'),
			'2091183' => array('site_url' => 'control20583525', 'country_code' => null, 'mr_referer' => 'control24846310', 'mr_page' => 'control24852838'),
			'2094229' => array('site_url' => 'control20623045', 'country_code' => null, 'mr_referer' => 'control24846330', 'mr_page' => 'control24852896'),
			'2130740' => array('site_url' => 'control21084983', 'country_code' => null, 'mr_referer' => 'control24846345', 'mr_page' => 'control24852909'),
			'2155772' => array('site_url' => 'control21411456', 'country_code' => null, 'mr_referer' => 'control24846355', 'mr_page' => 'control24852932'),
			'2130768' => array('site_url' => 'control21085410', 'country_code' => null, 'mr_referer' => 'control24846411', 'mr_page' => 'control24853017'),
			'2130764' => array('site_url' => 'control21085348', 'country_code' => null, 'mr_referer' => 'control24846542', 'mr_page' => 'control24853027'),
			'2130739' => array('site_url' => 'control21419721', 'country_code' => null, 'mr_referer' => 'control24874144', 'mr_page' => 'control24874148'), 
			'2130765' => array('site_url' => 'control21085365', 'country_code' => null, 'mr_referer' => 'control24846612', 'mr_page' => 'control24853048'),
			'2130766' => array('site_url' => 'control21085380', 'country_code' => null, 'mr_referer' => 'control24874208', 'mr_page' => 'control24874226'),
			'2215032' => array('site_url' => 'control22389810', 'country_code' => null, 'mr_referer' => 'control24874488', 'mr_page' => 'control24874490'),
			'2258090' => array('site_url' => 'control22944087', 'country_code' => null, 'mr_referer' => 'control24874514', 'mr_page' => 'control24874516'),
			'2105315' => array('site_url' => 'control20762222', 'country_code' => null, 'mr_referer' => 'control24874572', 'mr_page' => 'control24874575'),
			'2348500' => array('site_url' => 'control24186265', 'country_code' => null, 'mr_referer' => 'control24846727', 'mr_page' => 'control24853085'),
			'2348820' => array('site_url' => 'control24191235', 'country_code' => null, 'mr_referer' => 'control24846812', 'mr_page' => 'control24853167'),
			'2353052' => array('site_url' => 'control24250889', 'country_code' => null, 'mr_referer' => 'control24846874', 'mr_page' => 'control24853183'),
			'2353148' => array('site_url' => 'control24251845', 'country_code' => null, 'mr_referer' => 'control24846913', 'mr_page' => 'control24853222'),
			'2355051' => array('site_url' => 'control24279464', 'country_code' => null, 'mr_referer' => 'control24846976', 'mr_page' => 'control24853325'),
			'2355081' => array('site_url' => 'control24279833', 'country_code' => null, 'mr_referer' => 'control24847066', 'mr_page' => 'control24853360'),
			'2355112' => array('site_url' => 'control24280552', 'country_code' => null, 'mr_referer' => 'control24847130', 'mr_page' => 'control24853387'),
			'2735271' => array('site_url' => 'control29518476', 'country_code' => null, 'mr_referer' => 'control29518477', 'mr_page' => 'control29518478'),
//Newely added form ids 
			'1763458' => array('site_url' => 'control30474365', 'country_code' => null, 'mr_referer' => 'control30473940', 'mr_page' => 'control30474343'),
			'1685064' => array('site_url' => 'control31082593', 'country_code' => null, 'mr_referer' => 'control31074202', 'mr_page' => 'control31074200'),
			'1685510' => array('site_url' => 'control31075106', 'country_code' => null, 'mr_referer' => 'control31075104', 'mr_page' => 'control31075102'),
			'1985271' => array('site_url' => 'control31082507', 'country_code' => null, 'mr_referer' => 'control31082498', 'mr_page' => 'control31082494'),

			'1685051' => array('site_url' => 'control31443837', 'country_code' => null, 'mr_referer' => 'control31443833', 'mr_page' => 'control31443831'),
			'1685029' => array('site_url' => '', 'country_code' => null, 'mr_referer' => '', 'mr_page' => ''),
			'1763460' => array('site_url' => '', 'country_code' => null, 'mr_referer' => '', 'mr_page' => ''),
			
			'1685529' => array('site_url' => 'control31443982', 'country_code' => null, 'mr_referer' => 'control31443986', 'mr_page' => 'control31443981'),
			'1684380' => array('site_url' => 'control31436737', 'country_code' => null, 'mr_referer' => 'control31436735', 'mr_page' => 'control31436731'),
			'1685472' => array('site_url' => 'control31436774', 'country_code' => null, 'mr_referer' => 'control31436773', 'mr_page' => 'control31436772')
			
			
		);
		
		public function __construct() {
			if( !session_id()) session_start();
			parent::__construct('qiigo-contact-form');
		}

		public function ExtractArgs($args) {
			
			if( !isset($args['id']) )
				throw new \Exception('Missing required parameter [id] for shortcode "contact_form".');
			
			$this->form_id = $args['id'];
			
			if( !isset($this->formControls[$this->form_id]) )
				throw new \Exception('Invalid [id] "'.$this->form_id.'" for shortcode "contact_form".');
			
			$this->cc = null;
			$site_location_name = isset($_SESSION['site_location'])?$_SESSION['site_location']:'';
			if(!isset($_COOKIE['site_location_name'])) {
				if(!empty($site_location_name)){
					$this->site_url = $site_location_name;
				}else{
					$this->site_url = "Jan-Pro";
				}
			} else {
				$this->site_url = $_COOKIE['site_location_name'];
			}
			//$this->site_url = $_SERVER['HTTP_HOST'];
			$this->mr_referer = isset($_COOKIE['jp_referer'])?$_COOKIE['jp_referer']:(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'';
			$this->mr_page = $_SERVER['REQUEST_URI'];
			
			if( isset($this->_intlDomains[$this->site_url]) && isset($this->formControls[$this->form_id]['country_code']) && $this->formControls[$this->form_id]['country_code'] != 'REPLACEME' )
				$this->cc = $this->_intlDomains[$this->site_url];
		}
		
		public function Render() {
			
			$customVars = '';
			
			if( isset($this->cc) )
				$customVars = $this->formControls[$this->form_id]['country_code'].'='.$this->cc.'&';
			
				$customVars .= $this->formControls[$this->form_id]['site_url'] . '=' . urlencode($this->site_url).'&';
			
				$customVars .= $this->formControls[$this->form_id]['mr_referer'] . '=' . urlencode($this->mr_referer).'&';
				
				$customVars .= $this->formControls[$this->form_id]['mr_page'] . '=' . $this->mr_page;
			
			
?>

<script type="text/javascript">
	var servicedomain="//www.123contactform.com/";

	//var frmRef=window.top.location.href;
	var customVars="<?=$customVars?>";
	//var cfJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
	//document.write(unescape("%3Cscript src='" + cfJsHost + servicedomain + "/includes/easyXDM.min.js' type='text/javascript'%3E%3C/script%3E"));
	//document.write(unescape("%3Cscript src='" + cfJsHost + servicedomain + "/jsform-<?=$this->form_id?>.js?"+customVars+"&ref="+frmRef+"' type='text/javascript'%3E%3C/script%3E"));
document.write(decodeURI("%3Cdiv class='qiigoforms-wrapper'%3E%3Cscript src='"+servicedomain+"embed/<?=$this->form_id?>.js' data-role='form' data-custom-vars="+customVars+" type='text/javascript'%3E%3C/script%3E%3C/div%3E"));
</script>
<?php 
		
		//Newely added code 
		 $site_location_name = isset($_SESSION['site_location'])?$_SESSION['site_location']:'';
		
		if($site_location_name !=''){
			$site_location_name = $site_location_name;
		}else{
			$site_location_name = 'Jan-Pro';
		}			
	 ?>
<script>
	
  

		function setCookie(cname,cvalue,exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires=" + d.toGMTString();
		    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function checkCookie() {
		    setCookie("site_location_name", "<?php echo $site_location_name; ?>", 365);
		}
		checkCookie();
		</script>
<?php
		}
	}
}