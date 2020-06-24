<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Zeroseven\Z7Blog\Service\RepositoryService;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use Zeroseven\Z7Blog\Service\RootlineService;

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

    /** @var \Zeroseven\Z7Blog\Domain\Model\Category */
    protected $category;

    /** @var \Zeroseven\Z7Blog\Domain\Model\Author */
    protected $author;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Topic>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $topics;

    /** @var string */
    protected $tagList;

    /** @var array */
    protected $tags;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Post>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $related;

    protected function initStorageObjects(): void
    {
        parent::initStorageObjects();
        $this->topics = new ObjectStorage();
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

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function isTop(): bool
    {
        return (bool)$this->top;
    }

    public function setTop($top): self
    {
        $this->top = $top;
        return $this;
    }

    public function getArchiveDate(): ?\DateTime
    {
        return $this->archiveDate;
    }

    public function setArchiveDate(\DateTime $archiveDate): self
    {
        $this->archiveDate = $archiveDate;
        $this->archived = null;
        return $this;
    }

    public function isArchived(): bool
    {
        if ($this->archived === null && $archiveDate = $this->getArchiveDate()) {
            return $this->archived = $archiveDate->format('U') < time();
        }

        return (bool)$this->archived;
    }

    public function getCategory(): ?Category
    {
        if ($this->category === null && $uid = RootlineService::findCategory($this->getUid())) {
            return $this->category = RepositoryService::getCategoryRepository()->findByUid($uid);
        }

        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function addTopic(Topic $topic): void
    {
        $this->topics->attach($topic);
    }

    public function removeTopic(Topic $topicToRemove): void
    {
        $this->topics->detach($topicToRemove);
    }

    public function getTopics(): ObjectStorage
    {
        return $this->topics;
    }

    public function setTopics(ObjectStorage $topics): self
    {
        $this->topics = $topics;
        return $this;
    }

    public function getTagList(): string
    {
        return $this->tagList;
    }

    public function setTagList(string $tagList): self
    {
        $this->tagList = $tagList;
        $this->tags = null;
        return $this;
    }

    public function getTags(): ?array
    {
        if($this->tags === null) {
            return $this->tags = RepositoryService::getTagRepository()->findByPost($this);
        }

        return $this->tags;
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

    public function setRelated(ObjectStorage $related): self
    {
        $this->related = $related;
        return $this;
    }
}
