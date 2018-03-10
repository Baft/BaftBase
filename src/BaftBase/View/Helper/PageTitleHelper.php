<?php
namespace BaftBase\View\Helper;
use Zend\View\Exception;
use Zend\View\Helper\TranslatorAwareTrait;
use Zend\View\Helper\Placeholder\Container\AbstractStandalone;
use Zend\View\Helper\Placeholder\Container\AbstractContainer;

/**
 * Helper for setting and retrieving title element for HTML head.
 *
 * Duck-types against Zend\I18n\Translator\TranslatorAwareInterface.
 */
class PageTitleHelper extends AbstractStandalone
{
    use TranslatorAwareTrait;

    private $description = '';

    /**
     * Retrieve placeholder for title element and optionally set state
     *
     * @param string $title            
     * @param string $setType            
     * @return HeadTitle
     */
    public function __invoke ($title = null, $description = null)
    {
        $setType = (null === $this->getDefaultAttachOrder()) ? AbstractContainer::APPEND : $this->getDefaultAttachOrder();
        
        $title = (string) $title;
        if ($title !== '') {
            if ($setType == AbstractContainer::SET) {
                $this->set($title);
            } elseif ($setType == AbstractContainer::PREPEND) {
                $this->prepend($title);
            } else {
                $this->append($title);
            }
        }
        
        if(!empty($description))
            $this->setDescription($description);
        
        return $this;
    }

    /**
     * Render title (wrapped by title tag)
     *
     * @param string|null $indent            
     * @return string
     */
    public function toString ($indent = null)
    {
        $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();
        
        $output = $this->renderTitle();
        
        
        return  $output  ;
    }

    /**
     * Render title string
     *
     * @return string
     */
    public function renderTitle ()
    {
        $items = [];
        
        $itemCallback = $this->getTitleItemCallback();
        foreach ($this as $item) {
            $items[] = $itemCallback($item);
        }
        
        $description=$this->renderDescription($this->getDescription());
        
        $separator = $this->getSeparator();
        $output = '';
        
        $prefix = $this->getPrefix();
        if ($prefix) {
            $output .= $prefix;
        }
        
        $output .= implode($separator, $items);
        $output .= $description;
        
        $postfix = $this->getPostfix();
        if ($postfix) {
            $output .= $postfix;
        }
        
        return $output;
    }
    
    protected function renderDescription($description){
        $itemCallback = $this->getTitleItemCallback();
        $description=$itemCallback($description);
        
        return '<small> ' . $description .' </small>';
    }

    /**
     * Set a default order to add titles
     *
     * @param string $setType            
     * @throws Exception\DomainException
     * @return HeadTitle
     */
    public function setDefaultAttachOrder ($setType)
    {
        if (! in_array($setType, 
                [
                        AbstractContainer::APPEND,
                        AbstractContainer::SET,
                        AbstractContainer::PREPEND
                ])) {
            throw new Exception\DomainException(
                    "You must use a valid attach order: 'PREPEND', 'APPEND' or 'SET'");
        }
        $this->defaultAttachOrder = $setType;
        
        return $this;
    }

    /**
     * Get the default attach order, if any.
     *
     * @return mixed
     */
    public function getDefaultAttachOrder ()
    {
        return $this->defaultAttachOrder;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function setDescription($description){
        $this->description=$description;
    }

    /**
     * Create and return a callback for normalizing title items.
     *
     * If translation is not enabled, or no translator is present, returns a
     * callable that simply returns the provided item; otherwise, returns a
     * callable that returns a translation of the provided item.
     *
     * @return callable
     */
    private function getTitleItemCallback ()
    {
        if (! $this->isTranslatorEnabled() || ! $this->hasTranslator()) {
            return function ($item)
            {
                return $item;
            };
        }
        
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();
        return function ($item) use ( $translator, $textDomain)
        {
            return $translator->translate($item, $textDomain);
        };
    }
}
