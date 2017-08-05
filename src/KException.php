<?php
namespace xcranium\kdb;


class KException extends \Exception {

    const FORMULA_NOT_EXISTS = 10;
    
    public static $MSGS = [
	10 => 'Formula not found'
    ];
    
    public static function get($code,$msg=false) {
	
	if (!$msg) {
	    $msg = (array_key_exists($code, self::$MSGS))?self::$MSGS[$code]:""; 
	}
	
	return new KException($msg, $code);
    }
    
}