<?php

declare(strict_types=1);

namespace Zeroseven\Z7Blog\Domain\Demand;

use Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Zeroseven\Z7Blog\Service\TraitCollectorService;
use Zeroseven\Z7Blog\Service\TypeCastService;

/**
 * @method setUids(array $param)
 * @method getUids(array $param)
 * @method setOrdering(array $param)
 * @method getOrdering(string $param)
 * @method setParameterMapping(array $param)
 * @method getParameterMapping(array $param)
 * @method setTypeMapping(array $param)
 * @method getTypeMapping(array $param)
 */
abstract class AbstractDemand
{

    /** @var array */
    public $uids = [];

    /** @var string */
    public $ordering = '';

    /** @var array */
    protected $parameterMapping;

    /** @var array */
    protected $typeMapping;

    public function __construct()
    {

        // Create array of property names of reflection class
        foreach (GeneralUtility::makeInstance(\ReflectionClass::class, static::class)->getProperties() ?? [] as $reflection) {
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
        // Return create trait collector class
        if ($traits = TraitCollectorService::collect(static::class)) {
            TraitCollectorService::createClass(
                __NAMESPACE__,
                (new \ReflectionClass(static::class))->getShortName() . 'TraitCollector',
                static::class,
                $traits
            );

            return GeneralUtility::makeInstance(static::class . 'TraitCollector');
        }

        // Return default demand object
        return GeneralUtility::makeInstance(static::class);
    }

    /** @throws Exception */
    protected function checkPropertyAccess(string $propertyName): void
    {
        if (!$this->hasProperty($propertyName)) {
            throw new Exception(sprintf('Property "%s" not found in demand model.', $propertyName));
        }
    }

    public function hasProperty(string $propertyName)
    {
        try {
            if (GeneralUtility::makeInstance(\ReflectionClass::class, static::class)->getProperty($propertyName)) {
                return true;
            }
        } catch (\ReflectionException $e) {
        }

        return false;
    }

    public function getType(string $propertyName): ?string
    {
        try {
            $this->checkPropertyAccess($propertyName);
            return $this->typeMapping[$propertyName] ?? null;
        } catch (Exception $e) {
        }

        return null;
    }

    public function getProperty(string $propertyName)
    {
        $type = $this->getType($propertyName);

        if ($type === 'int') {
            return (int)$this->{$propertyName};
        }

        if ($type === 'string') {
            return (string)$this->{$propertyName};
        }

        if ($type === 'array') {
            return (array)$this->{$propertyName};
        }

        if ($type === 'bool') {
            return (bool)$this->{$propertyName};
        }

        return $this->{$propertyName};
    }

    /** @throws Exception */
    public function setProperty(string $propertyName, $value): AbstractDemand
    {
        $type = $this->getType($propertyName);

        if ($type === 'int') {
            $this->{$propertyName} = TypeCastService::int($value);
        } elseif ($type === 'string') {
            $this->{$propertyName} = TypeCastService::string($value);
        } elseif ($type === 'array') {
            $this->{$propertyName} = array_map(static function ($v) {
                return (string)$v;
            }, TypeCastService::array($value));
        } elseif ($type === 'bool') {
            $this->{$propertyName} = TypeCastService::bool($value);
        } else {
            $this->{$propertyName} = $value;
        }

        return $this;
    }

    public function getParameter(string $parameter)
    {
        return $this->getProperty(array_search($parameter, $this->parameterMapping, true));
    }

    public function addToProperty(string $propertyName, $value): self
    {
        if ($this->getType($propertyName) === 'array') {
            $array = $this->getProperty($propertyName);
            $array[] = $value;

            return $this->setProperty($propertyName, $array);
        }

        throw new Exception('AddToProperty is allowed on type array only');
    }

    public function removeFromProperty(string $propertyName, $value): self
    {
        if ($this->getType($propertyName) === 'array') {
            $array = array_filter(
                $this->getProperty($propertyName),
                static function ($i) use ($value) {
                    return $i !== $value;
                }
            );

            return $this->setProperty($propertyName, $array);
        }

        throw new Exception('RemoveFromProperty is allowed on type array only');
    }

    public function setParameterArray(bool $ignoreEmptyValues, ...$arguments): self
    {

        // Check the types of arguments
        foreach ($arguments as $argument) {
            if (!is_array($arguments)) {
                throw new Exception('Disallowed argument ' . gettype($argument));
            }

            // Set properties
            foreach ($this->parameterMapping as $propertyName => $parameter) {
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

    public function getParameterDiff(array $base, array $protectedParameters = null): array
    {
        $result = [];
        $parameterArray = $this->getParameterArray(false);

        foreach ($this->parameterMapping as $propertyName => $parameter) {
            if (
                isset($base[$parameter])
                && ($protectedParameters && in_array($parameter, $protectedParameters, true) || (
                    $this->getType($propertyName) !== 'array' && $base[$parameter] !== $parameterArray[$parameter] ||
                    $this->getType($propertyName) === 'array' && (count(array_diff(TypeCastService::array($base[$parameter]), $this->getProperty($propertyName))) || count(array_diff($this->getProperty($propertyName), TypeCastService::array($base[$parameter]))))
                ))
            ) {
                if (!empty($parameterArray[$parameter])) {
                    $result[$parameter] = $parameterArray[$parameter];
                } elseif (!empty($base[$parameter])) {
                    $result[$parameter] = '';
                }
            }
        }

        return $result;
    }

    public function reset(): void
    {
        foreach ($this->parameterMapping as $propertyName => $parameter) {
            $this->setProperty($propertyName, null);
        }
    }

    /** @throws Exception */
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

        throw new Exception(sprintf('Method "%s" not found in %s', $name, __CLASS__), 1659427214);
    }
}
