<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class Demand
{

    /** @var int */
    public const TOP_POSTS_FIRST = 1;

    /** @var int */
    public const TOP_POSTS_ONLY = 2;

    /** @var int */
    public const ARCHIVED_POSTS_HIDDEN = 0;

    /** @var int */
    public const ARCHIVED_POSTS_ONLY = 2;

    /** @var int */
    protected $stage;

    /** @var int */
    protected $category;

    /** @var int */
    protected $author;

    /** @var int */
    protected $topic;

    /** @var array */
    protected $tags;

    /** @var int */
    protected $topPostMode;

    /** @var int */
    protected $archiveMode;

    /** @var string */
    protected $ordering;

    /** @var int */
    protected $listId;

    /** @var array */
    public $allowedParameters;

    public function __construct()
    {

        // Create array of allowed parameters by property names of reflection class
        $properties = GeneralUtility::makeInstance(\ReflectionClass::class, self::class)->getProperties();
        $this->allowedParameters = array_map(static function($reflection) {
            return GeneralUtility::camelCaseToLowerCaseUnderscored($reflection->name);
        }, $properties);

        // Remove the parameter "allowed_parameters" from array of allowed parameters ;-)
        $this->allowedParameters = array_filter($this->allowedParameters, static function($parameter) {
            return $parameter !== 'allowed_parameters';
        });
    }

    public static function makeInstance(): self
    {
        return GeneralUtility::makeInstance(self::class);
    }

    public static function getProperties(): array
    {

    }

    protected function setTypeInt(&$property, $value): self
    {
        if ($value === null || is_int($value) || MathUtility::canBeInterpretedAsInteger($value)) {
            $property = (int)$value;
        } else {
            throw new Exception(sprintf('Type of "%s" can not be converted to integer.', gettype($value)));
        }

        return $this;
    }

    protected function setTypeString(&$property, $value): self
    {
        if($value === null || is_string($value)) {
            $property = (string)$value;
        } else {
            throw new Exception(sprintf('Type of "%s" can not be converted to string.', gettype($value)));
        }

        return $this;
    }

    protected function setTypeArray(&$property, $value): self
    {
        if (is_array($value)) {
            $property = $value;
        } elseif (is_string($value)) {
            $property = GeneralUtility::trimExplode(',', $value);
        } elseif ($value === null) {
            $property = null;
        } else {
            throw new Exception(sprintf('Type of "%s" can not be converted to array.', gettype($value)));
        }

        return $this;
    }

    public function getStage(): int
    {
        return (int)$this->stage;
    }

    public function setStage($stage): self
    {
        return $this->setTypeInt($this->stage, $stage);
    }

    public function getCategory(): int
    {
        return (int)$this->category;
    }

    public function setCategory($category): self
    {
        return $this->setTypeInt($this->category, $category);
    }

    public function getAuthor(): int
    {
        return (int)$this->author;
    }

    public function setAuthor($author): self
    {
        return $this->setTypeInt($this->author, $author);
    }

    public function getTopic(): int
    {
        return (int)$this->topic;
    }

    public function setTopic($topic): self
    {
        return $this->setTypeInt($this->topic, $topic);
    }

    public function getTags(): ?array
    {
        return empty($this->tags) ? null : $this->tags;
    }

    public function setTags($tags): self
    {
       return $this->setTypeArray($this->tags, $tags);
    }

    public function getTopPostMode(): int
    {
        return (int)$this->topPostMode;
    }

    public function setTopPostMode($topPostMode): self
    {
        return $this->setTypeInt($this->topPostMode, $topPostMode);
    }

    public function getArchiveMode(): int
    {
        return (int)$this->archiveMode;
    }

    public function setArchiveMode($archiveMode): self
    {
        return $this->setTypeInt($this->archiveMode, $archiveMode);
    }

    public function getOrdering(): ?string
    {
        return $this->ordering;
    }

    public function setOrdering($ordering): self
    {
        return $this->setTypeString($this->ordering, $ordering);
    }

    public function getListId(): int
    {
        return (int)$this->listId;
    }

    public function setListId($listId): self
    {
        return $this->setTypeInt($this->listId, $listId);
    }

    public function topPostsFirst(): bool
    {
        return $this->getTopPostMode() === self::TOP_POSTS_FIRST;
    }

    public function topPostsOnly(): bool
    {
        return $this->getTopPostMode() === self::TOP_POSTS_ONLY;
    }

    public function archivedPostsHidden(): bool
    {
        return $this->getTopPostMode() === self::ARCHIVED_POSTS_HIDDEN;
    }

    public function archivedPostsOnly(): bool
    {
        return $this->getTopPostMode() === self::ARCHIVED_POSTS_ONLY;
    }

    public function setParameterArray(bool $ignoreEmptyValues, ...$arguments): self
    {

        // Check the types of arguments
        foreach ($arguments as $argument) {
            if (!is_array($arguments)) {
                throw new Exception('Disallowed argument ' . gettype($argument));
            }

            // Set properties
            foreach ($argument as $parameter => $value) {
                if ((!empty($value) || !$ignoreEmptyValues) && in_array($parameter, $this->allowedParameters, true)) {

                    // Call function "set[PropertyName]()"
                    $method = sprintf('set%s', GeneralUtility::underscoredToUpperCamelCase($parameter));
                    if (is_callable([$this, $method])) {
                        $this->$method($value);
                    }
                }
            }
        }

        return $this;
    }

    public function getParameterArray(bool $ignoreEmptyValues = null): array
    {
        $parameters = [];

        // Call function "get[PropertyName]()" and add to array
        foreach ($this->allowedParameters as $parameter) {
            $method = sprintf('get%s', GeneralUtility::underscoredToUpperCamelCase($parameter));
            if (is_callable([$this, $method])) {
                $parameters[$parameter] = $this->$method();
            }
        }

        // Return array with/without empty values
        return !$ignoreEmptyValues ? $parameters : array_filter($parameters, static function ($value) {
            return !empty($value);
        });
    }
}
