<?php

namespace Mystamyst\Tablenice\Columns;

class TextColumn extends Column
{
    protected ?int $limit = null;

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getValue(\Illuminate\Database\Eloquent\Model $record): mixed
    {
        $value = parent::getValue($record);
        if ($this->limit && is_string($value)) {
            return \Illuminate\Support\Str::limit($value, $this->limit);
        }
        return $value;
    }
}