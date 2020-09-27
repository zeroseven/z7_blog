<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Event;

use Zeroseven\Z7Blog\Domain\Model\Post;

final class StructuredDataEvent
{

    /** @var Post */
    private $post;

    /** @var array */
    private $data;

    public function __construct(Post $post, array $data)
    {
        $this->post = $post;
        $this->data = $data;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(array $data): void
    {
        $this->setData(array_merge($this->getData(), $data));
    }

}
