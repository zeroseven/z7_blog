<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Zeroseven\Z7Blog\Domain\Model\Topic;

class TopicViewHelper extends AbstractValueProcessor
{
    protected $objectType = Topic::class;
}
