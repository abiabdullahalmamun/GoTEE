<?php

namespace App\Services\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CodeSlugGeneratorService
{
    /**
     * Generate a unique serial code like ITM001, MAX002.
     */
    public function generateCode(Model $model, string $prefix = 'CAT', string $column = 'code', int $padLength = 3): string
    {
        $table = $model->getTable();

        $latest = $model->newQuery()
            ->where($column, 'like', "$prefix%")
            ->orderByDesc($column)
            ->value($column);

        $number = 1;
        if ($latest) {
            $number = (int) preg_replace('/\D/', '', $latest) + 1;
        }

        return $prefix . str_pad($number, $padLength, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique slug for a given model.
     */
    public function generateSlug(Model $model, string $name, string $column = 'slug'): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while ($model->newQuery()->where($column, $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Generate the next sequence number for a model.
     */
    public function generateSequence(Model $model, string $column = 'sequence'): int
    {
        return (int) $model->newQuery()->max($column) + 1;
    }
}
