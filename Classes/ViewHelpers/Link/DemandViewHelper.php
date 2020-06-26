<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Link;

use Zeroseven\Z7Blog\Domain\Model\Demand;

class DemandViewHelper extends AbstractLinkViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        // Register arguments
        $this->registerArgument('addTag', 'string', 'Adds a tag');
        $this->registerArgument('removeTag', 'string', 'Removes a tag');
        $this->registerArgument('toggleTag', 'string', 'If the tag is existing removes it, otherwise adds a tag');
        $this->registerArgument('stateAttribute', 'bool', 'Display state of the link in data attributes', false, true);
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

    public function render(): string
    {
        // Override demand object
        if ($this->arguments['stateAttribute']) {

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
                $this->tag->addAttribute('data-demand-selected', 'true');
            } elseif (count($matchedProperties)) {
                $this->tag->addAttribute('data-demand-active', count($matchedProperties) . '/' . (count($matchedProperties) + count($unmatchedProperties)));
            }
        }

        return parent::render();
    }
}
