<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        // Keys are the query string params, and the values are the values for those params
        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                // can call the method for a given param if it exists
                $this->$key($value);
            }
        }

        return $builder;
    }
}
