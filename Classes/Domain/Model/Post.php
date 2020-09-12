<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RootlineService;
use TYPO3\CMS\Extbase\Annotation\ORM as Extbase;

class Post extends AbstractPageModel
{
    /** @var int */
    public const DOKTYPE = 147;

    /** @var string */
    protected const TAG_DELIMITER = ',';

    /** @var \DateTime */
    protected $date;

    /** @var bool */
    protected $top;

    /** @var \DateTime */
    protected $archiveDate;

    /** @var \Zeroseven\Z7Blog\Domain\Model\Category */
    protected $category;

    /** @var \Zeroseven\Z7Blog\Domain\Model\Author */
    protected $author;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Topic> */
    protected $topics;

    /** @var string */
    protected $tags;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Post>
     * @Extbase\Cascade("remove")
     * @Extbase\Lazy
     */
    protected $relationsTo;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Post>
     * @Extbase\Cascade("remove")
     * @Extbase\Lazy
     */
    protected $relationsFrom;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Zeroseven\Z7Blog\Domain\Model\Post> */
    protected $relations;

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
        return $this;
    }

    public function isArchived(): bool
    {
        return ($archiveDate = $this->getArchiveDate()) && $archiveDate->format('U') < time();
    }

    public function getArchiveDiff(): int
    {
        if ($this->archiveDate && !$this->isArchived()) {
            return date_diff(new \DateTime('today'), $this->archiveDate)->days;
        }

        return 0;
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

    public function getTags(): array
    {
        if ($tagList = $this->tags) {
            return GeneralUtility::trimExplode(self::TAG_DELIMITER, $tagList, true);
        }

        return [];
    }

    public function setTags($tags): self
    {
        $this->tags = is_array($tags) ? implode(self::TAG_DELIMITER, $tags) : (string)$tags;
        return $this;
    }

    public function getRelationsTo(): ObjectStorage
    {
        return $this->relationsTo;
    }

    public function setRelationsTo(ObjectStorage $relationsTo): self
    {
        $this->relationsTo = $relationsTo;
        $this->relations = null;
        return $this;
    }

    public function getRelationsFrom(): ObjectStorage
    {
        return $this->relationsFrom;
    }

    public function setRelationsFrom(ObjectStorage $relationsFrom): self
    {
        $this->relationsFrom = $relationsFrom;
        $this->relations = null;
        return $this;
    }

    public function getRelations(): ObjectStorage
    {
        if ($this->relations === null) {
            $this->relations = GeneralUtility::makeInstance(ObjectStorage::class);

            if ($relationsTo = $this->getRelationsTo()) {
                $this->relations->addAll($relationsTo);
            }

            if ($relationsFrom = $this->getRelationsFrom()) {
                $this->relations->addAll($relationsFrom);
            }
        }

        return $this->relations;
    }
}
