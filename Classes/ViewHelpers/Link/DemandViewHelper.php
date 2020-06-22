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

        $this->registerArgument('object', 'object', 'The demand object');
        $this->registerArgument('stateAttribute', 'bool', 'Display state of the link in data attributes', false, true);

        // Register all allowed properties of demand object
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $this->registerArgument($propertyName, $this->typeMapping[$propertyName] ?? null, sprintf('Override value "%s" in demand object.', $propertyName));
        }
    }

    public function render(): string
    {
        // Override demand object
        if ($this->arguments['stateAttribute'] && ($demand = $this->arguments['object']) instanceof Demand) {
            $this->demand = clone $demand;

            // Mark matched links
            // TODO: This is an experimental feature. Do some tests with real data
            $matchedProperties = [];
            $unmatchedProperties = [];
            foreach ($this->arguments as $propertyName => $value) {
                if ($value !== null && isset($this->typeMapping[$propertyName])) {

                    $type = $this->typeMapping[$propertyName];
                    $demandValue = $demand->getProperty($propertyName);

                    if (
                        $type === 'array' && 0 === count(array_diff((array)$value ?: null, $demandValue))
                        || $type === 'int' && (int)$value === (int)$demandValue
                        || $type === 'string' && (string)$value === (string)$demandValue
                        || $type === 'bool' && (bool)$value === (bool)$demandValue
                        || $value === $demandValue
                    ) {
                        $matchedProperties[] = $propertyName;
                    } else {
                        $unmatchedProperties[] = $propertyName;
                    }
                }
            }

            if(!count($unmatchedProperties)) {
                $this->tag->addAttribute('data-demand-selected', 'true');
            } elseif (count($matchedProperties)) {
                $this->tag->addAttribute('data-demand-active', count($matchedProperties) . '/' . (count($matchedProperties) + count($unmatchedProperties)));
            }
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
