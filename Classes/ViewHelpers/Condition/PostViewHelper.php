<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use Zeroseven\Z7Blog\Domain\Model\Post;

class PostViewHelper extends AbstractConditionViewHelper
{

    protected function validateCondition(): bool
    {
        return $this->getDoktype() === Post::DOKTYPE;
    }
}
