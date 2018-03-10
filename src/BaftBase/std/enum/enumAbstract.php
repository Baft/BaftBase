<?php
namespace BaftBase\std\enum;

abstract class enumAbstract implements enumInterface {

	/**
	 *
	 * @see \BaftBase\std\enumInterface::getEnumName()
	 */
	public static function getEnumName() {

		$enumReflect = new \ReflectionClass ( get_called_class () );
		return $enumReflect->getNamespaceName ();

	}

	/**
	 * search constants by their name
	 *
	 * @see \BaftBase\std\enumInterface::isExistConst()
	 */
	public static function isExistConst($const) {

		$constList = self::getConstList ();

		if (array_key_exists ( $const, $constList ))
			return true;

		return false;

	}

	/**
	 * search in constants by their value
	 *
	 * @see \BaftBase\std\enumInterface::isExistConstValue()
	 */
	public static function isExistValue($constValue) {

		$constList = self::getConstList ();
		if (array_search ( $constValue, $constList ) !== false)
			return true;

		return false;

	}

	/**
	 * enable enum to be use like : $enumObject->constName
	 *
	 * @param string $const
	 *        	: name of constant
	 * @return string
	 */
	public function __get($const) {

		return $this->getValue ( $const );

	}

	/**
	 * enable enum to be used like : $enumObject($constName)
	 *
	 * @param string $const
	 *        	: name of constant
	 * @return string : value of constant
	 */
	public function __invoke($const) {

		return $this->getValue ( $const );

	}

	/**
	 * find conatant by value and get constant name
	 *
	 * @param string $value
	 *        	: constant name
	 */
	public static function findConstByValue($constValue) {

		$constList = self::getConstList ();

		$result = array_keys ( $constList, $constValue, true );
		if (empty ( $result ))
			return null;
		return $result [0];

	}

	/**
	 * get value of a constant
	 *
	 * @see \BaftBase\std\enumInterface::getConst()
	 */
	public static function getValue($const) {

		$constList = self::getConstList ();
		if (array_key_exists ( $const, $constList )) {
			return $constList [$const];
		} else
			throw new \UnexpectedValueException ( "constant with name '{$const}' is not exsit in enum '{$this->getEnumName()}'" );

	}

	/**
	 * list of constants name-value
	 *
	 * @see \BaftBase\std\enumInterface::getConstList()
	 */
	public static function getConstList() {

		$enumReflect = new \ReflectionClass ( get_called_class () );
		$constList = $enumReflect->getConstants ();
		return $constList;

	}

	/**
	 * enable enum to be used like: isset($enumObject->constName)
	 *
	 * @param string $const
	 * @return boolean
	 */
	public function __isset($const) {

		return $this->isExistConst ( $const );

	}


}