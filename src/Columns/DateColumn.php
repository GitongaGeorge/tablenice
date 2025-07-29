<?php

namespace Mystamyst\Tablenice\Columns;

use Carbon\Carbon;

class DateColumn extends Column
{
    protected string $format = 'Y-m-d';

    public function format(string $format): static
    {
        $this->format = $format;
        return $this;
    }

    public function getValue(\Illuminate\Database\Eloquent\Model $record): mixed
    {
        $value = parent::getValue($record);
        if ($value) {
            try {
                return Carbon::parse($value)->format($this->format);
            } catch (\Exception $e) {
                // Handle invalid date format
            }
        }
        return null;
    }
}