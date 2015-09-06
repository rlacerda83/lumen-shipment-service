<?php

namespace App\Repositories\Eloquent;

use App\Models\Carrier;
use App\Models\Country;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Validator;
use Illuminate\Container\Container as App;

class CountryRepository extends AbstractRepository
{

    protected $tableCarriers = null;
    protected $tableCountry = null;
    protected $pivotTable = 'shipment_carriers_countries';

    public static $rules = [
        'name' => 'required|max:150',
        'code' => 'required'
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->tableCarriers = Carrier::getTableName();
        $this->tableCountry = $this->getModel()->getTableName();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\Country';
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
     * @param Request $request
     * @param $idCarrier
     * @param int $itemsPage
     *
     * @return mixed
     */
    public function findAllByCarrierPaginate(Request $request, $idCarrier, $itemsPage = 30)
    {
        $key = md5($itemsPage.$request->getRequestUri());

        $query = $this->getModel()->newQuery()
            ->select($this->tableCountry.'.*')
            ->join($this->pivotTable, $this->pivotTable.'.country_id', '=', $this->tableCountry .'.id')
            ->join($this->tableCarriers, $this->pivotTable .'.carrier_id', '=', $this->tableCarriers .'.id')
            ->where($this->tableCarriers.'.id', $idCarrier);

        $queryParser = new ParserRequest($request, $this->getModel(), $query);
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }
}
