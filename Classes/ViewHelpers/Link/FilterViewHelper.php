<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use Zeroseven\Z7Blog\Service\SettingsService;

class FilterViewHelper extends AbstractLinkViewHelper
{

    /** @var array */
    protected $arrayPropertyArguments;

    public function __construct()
    {
        parent::__construct();

        foreach ($this->demand->getTypeMapping() as $propertyName => $type) {
            if ($type === 'array') {
                $argumentSuffix = ucfirst(rtrim($propertyName, 's'));

                $this->arrayPropertyArguments['add' . $argumentSuffix] = $propertyName;
                $this->arrayPropertyArguments['remove' . $argumentSuffix] = $propertyName;
                $this->arrayPropertyArguments['toggle' . $argumentSuffix] = $propertyName;
            }
        }
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register arguments
        $this->registerArgument('dataAttributes', 'bool', 'Display state of the link in data attributes', false, true);
        $this->registerArgument('defaultList', 'bool', 'Apply arguments on the default list page', false, true);

        // Register add[PropertyName]/remove[PropertyName]/toggle[PropertyName] on array types in demand object
        foreach ($this->arrayPropertyArguments as $argument => $propertyName) {
            $this->registerArgument($argument, 'int|string', 'Take this parameter to edit the list of ' . $propertyName);
        }
    }

    public function prepareArguments()
    {

        // If the type of an argument have to be an integer and an object is specified, try to get the uid
        foreach ($this->arrayPropertyArguments as $argument => $propertyName) {
            if (($value = $this->arguments[$argument] ?? null) && $value instanceof AbstractDomainObject && method_exists($value, 'getUid')) {
                $this->arguments[$argument] = (string)$value->getUid();
            }
        }

        return parent::prepareArguments();
    }

    protected function overrideDemandParameters(): void
    {
        parent::overrideDemandParameters();

        // Reset the pagination
        $this->demand->setStage(0);

        // Add/remove/toggle the value in arrays
        foreach ($this->arrayPropertyArguments as $argument => $propertyName) {
            if ($value = $this->arguments[$argument] ?? null) {
                if (strpos($argument, 'add') === 0) {
                    $this->demand->addToProperty($propertyName, $value);
                } elseif (strpos($argument, 'remove') === 0) {
                    $this->demand->removeFromProperty($propertyName, $value);
                } else if (in_array($value, $this->demand->getProperty($propertyName), false)) {
                    $this->demand->removeFromProperty($propertyName, $value);
                } else {
                    $this->demand->addToProperty($propertyName, $value);
                }
            }
        }
    }

    protected function setDataAttributes(): void
    {
        // Mark active links
        if ($this->arguments['dataAttributes']) {

            // Mark matched links
            // TODO: This is an experimental feature. Do some tests with real data
            $matchedProperties = [];
            $unmatchedProperties = [];

            // Loop arguments
            foreach ($this->arguments as $propertyName => $value) {
                if ($value !== null && $this->demand->hasProperty($propertyName)) {

                    $type = $this->demand->getType($propertyName);
                    $demandValue = $this->demand->getProperty($propertyName);

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

            // Check array values
            foreach ($this->arrayPropertyArguments as $argument => $propertyName) {
                if (($value = $this->arguments[$argument]) !== null) {
                    if (in_array($value, $this->demand->getProperty($propertyName), false)) {
                        $matchedProperties[] = $propertyName;
                    } else {
                        $unmatchedProperties[] = $propertyName;
                    }
                }
            }

            // Set data attributes
            if (!count($unmatchedProperties)) {
                $this->tag->addAttribute('data-filter-selected', 'true');
            } elseif (count($matchedProperties)) {
                $this->tag->addAttribute('data-filter-active', count($matchedProperties) . '/' . (count($matchedProperties) + count($unmatchedProperties)));
            }
        }
    }

    protected function setPageUid(): void
    {
        if (empty($this->arguments['pageUid'])) {

            if ($this->arguments['defaultList']) {
                $settings = $this->templateVariableContainer->get('settings') ?? SettingsService::getSettings();

                if ($defaultListPage = $settings['post']['list']['defaultListPage'] ?? null) {
                    $this->arguments['pageUid'] = $defaultListPage;
                }
            } else {
                // TODO: Go recursive through the page tree
            }
        }
    }

    public function render(): string
    {

        $this->setDataAttributes();
        $this->setPageUid();

        return parent::render();
    }
}
