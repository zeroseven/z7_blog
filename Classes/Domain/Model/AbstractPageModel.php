<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use DateTime;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

abstract class AbstractPageModel extends AbstractEntity
{

    /** @var int */
    protected $doktype;

    /** @var int */
    protected $l10nParent;

    /** @var string */
    protected $title;

    /** @var string */
    protected $subtitle;

    /** @var string */
    protected $navTitle;

    /** @var string */
    protected $description;

    /** @var string */
    protected $abstract;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     * @Extbase\ORM\Lazy
     */
    protected $fileReferences;

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Core\Resource\FileReference> */
    protected $media;

    /** @var \TYPO3\CMS\Core\Resource\FileReference */
    protected $firstMedia;

    /** @var \TYPO3\CMS\Core\Resource\FileReference */
    protected $firstImage;

    /** @var \DateTime */
    protected $lastChange;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->fileReferences = new ObjectStorage();
    }

    public function getUid(): int
    {
        if ((int)GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'id', 0) > 0) {
            return $this->l10nParent;
        }

        return $this->uid;
    }

    public function getDoktype(): int
    {
        return (int)$this->doktype;
    }

    public function setDoktype(int $doktype): self
    {
        $this->doktype = $doktype;
        return $this;
    }

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSubtitle(): string
    {
        return (string)$this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getNavTitle(): string
    {
        return (string)$this->navTitle;
    }

    public function setNavTitle(string $navTitle): self
    {
        $this->navTitle = $navTitle;
        return $this;
    }

    public function getDescription(): string
    {
        return (string)$this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAbstract(): string
    {
        return (string)$this->abstract;
    }

    public function setAbstract(string $abstract): self
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function getFileReferences(): ?ObjectStorage
    {
        return $this->fileReferences;
    }

    public function setFileReferences(ObjectStorage $fileReferences): self
    {
        $this->fileReferences = $fileReferences;
        return $this;
    }

    public function getMedia(): ?ObjectStorage
    {
        if ($this->media === null && $fileReferences = $this->getFileReferences()) {
            $this->media = GeneralUtility::makeInstance(ObjectStorage::class);

            foreach ($fileReferences->toArray() as $fileReference) {
                if ($file = $fileReference instanceof \TYPO3\CMS\Extbase\Domain\Model\FileReference ? $fileReference->getOriginalResource() : null) {
                    $this->media->attach($file);
                }
            }
        }

        return $this->media;
    }

    public function setMedia(ObjectStorage $media): self
    {
        $this->media = $media;
        $this->firstMedia = null;
        $this->firstImage = null;
        return $this;
    }

    public function getFirstMedia(): ?FileReference
    {
        if ($this->firstMedia === null && $media = $this->getMedia()) {
            return $this->firstMedia = $media->offsetGet(0);
        }

        return $this->firstMedia;
    }

    public function getFirstImage(): ?FileReference
    {
        if ($this->firstImage === null && $media = $this->getMedia()) {
            foreach ($media->toArray() ?? [] as $asset) {
                if ($asset->getType() === AbstractFile::FILETYPE_IMAGE) {
                    return $this->firstImage = $asset;
                }
            }
        }

        return $this->firstImage;
    }

    public function getLastChange(): ?DateTime
    {
        return $this->lastChange;
    }

    public function setLastChange(DateTime $lastChange): self
    {
        $this->lastChange = $lastChange;
        return $this;
    }

}
