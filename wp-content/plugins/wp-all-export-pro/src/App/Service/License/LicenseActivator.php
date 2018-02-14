<?php

namespace Wpae\App\Service\License;


use \PMXE_Plugin;

class LicenseActivator
{
    const CONTEXT_PMXE = 1;
    const CONTEXT_SCHEDULING = 2;

    public function activateLicense($productName, $context = self::CONTEXT_PMXE)
    {
        if($context == self::CONTEXT_PMXE) {
            $licenseField = 'license';
            $licenseStatusField = 'license_status';
        } else {
            $licenseField = 'scheduling_license';
            $licenseStatusField = 'scheduling_license_status';
        }

        $options = PMXE_Plugin::getInstance()->getOption();

        if ($productName !== false) {
            // data to send in our API request
            $api_params = array(
                'edd_action' => 'activate_license',
                'license' => PMXE_Plugin::decode($options[$licenseField]),
                'item_name' => urlencode($productName), // the name of our product in EDD
                'url' => home_url()
            );

            // Call the custom API.
            $response = wp_remote_get(add_query_arg($api_params, $options['info_api_url']), array('timeout' => 15, 'sslverify' => false));

            // make sure the response came back okay
            if (is_wp_error($response))
                return false;

            // decode the license data
            $license_data = json_decode(wp_remote_retrieve_body($response));

            // $license_data->license will be either "active" or "inactive"

            $options[$licenseStatusField] = $license_data->license;

            PMXE_Plugin::getInstance()->updateOption($options);
        }
    }

    /**
     * @param $productName
     * @param $options
     * @param $context
     * @return bool
     */
    public function checkLicense($productName, $options, $context)
    {
        if($context == self::CONTEXT_PMXE) {
            $licenseField = 'license';
        } else {
            $licenseField = 'scheduling_license';
        }

        if (!empty($options[$licenseField])) {

            if ($productName !== false) {

                $api_params = array(
                    'edd_action' => 'check_license',
                    'license' => PMXE_Plugin::decode($options[$licenseField]),
                    'item_name' => urlencode($productName)
                );

                // Call the custom API.
                $response = wp_remote_get(add_query_arg($api_params, $options['info_api_url']), array('timeout' => 15, 'sslverify' => false));

                if (is_wp_error($response))
                    return false;

                $license_data = json_decode(wp_remote_retrieve_body($response));

                return $license_data->license;

            }
        }

        return false;
    }
}