<?php

namespace Wpae\App\Service;

class SnippetParser
{
    const SNIPPET_MATCH_REGEX = '/{([^}^\"^\']*)}/';

    const FUNCTION_MATCH_REGEX = '%(\[[^\]\[]*\])%';

    public function parseSnippets($string)
    {
        $snippets = array();

        preg_match_all(self::SNIPPET_MATCH_REGEX, $string, $snippets);

        if(is_array($snippets)) {
            $snippets = array_filter($snippets[1]);
        }

        foreach ($snippets as &$snippet) {
            $snippet = trim($snippet, "{}");
        }
        return $snippets;
    }

    public function parseFunctions($string)
    {
        $functions = array();
        $functionsResponse = array();

        preg_match_all(self::FUNCTION_MATCH_REGEX, $string, $functions);

        if(is_array($functions) && isset($functions[0]) && !empty($functions[0]) && $functions[0]) {

            $functionsResponse[] = $functions[0];
        }


        $functionsResponse = array_filter($functionsResponse);
        if(isset($functionsResponse[0])) {
            $functionsResponse = $functionsResponse[0];
        }

        foreach($functionsResponse as &$function) {
            $function = trim($function,"[]");

        }

        return $functionsResponse;
    }
}