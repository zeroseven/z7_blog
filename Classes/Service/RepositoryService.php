<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;
use Zeroseven\Z7Blog\Domain\Repository\AuthorRepository;
use Zeroseven\Z7Blog\Domain\Repository\CategoryRepository;
use Zeroseven\Z7Blog\Domain\Repository\PostRepository;
use Zeroseven\Z7Blog\Domain\Repository\TopicRepository;

class RepositoryService
{
    protected static function initializeClass(string $class): RepositoryInterface
    {
        // Get from cache
        if ($repository = $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['repository'][$class] ?? null) {
            return $repository;
        }

        // Get repository an store in cache
        return $GLOBALS['USER'][SettingsService::EXTENSION_KEY]['repository'][$class] = GeneralUtility::makeInstance(ObjectManager::class)->get($class);
    }

    public static function getPostRepository(): PostRepository
    {
        return self::initializeClass(PostRepository::class);
    }

    public static function getCategoryRepository(): CategoryRepository
    {
        return self::initializeClass(CategoryRepository::class);
    }

    public static function getAuthorRepository(): AuthorRepository
    {
        return self::initializeClass(AuthorRepository::class);
    }

    public static function getTopicRepository(): TopicRepository
    {
        return self::initializeClass(TopicRepository::class);
    }
}
