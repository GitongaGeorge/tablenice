<?php

namespace Mystamyst\TableNice\Enums\Icons;

interface Icon
{
    public function toHtml(array $attributes = []): string;
}