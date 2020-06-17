<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Topic extends AbstractEntity
{
    /** @var string */
    protected $title;

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return (string)$this->title;
    }

    /**
     * Sets the title and returns it self
     *
     * @param string $title
     * @return Topic
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

}
