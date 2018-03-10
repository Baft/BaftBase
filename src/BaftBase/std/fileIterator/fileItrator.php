<?php

namespace BaftBase\std\fileIterator;

use Zend\Db\Adapter\Driver\ResultInterface;

/**
 * iterate over data file line by line
 * data file , driver adapter for zend db
 * used when need read data file as resultset
 *
 * @author web
 *        
 */
class fileItrator implements ResultInterface {
	
	/**
	 * file resource
	 *
	 * @var \SplFileObject
	 */
	private $fileObject;
	
	/**
	 * fields delimeter
	 *
	 * @var string
	 */
	private $delimeter;
	
	/**
	 * line delimeter
	 *
	 * @var string
	 */
	private $lineSeparator;
	
	/**
	 * total count of lines
	 *
	 * @var integer
	 */
	private $lineNumber = false;

	public function __construct($fileObject, $delimeter, $lineSeparator) {

		if (is_string ( $fileObject ))
			$fileObject = new \SplFileObject ( $fileObject, "r" );
		
		if (! $fileObject instanceof \SplFileObject)
			throw new \Exception ( 'fileIterator expect fileResource to be instanceof splfile/fileName . type of " ' . gettype ( $fileResource ) . '" passed' );
		
		$fileObject->setFlags ( \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE );
		$this->fileObject = $fileObject;
		$this->delimeter = $delimeter;
		$this->lineSeparator = $lineSeparator;
		$this->count ();
	
	}

	public function __unset($name) {

		$this->fileObject->__destruct ();
		unset ( $this->fileObject );
	
	}

	public function __isset($name) {

		return $this->count () > 0;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Paginator\Adapter\Iterator::count()
	 */
	public function count() {
		// one time during object life time
		if ($this->lineNumber === false && ($this->fileObject->getFlags () & \SplFileObject::READ_AHEAD) == \SplFileObject::READ_AHEAD) {
			$this->lineNumber = iterator_count ( $this->fileObject );
		}
		
		// if count dose not worked because of READ_AHEAD flag
		if ($this->lineNumber === false) {
			try {
				$this->fileObject->seek ( $this->fileObject->getSize () );
				$this->lineNumber = $this->fileObject->key ();
			}
			catch ( \Exception $ex ) {
				// could not calculate filesize because file is in memory yet
				return false;
			}
		}
		
		// @TODO count file using another way , reading file handy not using spl ;)
		
		return $this->lineNumber;
	
	}

	/**
	 * count remain line to the end of file from current line
	 */
	public function countRemain() {

		if ($this->count () === false)
			return false;
		return $this->count () - $this->key ();
	
	}

	/**
	 * return field delimeter
	 *
	 * @return string
	 */
	public function getDelimeter() {

		return $this->delimeter;
	
	}

	/**
	 * return line separator
	 *
	 * @return string
	 */
	public function getLineDelimeter() {

		return $this->lineSeparator;
	
	}

	/**
	 * return lines in the range $offset<=range<=$offset*$itemCountPerPage
	 *
	 * @param integer $offset        	
	 * @param integer $itemCountPerPage        	
	 */
	public function getItems($offset, $itemCountPerPage) {
		// @TODO : limit in file line
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::buffer()
	 */
	public function buffer() {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::getAffectedRows()
	 */
	public function getAffectedRows() {

		return $this->count ();
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::getFieldCount()
	 */
	public function getFieldCount() {

		$line = $this->fileObject->current ();
		
		if (is_string ( $line ))
			return explode ( $this->delmeter, $line );
			
			// if csv flag is on
		if (is_array ( $line ))
			return $this->count ( $line );
	
	}

	/**
	 * is an alias for getFields function
	 *
	 * @param string $rowNumber        	
	 */
	public function getRow($rowNumber = true) {

		return $this->getFields ( $rowNumber );
	
	}

	/**
	 * return current record fileds
	 *
	 * @param boolean $withRowNumber
	 *        	is add row number in record
	 * @return array
	 */
	public function getFields($withRowNumber = false) {

		$record = $this->current ();
		if ($withRowNumber)
			$record = $this->key () . $this->delimeter . $record;
		return explode ( $this->delimeter, $record );
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::getGeneratedValue()
	 */
	public function getGeneratedValue() {
		// TODO Auto-generated method stub
	}

	/**
	 *
	 * @return \SplFileObject
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::getResource()
	 */
	public function getResource() {

		return $this->fileObject;
	
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::isBuffered()
	 */
	public function isBuffered() {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc)
	 * @see \Zend\Db\Adapter\Driver\ResultInterface::isQueryResult()
	 */
	public function isQueryResult() {
		// TODO Auto-generated method stub
	}

	/*
	 * (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current() {

		return $this->fileObject->current ();
	
	}

	/*
	 * (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() {

		return $this->fileObject->key ();
	
	}

	/*
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() {

		return $this->fileObject->next ();
	
	}

	/*
	 * (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {

		return $this->fileObject->rewind ();
	
	}

	/*
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {

		return $this->fileObject->valid ();
	
	}


}