<?php

namespace App\Enums;

trait EnumFunctions
{
//    static $options = null;

    public static function getKeysValues(
        string $without = '',
        array|null $modifyKeyName = null,
        string|null $options = null
    ): array {
        $cases = $options ? self::$options() : self::cases();

        $enumKeyValuePairs = [];
        foreach ($cases as $item) {
            if ($without !== $item->name) {
                $name = $modifyKeyName
                    ? $item->name === array_keys($modifyKeyName)[0]
                        ? $modifyKeyName[array_keys($modifyKeyName)[0]]
                        : $item->name
                    : $item->name;

                $enumKeyValuePairs[] = ['name' => $name, 'key' => $item->value];
            }
        }

        return $enumKeyValuePairs;
    }


    public static function getValues(string $without = ''): array
    {
        $values = [];
        foreach (self::cases() as $item) {
            if ($without !== $item->name) {
                $values[] = $item->value;
            }
        }

        return $values;
    }

    public static function getKeys(): array
    {
        $keys = [];
        foreach (self::cases() as $item) {
            $keys[] = $item->name;
        }

        return $keys;
    }

    public static function getByKey(string $key): CountyEnum|LanguageEnum|EventTypeEnum|null
    {
        foreach (self::cases() as $enum) {
            if ($enum->name === $key) {
                return $enum;
            }
        }

        return null;
    }
}
