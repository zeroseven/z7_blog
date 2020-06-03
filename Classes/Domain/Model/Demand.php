<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Demand
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
    protected $belowPage;

    /** @var int */
    protected $author;

    /** @var int */
    protected $topic;

    /** @var array */
    protected $tags;

    /** @var int */
    protected $topPostsMode;

    /** @var int */
    protected $archiveMode;

    /** @var string */
    protected $sorting;

    public function __construct(int $belowPage = null, int $author = null, int $topic = null, array $tags = null, int $topPostsMode = null, int $archiveMode = null, string $sorting = null)
    {
        $this->belowPage = $belowPage;
        $this->author = $author;
        $this->topic = $topic;
        $this->tags = $tags;
        $this->topPostsMode = $topPostsMode;
        $this->archiveMode = $archiveMode;
        $this->sorting = $sorting;
    }

    static public function makeInstance(...$args): self
    {
        return GeneralUtility::makeInstance(self::class, ...$args);
    }

    public function getBelowPage(): int
    {
        return (int)$this->belowPage;
    }

    public function setBelowPage(int $belowPage): self
    {
        $this->belowPage = $belowPage;
        return $this;
    }

    public function getAuthor(): int
    {
        return (int)$this->author;
    }

    public function setAuthor(int $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getTopic(): int
    {
        return (int)$this->topic;
    }

    public function setTopic(int $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getTags(): ?array
    {
        return empty($this->tags) ? null : $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getTopPostsMode(): int
    {
        return (int)$this->topPostsMode;
    }

    public function setTopPostsMode(int $topPostsMode): self
    {
        $this->topPostsMode = $topPostsMode;
        return $this;
    }

    public function getArchiveMode(): int
    {
        return (int)$this->archiveMode;
    }

    public function setArchiveMode(int $archiveMode): self
    {
        $this->archiveMode = $archiveMode;
        return $this;
    }

    public function getSorting(): ?string
    {
        return $this->sorting;
    }

    public function setSorting(string $sorting = null): self
    {
        $this->sorting = $sorting;
        return $this;
    }

    public function topPostsFirst(): bool
    {
        return $this->getTopPostsMode() === self::TOP_POSTS_FIRST;
    }

    public function topPostsOnly(): bool
    {
        return $this->getTopPostsMode() === self::TOP_POSTS_ONLY;
    }

    public function archivedPostsHidden(): bool
    {
        return $this->getTopPostsMode() === self::ARCHIVED_POSTS_HIDDEN;
    }

    public function archivedPostsOnly(): bool
    {
        return $this->getTopPostsMode() === self::ARCHIVED_POSTS_ONLY;
    }

}
