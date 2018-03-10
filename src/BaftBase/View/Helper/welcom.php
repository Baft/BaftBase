<?php 
namespace Application\view\helper;
use Zend\View\Helper\AbstractHelper;

class welcom extends AbstractHelper
{

    public function __invoke()
    {
        $this->count++;
        $output  = sprintf("I have seen 'The Jerk' %d time(s).", $this->count);
        $escaper = $this->getView()->plugin('escapehtml');
        return $escaper($output);
    }
}