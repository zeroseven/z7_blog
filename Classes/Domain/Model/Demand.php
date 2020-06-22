<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Zeroseven\Z7Blog\Service\SettingsService;

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
    private $stage = 0;

    /** @var int */
    private $category = 0;

    /** @var int */
    private $author = 0;

    /** @var int */
    private $topic = 0;

    /** @var array */
    private $tags = [];

    /** @var int */
    private $topPostMode = 0;

    /** @var int */
    private $archiveMode = 0;

    /** @var string */
    private $ordering = '';

    /** @var bool */
    private $ajax = false;

    /** @var int */
    private $listId = 0;

    /** @var array */
    protected $parameterMapping;

    /** @var array */
    protected $typeMapping;

    public function __construct()
    {

        // Create array of property names of reflection class
        foreach (GeneralUtility::makeInstance(\ReflectionClass::class, self::class)->getProperties() ?? [] as $reflection) {
            if (!$reflection->isProtected()) {

                $name = $reflection->getName();
                $value = $this->{$name};

                // Define parameter name of property
                $this->parameterMapping[$name] = GeneralUtility::camelCaseToLowerCaseUnderscored($name);

                // Map the type of properties
                $this->typeMapping[$name] = is_int($value) ? 'int' : (is_array($value) ? 'array' : (is_bool($value) ? 'bool' : (is_string($value) ? 'string' : null)));
            }
        }
    }

    public static function makeInstance(): self
    {

        // Return custom demand object
        if ($demand = $GLOBALS['TYPO3_CONF_VARS']['EXT'][SettingsService::EXTENSION_KEY]['demand'] ?? null) {
            if (class_exists($demand) && is_a($demand, self::class)) {
                return GeneralUtility::makeInstance($demand);
            }
        }

        // Return default demand object
        return GeneralUtility::makeInstance(self::class);
    }

    protected function castInt($value): int
    {
        if ($value === null || is_int($value) || MathUtility::canBeInterpretedAsInteger($value)) {
            return (int)$value;
        }

        throw new Exception(sprintf('Type of "%s" can not be converted to integer.', gettype($value)));
    }

    protected function castString($value): string
    {
        if ($value === null || is_string($value) || is_int($value)) {
            return (string)$value;
        }

        throw new Exception(sprintf('Type of "%s" can not be converted to string.', gettype($value)));
    }

    protected function castArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return GeneralUtility::trimExplode(',', $value);
        }

        throw new Exception(sprintf('Type of "%s" can not be converted to array.', gettype($value)));
    }

    protected function castBool($value): bool
    {
        if ($value === null || !is_array($value) && !is_object($value)) {
            return (bool)$value;
        }

        throw new Exception(sprintf('Type of "%s" can not be converted to bool.', gettype($value)));
    }

    public function getStage(): int
    {
        return (int)$this->stage;
    }

    public function setStage($stage): self
    {
        $this->stage = $this->castInt($stage);
        return $this;
    }

    public function getCategory(): int
    {
        return (int)$this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $this->castInt($category);
        return $this;
    }

    public function getAuthor(): int
    {
        return (int)$this->author;
    }

    public function setAuthor($author): self
    {
        $this->author = $this->castInt($author);
        return $this;
    }

    public function getTopic(): int
    {
        return (int)$this->topic;
    }

    public function setTopic($topic): self
    {
        $this->topic = $this->castInt($topic);
        return $this;
    }

    public function getTags(): array
    {
        return (array)$this->tags;
    }

    public function setTags($tags): self
    {
        $this->tags = $this->castArray($tags);
        return $this;
    }

    public function addTag(string $tag): self
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function removeTag(string $tag): self
    {
        $this->tags = array_filter(
            $this->getTags(), static function($v) use ($tag) {
                return $v !== $tag;
            }
        );

        return $this;
    }

    public function toggleTag(string $tag): self
    {
        if(in_array($tag, $this->getTags(), true)) {
            return $this->removeTag($tag);
        } else {
            return $this->addTag($tag);
        }
    }

    public function getTopPostMode(): int
    {
        return (int)$this->topPostMode;
    }

    public function setTopPostMode($topPostMode): self
    {
        $this->topPostMode = $this->castInt($topPostMode);
        return $this;
    }

    public function getArchiveMode(): int
    {
        return (int)$this->archiveMode;
    }

    public function setArchiveMode($archiveMode): self
    {
        $this->archiveMode = $this->castInt($archiveMode);
        return $this;
    }

    public function getOrdering(): string
    {
        return (string)$this->ordering;
    }

    public function setOrdering($ordering): self
    {
        $this->ordering = $this->castString($ordering);
        return $this;
    }

    public function isAjax(): bool
    {
        return (bool)$this->ajax;
    }

    public function setAjax($ajax): self
    {
        $this->ajax = $this->castBool($ajax);
        return $this;
    }

    public function getListId(): int
    {
        return (int)$this->listId;
    }

    public function setListId($listId): self
    {
        $this->listId = $this->castInt($listId);
        return $this;
    }

    public function getParameterMapping(): array
    {
        return $this->parameterMapping;
    }

    public function getTypeMapping(): array
    {
        return $this->typeMapping;
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
        return $this->getArchiveMode() === self::ARCHIVED_POSTS_HIDDEN;
    }

    public function archivedPostsOnly(): bool
    {
        return $this->getArchiveMode() === self::ARCHIVED_POSTS_ONLY;
    }

    public function getProperty(string $propertyName)
    {
        $method = sprintf('get%s', ucfirst($propertyName));
        if (is_callable([$this, $method])) {
           return $this->$method();
        }

        return null;
    }

    public function setProperty(string $propertyName, $value)
    {
        $method = sprintf('set%s', ucfirst($propertyName));
        if (is_callable([$this, $method])) {
            return $this->$method($value);
        }

        return $this;
    }

    public function setParameterArray(bool $ignoreEmptyValues, ...$arguments): self
    {

        // Check the types of arguments
        foreach ($arguments as $argument) {
            if (!is_array($arguments)) {
                throw new Exception('Disallowed argument ' . gettype($argument));
            }

            // Set properties
            foreach ($this->getParameterMapping() as $propertyName => $parameter) {
                if (isset($argument[$parameter]) && (($value = $argument[$parameter]) || !$ignoreEmptyValues)) {
                    $this->setProperty($propertyName, $value);
                }
            }
        }

        return $this;
    }

    public function getParameterArray(bool $ignoreEmptyValues = null): array
    {
        $parameters = [];

        // Collect values in array
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $parameters[$parameter] = $this->getProperty($propertyName);
        }

        // Return array with/without empty values
        return !$ignoreEmptyValues ? $parameters : array_filter($parameters, static function ($value) {
            return !empty($value);
        });
    }

}
