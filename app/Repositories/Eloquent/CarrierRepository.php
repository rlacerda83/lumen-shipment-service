<?php

namespace App\Repositories\Eloquent;

use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Validator;

class CarrierRepository extends AbstractRepository
{

    public static $rules = [
        'name' => 'required|max:150'
    ];

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\Carrier';
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function validateRequest(Request $request)
    {
        $rules = self::$rules;

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
        return $this->getModel()->hasMany('App\Models\Carrier\Services');
    }

    /**
     * @param Request $request
     * @param int $itemsPage
     *
     * @return mixed
     */
    public function findAllPaginate(Request $request, $itemsPage = 30)
    {
        $key = md5($itemsPage.$request->getRequestUri());
        $queryParser = new ParserRequest($request, $this->getModel());
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }
}
