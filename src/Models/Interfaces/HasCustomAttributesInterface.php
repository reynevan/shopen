<?php

namespace Shopen\Models\Interfaces;

interface HasCustomAttributesInterface
{
    function getEntityType(): string;

    public function setCustomAttribute($key, $value): static;
}