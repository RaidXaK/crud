<?php
//запуск каждый день в 6 утра
//0 6 * * * $HOME/bin/daily - если стандартный рабочий день, если есть возможность контролировать заказы круглосуточно,
// то можно отправлять чаще, например 0 */4 * * *

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

//при формировании заказов в инфоблоке
$id_array = array();
$now = new DateTime();

$arOrder = array ("ID" => "ASC");
$arFilter = array(
    "status" => "new",
    "<=DATE_CREATE" => $now->modify('-2 day')->format('d.m.Y H:i:s'),
);
$arSelect = array("ID");
$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement()){
    $arFields = $ob->GetFields();
    $id_array[] =$arFields["ID"];
}

if(!empty($id_array)){
    \Bitrix\Main\Mail\Event::send([
        "EVENT_NAME" => "ORDERS_WITH_NEW_STATUS",
        'MESSAGE_ID' => 1,
        "LID" => "s1",
        "C_FIELDS" => [
            'ID' => $id_array,
        ]
    ]);
}

//при формировании заказов в интернет-магазине
\Bitrix\Main\Loader::includeModule('sale');

$now = new DateTime();
$id_array = array();

$arSelect = array("ID");
$arFilter = array(
    "<=DATE_INSERT" => $now->modify('-2 day')->format('d.m.Y H:i:s'),
    "STATUS_ID" => "DN",
);
$arOrder = array("ID" => "ASC");
$dbRes = \Bitrix\Sale\Order::getList($arSelect, $arFilter, $arOrder);
while ($order = $dbRes->fetch()){
    $id_array[]  = $order["ID"];
}

if(!empty($id_array)){
    \Bitrix\Main\Mail\Event::send([
        "EVENT_NAME" => "ORDERS_WITH_NEW_STATUS",
        'MESSAGE_ID' => 1,
        "LID" => "s1",
        "C_FIELDS" => [
            'ID' => $id_array,
        ]
    ]);
}