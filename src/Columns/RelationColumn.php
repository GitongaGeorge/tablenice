<?php

namespace Mystamyst\TableNice\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class RelationColumn extends TextColumn
{
    protected string $relationName;
    protected string $relationAttribute;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->relationName = $name;
    }
    
    public function getRelationName(): string
    {
        return $this->relationName;
    }

    public function relation(string $relationName, string $attribute): self
    {
        $this->relationName = $relationName;
        $this->relationAttribute = $attribute;
        $this->name = $relationName . '.' . $attribute;
        return $this;
    }

    protected function resolveValue(Model $model)
    {
        // data_get will return the collection of related models
        $value = data_get($model, $this->relationName);

        if ($value instanceof Collection) {
            // If it's a collection (HasMany, BelongsToMany, etc.), pluck the attribute and join
            return $value->pluck($this->relationAttribute)->implode(', ');
        }
        
        // For singular relations, get the specific attribute
        return data_get($model, $this->name);
    }

    public function searchLogic(Builder $query, string $searchTerm): void
    {
        $query->orWhereHas($this->relationName, function (Builder $q) use ($searchTerm) {
            $q->where($this->relationAttribute, 'like', '%' . $searchTerm . '%');
        });
    }

    public function filterLogic(Builder $query, $value): void
    {
        $query->whereHas($this->relationName, function (Builder $q) use ($value) {
            $q->where($this->relationAttribute, $value);
        });
    }

    public function sortLogic(Builder $query, string $direction): Builder
    {
        $mainModel = $query->getModel();
        $relation = $mainModel->{$this->relationName}();

        // Sorting is only safe and non-duplicative for singular relationships.
        if (!($relation instanceof BelongsTo || $relation instanceof HasOne)) {
            // For HasMany, BelongsToMany, HasManyThrough etc., sorting by a specific
            // attribute is ambiguous and joining causes duplicate rows.
            // We will not apply any sort to prevent incorrect data.
            return $query;
        }

        $mainTable = $mainModel->getTable();
        $relatedModel = $relation->getRelated();
        $relatedTable = $relatedModel->getTable();
        $foreignKey = $relation->getForeignKeyName();
        $ownerKey = $relation->getOwnerKeyName();

        $joins = $query->getQuery()->joins ?? [];
        $isAlreadyJoined = collect($joins)->contains('table', $relatedTable);

        if (!$isAlreadyJoined) {
            $query->join($relatedTable, "{$mainTable}.{$ownerKey}", '=', "{$relatedTable}.{$foreignKey}");
        }

        if (empty($query->getQuery()->columns)) {
            $query->select("{$mainTable}.*");
        }

        return $query->orderBy("{$relatedTable}.{$this->relationAttribute}", $direction);
    }
}
