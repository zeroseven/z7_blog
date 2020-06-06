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
    protected $firstname;

    /** @var string */
    protected $lastname;

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


    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @Extbase\ORM\Cascade("remove")
     */
    protected $image;

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getExpertise(): ?string
    {
        return $this->expertise;
    }

    public function setExpertise($expertise): self
    {
        $this->expertise = $expertise;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPage(): ?string
    {
        return $this->page;
    }

    public function setPage(string $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(string $twitter): self
    {
        $this->twitter = $twitter;
        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(string $linkedin): self
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    public function getXing(): ?string
    {
        return $this->xing;
    }

    public function setXing(string $xing): self
    {
        $this->xing = $xing;
        return $this;
    }

    public function getPageLink(): ?string
    {
        if($this->pageLink === null) {
            return $this->pageLink = GeneralUtility::makeInstance(ContentObjectRenderer::class)->typoLink_URL([
                'parameter' => $this->getPage()
            ]);
        }

        return null;
    }

    public function getFullName(): ?string
    {
        if($this->fullName === null) {
            return $this->fullName = trim((string)$this->getFirstname() . ' ' . (string)$this->getLastname());
        }

        return null;
    }

}
