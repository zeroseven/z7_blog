<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

abstract class AbstractConditionViewHelper extends AbstractViewHelper implements ConditionInterface
{

    /** @var bool */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('negate', 'boolean', 'Negate the condition');
    }

    protected function getDoktype(): int
    {
        return (int)$GLOBALS['TSFE']->page['doktype'];
    }

    public function render(): string
    {
        $negate = $this->arguments['negate'];
        $match = $this->validateCondition();

        if ($match && !$negate || !$match && $negate) {
            return $this->renderChildren() ?: '1';
        }

        return '';
    }
}
