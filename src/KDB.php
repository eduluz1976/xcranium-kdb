<?php

namespace xcranium\kdb;

class KDB {
    
    const VERSION = 1;
    
    protected static $lsKDBs = [];
    protected $name;
    protected $textDefinition = false;
    protected $lsVars = [];
    protected $lsTags = [];
    protected $lsTargets = [];
    protected $version;
    
    /**
     * 
     * @param string $kdnName
     * @return KDB
     */
    public static function createNewDB($kdnName,$textDefinition=false) {	
	self::$lsKDBs[$kdnName] = new KDB($kdnName, $textDefinition);
	
	return self::$lsKDBs[$kdnName];
    }
    
    /**
     * 
     * @param string $kdnName
     * @param string $textDefinition
     * @return KDB
     */
    public static function get($kdnName, $textDefinition=false) {
	if (!array_key_exists($kdnName, self::$lsKDBs)) {
	    return self::createNewDB($kdnName);
	}
	return self::$lsKDBs[$kdnName];
    }
    
    
    
    public function __construct($kdnName,$textDefinition=false) {
	$this->name = $kdnName;
	if ($textDefinition) {
	    $this->textDefinition = $textDefinition;
	    // $this->parse($textDefinition);
	}
	
	$this->version = ''. self::VERSION .microtime(true) . strftime("%03d",rand(0, 1000));
	//echo "KDB $kdnName created";
    }
    
    
    
    public function getVersion() {
	return $this->version;
    }
    
    /**
     * 
     * @param string $name
     * @param mixed $defs
     * @return KDB
     */
    public function addKVar($name, $defs=[]) {
	$v = new KVar($name, $defs);
	
	//$deps = $v->getReferences();
	
	$this->lsVars[$name] = $v;
	
	return $this;
    }
    
    
    public function addKTag($name, $defs=[]) {
	$this->lsTags[$name] = new KTag($name, $defs);
	return $this;
    }
    
    
    public function updateIndex() {
	
	$tags = array_keys($this->lsTags);
	
	foreach ($this->lsVars as $v) {
	    if (false) $v = new KVar ("");
	    
	    $myTags = $v->getTags();
	    foreach ($myTags as $myTag) {
		if (in_array($myTag, $tags)) {
		    $this->lsTags[$myTag]->addRef($v->getName());
		}
	    }
//	    print_r($v->getTags()); exit;

	}
    }
    
    
    public function queryTag($tagName, $setTarget=false) {
	$resp = [];
	
	$tag = $this->lsTags[$tagName];
	$refs = $tag->getRefs();
	
	$resp['refs'] = $refs;
	$resp['values'] = [];
	foreach ($refs as $k) {
	    if ($setTarget) {
		$this->lsTargets[] = $k;
	    }
	    
	    $resp['values'][$k] = $this->lsVars[$k]->getValue();
	}
	
	//foreach ($this->)
	
	return $resp;
    }
    
    
    public function getMetaVar($varName) {
	return $this->lsVars[$varName]->getMeta();
    }
    
    public function setValue($varName, $varValue) {
	$this->lsVars[$varName]->setValue($varValue);
    }
    
    public function getValue($varName, $setTarget=false) {
	if ($setTarget) {
	    $this->lsTargets[] = $varName;
	}
	
	return $this->getVar($varName)->getValue();
    }
    
    public function getVar($varName) {
	if (array_key_exists($varName, $this->lsVars)) {
	    return $this->lsVars[$varName];	
	} else {
	    throw new KException("getVar error - $varName ");
	}
	
    }
    
    /**
     * 
     * @param callable $callback
     * @param boolean $forceAll
     */
    public function evaluate($callback=false, $forceAll=false) {
	// try to figurate which variables must be evaluated (chaining) and which 
	// should be updated
	
        KFormula::$currentKDB = $this;
        
        
	foreach ($this->lsVars as $varName => $varObj) {
	    if ($forceAll) {
		$this->getValue($varName,true);
	    }
	    KFormula::$vars[$varName] = $varObj->getValue();
	}
	

	
	$ls = $this->lsTargets;
			
	$resp = $this->iterateEvaluation($ls,true);
	
	
	return $resp;
	
	// try to find if will happen any looping (circular reference)
	
	// if have a valid callback, execute it when finish the processing
	
    }
    
    
    
    protected function iterateEvaluation($ls,$first=false) {
	
	$lsResp = [];
	
	$ls2 = [];
//	
//	if ($first) {
//	    print_r($ls);
////	    $ls2 = $ls;
//	}
	
	foreach ($ls as $varName) {
	    if ($this->getVar($varName)->isMissing()) {
		$ls2[] = $varName;
	    } 
	}
	
	$ls3 = [];

	
	foreach ($ls2 as $varName) {
	    $ls3 = array_merge($ls3, $this->getVar($varName)->getLsRefs() );
	}
	
	
	$ls4 = [];
	

	
	for($i=0;$i<count($ls3);$i++) {
	    
	    if (substr($ls3[$i],0,1)=='@') {
		//
	    } else { 
		$varName = $ls3[$i];

		if (substr($varName,0,1)=='$') {
		    $varName = substr($varName,1);

		    if ($this->getVar($varName)->isMissing()) {
			$ls4[] = $varName;
		    }

		}
	    }
	    
	}
	
	
	
	
	if (!empty($ls4)) {
	    $ls4 = array_merge($ls4, $this->iterateEvaluation($ls4));
	}
	
	
	if ($first) {
//	    print_r($ls);
	    $ls4 = array_merge($ls4,$ls);
//	    print_r($ls4);
//	    exit;
	}
	
	foreach ($ls4 as $varName) {
	    
	    try {
		$formula = $this->getVar($varName)->getFormula();
		$result = KFormula::evaluate($formula, $varName);
	    } catch (KException $ex) {		
		// is not an error
		//echo "\n ".$ex->getMessage() . " ($varName) \n";
	    }
	}
	
	$lsResp = $ls4;
	
	return $lsResp;
    }
    
    
    
    public function getAllValues() {
        $resp = [];
        foreach ($this->lsVars as $varName => $var) {
            $resp[$varName] = $var->getValue();
        }
        return $resp;
    }
    
    
    public function getAllVas() {        
        return $this->lsVars;
    }
    
    
}

