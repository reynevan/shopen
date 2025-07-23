<?php

namespace Shopen\Core\Support\Product\Sorting;


interface ProductSorter
{
    public function setKey($key): static;

    public function keys(): array;

    public function label(string $key): ?string;

    public function build(): array;
}