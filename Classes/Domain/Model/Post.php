<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Zeroseven\Z7Blog\Service\RepositoryService;
use TYPO3\CMS\Extbase\Annotation as Extbase;

class Post extends AbstractPageModel
{
    /** @var int */
    public const DOKTYPE = 147;

    /** @var \DateTime */
    protected $date;

    /** @var bool */
    protected $top;

    /** @var \DateTime */
    protected $archiveDate;

    /** @var bool */
    protected $archived;

    /** @var \Blog\Blogpages\Domain\Model\Category */
    protected $category;

    /** @var \Blog\Blogpages\Domain\Model\Author */
    protected $author;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blog\Blogpages\Domain\Model\Tag>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $tags;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Blog\Blogpages\Domain\Model\Post>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $related;

    protected function initStorageObjects(): void
    {
        parent::initStorageObjects();
        $this->tags = new ObjectStorage();
        $this->related = new ObjectStorage();
    }

    public function getDoktype(): int
    {
        return self::DOKTYPE;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function isTop(): bool
    {
        return (bool)$this->top;
    }

    public function setTop($top): void
    {
        $this->top = $top;
    }

    public function getArchiveDate(): ?\DateTime
    {
        return $this->archiveDate;
    }

    public function setArchiveDate(\DateTime $archiveDate): void
    {
        $this->archiveDate = $archiveDate;
    }

    public function isArchived(): bool
    {
        if($this->archived === null && ($archiveDate = $this->getArchiveDate())) {
            return $this->archived = $archiveDate->format('U') > time();
        }

        return $this->archived;
    }

    public function getCategory(): ?Category
    {
        if($this->category === null) {
            $rootLine = $GLOBALS['TSFE']->rootLine ?: GeneralUtility::makeInstance(RootlineUtility::class, $this->getUid())->get();

            foreach ($rootLine as $key => $row) {
                if ((int)$row['doktype'] === Category::DOKTYPE) {
                    return $this->category = RepositoryService::getCategoryRepository()->findByUid((int)$row['uid']);
                }
            }
        }

        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): void
    {
        $this->author = $author;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags->attach($tag);
    }

    public function removeTag(Tag $tagToRemove): void
    {
        $this->tags->detach($tagToRemove);
    }

    public function getTags(): ObjectStorage
    {
        return $this->tags;
    }

    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }

    public function addRelated(Post $related): void
    {
        $this->related->attach($related);
    }

    public function removeRelated(Post $relatedToRemove): void
    {
        $this->related->detach($relatedToRemove);
    }

    public function getRelated(): ObjectStorage
    {
        return $this->related;
    }

    public function setRelated(ObjectStorage $related): void
    {
        $this->related = $related;
    }
}
