<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper;
use Zeroseven\Z7Blog\Domain\Model\Demand;

class DemandViewHelper extends ActionViewHelper
{

    /** @var Demand */
    protected $demand;

    /** @var array */
    protected $parameterMapping;

    /** @var array */
    protected $typeMapping;

    public function __construct()
    {
        parent::__construct();

        $this->demand = Demand::makeInstance();
        $this->parameterMapping = $this->demand->getParameterMapping();
        $this->typeMapping = $this->demand->getTypeMapping();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register demand object
        $this->registerArgument('object', 'object', 'The demand object');

        // Register all allowed properties of demand object
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $this->registerArgument($propertyName, $this->typeMapping[$propertyName] ?? null, sprintf('Override value "%s" in demand object.', $propertyName));
        }
    }

    public function render(): string
    {

        // Override demand object
        if (($demand = $this->arguments['object']) instanceof Demand) {
            $this->demand = clone $demand;

        }

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

        // Set some "action" parameters
        $this->arguments['controller'] = 'Post';
        $this->arguments['pluginName'] = 'List';
        $this->arguments['arguments'] = $this->demand->getParameterArray(true);

        // Call the action ViewHelper (like <f:link.action … />)
        return parent::render();
    }
}
