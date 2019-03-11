<?php

if(isset($_POST['ldsubmit'])){
    update_option('ldcode',stripslashes($_POST['ldcodesnippet']));
    $hidecodevalue = !empty($_POST['hidecode']) ? $_POST['hidecode'] : 0;
    update_option('hidecode',stripslashes($hidecodevalue));
}

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Zoho SalesIQ</title>
<style>
	body{ margin:0; font:normal 12px Arial, Helvetica, sans-serif, "Open Sans";}
	.main{ width:100%;}
	.lvd_note, .lvd_notesb{ background-color:#fef7e9; color:#900; border-left:3px solid #e49d52; padding:20px; margin:15px 0 10px 0; text-shadow:0 2px 0 #fff; width:938px;}
	.lvd_notesb{ margin:10px 0 20px 0;}
	.lvd_embd{ background-color:#fdfdfd;padding:15px 30px 15px 20px; margin:30px 0 10px 0; overflow:hidden;height:150px;}
	.lvd_embdlft{ width:33%; float:left; font-weight:bold; text-align:center; color:#666; line-height:20px; padding-top:4px; font-size:13px; text-shadow:0 2px 0 #fff;}
	.lvd_embdlft span{  }
	.lvd_embdlft a{ text-decoration:underline; color:#51A50F;}
	.lvd_embdlft a:hover{ text-decoration:none;} 
	.lvd_embdmid{width:45px; float:right; margin:35px 10px 0 0;  text-shadow:0 2px 0 #fff;  font-size:35px;}
	.lvd_embdrht{ width:60%; float:right; height:60px; margin-top:6px;}
	.lvd_embdrht textarea{ color:#999; border:1px dashed #bbb; word-wrap:break-word; background-color:#fff; width:100%; font-family:inherit; padding:5px; font-size:12px;resize:none;}
	.lvd_embdrht textarea:focus{ box-shadow:0 0 3px #dedede; border-color:#999;}
	.lvd_tit{ font-size:17px; border-bottom:1px solid #ddd; padding-bottom:10px; color:#333;}
	.lvd_tit span{ vertical-align:middle; }
	.lvd_sbdata{ margin-top:50px; }
	.lvd_hd th, .lvd_hd td { padding:8px; width:20%; text-align:left}
	.lvd_hd th { background-color:#f2f2f2;}
	.tdbg{ background-color:#fcfcfc ;}
	.tdbg1{background-color:#fdfdfd	;}
	.lvds_btn{background-color:#003a88; color:#fff; padding:4px 18px; border-radius:10px; border:1px solid #084fae}
	
	.lvd_sbdata ul{ margin:0; padding:0;}
.lvd_sbdata li{ list-style-type:circle; margin-left:30px; line-height:20px; color:#000; }
.lvd_sbdata li span{color:#555; font-weight:bold;}
.lvds_btnmn{ margin-top:30px;}
.lvds_btnmn a{background-color: #21759B;
    background-image: linear-gradient(to bottom, #2A95C5, #21759B);
    border-color: #21759B #21759B #1E6A8D;
    box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
    color: #FFFFFF;
    text-decoration: none;
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
	border-radius:3px;
    border:1px solid;
    cursor: pointer;
    display: inline-block;
    font-size: 12px;
    height: 24px;
    line-height: 23px;
    margin:0;
    padding:0 10px 1px;
    white-space:nowrap;
	border-color:#21759B #21759B #1E6A8D;}
	
.lvds_btnmn a:hover{ background-color:#278AB7;
    background-image: linear-gradient(to bottom, #2E9FD2, #21759B);
    border-color: #1B607F;
    box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset;
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);}
</style>
</head>

<body>



<div class="main">

<h3 class="lvd_tit" style=" font-size:23px; font-weight:normal">
	<img src="<?php echo plugins_url(); ?>/zoho-salesiq/logo.png" height="57" width="57" style="vertical-align:middle; margin-right:5px"  />
    <span>Zoho SalesIQ</span></h3>
	<div class="lvd_note" id="info_bar">Chat with your visitors proactively, engage them effectively and close more deals.</div>
    
    
<div class="lvd_embd">
   		<div style="width:950px;">
            <div class="lvd_embdrht">
       

<form method="post" action="">

<textarea id="ldcodesnippet" onclick="this.select()" name="ldcodesnippet" style="height:100px;width:400px"><?php  echo get_option('ldcode') ?></textarea>

<?php $var = get_option('ldcode'); if ( !empty($var) && !preg_match( "/^<script[^>]*>\s*.+\s*(float\.ls|.+widgetcode.+\/widget)\s*.+\s*<\/script>$/s", $var )){ ?>
<script>document.getElementById("info_bar").innerHTML = "<b>Please paste valid chat widget code</b>";</script>
<?php } ?>

<div style = "margin-top: 10px; width: 600px;">
         <input type = "checkbox" id = "hidecode" name = "hidecode" value = "1" <?php if(get_option('hidecode') == 1) echo 'checked';?>/> Hide Chat widget ( The chat widget on your website will be disabled, and only the visitor tracking will work)
         </div>

            </div>
            <div class="lvd_embdmid">→</div>
            <div class="lvd_embdlft">Just paste the SalesIQ code snippet here. <div class="">And have your Zoho SalesIQ up and running in no time! <br> Incase you don't have an account. <br>Just <span><a target="_blank" href="https://salesiq.zoho.com/register.sas?source=WordPress.salesiqplugin">Register here</a></span> 	
            </div>
		</div>
   	</div>
	</div>
   
<div class="lvd_sbdata">
    	<h3 class="lvd_tit">How to get Chat Widget code snippet for Wordpress?</h3>
        
  		<ul>
        	<li><span>Log-in to your <a target="_blank" href="https://salesiq.zoho.com/login">Zoho SalesIQ</a> account.</span></li>
            <li><span>Go to Settings -> Websites and click on the Website's name.</span></li>
	    <li><span>Choose your preferred platform to engage with visitors, copy the code snippet and paste it here.</span></li>
	    <li><span>And, voila! You're finally done adding Live chat widget to your Wordpress.</span></li>
  	    <li><span>You can also refer our <a target="_blank" href="http://www.zoho.com/salesiq/help/getting-started-with-live-chat.html">User Guides</a> for more information.</span></li>

        </ul>
</div>

<div class="lvds_btnmn">

<input type="submit" name="ldsubmit" class="button button-primary" value="Save Changes" />
</form>
   

</div>
</div>


</body>
</html>



