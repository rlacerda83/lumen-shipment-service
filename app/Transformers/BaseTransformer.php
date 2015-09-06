<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    /**
     * @param $model
     * @return mixed
     */
    public function transform($model)
    {
        if ($model instanceof \stdClass) {
            return json_decode(json_encode($model), true);
        }

        return $model->toArray();
    }
}
