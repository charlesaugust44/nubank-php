<?php

namespace CharlesAugust44\NubankPHP\Models;

abstract class Base
{
    public function __construct(object|string|array $data = null)
    {
        if ($data === null) {
            return;
        }

        $this->unserialize($data);
    }

    protected function getClassName(): string
    {
        return self::class;
    }

    public function unserialize(object|string|array $data): void
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_array($data)) {
            $data = (object)$data;
        }

        $class = $this->getClassName();

        if ($class === 'CharlesAugust44\NubankPHP\Models\Base') {
            throw new \Exception("unserialize():void can't be accessed directly from Base class");
        }

        $reflection = new \ReflectionClass($class);

        foreach ($data as $key => $value) {
            $jsonType = $this->matchJsonType($value);

            if (!$reflection->hasProperty($key)) {
                error_log("JSON field $key of type $jsonType does not exist as property on class $class\n");
                continue;
            }

            $classType = $reflection->getProperty($key)->getType()->getName();

            if ($jsonType !== 'object' && $classType !== $jsonType) {
                error_log("JSON field $key of type $jsonType is different than class type $classType\n");
                continue;
            } elseif ($jsonType === 'object' && !class_exists($classType) && $classType !== 'array') {
                error_log("Class $classType is not an instantiable class or json field $key is of the wrong type\n");
                continue;
            }

            if ($jsonType === 'object' && $classType !== 'array') {
                $this->$key = new $classType();
                $this->$key->unserialize($value);
                continue;
            }

            $type = $this->getArrayType($key);

            if ($type !== null) {
                $value = $this->prepareArray($value, $type);
            }

            $this->$key = $value;
        }
    }

    private function prepareArray(array|object $values, string $type): array
    {
        if (is_object($values)) {
            $values = (array)$values;
        }

        $output = [];

        foreach ($values as $key => $value) {
            if (!class_exists($type)) {
                $output[$key] = $type === 'array' ? (array)$value : $value;
                continue;
            }

            $object = new $type();
            $object->unserialize($value);
            $output[$key] = $object;
        }

        return $output;
    }

    protected function getArrayType(string $key): ?string
    {
        return null;
    }

    private function matchJsonType(mixed $jsonValue): string
    {
        $jsonType = gettype($jsonValue);

        return match ($jsonType) {
            'double' => 'float',
            'integer' => 'int',
            'boolean' => 'bool',
            default => $jsonType
        };
    }
}
