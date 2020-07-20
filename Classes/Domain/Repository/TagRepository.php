<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use Zeroseven\Z7Blog\Domain\Demand\PostDemand;
use Zeroseven\Z7Blog\Service\RepositoryService;

class TagRepository
{

    public function findByPosts(array $posts): ?array
    {
        $tags = [];

        foreach ($posts as $post) {
            foreach ($post->getTags() ?? [] as $tag) {
                if (!in_array($tag, $tags, true)) {
                    $tags[] = $tag;
                }
            }
        }

        sort($tags, SORT_STRING);

        return $tags;
    }

    public function findAll(PostDemand $demandObject = null, bool $ignoreTagsFromDemand = null, int $languageUid = null): ?array
    {

        // Create demand object, if empty
        $demand = $demandObject === null ? PostDemand::makeInstance() : clone $demandObject;

        // Get post repository
        $repository = RepositoryService::getPostRepository();

        // Override language
        if($languageUid !== null) {
            $querySettings = $repository->getDefaultQuerySettings();
            $querySettings->setLanguageUid($languageUid);
            $repository->setDefaultQuerySettings($querySettings);
        }

        // Find Posts and return their tags
        if ($posts = $repository->findByDemand($ignoreTagsFromDemand === true ? $demand->setTags(null) : $demand)) {
            return $this->findByPosts($posts->toArray());
        }

        return null;
    }

}
