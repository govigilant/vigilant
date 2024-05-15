<?php

namespace Vigilant\Core\Data;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Validator;

abstract class Data implements Arrayable, ArrayAccess
{
    public array $rules = [];

    final public function __construct(
        public array $data
    ) {
        $this->validate($data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function validate(array $data): void
    {
        Validator::make($data, $this->rules)->validate();
    }

    public static function of(array $data): static
    {
        return new static($data);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
