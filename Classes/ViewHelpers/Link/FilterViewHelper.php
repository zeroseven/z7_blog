<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Service\SettingsService;

class FilterViewHelper extends AbstractLinkViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register arguments
        $this->registerArgument('addTag', 'string', 'Adds a tag');
        $this->registerArgument('removeTag', 'string', 'Removes a tag');
        $this->registerArgument('toggleTag', 'string', 'If the tag is existing removes it, otherwise adds a tag');
        $this->registerArgument('dataAttributes', 'bool', 'Display state of the link in data attributes', false, true);
        $this->registerArgument('defaultList', 'bool', 'Apply arguments on the default list page', false, true);
    }

    protected function overrideDemandParameters(): void
    {
        parent::overrideDemandParameters();

        // Reset the pagination
        $this->demand->setStage(0);

        // Add/remove/toggle tags
        if ($tag = $this->arguments['addTag'] ?? null) {
            $this->demand->addToTags($tag);
        } elseif ($tag = $this->arguments['removeTag'] ?? null) {
            $this->demand->removeFromTags($tag);
        } elseif ($tag = $this->arguments['toggleTag'] ?? null) {
            if (in_array($tag, $this->demand->getTags(), true)) {
                $this->demand->removeFromTags($tag);
            } else {
                $this->demand->addToTags($tag);
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

            // Check tags
            foreach (['addTag', 'toggleTag'] as $tag) {
                if (($tag = $this->arguments[$tag]) !== null) {
                    if (in_array($tag, $this->demand->getTags(), true)) {
                        $matchedProperties[] = $tag;
                    } else {
                        $unmatchedProperties[] = $tag;
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
