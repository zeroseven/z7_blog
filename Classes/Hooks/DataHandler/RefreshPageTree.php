<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Hooks\DataHandler;

use TYPO3\CMS\Core\DataHandling\DataHandler;

class RefreshPageTree
{
    /** @var array */
    protected $fields = ['post_top', 'post_archive', 'post_redirect_category'];

    public function processDatamap_postProcessFieldArray(bool $status, string $table, $id, array $fieldArray, DataHandler $dataHandler): void
    {
        if ($table === 'pages' && !empty(array_intersect($this->fields, array_keys($fieldArray)))) {
            $dataHandler->pagetreeNeedsRefresh = true;
        }
    }
}
