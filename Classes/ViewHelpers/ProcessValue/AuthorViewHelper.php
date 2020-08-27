<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\ViewHelpers\ProcessValue;

use Zeroseven\Z7Blog\Domain\Model\Author;

class AuthorViewHelper extends AbstractValueProcessor
{
    public function __construct()
    {
        parent::__construct();

        $this->objectType = Author::class;
    }
}
