<?php if(! defined('WSAL_OPT_PREFIX')) { exit('Invalid request'); }
/**
 * Class WSAL_NP_Expression
 * Utility class to evaluate an expression that will replace the eval function
 * @author wp.kytten
 */
class WSAL_NP_Expression
{
    private $notif = null;
    private $_s1Data = null;
    private $_s2Data = null;
    private $_s3Data = null;
    private $title = '';
    private $_expr = null;

    final public function __construct(WSAL_NP_Notifier $notif, array $s1Data, array $s2Data, array $s3Data, $title){
        $this->notif = $notif;
        $this->_s1Data = $s1Data;
        $this->_s2Data = $s2Data;
        $this->_s3Data = $s3Data;
        $this->title = $title;
    }

    final public function EvaluateConditions(array $data){
        $result = false;
        if(empty($data)){
            return $result;
        }

        $firstItem = false;
        $prevOp = null;
        $expArray = array();

        foreach($data as $i => $entry)
        {
            // Single
            if(isset($entry['select1']))
            {
                $r = $this->_evaluateTrigger($entry);

                if($i < 1){
                    array_push($expArray, $r);
                    $firstItem = true;
                }
                else {
                    if($entry['select1'] == 1){
                        $prevOp = '||';
                    } else { $prevOp = '&&'; }
                    array_push($expArray, $prevOp, $r);
                    $firstItem = true;
                }
            }
            // Group
            else {
                $ca = array();
                foreach($entry as $k => $item)
                {
                    $r = $this->_evaluateTrigger($item);
                    // first group - op before the array
                    if(empty($ca) && $firstItem){
                        $prevOp = $this->_getOperator($item['select1']);
                        array_push($expArray, $prevOp);
                    }
                    if(isset($entry[$k+1])){ // next is available
                        $prevOp = $this->_getOperator($entry[$k+1]['select1']);
                        array_push($ca, $r, $prevOp);
                    }
                    else { array_push($ca, $r); }
                }
                array_push($expArray, $ca);
                $firstItem = true;
            }
        }
        $this->_expr = $expArray;
        return $this->__evaluateFinalArray($expArray);
    }

    final protected function __evaluateArray($array){
        $prevResult = null;
        $op = null;
        // evaluate as we move forward into the array
        foreach($array as $k => $value)
        {
            if(is_bool($value)){
                if(is_null($prevResult)){
                    $prevResult = $value;
                }
                else {
                    if($op=='||'){
                        $prevResult = $prevResult || $value;
                    }
                    else {
                        $prevResult = $prevResult && $value;
                    }
                }
            }
            elseif(is_string($value)){
                $op = $value;
            }
        }
        return $prevResult;
    }

    final protected  function __evaluateFinalArray($array)
    {
        $prevResult = null;
        $op = null;
        foreach ($array as $k => $value) {
            if (is_bool($value)) {
                if (is_null($prevResult)) {
                    $prevResult = $value;
                } else {
                    if ($op=='||') {
                        $prevResult = $prevResult || $value;
                    } else {
                        $prevResult = $prevResult && $value;
                    }
                }
            } elseif (is_string($value)) {
                $op = $value;
            } elseif (is_array($value)) {
                $t = $this->__evaluateArray($value);
                if (is_null($prevResult)) {
                    $prevResult = $t;
                } else {
                    if ($op=='||') {
                        $prevResult = $prevResult || $t;
                    } else {
                        $prevResult = $prevResult && $t;
                    }
                }
            }
        }
        return $prevResult;
    }

    /**
     * @param integer $s1
     * @return string
     */
    final protected function _getOperator($s1){
        if($s1 == 1){
            return '||';
        }
        return '&&';
    }

    final public function GetLastExpressionArray(){
        return $this->_expr;
    }

    final public function GetExpressionAsString(array $expression){
        $exprString = '';
        foreach($expression as $item){
            if(is_bool($item)){
                if($item){ $exprString .= 'TRUE'; }
                else { $exprString .= 'FALSE'; }
            }
            elseif(is_string($item)){
                $exprString .= ' '.$item.' ';
            }
            elseif(is_array($item)){
                $exprString .= '(';
                foreach($item as $entry){
                    if(is_bool($entry)){
                        if($entry){ $exprString .= 'TRUE'; }
                        else { $exprString .= 'FALSE'; }
                    }
                    elseif(is_string($entry)){
                        $exprString .= ' '.$entry.' ';
                    }
                }
                $exprString .= ')';
            }
        }
        return $exprString;
    }

    /**
     * Evaluate a trigger
     * @param array $condition
     * @return bool
     */
    final protected function _evaluateTrigger(array $condition){
        if(empty($condition)){ return false;}
        $s1 = $this->_s1Data[$condition['select1']];
        $s2 = $this->_s2Data[$condition['select2']];
        $s3 = $this->_s3Data[$condition['select3']];
        $i1 = $condition['input1'];
        return $this->notif->_checkIfConditionMatch($s1, $s2, $s3, $i1, $this->title);
    }
}