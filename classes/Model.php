<?php
/*
 * Crazy4Customer
 * Copyright (C) 2013 ackintosh All Rights Reserved.
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * モデルクラス
 *
 * @package Crazy4Customer
 * @author ackintosh
 * @version $Id: $
 */
class Model
{
    private static $_settings = false;

    /**
     * プラグイン情報を取得
     * @param string $key
     * @return string
     */
    public static function getSetting($key)
    {
        if (self::$_settings === false) self::initSettings();
        return self::$_settings[$key];
    }

    public static function initSettings()
    {
        self::$_settings = SC_Plugin_Util_Ex::getPluginByPluginCode("Crazy4Customer");
        self::$_settings['search_cond'] = unserialize(self::$_settings['free_field1']);
    }

    /**
     * 会員検索条件をfree_field1に保存
     * @param array $data
     * @return boolean
     */
    public static function save(SC_Query_Ex $objQuery, Array $data)
    {
        $search_cond = self::filtering($data);
        $sqlval['free_field1'] = serialize($search_cond);
        $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
        $where = "plugin_code = ?";
        
        return $objQuery->update('dtb_plugin', $sqlval, $where, array(self::getSetting('plugin_code')));
    }

    /**
     * 検索条件を抽出
     * @param array $post
     * @return array $result    抽出した検索条件
     */
    public static function filtering($data)
    {
        $search_cond = array(
                'search_customer_id', 
                'search_name', 
                'search_kana', 
                'search_pref', 
                'search_b_start_year', 
                'search_b_start_month', 
                'search_b_start_day', 
                'search_b_end_year', 
                'search_b_end_month', 
                'search_b_end_day', 
                'search_birth_month', 
                'search_email', 
                'search_email_mobile', 
                'search_tel', 
                'search_buy_total_from', 
                'search_buy_total_to', 
                'search_buy_times_from', 
                'search_buy_times_to', 
                'search_start_year', 
                'search_start_month', 
                'search_start_day', 
                'search_end_year', 
                'search_end_month', 
                'search_end_day', 
                'search_buy_start_year', 
                'search_buy_start_month', 
                'search_buy_start_day', 
                'search_buy_end_year', 
                'search_buy_end_month', 
                'search_buy_end_day', 
                'search_buy_product_code', 
                'search_buy_product_name', 
                'search_category_id', 
                'search_sex', 
                'search_status', 
                'search_job', 
            );
        $result = array();
        foreach ($data as $k => $v) {
            if (in_array($k, $search_cond)) $result[$k] = $v;
        }

        return $result;
    }

    /**
     * 対象の会員か判定
     * @param   SC_Customer_Ex $objCustomer
     * @return  boolean
     */
    public static function isTarget($objCustomer)
    {
        // session 設定済みの場合
        if ($_SESSION['crazy4customer'] !== null) {
            return $_SESSION['crazy4customer'];
        } else {
            // session 設定
            if ($objCustomer->isLoginSuccess(true) === false) {
                $_SESSION['crazy4customer'] = false;
                return $_SESSION['crazy4customer'];
            } else {
                $_SESSION['crazy4customer'] = self::isTargetCustomer($objCustomer->getvalue('customer_id'));
                return $_SESSION['crazy4customer'];
            }
        }
    }

    /**
     * 対象の会員か判定
     * @param   int     $customer_id 会員ID
     * @return  boolean 
     */
    public static function isTargetCustomer($customer_id)
    {
        // 検索条件取得
        $cond = unserialize(self::getSetting('free_field1'));
        if (empty($cond)) return false;

        // 条件の会員IDと異なればfalse
        if (!empty($cond['search_customer_id']) && $cond['search_customer_id'] != $customer_id) return false;
        $cond['search_customer_id'] = $customer_id;

        list($tpl_linemax, $arrData, $objNavi) = SC_Helper_Customer_Ex::sfGetSearchData($cond);
        return !empty($arrData);
    }

    /**
     * 保存した条件で検索する用のHTMLを返す
     */
    public static function getHTML($source, $objPage)
    {
        $search_cond = unserialize(self::getSetting('free_field1'));
        if (empty($search_cond)) return $source;

        $html = <<<EOS
<form name="c4c" id="c4c" method="post" action="?">
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="edit_customer_id" value="" />
EOS;
        foreach ($search_cond as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $vv) {
                    $html .= sprintf('<input type="hidden" name="%s[]" value="%s" />', $k, $vv);
                }
            } else {
                $html .= sprintf('<input type="hidden" name="%s" value="%s" />', $k, $v);
            }
        }
        $html .= sprintf('<input type="hidden" name="%s" value="%s" />', TRANSACTION_ID_NAME, $objPage->transactionid);
        $html .= '</form>';

        $html .= <<<EOS
<script type="text/javascript">
function fnC4Csearch() {
    document.c4c.submit();
}
</script>
EOS;

        $source = str_replace('<div id="customer"', $html . '<div id="customer"', $source);

        $search_btn = '<a class="btn-normal" href="javascript:;" onclick="fnC4Csearch(); return false;">保存した条件で検索</a>';
        $source = str_replace('<!--c4c_search-->', $search_btn, $source);
        return $source;
    }
}
