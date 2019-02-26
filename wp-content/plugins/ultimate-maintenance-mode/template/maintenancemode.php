<?php
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 86400'); // in seconds

// Load Options & Functions
$seedprod_maintenancemode_options = get_option('seedprod_maintenancemode_options');
extract($seedprod_maintenancemode_options);
// Generate password for mshot
$pass = get_option('seedprod_maintenancemode_mshot_pass');

if(empty($pass)){
	$pass = wp_generate_password(12,false);
	update_option('seedprod_maintenancemode_mshot_pass',$pass);
}


$url = home_url().'/?mshot='.$pass;
//$url = 'http://wordpress.com';
if(empty($comingsoon_bg_image)){
	$mshot = 'http://s.wordpress.com/mshots/v1/'. urlencode($url) .'?w=1600';
}else{
	$mshot = $comingsoon_bg_image;
}

if(empty($comingsoon_headline)){
	$title = 'Down for Maintenance - ' . get_bloginfo( 'name');
}else{
	$title = $comingsoon_headline;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="<?php echo includes_url(); ?>js/jquery/jquery.js"></script>
		<script src="<?php echo plugins_url('template/script.js',dirname(__FILE__)); ?>"></script>
		<script src="<?php echo plugins_url('bootstrap/js/bootstrap.js',__FILE__); ?>"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('bootstrap/css/bootstrap.min.css',__FILE__); ?>">
		<link rel="stylesheet" href="<?php echo plugins_url('bootstrap/css/bootstrap-responsive.min.css',__FILE__); ?>">

		<style>
		body{
			background-color: #000;
			background-image: url('<?php echo $mshot; ?>');
			background-repeat: no-repeat;
			background-attachment: fixed;
			background-position: top center;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
			text-align: center;
		}
		body p{
			font-size:14px;
		}
		</style>
	</head>
	<body>
	<div id="mmModal" class="modal hide fade">
	  <div class="modal-header">
	  	<?php if(empty($comingsoon_headline)): ?>
	    <h1><?php echo bloginfo( 'name'); ?> <?php echo __("is Down for Maintenance","ultimate-maintenance-mode"); ?></h1>
		<?php else: ?>
		<h1><?php echo $comingsoon_headline; ?></h1>
		<?php endif; ?>
	  </div>
	  <div class="modal-body">
	    <?php echo wpautop($msg); ?>
	  </div>
	</div>
	<script>
	jQuery(document).ready(function($){
		$('#mmModal').modal({
		  keyboard: false
		})
		$('#mmModal').on('shown', function () {
			$('body,.modal-backdrop').unbind();
		});
		
	});
	</script>
	  <!--[if lt IE 9]>
	  <script>
	  jQuery(document).ready(function($){
	    <?php
	    if(!empty($mshot)):
	    ?>
	    $.supersized({
	      slides:[ {image : '<?php echo $mshot; ?>'} ]
	    });
	    <?php
	    endif;
	    ?>
	  });
	  </script>
	  <![endif]-->
	</body>
</html>
<?php exit(); ?>