<?php

namespace App\Models;

use Validator;
use Illuminate\Http\Request;

class Carrier extends BaseModel
{

    protected $table = 'shipment_carriers';

    const SEND_TYPE_QUEUE = 'QUEUE';
    const SEND_TYPE_SYNC = 'NOW';

    //protected $casts = ['cc' => 'array', 'bcc' => 'array'];

    protected $fillable = ['document1', 'name', 'document2', 'address', 'city', 'state', 'postal_code', 'country', 'min_volume', 'max_volume', 'status'];

    /**
     * @param Request $request
     * @return bool
     */
    public function validateRequest(Request $request)
    {
        $rules = [
            'name' => 'required|max:150'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return true;
    }

    /**
     * Get the comments for the blog post.
     */
    public function services()
    {
        return $this->hasMany('App\Models\Carrier\Services');
    }


    /**
     * @param array $attributes
     * @return Email
     * @throws \Exception
     */
    public function customFill(array $attributes)
    {
        try {
            $this->fill($attributes);

            if (isset($attributes['bcc']) && is_array($attributes['bcc'])) {
                $this->bcc = json_encode($attributes['bcc']);
            }

            if (isset($attributes['cc']) && is_array($attributes['cc'])) {
                $this->cc = json_encode($attributes['cc']);
            }

        } catch (\Exception $e) {
            throw $e;
        }

        return $this;

    }
}
