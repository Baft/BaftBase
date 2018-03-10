<?php

namespace baft\model\generator;

use Zend\Db\Metadata as ZMetadata;

class repositoryGenerator {
	private $class = <<<'repositoryClass'
<?php
namespace %1$s;

use baft\model\repository\repositoryAbstract;
use baft\model\reflectionVoHydrator;
use baft\model\repository\paginationRepository;
use %2$s as myselfDao;

class %3$s extends repositoryAbstract {

	public function getDaoInstance() {
		return new myselfDao($this->getServiceLocator()
				->get('%4$s'));
	}
}
repositoryClass;

	private function generateRepository($namespace, $className, $daoNamespace, $adapterServiceName, $classPostfix = 'Repository') {

		$className = $className . $classPostfix;
		return sprintf ( $this->class, $namespace, $daoNamespace, $className, $adapterServiceName );
	
	}


}
