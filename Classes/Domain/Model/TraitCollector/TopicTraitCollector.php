<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model\TraitCollector;

use Zeroseven\Z7Blog\Domain\Model\Topic;
use Zeroseven\Z7Blog\Service\TraitCollectorService;

TraitCollectorService::createClass(
    __NAMESPACE__,
    'TopicTraitCollector',
    Topic::class
);

// Fallback for the ClassesConfigurationFactory
if (!class_exists(TopicTraitCollector::class)) {
    class TopicTraitCollector extends Topic
    {

    }
}
