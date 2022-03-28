<?php

use \Bitrix\Main\Loader;
use \Bitrix\Sale;

class BasketProducts extends CBitrixComponent
{
    /**
     * Подготовка параметров компонента
     * @param $params
     * @return mixed
     */
    public function onPrepareComponentParams($params)
    {
        return $_REQUEST;
    }

    /**
     * Точка входа в компонент
     */
    public function executeComponent()
    {

    }

    /**
     * добавление товара
     * @param $request
     */
    public static function addProd($request)
    {
        $quantity = 1; // по умолчанию 1 товар
        $basket = self::getBasket();
        if ($item = $basket->getExistsItem('catalog', $request['id'])) {  // увеличение количества одного вида товара, если уже в корзине
            $item->setField('QUANTITY', $item->getQuantity() + $quantity);
        }
        else {
            $item = $basket->createItem('catalog', $request['id']); //  добавляем товар в корзину
            $item->setFields(array(
                'QUANTITY' => $quantity,
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            ));
        }
        $basket->save();
    }

    /**
     * удаление товара
     * @param $request
     */
    public static function deleteProd($request)
    {
        $basket = self::getBasket();
        if($item = $basket->getExistsItem('catalog', $request['id'])) {
            $item->delete();
    }
        $basket->save();
    }

    /**
     * изменение товара
     * @param $request
     */
    public static function updateProd($request)
    {
        $quantity = 1;
        $basket = self::getBasket();
        if($item = $basket->getExistsItem('catalog', $request['id'])) {     //пример уменьшения количества одного вида товара
            if($item->getQuantity() > 1) {
                $item->setField('QUANTITY', $item->getQuantity() - $quantity);
            }
            else{                   //удаляем товар из корзины
                self::deleteProd($request);
            }
        }
        if($request['update_params']){ // допустим есть некий массив с изменением свойств товара вида ["PROPERTY1" => "VALUE1"]
            if($item = $basket->getExistsItem('catalog', $request['id'])) {     //пример уменьшения количества одного вида товара
                $item->setFields($request["update_params"]);
            }
    }
        $basket->save();
    }

    /**
     * получаем корзину
     */
    public  static function getBasket(){
        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        return $basket;
    }

    /**
     * получаем некие свойства элементов в корзине
     * @param $request
     */
    public static function readProd($request){
        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        $item = $basket->getExistsItem('catalog', $request['id']);
        return $item->getPropertyCollection();
    }
}