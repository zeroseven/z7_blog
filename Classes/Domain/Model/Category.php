<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use Zeroseven\Z7Blog\Service\RepositoryService;
use Zeroseven\Z7Blog\Service\RootlineService;

class Category extends AbstractPageModel
{

    /** @var int */
    public const DOKTYPE = 146;

    /** @var bool */
    protected $redirect;

    /** @var Category */
    protected $parentCategory;

    public function getDoktype(): int
    {
        return self::DOKTYPE;
    }

    public function isRedirect(): bool
    {
        return (bool)$this->redirect;
    }

    public function setRedirect($redirect): self
    {
        $this->redirect = $redirect;
        return $this;
    }

    public function getParentCategory(): ?Category
    {
        if ($this->parentCategory === null && $uid = RootlineService::findCategory($this->getPid())) {
            $this->parentCategory = RepositoryService::getCategoryRepository()->findByUid($uid);
        }

        return $this->parentCategory;
    }

}
