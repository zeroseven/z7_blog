<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DrawHeaderHook;

use Zeroseven\Z7Blog\Domain\Model\Post;
use Zeroseven\Z7Blog\Service\RepositoryService;

class PostHeaderHook extends AbstractHeaderHook
{

    public function render(): string
    {

        // Check if the page is a category
        if ((int)$this->row['doktype'] === Post::DOKTYPE) {
            return $this->createView('EXT:z7_blog/Resources/Private/Backend/Templates/Post/Info.html', [
                'post' => RepositoryService::getPostRepository()->findByUid($this->id, true)
            ])->render();
        }

        return '';
    }

}
