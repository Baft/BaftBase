<?php

namespace bmi;

use codeIgniter\CIFTP;

class ftp {
	
	/**
	 * decoreated object
	 *
	 * @var codeIgniter\CIFTP
	 */
	protected $orginalFtp;

	function __construct($config = array()) {

		$this->orginalFtp = new CIFTP ( $config );
	
	}

	function __call($method_name, $arguments) {

		if (method_exists ( $this->orginalFtp, $method_name ))
			return call_user_func_array ( array (
					$this->orginalFtp,
					$method_name 
			), $arguments );
	
	}

	/**
	 * return ftp conncetion resource
	 *
	 * @return resource
	 */
	function getConnection() {

		return $this->orginalFtp->conn_id;
	
	}

	/**
	 * cehck if directory path eixsit
	 *
	 * @param string $direName
	 *        	:
	 * @return boolean
	 */
	function dir_exist($dirPath) {

		$dirPath = $this->normalizePath ( $dirPath );
		$currentDir = @ftp_pwd ( $this->getConnection () );
		if (@ftp_chdir ( $this->getConnection (), $dirPath )) {
			@ftp_chdir ( $this->getConnection (), $currentDir );
			return true;
		}
		return false;
	
	}

	public function listContent($dirPath, $recursive = false) {
		// @TODO avoid loop when $dirPath=. or ..
		$dirPath = $this->normalizePath ( $dirPath );
		if (is_array ( $children = @ftp_rawlist ( $this->orginalFtp->conn_id, $dirPath ) )) {
			$items = array ();
			
			foreach ( $children as $child ) {
				$chunks = preg_split ( "/\s+/", $child );
				// @TODO check systme type by ftp_systype() to determined how to parse
				list ( $item ['rights'], $item ['number'], $item ['user'], $item ['group'], $item ['size'], $item ['month'], $item ['day'], $item ['time'] ) = $chunks;
				$item ['type'] = $item ['rights'] {0} === 'd' ? 'directory' : 'file';
				array_splice ( $chunks, 0, 8 );
				// set file name as key
				$fileName = implode ( " ", $chunks );
				$items [$fileName] = $item;
				if ($recursive && strcasecmp ( $item ['type'], 'directory' ) == 0) {
					$childItems = $this->listContent ( $dirPath . "/{$fileName}", $recursive );
					foreach ( $childItems as $childItemName => $childItem ) {
						$items [$dirPath . "/{$fileName}/" . $childItemName] = $childItem;
					}
				}
			}
			
			return $items;
		}
	
	}

	/**
	 * make directory recusivley
	 *
	 * @param string $dirPath
	 *        	: absolute path to create , if relative passed convert to absolute
	 * @return boolean
	 */
	function mkdir($dirPath) {

		$dirPath = $this->normalizePath ( $dirPath );
		$dirNames = split ( "/", $dirPath );
		$path = "";
		$result = true;
		foreach ( $dirNames as $dirName ) {
			$path .= "/" . $dirName;
			if (! @ftp_chdir ( $this->getConnection (), $path )) {
				if (! $this->orginalFtp->mkdir ( $path )) {
					$result = $result && false;
					throw new \Exception ( "can not create folder '{$path}' \n" );
					return $result;
				}
				@ftp_chdir ( $this->getConnection (), $path );
			}
		}
		return $result;
	
	}

	function normalizePath($path) {

		return $path;
		// @TODO: PREG to check false path like : "/.","/.." in start of path , throw error is better
		// @TODO: correct and replace with absolute path these : "./","../" in start of path
		// @TODO: correct and replace with absolute path these : "... ./../../. ..." any combination of "."&".." in the path
		// @TODO: remove slash from end of path
		
		$currentDir = @ftp_pwd ( $this->getConnection () );
		$path = preg_replace ( array (
				'/^(.\/)/',
				'/^(\/.)/',
				'/(\/.\/)/',
				'/(\/.)$/' 
		), array (
				$currentDir,
				'',
				'/',
				'' 
		), $path );
		$dirNames = split ( "/", $path );
	
	}

	/**
	 *
	 * @param unknown $fileName        	
	 */
	function file_exist($fileName) {

		$dirPath = $this->normalizePath ( $dirPath );
	
	}


}