<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Demand;

/**
 * @method setStage(int $param)
 * @method getStage(int $param)
 * @method setCategory(int $param)
 * @method getCategory(int $param)
 * @method setAuthor(int $param)
 * @method getAuthor(int $param)
 * @method setTopics(array $value)
 * @method getTopics(array $value)
 * @method setTags(array $value)
 * @method getTags(array $value)
 * @method setTopPostMode(int $value)
 * @method getTopPostMode(int $value)
 * @method setArchiveMode(int $value)
 * @method getArchiveMode(int $value)
 * @method setListId(int $value)
 * @method getListId(int $value)
 */
class PostDemand extends AbstractDemand
{

    /** @var int */
    public const TOP_POSTS_FIRST = 1;

    /** @var int */
    public const TOP_POSTS_ONLY = 2;

    /** @var int */
    public const ARCHIVED_POSTS_HIDDEN = 0;

    /** @var int */
    public const ARCHIVED_POSTS_ONLY = 2;

    /** @var int */
    public $stage = 0;

    /** @var int */
    public $category = 0;

    /** @var int */
    public $author = 0;

    /** @var array */
    public $topics = [];

    /** @var array */
    public $tags = [];

    /** @var int */
    public $topPostMode = 0;

    /** @var int */
    public $archiveMode = 0;

    /** @var int */
    public $listId = 0;

    public function topPostsFirst(): bool
    {
        return $this->getTopPostMode() === self::TOP_POSTS_FIRST;
    }

    public function topPostsOnly(): bool
    {
        return $this->getTopPostMode() === self::TOP_POSTS_ONLY;
    }

    public function archivedPostsHidden(): bool
    {
        return $this->getArchiveMode() === self::ARCHIVED_POSTS_HIDDEN;
    }

    public function archivedPostsOnly(): bool
    {
        return $this->getArchiveMode() === self::ARCHIVED_POSTS_ONLY;
    }
}
