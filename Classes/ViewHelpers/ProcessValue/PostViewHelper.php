<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use Zeroseven\Z7Blog\Domain\Model\Post;

class PostViewHelper extends AbstractValueProcessor
{
    protected $objectType = Post::class;

    /** @throws AspectNotFoundException | DBALException */
    protected function processFallback($value, string $property = null): ?string
    {
        if ($property === 'category') {
            return $this->getDatabaseValue((int)$value, $this->dataMap->getTableName());
        }

        return parent::processFallback($value, $property);
    }
}
