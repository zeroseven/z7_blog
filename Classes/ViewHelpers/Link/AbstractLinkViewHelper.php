<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper;
use Zeroseven\Z7Blog\Domain\Demand\PostDemand;

abstract class AbstractLinkViewHelper extends ActionViewHelper
{

    /** @var PostDemand */
    protected $demand;

    /** @var array */
    protected $parameterMapping;

    public function __construct()
    {
        parent::__construct();

        $this->demand = PostDemand::makeInstance();
        $this->parameterMapping = $this->demand->getParameterMapping();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register demand argument
        $this->registerArgument('demand', 'object', 'The demand object');

        // Register all allowed properties of demand object
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $this->registerArgument($propertyName, $this->demand->getType($propertyName), sprintf('Override value "%s" in demand object.', $propertyName));
        }
    }

    public function prepareArguments()
    {

        // If the type of an argument have to be an integer and an object is specified, try to get the uid
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            if (($value = $this->arguments[$propertyName] ?? null)
                && $value instanceof AbstractDomainObject
                && $this->demand->getType($propertyName) === 'int'
                && method_exists($value, 'getUid')) {
                $this->arguments[$propertyName] = $value->getUid();
            }
        }

        // Return the value of the parent function
        return parent::prepareArguments();
    }

    protected function overrideDemandParameters(): void
    {
        // Collect overrides
        $parameters = [];
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            if (($value = $this->arguments[$propertyName]) !== null) {
                $parameters[$parameter] = $value;
            }
        }

        // Override demand values
        if (!empty($parameter)) {
            $this->demand->setParameterArray(false, $parameters);
        }
    }

    protected function beforeRendering(): void
    {
    }

    public function initialize(): void
    {
        parent::initialize();

        // Take copy of given demand object
        if (($demand = $this->arguments['demand']) instanceof PostDemand) {
            $this->demand = clone $demand;
        }

        // Set default arguments
        $this->arguments['controller'] = 'Post';
        $this->arguments['pluginName'] = 'List';
    }

    public function render(): string
    {
        // Override demand arguments
        $this->overrideDemandParameters();

        // Set arguments
        $overrides = $this->demand->getParameterDiff($this->templateVariableContainer->get('settings'), ['list_id']);
        $this->arguments['arguments'] = array_merge((array)$this->arguments['arguments'], $overrides);

        // Call this method before the tag will be rendered by the actionViewHelper
        $this->beforeRendering();

        // Call the action ViewHelper (like <f:link.action … />)
        return parent::render();
    }
}
