<?php

namespace Pricecurrent\LaravelEloquentFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Pricecurrent\LaravelEloquentFilters\Contracts\EloquentFilterContract;
use Pricecurrent\LaravelEloquentFilters\Exceptions\EloquentFiltersException;
use Throwable;

class EloquentFilters extends Collection
{
    /**
     * @param  array $items
     * @throws Throwable
     */
    public function __construct($items = [])
    {
        static::validateItems($items);

        return parent::__construct($items);
    }

    /**
     * {@inheritDoc}
     */
    public static function make($items = [], ...$args)
    {
        static::validateItems($items);

        return parent::make($items);
    }

    /**
     * @param  Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        $this
            ->removeNotApplicable()
            ->each(fn ($filter) => $filter->apply($builder));

        return $builder;
    }

    /**
     * @return mixed
     */
    public function removeNotApplicable()
    {
        return $this->filter->isApplicable();
    }

    /**
     * @param $items
     * @throws Throwable
     */
    protected static function validateItems($items)
    {
        collect($items)->each(fn ($item) => throw_unless(
            $item instanceof EloquentFilterContract,
            new EloquentFiltersException("Filter must implement EloquentFilterContract")
        ));
    }
}
