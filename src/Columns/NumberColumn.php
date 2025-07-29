<?php

namespace Mystamyst\Tablenice\Columns;

class NumberColumn extends Column
{
    protected int $decimals = 0;
    protected string $decimalPoint = '.';
    protected string $thousandsSeparator = ',';

    public function decimals(int $decimals): static
    {
        $this->decimals = $decimals;
        return $this;
    }

    public function decimalPoint(string $point): static
    {
        $this->decimalPoint = $point;
        return $this;
    }

    public function thousandsSeparator(string $separator): static
    {
        $this->thousandsSeparator = $separator;
        return $this;
    }

    public function getValue(\Illuminate\Database\Eloquent\Model $record): mixed
    {
        $value = parent::getValue($record);
        if (is_numeric($value)) {
            return number_format($value, $this->decimals, $this->decimalPoint, $this->thousandsSeparator);
        }
        return $value;
    }
}