<?php

namespace Wpae\App\Service;


use Wpae\App\Service\SnippetParser;

class CombineFields
{
    const DOUBLEQUOTES = "**DOUBLEQUOTES**";

    /** @var  SnippetParser */
    private $snippetParset;

    public function __construct()
    {
        $this->snippetParset = new SnippetParser();
    }

    /**
     * @param $functions
     * @param $combineMultipleFieldsValue
     * @param $articleData
     * @return string
     * @internal param $snippetParser
     */
    public static function prepareMultipleFieldsValue($functions, $combineMultipleFieldsValue, $articleData)
    {
        $combineFields = new CombineFields();

        $quotedFunctions = array();

        foreach ($functions as $function) {

            $function = str_replace("\\", '', $function);
            $function = str_replace('"', '', $function);
            $function = str_replace("'", '', $function);
            $function = str_replace(array('{'), '"{', $function);
            $function = str_replace(array('}'), '}"', $function);

            $quotedFunctions[] = $function;
        }

        foreach ($articleData as $key => $vl) {
            $vl = str_replace("\"", self::DOUBLEQUOTES,$vl);
            foreach ($quotedFunctions as &$quotedFunction) {
                $quotedFunction = str_replace('{' . $key . '}', $vl, $quotedFunction);
            }
        }

        foreach ($functions as $key => $function) {
            if (!empty($function)) {
                $combineMultipleFieldsValue = str_replace('[' . $function . ']', eval('return ' . $quotedFunctions[$key] . ';'), $combineMultipleFieldsValue);
            }
        }

        foreach ($articleData as $key => $vl) {
            $combineMultipleFieldsValue = str_replace('{' . $key . '}', str_replace(self::DOUBLEQUOTES, "\"", $vl), $combineMultipleFieldsValue);
        }

        $snippets = $combineFields->snippetParset->parseSnippets($combineMultipleFieldsValue);

        // Replace empty snippets with empty string
        foreach ($snippets as $snippet) {
            $combineMultipleFieldsValue = str_replace('{'.$snippet.'}', '', $combineMultipleFieldsValue);
        }
        return $combineMultipleFieldsValue;
    }
}