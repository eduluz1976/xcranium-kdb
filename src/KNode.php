<?php
namespace xcranium\kdb;

class KNode  {
    
    protected $id;
    protected $kdb;
    protected $objContainerID;
    
    
    /**
     * 
     * @param KDB $kdb
     * @param mixed $objContainerID object associated with this 
     */
    public function __construct($kdb, $objContainerID=false) {
	
	$this->kdb = clone $kdb;
//	$this->values = $kdb->getAllValues();
	$this->objContainerID = $objContainerID;
    }
    
    public function dump () {
	print_r($this);
    }
    
    public function __call($methodName, $arguments) {
	if (method_exists($this->kdb, $methodName)) {	    
	    switch (count($arguments)) {
		case 0 : return $this->kdb->$methodName();
		case 1 : return $this->kdb->$methodName($arguments[0]);
		case 2 : return $this->kdb->$methodName($arguments[0],$arguments[1]);
		case 3 : return $this->kdb->$methodName($arguments[0],$arguments[1],$arguments[2]);
		case 4 : return $this->kdb->$methodName($arguments[0],$arguments[1],$arguments[2],$arguments[3]);
	    }
	}
    }
    
    
    /**
     * 
     * @return KDB
     */
    public function getKDB() {
	return $this->kdb;
    }
    
//    public function getVars() {
//	return $this->kdb->getAllVars();
//    }
//    
//    public function getValues() {
//	return $this->kdb->getAllValues();
//    }
//    
//    public function getVar($varName) {
//	
//    }
    
    
    
}
