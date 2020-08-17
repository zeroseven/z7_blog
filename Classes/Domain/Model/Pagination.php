<?php declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Zeroseven\Z7Blog\Service\TypeCastService;

class Stage extends ObjectStorage
{

    /** @var Pagination */
    protected $pagination;

    /** @var int */
    protected $index;

    /** @var bool */
    protected $active;

    /** @var bool */
    protected $selected;

    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function getIndex(): int
    {
        return (int)$this->index;
    }

    public function setIndex(int $index): self
    {
        $this->index = $index;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;
        return $this;
    }

    public function getItems(): array
    {
        return $this->toArray();
    }

    public function getRange(): array
    {
        return $this->pagination->getRange($this->getIndex());
    }

}

class Stages extends ObjectStorage
{

    protected $pagination;

    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function initialize(): void
    {

        // Remove all existing objects
        $this->removeAll($this);

        // Create array of items
        $items = $this->pagination->getItems();

        // Build new stages
        foreach ($this->pagination->getStageLengths() as $index => $stageLength) {
            if (count($items)) {

                // Add items to stage
                $stage = GeneralUtility::makeInstance(Stage::class, $this->pagination);
                foreach (array_splice($items, 0, $stageLength) as $item) {
                    $stage->attach($item);
                }

                // Set attributes on stage object
                $stage->setIndex($index)
                    ->setActive($index <= $this->pagination->getSelectedStage())
                    ->setSelected($index === $this->pagination->getSelectedStage());

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
        return array_filter($this->toArray(), static function ($stage) {
            return $stage->isActive();
        });
    }

    public function getInactive(): array
    {
        return array_filter($this->toArray(), static function ($stage) {
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

    /** @var array */
    protected $stageLengths;

    public function __construct($items, $selectedStage = null, $itemsPerStage = null, $maxStages = null)
    {

        $this->stages = GeneralUtility::makeInstance(Stages::class, $this);

        $this->setItems($items, false)
            ->setSelectedStage($selectedStage, false)
            ->setItemsPerStage($itemsPerStage, false)
            ->setMaxStages($maxStages, false)
            ->initialize();
    }

    protected function updateStageLengths(): void
    {
        $stageLengths = GeneralUtility::intExplode(',', $this->getItemsPerStage(), true);
        $stages = array_slice($stageLengths, 0, $this->getMaxStages());

        // Set calculated lengths
        $this->stageLengths = array_replace(array_fill(0, $this->getMaxStages(), end($stages)), array_values($stages));
    }

    protected function initialize(): void
    {
        $this->updateStageLengths();
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

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems($items, bool $updatePagination = null): self
    {
        $this->items = TypeCastService::array($items);

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

    public function setSelectedStage($stage = null, bool $updatePagination = null): self
    {
        $this->selectedStage = MathUtility::canBeInterpretedAsInteger($stage) ? (int)$stage : 0;

        if ($updatePagination !== false) {
            $this->update();
        }

        return $this;
    }

    protected function getItemsPerStage(): string
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

    public function getStageLengths(): array
    {
        return $this->stageLengths;
    }

    public function getSelectedStage(): int
    {
        return $this->selectedStage;
    }

    public function getNextStage(): ?int
    {
        $range = $this->getRange();
        return $this->getSelectedStage() < $this->getMaxStages() - 1 && count($this->getItems()) > $range['to'] ? ($this->getSelectedStage() + 1) : null;
    }

    public function getPreviousStage(): ?int
    {
        return $this->getSelectedStage() > 0 ? $this->getSelectedStage() - 1 : null;
    }

    public function getRange(int $stage = null): array
    {

        // Determine stage with fallback of the selected
        $stage = $stage ?? $this->getSelectedStage();

        // Calculate the start of the range
        $range['from'] = array_sum(array_slice($this->stageLengths, 0, $stage));

        // Calculate the length of items in current range
        $range['length'] = min($this->stageLengths[$stage], count($this->getItems()) - $range['from']);

        // Calculate the "to" property
        $range['to'] = $range['from'] + $range['length'];

        // Return array
        return $range;
    }

    public function getIndicators(): array
    {
        $items = [];
        $count = 0;
        $total = count($this->getItems());

        foreach ($this->getStageLengths() as $key => $value) {
            if(($count += $value) > $total) {
                return $items;
            }

            $items[$key] = $key + 1;
        }

        return $items;
    }

}
