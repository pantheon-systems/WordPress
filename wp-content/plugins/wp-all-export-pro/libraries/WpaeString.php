<?php


class WpaeString
{
    const MIDDLE_COMMA = "*middlecomma*";

    public function isBetween($haystack, $search, $start, $end)
    {
        if($haystack == $search) {
            return false;
        }
        if (strpos($haystack, $start) === false) {
            return false;
        }

        $haystack = str_replace($search, "#SNIPPET#", $haystack);
        $search = "#SNIPPET#";

        $searchPosition = strpos($haystack, $search);

        $firstString = substr($haystack, 0, $searchPosition);
        $lastString = substr($haystack, $searchPosition + strlen($search), strlen($haystack));

        $isInFirstString = false;
        $isInLastString = false;

        // Make sure the number of strings in the part before and after is not equal
        // to exclude cases like [a]b[c] we want only cases like a[b]c
        $numberOfStartInFirstString = substr_count($firstString, $start);
        $numberOfEndInFirstString = substr_count($firstString, $end);

        $numberOfStartInLastString = substr_count($lastString, $start);
        $numberOfEndInLastString = substr_count($lastString, $end);

        if(strpos($firstString, $start) !== false && $numberOfStartInFirstString - $numberOfEndInFirstString) {
            $isInFirstString = true;
        }

        if(strpos($lastString, $end) !== false && $numberOfStartInLastString - $numberOfEndInLastString) {
            $isInLastString = true;
        }

        return $isInFirstString && $isInLastString;
    }

    /**
     * @param $sanitizedSnippet
     * @return mixed
     */
    public function quoteParams($sanitizedSnippet)
    {
        if(strpos($sanitizedSnippet, 'array') !== false) {
            return $sanitizedSnippet;
        }

        if(strpos($sanitizedSnippet, WpaeXmlProcessor::SNIPPET_DELIMITER) === false && strpos($sanitizedSnippet, '"') === false && strpos($sanitizedSnippet, "'") === false ) {
            $sanitizedSnippet = str_replace("(","(\"",$sanitizedSnippet);
            $sanitizedSnippet = str_replace(")","\")",$sanitizedSnippet);

            return $sanitizedSnippet;
        }

        $sanitizedSnippet = str_replace(WpaeXmlProcessor::SNIPPET_DELIMITER, '"', $sanitizedSnippet);

        $sanitizedString = "";

        $isInString = false;

        for($i=0; $i< strlen($sanitizedSnippet); $i++) {
            if($sanitizedSnippet[$i] == "\"") {
                if($isInString) {
                    $isInString = false;
                } else {
                    $isInString = true;
                }

            }

            if($sanitizedSnippet[$i] === "," && $isInString) {
                $sanitizedString.= self::MIDDLE_COMMA;
            } else {
                $sanitizedString .= $sanitizedSnippet[$i];
            }
        }

        $sanitizedSnippet = $sanitizedString;

        if(strpos($sanitizedSnippet,"(") !== false && strpos($sanitizedSnippet, ")") !== false) {

            $sanitizedSnippet = str_replace("()", '("")', $sanitizedSnippet);
            $parts = explode("(", $sanitizedSnippet);
            if (!isset($parts[1])) {
                //TODO: Can this happen?
            }
            $params = $parts[1];
            $parameterPart = $parts[1];
            $originalParameterPart = $parts[1];

            $params = explode(")", $params);
            $params = $params[0];

            if (strpos($params, ",") !== false) {
                $params = explode(",", $params);
            } else {
                $params = array($params);
            }

            foreach ($params as $param) {
                if (!preg_match('/".*"/', $param)) {
                    $parameterPart = str_replace(','.$param, ',"' .trim($param) . '"', $parameterPart);
                    $parameterPart = str_replace('('.$param, '("' .trim($param) . '"', $parameterPart);
                }
            }

            $sanitizedSnippet = str_replace($originalParameterPart, $parameterPart, $sanitizedSnippet);
        }

        $sanitizedSnippet = str_replace(self::MIDDLE_COMMA, ',', $sanitizedSnippet);

        return $sanitizedSnippet;
    }

    /**
     * @param $sanitizedSnippet
     * @return string
     */
    private function quoteStringWithTokenizer($sanitizedSnippet)
    {
        $sanitizedSnippet = explode("(", $sanitizedSnippet);
        $functionName = $sanitizedSnippet[0];
        $sanitizedSnippet = $sanitizedSnippet[1];
        $sanitizedSnippet = str_replace(')', '', $sanitizedSnippet);

        $tokens = token_get_all('<?php ' . $sanitizedSnippet . ' ?>');

        $sanitizedString = "";

        foreach ($tokens as $token) {
            if ($token[0] == 319) {
                $sanitizedString .= '"' . $token[1] . '",';
            }
            if ($token[0] == 323) {
                $sanitizedString .= $token[1] . ',';
            }
        }

        $sanitizedString = substr($sanitizedString, 0, -1);
        $sanitizedString = $functionName . '(' . $sanitizedSnippet . ')';

        return $sanitizedString;
    }
}