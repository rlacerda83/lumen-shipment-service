<?php

namespace App\Repositories\Eloquent;

use App\Models\Carrier;
use App\Models\Country;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Validator;

class CarrierRepository extends AbstractRepository
{

    protected $enableCaching = true;

    public static $rules = [
        'name' => 'required|max:150',
        'code' => 'required'
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

    public function allWithCountry(Country $country)
    {
        $query = $this->queryBuilder
            ->select(Carrier::getTableName().'.*')
            ->join('shipment_carriers_countries', 'shipment_carriers_countries.carrier_id', '=', Carrier::getTableName() .'.id')
            ->join(Country::getTableName(), 'shipment_carriers_countries.country_id', '=', Country::getTableName() .'.id')
            ->where(Country::getTableName().'.id', $country->id);

        $key = md5($query->toSql().$country->id);
        return $this->cacheQueryBuilder($key, $query);
    }

    /**
     * Get the comments for the blog post.
     */
    public function getServices()
    {
        return $this->getModel()->hasMany('App\Models\CarrierService')->get();
    }

    public function getCountries()
    {
        return Carrier::all()->belongsToMany('App\Models\Country', 'shipment_carriers_countries');
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
