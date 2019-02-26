<!doctype html>
<html>
    <head>
        <style>
            @media screen and (max-width: 500px) {
                #wrap {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body style="margin:0;padding:0;background:#efefef;">
        <table align="center" cellpadding="0" cellspacing="0" width="500" id="wrap">
            <tr>
                <td height="60"></td>
            </tr>
            <!-- Content -->
            <tr>
                <td>
                    <table style="padding:0 25px;" cellspacing="0" cellspacing="0" style="border:1px solid #e3e5e5" bgcolor="#fff" width="100%">
                        <tr>
                            <td height="60"></td>
                        </tr>
                        <tr>
                            <td style="text-align:center;">
                                <img src="<?php echo WPMM_IMAGES_URL . 'icon-48.png'; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td height="40"></td>
                        </tr>
                        <tr>
                            <td style="color:#747e7e;font-family:Lato, Helvetica, Arial, sans-serif;text-align:center;font-size:18px;font-weight:normal;">
								<?php printf(__('You have been contacted via %s.', $this->plugin_slug), get_bloginfo('name')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td height="30"></td>
                        </tr>
                        <tr>
                            <td width="100%">
                                <table cellspacing="0" cellpadding="0" width="100%">
                                    <tbody>
										<?php do_action('wpmm_contact_template_start'); ?>

                                        <tr>
                                            <td height="30"></td>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td width="20%" style="border-bottom:1px solid #e3e5e5;padding:0 0 30px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;font-weight:bold;">
												<?php _e('Name:', $this->plugin_slug); ?>
                                            </td>
                                            <td width="80%" style="border-bottom:1px solid #e3e5e5;padding:0 0 30px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;">
												<?php echo sanitize_text_field($_POST['name']); ?>
                                            </td>                                            
                                        </tr>
                                        <tr>
                                            <td height="30"></td>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td width="20%" style="border-bottom:1px solid #e3e5e5;padding:0 0 30px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;font-weight:bold;">
												<?php _e('Email:', $this->plugin_slug); ?>
                                            </td>
                                            <td width="80%" style="border-bottom:1px solid #e3e5e5;padding:0 0 30px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;">
												<?php echo sanitize_text_field($_POST['email']); ?>
                                            </td>
                                        </tr>

										<?php do_action('wpmm_contact_template_before_message'); ?>

                                        <tr>
                                            <td height="30"></td>
                                            <td height="30"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding:0 0 30px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;font-weight:bold;">
												<?php _e('Content:', $this->plugin_slug); ?>
                                            </td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2" style="padding:0 0 20px 20px;text-align:left;font-size:14px;font-family:Lato, Helvetica, Arial, sans-serif;color:#747e7e;">
												<?php echo nl2br(stripslashes($_POST['content'])); ?>
                                            </td>
                                        </tr> 

										<?php do_action('wpmm_contact_template_after_message'); ?>

										<?php do_action('wpmm_contact_template_end'); ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="60"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="60"></td>
            </tr>
            <!-- End Content -->
        </table>
    </body>
</html>