<?php
declare(strict_types=1);

namespace Zeroseven\Z7Blog\Service;

class TraitCollector
{

    public static function collect(string $className): ?array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['z7_blog']['traits'][$className] ?? null;
    }

    public static function createClass(string $namespace, string $className, string $targetClassName = null): void
    {
        if(!class_exists('\\' . $namespace . '\\' . $className)) {
            eval('
                namespace ' . ltrim($namespace, '\\') . ';
    
                class ' . $className . ($targetClassName ? (' extends \\' . ltrim($targetClassName, '\\')) : '') .'
                {' .
                    (($traits = static::collect($targetClassName)) ? 'use ' . implode(',', array_map(static function ($trait) {
                        return '\\' . ltrim($trait, '\\');
                    }, $traits)) . ';' : '')
               . '}
            ');
        }
    }
}
