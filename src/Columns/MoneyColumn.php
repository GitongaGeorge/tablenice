<?php

namespace Mystamyst\Tablenice\Columns;

class MoneyColumn extends Column
{
    protected string $currency = '$';
    protected int $decimals = 2;
    protected string $decimalPoint = '.';
    protected string $thousandsSeparator = ',';

    public function currency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

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
            return $this->currency . number_format($value, $this->decimals, $this->decimalPoint, $this->thousandsSeparator);
        }
        return $value;
    }
}