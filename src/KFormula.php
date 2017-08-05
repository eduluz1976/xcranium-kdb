<?php

namespace xcranium\kdb;



class KFormula {
    
    public static $FORMULAS = [
	'@current_date_sub' => 'xcranium\kdb\KFormulaDate::getDiffYearsFromCurrentDate'
    ];
    
    public static $vars = [];
    public static $currentKDB = null;
    
    
    public static function evaluate($formula, $targetVarName) {	
	$ret = null;
	$preFormula = ' ';
//	$ks = array_keys(self::$FORMULAS);
	$output = [];
	preg_match_all('/(\$[\w]*)/', $formula, $output);
	if (is_array($output) && (count($output)>0)) {	    
	    $refs = $output[0];
//            print_r($output[0]); exit;
//            die(json_encode($output));
	     foreach ($refs as $varName) {
                 $rawVarName = substr($varName,1);
		 if (array_key_exists($rawVarName, self::$vars)) {
		     $preFormula .= "  $varName = '". self::$vars[$rawVarName] . "'; \n";
		 }
	     }
	}
	
        $preFormula .= "\n return ";
	
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
	
	//echo "\n\n --- formula = $formulaFinal \n\n";
	
        //echo json_encode(self::$vars)."\n\n";
        
        
        foreach (self::$vars as $k => $v) {
            $$k = $v;
        }
        
        $ret = eval($formulaFinal);
        
        self::$vars[$targetVarName] = $ret;
        
        if (!is_null(self::$currentKDB)) {
            self::$currentKDB->getVar($targetVarName)->setValue($ret);
        }
        
	return $ret;
    }
    
}




class KFormulaDate {
    
    public function subtractCurrentDate($date) {
        
        $date1 = \DateTime::createFromFormat('Y-m-d', $date);
       
        
        
	return $date1;
    }
    public function getDiffYearsFromCurrentDate($date) {
        
        $date1 = \DateTime::createFromFormat('Y-m-d', $date);
       
        $currDate = new \DateTime();
        $di = $currDate->diff($date1);
        
//        $di = new \DateInterval();
//        $di->
        
        $years = $di->y;
        
	return $years;
    }
    
}