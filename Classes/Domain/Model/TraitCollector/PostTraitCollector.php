<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model\TraitCollector;

use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\TraitCollectorService;

TraitCollectorService::createClass(
    __NAMESPACE__,
    'PostTraitCollector',
    Post::class
);

// Fallback for the ClassesConfigurationFactory
if (!class_exists(PostTraitCollector::class)) {
    class PostTraitCollector extends Post
    {
    }
}
