<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

abstract class AbstractPageModel extends AbstractEntity
{

    /** @var int */
    protected $doktype;

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

    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Core\Resource\File> */
    protected $media;

    /** @var \TYPO3\CMS\Core\Resource\File */
    protected $firstMedia;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->fileReferences = new ObjectStorage();
    }

    public function getDoktype(): int
    {
        return (int)$this->doktype;
    }

    public function setDoktype(int $doktype): void
    {
        $this->doktype = $doktype;
    }

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): string
    {
        return (string)$this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getNavTitle(): string
    {
        return (string)$this->navTitle;
    }

    public function setNavTitle(string $navTitle): void
    {
        $this->navTitle = $navTitle;
    }

    public function getDescription(): string
    {
        return (string)$this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAbstract(): string
    {
        return (string)$this->abstract;
    }

    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function getFileReferences(): ?ObjectStorage
    {
        return $this->fileReferences;
    }

    public function setFileReferences(ObjectStorage $fileReferences): void
    {
        $this->fileReferences = $fileReferences;
    }

    public function getMedia(): ?ObjectStorage
    {
        if($this->media === null && $fileReferences = $this->getFileReferences()) {
            $this->media = GeneralUtility::makeInstance(ObjectStorage::class);

            foreach ($fileReferences->toArray() as $fileReference) {
                if($file = $fileReference instanceof FileReference ? $fileReference->getOriginalResource()->getOriginalFile() : null) {
                    $this->media->attach($file);
                }
            }
        }

        return $this->media;
    }

    public function setMedia(ObjectStorage $media): void
    {
        $this->media = $media;
    }

    public function getFirstMedia(): ?File
    {
        if($this->firstMedia === null && $fileReferences = $this->getFileReferences()) {
            $fileReference = $fileReferences->offsetGet(0);
            return $this->firstMedia = $fileReference instanceof FileReference ? $fileReference->getOriginalResource()->getOriginalFile() : null;
        }

        return $this->firstMedia;
    }

    public function setFirstMedia(File $firstMedia): void
    {
        $this->firstMedia = $firstMedia;
    }
}
