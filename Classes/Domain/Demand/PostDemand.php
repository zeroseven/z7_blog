<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Demand;

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
