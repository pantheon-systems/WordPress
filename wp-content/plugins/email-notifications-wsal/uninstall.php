<?php if (!defined( 'WP_UNINSTALL_PLUGIN')) exit();
require_once('email-notifications-wsal.php');
    /**
     * @internal
     * Deletes all notifications if any
     */
	function wsal_np_uninstall()
    {
        $this->wsal->DeleteByPrefix(WSAL_OPT_PREFIX);
    }
    /*
     * we leave the notifications for now
     */
//wsal_np_uninstall();