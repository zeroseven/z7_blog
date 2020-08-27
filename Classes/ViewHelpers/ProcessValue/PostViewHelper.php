<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Zeroseven\Z7Blog\Domain\Model\Post;

class PostViewHelper extends AbstractValueProcessor
{
    protected $objectType = Post::class;

    protected function processFallback($value, string $property): ?string
    {
        if($property === 'category') {
            return $this->getDatabaseValue((int)$value, $this->dataMap->getTableName());
        }

        return parent::processFallback($value, $property);
    }
}
