<?php

use Underscore\Parse;
use Underscore\Types\Arrays;

class AlertService extends Service
{


    public function __construct(Alert $model)
    {
        parent::__construct($model);
    }

    public function filter(Array $filters)
    {

        $limit = Maybe(Arrays::get($filters, 'limit'))
            ->map(function ($maybe) {
                $limit = Parse::toInteger($maybe->val($this->defaultLimit));

                return $limit <= 50 ? $limit : $this->defaultLimit;
            })
            ->val($this->defaultLimit);

        $availableSorts = ['created_at', 'price'];

        $sortBy = Maybe(Arrays::get($filters, 'sort'))
            ->map(function ($maybe) use ($availableSorts) {
                $sort = $maybe->val();

                return Arrays::contains($availableSorts, $maybe->val())
                    ? $sort
                    : $this->defaultSort;
            })
            ->val($this->defaultSort);

        $availableOrders = ['asc', 'desc'];
        $order = Maybe(Arrays::get($filters, 'order'))
            ->map(function ($maybe) use ($availableOrders) {
                $order = $maybe->val();

                return Arrays::contains($availableOrders, $maybe->val())
                    ? $order
                    : $this->defaultOrder;
            })
            ->val($this->defaultOrder);

        return $this->model->with([])->orderBy($sortBy, $order)->paginate($limit);
    }

    public function create($item)
    {
        // TODO: Implement create() method.
    }
}