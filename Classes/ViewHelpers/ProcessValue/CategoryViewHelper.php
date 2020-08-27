<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Zeroseven\Z7Blog\Domain\Model\Category;

class CategoryViewHelper extends AbstractValueProcessor
{

    protected $objectType = Category::class;
    
}
