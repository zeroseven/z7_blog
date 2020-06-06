<?php

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

class Pagination
{

    /** @var array */
    protected $items = [];

    /** @var int */
    protected $total;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var array */
    protected $stages = [
        'current' => 0,
        'next' => null,
        'previous' => null,
        'limit' => null,
        'list' => [0]
    ];

    public function __construct(QueryResult $items, int $stage, $itemsPerStage, int $maxStages = null)
    {

        // Set limit of stages
        $this->stages['limit'] = $maxStages ?: 99;

        // Calculate the stages by given string
        $stages = $this->determineStages($itemsPerStage);

        // Store some values
        $this->total = $items->count();
        $this->offset = array_sum(array_slice($stages, 0, $stage));
        $this->limit = max(1, $stages[$stage]);

        // Setup stage array
        $this->stages['current'] = $stage;
        $this->stages['next'] = ($this->stages['current'] < $this->stages['limit'] - 1) && $this->total > ($this->offset + $this->limit) ? ($stage + 1) : null;
        $this->stages['previous'] = $stage > 0 ? $stage - 1 : null;
        $this->stages['list'] = $this->createStageItems($stages);

        // Here are the items in right range
        $this->items = array_slice($items->toArray(), $this->offset, $this->limit);
    }

    protected function determineStages($itemsPerStage): array
    {
        $limit = $this->stages['limit'];
        $stageLengths = GeneralUtility::intExplode(',', (string)$itemsPerStage, true);
        $stages = array_splice($stageLengths, 0, $limit);

        // TODO: Check length of stages to prevent "0" or negative values …
        return array_replace(array_fill(0, $limit, end($stages)), array_values($stages));
    }

    protected function createStageItems(array $stages): array
    {
        $items = [];
        $count = 0;

        foreach ($stages as $key => $value) {
            if (($count += $value) > $this->total) {
                return $items;
            }

            $items[$key] = $key;
        }
        return $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getStages(): array
    {
        return $this->stages;
    }

}
