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
        $query = $this->getModel()->newQuery()
            ->select(Carrier::getTableName().'.*')
            ->join('shipment_carriers_countries', 'shipment_carriers_countries.carrier_id', '=', Carrier::getTableName() .'.id')
            ->join(Country::getTableName(), 'shipment_carriers_countries.country_id', '=', Country::getTableName() .'.id')
            ->where(Country::getTableName().'.id', $country->id);

        $key = md5($query->toSql().$country->id);
        return $this->cacheQueryBuilder($key, $query);
    }

    public function getServices()
    {
        $query = $this->getModel()->hasMany('App\Models\CarrierService');
        return $this->cacheQueryBuilder(md5('services'.$this->getModel()->id), $query);
    }

    public function getCountries()
    {
        return $this->getModel()->belongsToMany('App\Models\Country', 'shipment_carriers_countries');
    }

    public function getCountriesBy($attribute, $value)
    {
        $data = $this->getCountries()->where($attribute, $value)->get();
        $key = 'countriesBy'.$attribute.$value.$this->getModel()->id;
        return $this->cacheGenericData(md5($key), $data, 'CarriersCountries');
    }

    public function addCountry(Country $country)
    {
        $countCountries = $this->getCountriesBy(Country::getTableName().'.code', $country->code)->count();
        if($countCountries == 0) {
            $this->getCountries()->attach($country->id);
        }
        $this->flushCacheByTags('CarriersCountries');
    }

    public function removeCountry(Country $country)
    {
        $this->getCountries()->detach($country->id);
        $this->flushCacheByTags('CarriersCountries');
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
