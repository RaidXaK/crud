<?
use Bitrix\Main\Application;
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CBitrixComponent::includeComponentClass("prod:BasketProducts");
$request = Application::getInstance()->getContext()->getRequest();

switch ($request['query']) {
    case 'add':
        BasketProducts::addProd($request);
        break;
    case 'delete':
        BasketProducts::deleteProd($request);
        break;
    case 'update':
        BasketProducts::updateProd($request);
        break;
    case 'getinfo':
        BasketProducts::readProd($request);
        break;
}