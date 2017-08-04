<?php

namespace xcranium\kdb;

class KTag {
    
    protected $name;
    protected $defs = [];
    protected $lsRefs = [];
    
    
    public function __construct($name, $defs=[]) {
	$this->name = $name;
	$this->defs = $defs;
    }
    
    public function addRef($name) {
	$this->lsRefs[$name] = 1;
    }
    
    public function getRefs() {
	return array_keys($this->lsRefs);
    }
    
}