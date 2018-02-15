<?php

namespace Wpae\Security;


class AccessControl
{

    public function checkAdminReferrer()
    {
        \check_admin_referer('options', '_wpnonce_options');
    }

}