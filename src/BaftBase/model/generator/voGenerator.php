<?php

namespace baft\model\generator;

use Zend\Db\Metadata as ZMetadata;

/**
 *
 * @author root
 *        
 *         $generatorVo = new voGenerator();
 *         $voNameList = $generatorVo->generate($adapter, APP_ROOT . DS . 'DDD' . DS . 'vo2', 'DDD\vo');
 *        
 */
class voGenerator {

	/**
	 *
	 * @param unknown $nameSpace        	
	 * @param unknown $tableName        	
	 * @param unknown $schema        	
	 * @param array $columns        	
	 * @return string
	 */
	private function generateVo($nameSpace, $tableName, $schema, array $columns) {

		$classTemplate = <<<'classTemplate'
<?php
namespace %1$s;
use baft\model\vo\voAbstract;
class %2$s extends voAbstract{
	//properties
	const TABLE = "%2$s";
	const SCHEMA = "%3$s";

%4$s
	//setterGetter
	%5$s
}
classTemplate;
		$columnsTemplate = <<<'columnTemplate'
		private $%s;
columnTemplate;
		$setterTemplate = <<<'setterTemplate'
public function set%1$s($value){
	$this->%2$s=$value;
	return $this;
}
setterTemplate;
		$getterTemplate = <<<'getterTemplate'
public function get%1$s(){
	return $this->%2$s;
}
getterTemplate;
		$classPropertiesString = '';
		$classSettersString = '';
		$classGettersString = '';
		if (! empty ( $columns ))
			foreach ( $columns as $column ) {
				$columnName = ucfirst ( $column->getName () );
				$classPropertiesString .= sprintf ( $columnsTemplate, $column->getName () ) . PHP_EOL;
				$classSettersString .= sprintf ( $setterTemplate, $columnName, $column->getName () ) . PHP_EOL;
				$classGettersString .= sprintf ( $getterTemplate, $columnName, $column->getName () ) . PHP_EOL;
			}
		$classMethodsString = $classSettersString . $classGettersString;
		$classString = sprintf ( $classTemplate, $nameSpace, $tableName, $schema, $classPropertiesString, $classMethodsString );
		return $classString;
	
	}

	/**
	 *
	 * @param AdapterInterface $adapter        	
	 * @param string $savePath
	 *        	: path to save vo class file
	 * @param string $namespace
	 *        	: namespace of vo classes
	 * @param string $fileNamePostfix
	 *        	: postfix of table name , like `user` . `_vo` = `user_vo`
	 * @throws \Exception on directory access or not existance
	 * @return array of vo name list
	 */
	public function generate($adapter, $savePath, $namespace, $fileNamePostfix = '') {

		$metadata = new ZMetadata\Metadata ( $adapter );
		$tableNames = $metadata->getTableNames ();
		$schemaName = $adapter->getCurrentSchema ();
		$voNameList = [ ];
		foreach ( $tableNames as $tableName ) {
			
			$tableObject = $metadata->getTable ( $tableName );
			$tableColumns = $tableObject->getColumns ();
			$tableConstraints = $metadata->getConstraints ( $tableName );
			
			$forignKeys = array ();
			foreach ( $tableConstraints as $constraint ) {
				if (! $constraint->hasColumns ()) {
					continue;
				}
				$constaintColumns = $constraint->getColumns ();
				// indexed fileds -> echo $constraint->getTableName().":".implode(',', $constraint->getColumns()).'<br/>';
				if ($constraint->isForeignKey ()) {
					foreach ( $constraint->getReferencedColumns () as $refColumn ) {
						$forignKeys [$constraint->getReferencedTableName ()] = $refColumn;
					}
				}
			}
			
			$classString = $this->generateVo ( $namespace, $tableName, $schemaName, $tableColumns );
			$fileName = $tableName . $fileNamePostfix;
			try {
				
				if (! is_dir ( $savePath )) {
					if (! mkdir ( $savePath ))
						throw new \Exception ( 'directory is not exist' );
				}
				$orginalPermission = substr ( sprintf ( '%o', fileperms ( $savePath ) ), - 4 );
				chmod ( $savePath, 755 );
				$classFileResource = fopen ( $savePath . DS . $fileName . ".php", 'w' );
				if ($classFileResource) {
					fwrite ( $classFileResource, $classString );
					fclose ( $classFileResource );
					$voNameList [] = $fileName;
				}
				chmod ( $savePath, $orginalPermission );
			}
			catch ( \Exception $ex ) {
				throw new \Exception ( 'sss' );
			}
		}
		
		return $voNameList;
	
	}


}
