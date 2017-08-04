<?php

namespace xcranium\kdb;

class KDB {
    
    protected static $lsKDBs = [];
    protected $name;
    protected $textDefinition = false;
    protected $lsVars = [];
    protected $lsTags = [];
    
    
    
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
	
	echo "KDB $kdnName created";
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
//	return $this->lsVars[$name];
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
    
    
    public function queryTag($tagName) {
	$resp = [];
	
	$tag = $this->lsTags[$tagName];
	$refs = $tag->getRefs();
	
	$resp['refs'] = $refs;
	$resp['values'] = [];
	foreach ($refs as $k) {
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
    
    
    /**
     * 
     * @param callable $callback
     */
    public function evaluate($callback=false) {
	// try to figurate which variables must be evaluated (chaining) and which 
	// should be updated
	
	// try to find if will happen any looping (circular reference)
	
	// if have a valid callback, execute it when finish the processing
	
    }
    
}

