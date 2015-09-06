<?php

namespace App\Services\Shipment\Shippers\PostOffice;

class Response extends \App\Services\Shipment\Response
{
    protected $ownHandsPrice;

    protected $deliveryNotificationPrice;

    protected $declaredValuePrice;

    protected $homeDelivery;

    protected $priceWithouAdditional;

    protected $deliverySaturday;

    public function __construct(\stdClass $response)
    {
        $this->setCode($response->Codigo);
        $this->setName($response->Nome);
        $this->setError($response->Erro);
        $this->setMessageError($response->MsgErro);
        $this->setDeliveryTime($response->PrazoEntrega);
        $this->setHomeDelivery($response->EntregaDomiciliar);
        $this->setDeliverySaturday($response->EntregaSabado);
        $this->setPrice($response->Valor);
        $this->setOwnHandsPrice($response->ValorMaoPropria);
        $this->setDeliveryNotificationPrice($response->ValorAvisoRecebimento);
        $this->setDeclaredValuePrice($response->ValorValorDeclarado);
        $this->setPriceWithoutAdditional($response->ValorSemAdicionais);
    }

    private function setPriceWithoutAdditional($priceWithouAdditional)
    {
        $this->priceWithouAdditional = number_format((float) str_replace(',', '.', $priceWithouAdditional), 2);

        return $this;
    }

    private function setOwnHandsPrice($ownHandsPrice)
    {
        $this->ownHandsPrice = number_format((float) str_replace(',', '.', $ownHandsPrice), 2);

        return $this;
    }

    private function setDeliveryNotificationPrice($deliveryNotificationPrice)
    {
        $this->deliveryNotificationPrice = number_format((float) str_replace(',', '.', $deliveryNotificationPrice), 2);

        return $this;
    }

    private function setDeclaredValuePrice($declaredValuePrice)
    {
        $this->declaredValuePrice = number_format((float) str_replace(',', '.', $declaredValuePrice), 2);

        return $this;
    }

    private function setHomeDelivery($homeDelivery)
    {
        $this->homeDelivery = (boolean) ($homeDelivery);

        return $this;
    }

    private function setDeliverySaturday($deliverySaturday)
    {
        $this->deliverySaturday = (boolean) ($deliverySaturday);

        return $this;
    }

    public function gePriceWithoutAdditional()
    {
        return $this->priceWithouAdditional;
    }

    public function getOwnHandsPrice()
    {
        return $this->ownHandsPrice;
    }

    public function getDeliveryNotificationPrice()
    {
        return $this->deliveryNotificationPrice;
    }

    public function getDeclaredValuePrice()
    {
        return $this->declaredValuePrice;
    }

    public function getHomeDelivery()
    {
        return $this->homeDelivery;
    }

    public function getDeliverySaturday()
    {
        return $this->deliverySaturday;
    }
}
