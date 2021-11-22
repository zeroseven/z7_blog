<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

interface ConditionInterface
{
    public function validateCondition(): bool;
}
