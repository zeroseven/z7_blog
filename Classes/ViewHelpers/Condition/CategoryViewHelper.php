<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use Zeroseven\Z7Blog\Domain\Model\Category;

/**
 * This view helper will check if the current page is a blog category
 *
 * <blog:condition.category>
 *   The current page is a blog category
 * </blog:condition.category>
 *
 * <blog:condition.category negate="1">
 *   The current page is NOT a blog category
 * </blog:condition.category>
 */
class CategoryViewHelper extends AbstractConditionViewHelper
{

    protected function validateCondition(): bool
    {
        return $this->getDoktype() === Category::DOKTYPE;
    }
}
