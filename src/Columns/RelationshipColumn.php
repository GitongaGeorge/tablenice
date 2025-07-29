<?php

namespace Mystamyst\Tablenice\Columns;

class RelationshipColumn extends Column
{
    protected string $relationship;
    protected string $displayAttribute;

    public function __construct(string $name, string $relationship, string $displayAttribute, ?string $label = null)
    {
        parent::__construct($name, $label);
        $this->relationship = $relationship;
        $this->displayAttribute = $displayAttribute;
        $this->attribute = $relationship . '.' . $displayAttribute; // Set attribute for data_get
    }

    public static function make(string $name, ?string $label = null): static
    {
        // You may throw an exception or return a default instance, as make() cannot accept relationship/displayAttribute here.
        throw new \BadMethodCallException('Use RelationshipColumn::makeWithRelationship() instead.');
    }

    public static function makeWithRelationship(string $name, string $relationship, string $displayAttribute, ?string $label = null): static
    {
        return new static($name, $relationship, $displayAttribute, $label);
    }

    public function getRelationship(): string
    {
        return $this->relationship;
    }

    public function getDisplayAttribute(): string
    {
        return $this->displayAttribute;
    }
}