<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model\TraitCollector;

use Zeroseven\Z7Blog\Domain\Model\Author;
use Zeroseven\Z7Blog\Service\TraitCollectorService;

TraitCollectorService::createClass(
    __NAMESPACE__,
    'AuthorTraitCollector',
    Author::class
);

// Fallback for the ClassesConfigurationFactory
if (!class_exists(AuthorTraitCollector::class)) {
    class AuthorTraitCollector extends Author
    {
    }
}
