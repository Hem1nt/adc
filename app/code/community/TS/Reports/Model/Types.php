<?php

class TS_Reports_Model_Types {

	const UNKNOWN 	= -1;
	const REGULAR 	= 0;
	const GROUP 	= 1;
	const SPECIAL 	= 2;
	const TIER 		= 3;
	const CATALOG	= 4;
	const ADMIN		= 5;
	
	private static $types;
	private static $typeNames;
	
	public function __construct(){
		$r = new ReflectionClass($this);
        self::$types = $r->getConstants();
        self::$typeNames = array_flip(self::$types);
	}
	
	public static function getTypes(){ // const VALUES as keys
		return self::$types;
	}
	
	public static function getTypeNames(){ //const NAMES as keys
		return self::$typeNames;
	}
}