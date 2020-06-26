<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper;
use Zeroseven\Z7Blog\Domain\Model\Demand;

abstract class AbstractLinkViewHelper extends ActionViewHelper
{

    /** @var Demand */
    protected $demand;

    /** @var array */
    protected $parameterMapping;

    public function __construct()
    {
        parent::__construct();

        $this->demand = Demand::makeInstance();
        $this->parameterMapping = $this->demand->getParameterMapping();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register demand argument
        $this->registerArgument('object', 'object', 'The demand object');

        // Register all allowed properties of demand object
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $this->registerArgument($propertyName, $this->demand->getType($propertyName), sprintf('Override value "%s" in demand object.', $propertyName));
        }
    }

    public function initialize(): void
    {
        parent::initialize();

        // Take copy of given demand object
        if (($demand = $this->arguments['object']) instanceof Demand) {
            $this->demand = clone $demand;
        }

        // Set default arguments
        $this->arguments['controller'] = 'Post';
        $this->arguments['pluginName'] = 'List';
    }

    public function overrideDemandParameters(): void
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

    public function render(): string
    {
        // Override demand arguments
        $this->overrideDemandParameters();

        // Set arguments
        $this->arguments['arguments'] = array_merge($this->arguments['arguments'] ?? [], $this->demand->getParameterArray(true));

        // Call the action ViewHelper (like <f:link.action … />)
        return parent::render();
    }
}
