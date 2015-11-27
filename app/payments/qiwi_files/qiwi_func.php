<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

// rus_build_pack dbazhenov

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function cancelBill($txn_id, &$service)
{
    $params = new cancelBill();
    $params->login = LOGIN;
    $params->password = PASSWORD;
    $params->txn = $txn_id;

    $res = $service->cancelBill($params);

    print($res->cancelBillResult);
}

function createBill($data, &$service)
{
    $params = new createBill();
    $params->login = $data['login'];
    $params->password = $data['password'];
    $params->user = $data['phone'];
    $params->amount = $data['amount'];
    $params->comment = $data['comment'];
    $params->txn = $data['txn_id'];
    $params->lifetime = $data['lifetime'];

    $params->alarm = $data['alarm'];

    $params->create = $data['create'];

    $res = $service->createBill($params);
    $rc = $res->createBillResult;

    return $rc;
}
