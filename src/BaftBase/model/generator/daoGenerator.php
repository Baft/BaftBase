<?php

namespace baft\model\generator;

use Zend\Db\Metadata as ZMetadata;

class daoGenerator {
	private $daoClass = <<<'daoClass'
<?php
namespace %1$s;

use %2$s as myselfVo;
use baft\model\dao\daoAbstract;
use baft\model\reflectionVoHydrator;

class %3$s extends daoAbstract {

	public function __init() {
	}

	public function getVo($voData = null) {
		return new myselfVo();
	}

}
daoClass;

	private function generateRepository($namespace, $className, $voNamespace, $classPostfix = 'Dao') {

		$className = $className . $classPostfix;
		return sprintf ( $this->class, $namespace, $voNamespace, $className );
	
	}

	public function generate($savePath, $namespace, $className, $voNamespace, $classPostfix = 'Dao') {

		$fileName = $className . $classPostfix;
		$daoNameList = [ ];
		$classString = $this->generateRepository ( $namespace, $className, $voNamespace, $classPostfix );
		
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
			$daoNameList [] = $fileName;
		}
		chmod ( $savePath, $orginalPermission );
	
	}


}
