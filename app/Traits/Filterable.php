<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeSearch(Builder $query, ?string $field = null): Builder
    {
        $search = request()->get("search", null);

        if ($search && $field && in_array($field, $this->filter_fields ?? [])) {
            $query->where($field, 'LIKE', "%{$search}%");
        }

        return $query;
    }

    public function scopeFilter(Builder $query): Builder
    {
        $filters = request()->all();

        foreach ($filters as $key => $value) {
            if (preg_match('/(.*?)\[(.*)\]/', $key, $matches)) {
                $field = $matches[1];
                $operator = $matches[2];
            } else {
                $field = $key;
                $operator = 'eq';
            }

            if (!in_array($field, $this->filter_fields)) {
                continue;
            };

            switch (strtolower($operator)) {
                case 'eq':
                    $query->where($field, 'LIKE', "%{$value}%");
                    break;

                case 'gt':
                    $query->where($field, '>', $value);
                    break;

                case 'lt':
                    $query->where($field, '<', $value);
                    break;

                case 'gte':
                    $query->where($field, '>=', $value);
                    break;

                case 'lte':
                    $query->where($field, '<=', $value);
                    break;

                default:
                    throw new \InvalidArgumentException("Operator not supported: {$operator}");
            }
        }

        return $query;
    }

    public function scopeSort(Builder $query): Builder
    {

        $field = request()->input('sort');
        $order = null;

        if (str_starts_with($field, '-')) {
            $order = "desc";
        } else {
            $order = "asc";
        }

        $field = ltrim($field, '-');

        if (!in_array($field, $this->sort_fields)) {
            if ($field) {
                $query->orderBy($field, $order);
            }
        }

        return $query;
    }
}
