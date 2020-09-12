<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Topic extends AbstractEntity
{
    /** @var string */
    protected $title;

    public function getTitle(): string
    {
        return (string)$this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

}
