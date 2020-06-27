<?php

namespace Zeroseven\Z7Blog\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Domain\Model\Demand;
use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class TagRepository
{

    public function findByPost(Post $post): ?array
    {
        if ($tagList = $post->getTagList()) {
            return GeneralUtility::trimExplode(',', $tagList, true);
        }

        return null;
    }

    public function findByPosts(array $posts): ?array
    {
        $tags = [];

        foreach ($posts as $post) {
            foreach ($this->findByPost($post) ?? [] as $tag) {
                if (!in_array($tag, $tags, true)) {
                    $tags[] = $tag;
                }
            }
        }

        sort($tags, SORT_STRING);

        return $tags;
    }

    public function findAll(Demand $demandObject = null, bool $ignoreTagsFromDemand = null, int $languageUid = null): ?array
    {

        // Create demand object, if empty
        $demand = $demandObject === null ? Demand::makeInstance() : clone $demandObject;

        // Get post repository
        $repository = RepositoryService::getPostRepository();

        // Override language
        if($languageUid !== null) {
            $querySettings = $repository->getDefaultQuerySettings();
            $querySettings->setLanguageUid($languageUid);
            $repository->setDefaultQuerySettings($querySettings);
        }

        // Find Posts and return their tags
        if ($posts = $repository->findAll($ignoreTagsFromDemand === true ? $demand->setTags(null) : $demand)) {
            return $this->findByPosts($posts->toArray());
        }

        return null;
    }

}
