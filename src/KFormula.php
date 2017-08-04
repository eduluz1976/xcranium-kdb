<?php

namespace xcranium\kdb;



class KFormula {
    
    public static $FORMULAS = [
	'@current_date_sub' => 'KFormulaDate::subtractCurrentDate'
    ];
    
    public static $vars = [];
    
    public static function evaluate($formula) {	
	$ret = null;
	$preFormula = '';
//	$ks = array_keys(self::$FORMULAS);
	$output = [];
	preg_match_all('/(\$[\w]*)/', $formula, $output);
	if (is_array($output) && (count($output)>0)) {	    
	    $refs = $output[0];
	     foreach ($refs as $varName) {
		 if (array_key_exists($varName, self::$vars)) {
		     $preFormula .= " $$$varName = '". self::$vars[$varName] . "'; \n";
		 }
	     }
	}
	
	
//	$formula = $preFormula . $formula;
	
	
	$output = [];
	preg_match_all('/(\@[\w]*)/', $formula, $output);
	if (is_array($output) && (count($output)>0)) {
	    $refs =  $output[0];
	    
	    foreach ($refs as $funcName) {
		if (array_key_exists($funcName, self::$FORMULAS)) {
		    $formula = str_replace($funcName, self::$FORMULAS[$funcName], $formula);
		}
	    }
	}

	$formulaFinal = $preFormula . $formula . ';';
	
	echo "\n\n --- formula = $formulaFinal \n\n";
	
	return $ret;
    }
    
}




class KFormulaDate {
    
    public function subtractCurrentDate($date) {
	return 37;
    }
    
}