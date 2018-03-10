<?php
namespace BaftBase\Listener;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Router\RouteMatch;

class MaintenanceListener extends AbstractListenerAggregate
{

    private $baftBaseOption = [];

    public function __construct ($moduelOption)
    {
        $this->setOption($moduelOption);
    }

    public function attach (EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, 
                array(
                        $this,
                        'toMaintenace'
                ), - 999);
    }

    public function toMaintenace (MvcEvent $event)
    {
        
        
        $sm = $event->getTarget()->getServiceManager();
        
        if (! $this->getOption()->getEnableMaintenance()){
            
            $response = $event->getApplication()->getResponse();
            $response->setStatusCode(404);
            $response->setContent(
                    "due to login process , problem accured in requested adress");
            return $event->setResponse($response);
        }
        
        return ;
    }

}