<?php

namespace Qiigo\Plugin\LocalIntegration\Shortcodes {
	class ContactForm extends Base {
		protected $form_id;
		protected $site_url;
		protected $cc;
		
		protected $_intlDomains = array(
		);
		protected $formControls = array(
			'1684291' => array('site_url' => 'control18429145', 'country_code' => null),
			'1684275' => array('site_url' => 'control18429214', 'country_code' => null),
			'1684264' => array('site_url' => 'control18429274', 'country_code' => null),
			'1684246' => array('site_url' => 'control18429450', 'country_code' => null),
			'1684202' => array('site_url' => 'control18429485', 'country_code' => null),
			'1684198' => array('site_url' => 'control18429511', 'country_code' => null),
			'1684164' => array('site_url' => 'control18429524', 'country_code' => null),
			'1684103' => array('site_url' => 'control18429676', 'country_code' => null),
			'1684096' => array('site_url' => 'control18430918', 'country_code' => null),
			'1684055' => array('site_url' => 'control18431072', 'country_code' => null),
			'1683995' => array('site_url' => 'control18431113', 'country_code' => null),
			'2091183' => array('site_url' => 'control20583525', 'country_code' => null),
			'2094229' => array('site_url' => 'control20623045', 'country_code' => null),
			'2130740' => array('site_url' => 'control21084983', 'country_code' => null),
			'2155772' => array('site_url' => 'control21411456', 'country_code' => null),
			'2130768' => array('site_url' => 'control21085410', 'country_code' => null),
			'2130764' => array('site_url' => 'control21085348', 'country_code' => null),
			'2130739' => array('site_url' => 'control21419721', 'country_code' => null),
			'2130765' => array('site_url' => 'control21085365', 'country_code' => null),
			'2130766' => array('site_url' => 'control21085380', 'country_code' => null),
			'2215032' => array('site_url' => 'control22389810', 'country_code' => null),
			'2258090' => array('site_url' => 'control22944087', 'country_code' => null),
			'2105315' => array('site_url' => 'control20762222', 'country_code' => null),
			'2348500' => array('site_url' => 'control24186265', 'country_code' => null),
			'2348820' => array('site_url' => 'control24191235', 'country_code' => null),
			'2353052' => array('site_url' => 'control24250889', 'country_code' => null),
			'2353148' => array('site_url' => 'control24251845', 'country_code' => null),
			'2355051' => array('site_url' => 'control24279464', 'country_code' => null),
			'2355081' => array('site_url' => 'control24279833', 'country_code' => null),
			'2355112' => array('site_url' => 'control24280552', 'country_code' => null)
		);
		
		public function __construct() {
			parent::__construct('qiigo-contact-form');
		}

		public function ExtractArgs($args) {
			if( !isset($args['id']) )
				throw new \Exception('Missing required parameter [id] for shortcode "contact_form".');
			
			$this->form_id = $args['id'];
			
			if( !isset($this->formControls[$this->form_id]) )
				throw new \Exception('Invalid [id] "'.$this->form_id.'" for shortcode "contact_form".');
			
			$this->cc = null;
			$this->site_url = $_SERVER['HTTP_HOST'];
			
			if( isset($this->_intlDomains[$this->site_url]) && isset($this->formControls[$this->form_id]['country_code']) && $this->formControls[$this->form_id]['country_code'] != 'REPLACEME' )
				$this->cc = $this->_intlDomains[$this->site_url];
		}
		
		public function Render() {
			$customVars = '';
			
			if( isset($this->cc) )
				$customVars = $this->formControls[$this->form_id]['country_code'].'='.$this->cc.'&';
			
			$customVars .= $this->formControls[$this->form_id]['site_url'] . '=' . urlencode($this->site_url);
?>
<script type="text/javascript">
	var servicedomain="www.123contactform.com";
	var frmRef=window.top.location.href;
	var customVars="<?=$customVars?>";
	var cfJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
	document.write(unescape("%3Cscript src='" + cfJsHost + servicedomain + "/includes/easyXDM.min.js' type='text/javascript'%3E%3C/script%3E"));
	document.write(unescape("%3Cscript src='" + cfJsHost + servicedomain + "/jsform-<?=$this->form_id?>.js?"+customVars+"&ref="+frmRef+"' type='text/javascript'%3E%3C/script%3E"));
</script>
<?php
		}
	}
}