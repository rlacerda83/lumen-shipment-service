<?php

namespace App\Services\Shippers;

use App\Services\Shipment;
use App\Services\ShippersAbstract;

class PostOffice extends ShippersAbstract {

    /**
     *
     * @var \SimpleXMLElement
     */
    protected $config = null;

    /**
     *
     * @var string the array to be sent to the UPS API
     */
    protected $request = array();

    /**
     *
     * @var Shipment
     */
    protected $Shipment = null;


    protected $services = null;

    /**
     *
     * @var object the API call response object
     */
    protected $Response = null;


    public function getRate() {
        foreach ($this->services as $service) {

            $valorDeclarado = 0;
            $peso = 0;

            if ($service->getStatus() == false) {
                continue;
            }

            if(en('APP_ENV') == 'production') {

                $cepDestino = preg_replace('/[^0-9]/', '', $this->getEntrega()->getEndereco()->getCep());
                $cepOrigem = preg_replace('/[^0-9]/', '', $this->config->cepOrigem);

                $cacheID = eregi_replace('[^0-9|a-z|A-Z|_]', '', "{$cepDestino}_{$valorDeclarado}_{$peso}_{$servico->getCodigo()}");
                if (!($dataCache = $this->cache->load($cacheID))) {
                    $peso = number_format($peso, 2, ',', '');
                    $valorDeclarado = number_format($valorDeclarado, 2, ',', '');

                    $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$this->config->empresa}&sDsSenha={$this->config->senha}&sCepOrigem={$cepOrigem}&sCepDestino={$cepDestino}&nVlPeso={$peso}&nCdFormato={$this->config->formato}&nVlComprimento=21&nVlAltura=14&nVlLargura=11&sCdMaoPropria={$this->config->maoPropria}&nVlValorDeclarado={$valorDeclarado}&sCdAvisoRecebimento={$this->config->avisoRecebimento}&nCdServico={$servico->getCodigo()}&nVlDiametro=0&StrRetorno=xml";
                    $xml = simplexml_load_file($url);
                    $arrayRetorno = array();
                    $count = 0;

                    foreach( $xml->cServico as $servicoCorreio ) {
                        if($servicoCorreio->Erro == 0) {
                            $servico->setValor(0)->setStatus(true);
                            $this->cache->save(array('valor' => 0, 'status' => true), $cacheID);
                        } else {
                            $servico->setStatus(false);
                            $this->cache->save(array('valor' => 0, 'status' => false), $cacheID);
                        }
                    }

                } else {
                    $servico->setValor($dataCache['valor']);
                    $servico->setStatus($dataCache['status']);
                }

            } else {
                if($valorDeclarado > 0) {

                    $servico->setValor(1.00);
                    $servico->setDescricao("(Teste) Prazo de 10 (dias úteis)");

                } else {

                    $servico->setValor(0)->setStatus(true);
                }
            }

        }
    }

    public function createLabel(array $params=array()) {

    }

}
