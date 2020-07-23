<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model\TraitCollector;

use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\TraitCollectorService;

TraitCollectorService::createClass(
    __NAMESPACE__,
    'AuthorTraitCollector',
    Post::class
);
