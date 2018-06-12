<?php

namespace Wpae\Scheduling;


class Connection
{
   public function checkConnection()
   {
       $response = wp_remote_request(

           $this->getApiUrl('connection'),
           array(
               'method' => 'GET',
               'body' => \json_encode(array(get_site_url()))
           )
       );

       if($response instanceof \WP_Error) {
           return false;
       }

       if ($response['response']['code'] == 200) {
           return true;
       } else {
           return false;
       }
   }
}