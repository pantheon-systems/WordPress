<?php

namespace Wpae\App\Controller;

use Wpae\Controller\BaseController;
use Wpae\Http\JsonResponse;
use Wpae\Http\Request;

class AttributesController extends BaseController
{
    public function getAction(Request $request)
    {
        $attributes_fields = array();

        global $wp_taxonomies;

        if(is_array($wp_taxonomies)){
            foreach ($wp_taxonomies as $key => $obj) {	if (in_array($obj->name, array('nav_menu'))) continue;

                if (strpos($obj->name, "pa_") === 0 and strlen($obj->name) > 3 )
                    $attributes_fields[] = array('atrribute_id' => $obj->id, 'attribute_name' => $obj->name, 'attribute_label' =>$obj->label);

            }
        }


        return new JsonResponse($attributes_fields);
    }
}