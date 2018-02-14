<?php

namespace Wpae\App\Feed;


class Feed
{
    private $feedData;

    public function __construct($feedData)
    {
        $this->feedData = $feedData;

        //CASES:
        // function("{Snippet}");
        // function("pre-{Snippet}");
        // function("{Snippet}-after");
        // function("{Snippet1}{Snippet2}");
        // function("pre-{Snippet1}-{Snippet2}-pro");
        
        array_walk_recursive($this->feedData, function(&$item){
            $functions = array();
            preg_match_all('%(\[[^\]\[]*\])%', $item, $functions);

            if(is_array($functions) && count($functions) && !empty($functions[0])) {

                if(
                    strpos("\"{", $item) !== false ||
                    strpos("'{'", $item) !== false ||
                    strpos($item, ",{") !== false ||
                    strpos($item, "},") !== false ||
                    strpos($item, "({") !== false ||
                    strpos($item, "})") !== false
                ) {
                    $item = str_replace(array("\"{", "'{"), "{", $item);
                    $item = str_replace(array("}\"", "}'"), "}", $item);
                    $item = str_replace("{", "\"{", $item);
                    $item = str_replace("}", "}\"", $item);
                }
            }

        });
    }

    public function getFeedData()
    {
        return $this->feedData;
    }

    public function getSectionFeedData($section)
    {
        if(isset($this->feedData[$section])) {
            return $this->feedData[$section];
        } else {
            throw new \Exception('Unknown feed section '.$section);
        }
    }
}