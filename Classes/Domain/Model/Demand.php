<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Model;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Zeroseven\Z7Blog\Service\SettingsService;
use Zeroseven\Z7Blog\Service\TypeCastService;

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
    public $stage = 0;

    /** @var array */
    public $uids = [];

    /** @var int */
    public $category = 0;

    /** @var int */
    public $author = 0;

    /** @var array */
    public $topics = [];

    /** @var array */
    public $tags = [];

    /** @var int */
    public $topPostMode = 0;

    /** @var int */
    public $archiveMode = 0;

    /** @var string */
    public $ordering = '';

    /** @var int */
    public $listId = 0;

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

    protected function checkPropertyAccess(string $propertyName): void
    {
        if(!$this->hasProperty($propertyName)) {
            throw new Exception(sprintf('Property "%s" not found in demand model.', $propertyName));
        }
    }

    public function hasProperty(string $propertyName)
    {
        try {
            if(GeneralUtility::makeInstance(\ReflectionClass::class, self::class)->getProperty($propertyName)) {
                return true;
            }
        } catch (\ReflectionException $e) {
        }

        return false;
    }

    public function getType(string $propertyName): ?string
    {
        $this->checkPropertyAccess($propertyName);
        return $this->typeMapping[$propertyName] ?? null;
    }

    public function getProperty(string $propertyName)
    {
        $type = $this->getType($propertyName);

        if ($type === 'int') {
            return (int)$this->{$propertyName};
        } elseif ($type === 'string') {
            return (string)$this->{$propertyName};
        } elseif ($type === 'array') {
            return (array)$this->{$propertyName};
        } elseif ($type === 'bool') {
            return (bool)$this->{$propertyName};
        }

        return $this->{$propertyName};
    }

    public function setProperty(string $propertyName, $value)
    {
        $type = $this->getType($propertyName);

        if ($type === 'int') {
            $this->{$propertyName} = TypeCastService::int($value);
        } elseif ($type === 'string') {
            $this->{$propertyName} = TypeCastService::string($value);
        } elseif ($type === 'array') {
            $this->{$propertyName} = array_map(static function($v) {
                return (string)$v;
            }, TypeCastService::array($value));
        } elseif ($type === 'bool') {
            $this->{$propertyName} = TypeCastService::bool($value);
        } else {
            $this->{$propertyName} = $value;
        }

        return $this;
    }

    public function addToProperty(string $propertyName, $value): self
    {
        if($this->getType($propertyName) === 'array') {
            $array = $this->getProperty($propertyName);
            $array[] = $value;

            return $this->setProperty($propertyName, $array);
        }

        throw new Exception('AddToProperty is allowed on type array only');

    }

    public function removeFromProperty(string $propertyName, $value): self
    {
        if($this->getType($propertyName) === 'array') {
            $array = array_filter(
                $this->getProperty($propertyName), static function ($i) use ($value) {
                return $i !== $value;
            });

            return $this->setProperty($propertyName, $array);
        }

        throw new Exception('RemoveFromProperty is allowed on type array only');
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
            $value = $this->getProperty($propertyName);

            if (is_array($value)) {
                sort($value); // Reduce length of cHashes
                $parameters[$parameter] = implode(',', $value);
            } else {
                $parameters[$parameter] = (string)$value;
            }
        }

        // Return array with/without empty values
        return !$ignoreEmptyValues ? $parameters : array_filter($parameters, static function ($value) {
            return !empty($value);
        });
    }

    public function __call($name, $arguments)
    {
        if (preg_match('/((?:s|g)et|is|has|addTo|removeFrom)([A-Z].*)/', $name, $matches)) {

            $action = $matches[1];
            $propertyName = lcfirst($matches[2]);

            if ($action === 'set') {
                return $this->setProperty($propertyName, ...$arguments);
            }

            if ($action === 'get') {
                return $this->getProperty($propertyName);
            }

            if ($action === 'is') {
                return (bool)$this->getProperty($propertyName);
            }

            if ($action === 'has') {
                return $this->hasProperty($propertyName);
            }

            if ($action === 'addTo') {
                return $this->addToProperty($propertyName, ...$arguments);
            }

            if ($action === 'removeFrom') {
                return $this->removeFromProperty($propertyName, ...$arguments);
            }
        }

        throw new Exception(sprintf('Method "%s" not found in %s', $name, __CLASS__));
    }

}
