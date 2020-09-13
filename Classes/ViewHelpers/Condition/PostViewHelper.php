<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use Zeroseven\Z7Blog\Domain\Model\Post;

/**
 * This view helper will check if the current page is a blog post
 *
 * <blog:condition.post>
 *   The current page is a blog post
 * </blog:condition.post>
 *
 * <blog:condition.post negate="1">
 *   The current page is NOT a blog post
 * </blog:condition.post>
 */
class PostViewHelper extends AbstractConditionViewHelper
{
    protected function validateCondition(): bool
    {
        return $this->getDoktype() === Post::DOKTYPE;
    }
}
