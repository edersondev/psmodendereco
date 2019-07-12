<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Setar esse formato ao instalar o módulo
// ps_address_format
// id_contry=58

// firstname lastname
// company
// postcode
// address1
// vat_number
// address2
// other
// city
// State:name
// phone

$sql = array();
$db_prefix = _DB_PREFIX_;
$db_engine = _MYSQL_ENGINE_;
$format = 'firstname lastname\ncompany\npostcode\naddress1\nvat_number\naddress2\nother\ncity\nState:name\nphone';
$sql[] = "UPDATE `{$db_prefix}address_format` SET `format`='{$format}' WHERE `id_country`=58";

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
