<?php

namespace App\Services\Shipment\Shippers;

use App\Services\Shipment;
use App\Services\Shipment\Shippers\ShippersAbstract;
use Cache;
use Validator;
use App\Services\Shipment\Shippers\PostOffice\Response as ResponsePostOffice;

class PostOffice extends ShippersAbstract {

    const CONSULT_METHOD = 'CalcPrecoPrazo';
    const RETURN_METHOD = 'CalcPrecoPrazoResult';

    public static $rules = [
        'sCepOrigem' => 'required|max:8',
        'sCepDestino' => 'required|max:8',
        'nVlPeso' => 'required',
        'nVlComprimento' => 'required',
        'nVlAltura' => 'required',
        'nVlLargura' => 'required',
        'sCdMaoPropria' => 'required',
        'nVlValorDeclarado' => 'required',
        'sCdAvisoRecebimento' => 'required',
        'nVlDiametro' => 'required',
        'nCdFormato' => 'required',
        'nCdServico' => 'required'
    ];

    public static $messages = [
        'sCepOrigem.required' => 'Field from_zip is required',
        'sCepDestino.required' => 'Field to_zip is required',
        'nVlPeso.required' => 'Field parcel_weight is required',
        'nVlComprimento.required' => 'Field parcel_length is required',
        'nVlAltura.required' => 'Field parcel_heigth is required',
        'nVlLargura.required' => 'Field parcel_width is required',
        'sCdMaoPropria.required' => 'Field ownHands is required',
        'nVlValorDeclarado.required' => 'Field declaredValue is required',
        'sCdAvisoRecebimento.required' => 'Field deliveryNotification is required',
        'nVlDiametro.required' => 'Field diameter is required',
        'nCdFormato.required' => 'Package format is required',
        'nCdServico.required' => 'Field services is required',
    ];

    protected $cacheForMinutes = 60;

    protected $url;

    protected $company;

    protected $password;

    protected $deliveryNotification;

    protected $ownHands;

    protected $format;

    protected $servicesCodesNames = [];

    public function initConfig()
    {
        $this->config = json_decode($this->config);
        $this->url = $this->config->url;
        $this->company = $this->config->company;
        $this->password = $this->config->password;
        $this->deliveryNotification = $this->config->deliveryNotification;
        $this->format = $this->config->format;
        $this->ownHands = $this->config->ownHands;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setDeliveryNotification($deliveryNotification)
    {
        $this->deliveryNotification = $deliveryNotification;
        return $this;
    }

    public function getDeliveryNotification()
    {
        return $this->deliveryNotification;
    }

    public function setOwnHands($ownHands)
    {
        $this->ownHands = $ownHands;
        return $this;
    }

    public function getOwnHands()
    {
        return $this->ownHands;
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    public function getFormat()
    {
        return $this->format;
    }

    private function getCodesServices()
    {
        $servicesCodes = [];
        foreach ($this->services as $service) {
            $this->servicesCodesNames[$service->code] = $service->name;
            if ($service->status == 0) continue;
            $servicesCodes[] = $service->code;
        }
        $stringServices = implode(',', $servicesCodes);
        return $stringServices;
    }


    protected function bindParams()
    {
        return [
            'nCdEmpresa' => (string) $this->getCompany(),
            'sDsSenha' => (string) $this->getPassword(),
            'sCepOrigem' => (string) $this->shipment->getFromPostalCode(),
            'sCepDestino' => (string) $this->shipment->getToPostalCode(),
            'nCdServico' => $this->getCodesServices(),
            'nVlPeso' => (float) $this->shipment->getPackage()->getWeight(),
            'nCdFormato' => (integer) $this->getFormat(),
            'nVlComprimento' => (float) $this->shipment->getPackage()->getLenght(),
            'nVlAltura' => (float) $this->shipment->getPackage()->getHeight(),
            'nVlLargura' => (float) $this->shipment->getPackage()->getWidth(),
            'sCdMaoPropria' => (bool) $this->getOwnHands() ? 'S' : 'N',
            'nVlValorDeclarado' => (float) $this->shipment->getPackage()->getDeclaredValue(),
            'sCdAvisoRecebimento' => (bool) $this->getDeliveryNotification() ? 'S' : 'N',
            'nVlDiametro' => '0'
        ];
    }

    public function getRate() {
        ini_set("allow_url_fopen", 1);
        ini_set("soap.wsdl_cache_enabled", 0);

        try {
            $params = $this->bindParams();
            $returnValidate = $this->validateParams($params);

            if ($returnValidate !== true) {
                return ['errors' => $returnValidate];
            }

            $cacheKey = md5(json_encode($params));

            $response = Cache::tags(get_class($this))->remember($cacheKey, $this->cacheForMinutes, function () use ($params) {
                $method = self::CONSULT_METHOD;
                $soap = new \SoapClient($this->getUrl(), array("connection_timeout" => 15));
                return $soap->$method($params);
            });

            return $this->parseResponse($response);
        } catch (\SoapFault $e) {
            throw new \Exception($e->getMessage());
        }

    }

    protected function validateParams($params)
    {
        $validator = Validator::make($params, self::$rules, self::$messages);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }
        return true;
    }

    protected function parseResponse($response)
    {
        $rates = [];
        if ($response instanceof \stdClass) {
            $returnMethod = self::RETURN_METHOD;
            $returnItens = $response->$returnMethod->Servicos->cServico;

            if (is_array($returnItens)) {
                foreach ($returnItens as $item) {
                    $rates[] = $this->setResponse($item);
                }
            } else if ($returnItens instanceof \stdClass) {
                $rates[] = $this->setResponse($returnItens);
            }

        } else {
            throw new \Exception('Invalid return!');
        }

        return $rates;
    }

    private function setResponse(\stdClass $item)
    {
        $item->Nome = $this->servicesCodesNames[$item->Codigo];
        $response = new ResponsePostOffice($item);
        return $response->toArray();
    }

    public function createLabel(array $params=array()) {

    }

}
