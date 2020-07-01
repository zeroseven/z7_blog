<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class Author extends AbstractEntity
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $expertise;

    /** @var string */
    protected $email;

    /** @var string */
    protected $description;

    /** @var string */
    protected $page;

    /** @var string */
    protected $twitter;

    /** @var string */
    protected $linkedin;

    /** @var string */
    protected $xing;

    /** @var string */
    protected $pageLink;

    /** @var string */
    protected $fullName;

    /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference */
    protected $image;

    public function getFirstName(): string
    {
        return (string)$this->firstName;
    }

    public function setFirstName($firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return (string)$this->lastName;
    }

    public function setLastName($lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getExpertise(): string
    {
        return (string)$this->expertise;
    }

    public function setExpertise($expertise): self
    {
        $this->expertise = $expertise;
        return $this;
    }

    public function getEmail(): string
    {
        return (string)$this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getImage(): ?FileReference
    {
        return $this->image;
    }

    public function setImage(FileReference $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getDescription(): string
    {
        return (string)$this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPage(): string
    {
        return (string)$this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getTwitter(): string
    {
        return (string)$this->twitter;
    }

    public function setTwitter(string $twitter): self
    {
        $this->twitter = $twitter;
        return $this;
    }

    public function getLinkedin(): string
    {
        return (string)$this->linkedin;
    }

    public function setLinkedin(string $linkedin): self
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    public function getXing(): string
    {
        return (string)$this->xing;
    }

    public function setXing(string $xing): self
    {
        $this->xing = $xing;
        return $this;
    }

    public function getPageLink(): string
    {
        if($this->pageLink === null) {
            return $this->pageLink = GeneralUtility::makeInstance(ContentObjectRenderer::class)->typoLink_URL([
                'parameter' => $this->getPage()
            ]);
        }

        return (string)$this->pageLink;
    }

    public function getFullName(): string
    {
        if($this->fullName === null) {
            return $this->fullName = trim($this->getFirstName() . ' ' . $this->getLastName());
        }

        return (string)$this->fullName;
    }

}
