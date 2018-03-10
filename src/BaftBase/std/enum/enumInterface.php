<?php

namespace BaftBase\std\enum;

interface enumInterface {

	/**
	 * get constValue of a constName , return exception if not exist
	 *
	 * @param string $const
	 *        	: const name
	 * @return string
	 */
	public static function getValue($const);

	/**
	 * find constant name by value and return name of constant
	 *
	 * @param stirng $constValue
	 */
	public static function findConstByValue($constValue);

	/**
	 * list of consts
	 *
	 * @return array
	 */
	public static function getConstList();

	/**
	 * check if is exist const in ConstsList , search by constName
	 *
	 * @param string $const
	 *        	: constName
	 * @return boolean
	 */
	public static function isExistConst($const);

	/**
	 * check if constValue is exsit in constants , search by constValue
	 *
	 * @param string $constValue
	 *        	: value of const
	 * @return boolean
	 */
	public static function isExistValue($constValue);

	/**
	 * return name of enumerationClass
	 *
	 * @return string
	 */
	public static function getEnumName();


}