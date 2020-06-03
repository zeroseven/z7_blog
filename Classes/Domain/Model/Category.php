<?php

namespace Zeroseven\Z7Blog\Domain\Model;

class Category extends AbstractPageModel
{

    /** @var int */
    public const DOKTYPE = 146;

    /** @var bool */
    protected $redirectCategory;

    public function getDoktype(): int
    {
        return self::DOKTYPE;
    }

    public function isRedirectCategory(): bool
    {
        return (bool)$this->redirectCategory;
    }

    public function setRedirectCategory($redirectCategory): self
    {
        $this->redirectCategory = $redirectCategory;
        return $this;
    }
}
