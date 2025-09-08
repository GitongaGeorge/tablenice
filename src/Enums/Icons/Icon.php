<?php

namespace App\Enums;

interface Icon
{
    public function toHtml(array $attributes = []): string;
}