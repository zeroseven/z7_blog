<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model\TraitCollector;

use Zeroseven\Z7Blog\Domain\Model\Category;
use Zeroseven\Z7Blog\Service\TraitCollectorService;

TraitCollectorService::createClass(
    __NAMESPACE__,
    'CategoryTraitCollector',
    Category::class
);

// Fallback for the ClassesConfigurationFactory
if (!class_exists(CategoryTraitCollector::class)) {
    class CategoryTraitCollector extends Category
    {
    }
}
