<?php

namespace Mystamyst\Tablenice\Forms\Fields;

use Illuminate\Database\Eloquent\Model;

class RelationshipSelectField extends SelectField
{
    protected string $model;
    protected string $relationDisplayAttribute;
    protected ?string $relationKey = 'id';
    protected ?array $queryScopes = [];

    public function __construct(string $name, string $label, string $model, string $relationDisplayAttribute)
    {
        parent::__construct($name, $label);
        $this->model = $model;
        $this->relationDisplayAttribute = $relationDisplayAttribute;
    }

    public static function make(string $name, ?string $label = null): static
    {
        // Provide default values for model and relationDisplayAttribute, or throw if not set later
        return new static($name, $label ?? '', '', '');
    }

    /**
     * Custom static constructor for RelationshipSelectField.
     */
    public static function makeWithRelationship(string $name, string $label, string $model, string $relationDisplayAttribute): static
    {
        return new static($name, $label, $model, $relationDisplayAttribute);
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getRelationDisplayAttribute(): string
    {
        return $this->relationDisplayAttribute;
    }

    public function relationKey(string $key): static
    {
        $this->relationKey = $key;
        return $this;
    }

    public function getRelationKey(): ?string
    {
        return $this->relationKey;
    }

    public function queryScopes(array $scopes): static
    {
        $this->queryScopes = $scopes;
        return $this;
    }

    /**
     * Dynamically load options from the relationship.
     * This will be called in the Blade view or by Livewire.
     */
    public function getOptions(): array
    {
        $query = $this->model::query();

        foreach ($this->queryScopes as $scope => $params) {
            if (is_numeric($scope)) { // If scope is just a method name
                $query->{$params}();
            } else {
                $query->{$scope}(... (array) $params);
            }
        }

        return $query->pluck($this->relationDisplayAttribute, $this->relationKey)->toArray();
    }
}