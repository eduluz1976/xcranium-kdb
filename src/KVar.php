<?php

namespace xcranium\kdb;

class KVar {
    
    const NIL = 'NULL';
    public static $PROPERTIES = ['tags','title','type','formula'];
    
//    protected $defs = [];
    protected $name;
    protected $value = 'NULL';
    protected $lsRefs = [];
    protected $isMissing = true;


    public function __construct($name, $defs) {
	$this->name = $name;
//	$this->defs = $defs;
	if (is_array($defs)) {
	    foreach ($defs as $k => $v) {
		if (in_array($k, self::$PROPERTIES)) {
		    $this->$k = $v;
		    
		    if ($k === 'formula') {
			$this->analyze($v);
		    }
		}
	    }
	    
	}
	
    }
    
    
    public function getLsRefs() {
	return $this->lsRefs;
    }
    
    protected function analyze($v) {
	$refs = [];
	$output = [];
	preg_match_all('/(\$[\w]*)/', $v, $output);
	if (is_array($output) && (count($output)>0)) {
	    $refs = $output[0];
	}
	
	
	$output = [];
	preg_match_all('/(\@[\w]*)/', $v, $output);
	if (is_array($output) && (count($output)>0)) {
	    $refs =  array_merge($output[0],$refs);
	}
	$this->lsRefs = $refs;
    }
    
    
    public function getName() {
	return $this->name;
    }
    
    public function getValue() {
	return $this->value;
    }
    
    public function setValue($value) {
	$this->value = $value;
	$this->isMissing = false;
    }
    
    
    public function getTags() {
	$r = [];
	if (property_exists($this, "tags")) {
	    $r = $this->tags;
	}
	return $r;
    }
    
    public function getMeta() {
	return json_decode(json_encode($this),true);
    }
    
    public function isMissing() {
	return $this->isMissing;
    }
    
    public function getFormula() {
	if (property_exists($this, "formula")) {
	    return $this->formula;
	}
	return null;
    }
    
    
}
