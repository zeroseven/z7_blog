<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\Condition;

use Zeroseven\Z7Blog\Domain\Model\Category;

/**
 * This view helper will check if the current page is a blog category
 *
 * <blog:condition.isCategory>
 *   The current page is a blog category
 * </blog:condition.isCategory>
 *
 * <blog:condition.isCategory negate="1">
 *   The current page is NOT a blog category
 * </blog:condition.isCategory>
 *
 * <f:if condition="{blog:condition.isCategory()} && {media}">
 *   The current page is a blog category with a media asset
 * </f:if>
 */
class IsCategoryViewHelper extends AbstractConditionViewHelper
{
    public function validateCondition(): bool
    {
        return $this->getDoktype() === Category::DOKTYPE;
    }
}
