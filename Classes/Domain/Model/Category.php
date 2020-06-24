<?php

namespace Zeroseven\Z7Blog\Domain\Model;

class Category extends AbstractPageModel
{

    /** @var int */
    public const DOKTYPE = 146;

    /** @var bool */
    protected $redirect;

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
}
