<?php


namespace Arato\Transformers;


class UserTransformer extends Transformer
{
    public function basicTransform($item)
    {
        return [
            'id'    => $item['id'],
            'email' => $item['email']
        ];
    }

    public function extendedTransform($item)
    {
        return $this->basicTransform($item);
    }

    public function fullTransform($item)
    {
        return $this->extendedTransform($item);
    }
}