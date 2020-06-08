<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class Stage extends ObjectStorage
{

    /** @var int */
    protected $index;

    /** @var bool */
    protected $active;

    /** @var bool */
    protected $selected;

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected)
    {
        $this->selected = $selected;
    }

    public function getItems(): array
    {
        return $this->toArray();
    }

}

class Stages extends ObjectStorage
{

    protected $pagination;

    protected $stageLengths;

    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    protected function determineStageLengths(): void
    {
        $stageLengths = GeneralUtility::intExplode(',', $this->pagination->getItemsPerStage(), true);
        $stages = array_slice($stageLengths, 0, $this->pagination->getMaxStages());

        // Set calculated lengths
        $this->stageLengths = array_replace(array_fill(0, $this->pagination->getMaxStages(), end($stages)), array_values($stages));
    }

    public function initialize(): void
    {

        // Remove all existing objects
        $this->removeAll($this);

        // Recalculate the stage lengths
        $this->determineStageLengths();

        // Create array of items
        $items = $this->pagination->getItems()->toArray();

        // Build new stages
        foreach ($this->stageLengths as $index => $stageLength) {
            if (count($items)) {

                // Add items to stage
                $stage = GeneralUtility::makeInstance(Stage::class);
                foreach (array_splice($items, 0, $stageLength) as $item) {
                    $stage->attach($item);
                }

                // Set attributes on stage object
                $stage->setIndex($index);
                $stage->setActive($index <= $this->pagination->getSelectedStage());
                $stage->setSelected($index === $this->pagination->getSelectedStage());

                // Add stage to the stages
                $this->attach($stage);
            }
        }
    }

    public function getFirst(): ?Stage
    {
        return $this->offsetGet(0);
    }

    public function getSelected(): ?Stage
    {
        return $this->offsetGet($this->pagination->getSelectedStage());
    }

    public function getActive(): array
    {
        return array_filter($this->toArray(), static function($stage){
            return $stage->isActive();
        });
    }

    public function getInactive(): array
    {
        return array_filter($this->toArray(), static function($stage){
            return !$stage->isActive();
        });
    }

}

class Pagination
{

    /** @var QueryResultInterface */
    protected $items;

    /** @var Stages */
    protected $stages;

    /** @var int */
    protected $selectedStage;

    /** @var string */
    protected $itemsPerStage;

    /** @var int */
    protected $maxStages;

    public function __construct(QueryResultInterface $items, $selectedStage = null, $itemsPerStage = null, $maxStages = null)
    {

        $this->stages = GeneralUtility::makeInstance(Stages::class, $this);

        $this->setItems($items, false)
            ->setSelectedStage($selectedStage, false)
            ->setItemsPerStage($itemsPerStage, false)
            ->setMaxStages($maxStages, false)
            ->initialize();
    }

    protected function initialize(): void
    {
        $this->getStages()->initialize();
    }

    protected function update(): void
    {
        $this->initialize();
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function getStagesLength(): int
    {
        return $this->getStages()->count();
    }

    public function getItemsLength(): int
    {
        return $this->items->count();
    }

    public function getItems(): QueryResultInterface
    {
        return $this->items;
    }

    public function setItems(QueryResultInterface $items, bool $updatePagination = null): self
    {
        $this->items = $items;

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

    public function getSelectedStage(): int
    {
        return $this->selectedStage;
    }

    public function setSelectedStage($stage = null, bool $updatePagination = null): self
    {
        $this->selectedStage = MathUtility::canBeInterpretedAsInteger($stage) ? (int)$stage : 0;

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

    public function getItemsPerStage(): string
    {
        return $this->itemsPerStage;
    }

    public function setItemsPerStage($itemsPerStage, bool $updatePagination = null): self
    {
        $this->itemsPerStage = $itemsPerStage === '' || !is_string($itemsPerStage) ? '' : $itemsPerStage;

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

    public function getMaxStages(): int
    {
        return $this->maxStages;
    }

    public function setMaxStages($maxStages, bool $updatePagination = null): self
    {
        $this->maxStages = min(99, max(1, MathUtility::canBeInterpretedAsInteger($maxStages) && (int)$maxStages > 0 ? (int)$maxStages : 99));

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

}
