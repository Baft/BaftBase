<?php

namespace baft\model\vo;

abstract class voProxyAbstract {

	/*
	 * اگر یک موجودیت چندین شی پروکسی داشته باشد
	 * انگاه که یک تابعی از موجودیت را فرا می خوانیم که باید به ازای ان یک شی پروکسی
	 * مورد نطر فراحوانده شود
	 * چگونه بفهمیم که کدام کلاس را باید بار کنیم
	 */
	public function getVoProxy() {

		if (! isset ( $this->userProxy ))
			$this->billProxy = new userProxy ();
		return $this->billProxy;
	
	}

	public function __call($proxyMethod, $args = array()) {

		$this->setVoProxy ();
		if (method_exists ( $this->userProxy, $proxyMethod )) {
			$proxyRef = new \ReflectionClass ( $this->userProxy );
			$proxyMethod = $proxyRef->getMethod ( $proxyMethod );
			$proxyMethodParameters = $proxyMethod->getParameters ();
			$parametersValue = array ();
			if (! empty ( $proxyMethodParameters )) {
				foreach ( $proxyMethodParameters as $paramObejct ) {
					$paramName;
					if (isset ( $args [$paramObejct->getName ()] )) {
						$parametersValue [] = $args [$paramObejct->getName ()];
						continue;
					} elseif (! $paramObejct->isOptional ()) {
						$currentClassName = __CLASS__;
						throw new \Exception ( "\"{$paramObejct->getName()}\" parameter of \"{$proxyMethod}\" method from proxyObject \"{$proxyRef->getName()}\" is not passed any value , when calling from \"{$currentClassName}\" vo Object" );
					} else {
						$defaultValue = $paramObejct->getDefaultValue ();
						if ($defaultValue == null)
							$defaultValue = '';
						$parametersValue [] = $defaultValue;
						continue;
					}
				}
			}
			return call_user_func_array ( array (
					$this->userProxy,
					$proxyMethod 
			), $param_arr );
		}
	
	}


}
