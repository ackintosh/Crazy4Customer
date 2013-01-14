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
require_once PLUGIN_UPLOAD_REALDIR . 'Crazy4Customer/classes/Model.php';

/* 
 * 会員属性を指定して表示変更
 */
class Crazy4Customer extends SC_Plugin_Base {

    private static $_template_dir;

    /**
     * コンストラクタ
     * プラグイン情報(dtb_plugin)をメンバ変数をセットします.
     * @param array $arrSelfInfo dtb_pluginの情報配列
     * @return void
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
        self::$_template_dir = PLUGIN_UPLOAD_REALDIR . $this->arrSelfInfo['plugin_code'] . '/templates/';
    }

    /**
     * インストール時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function install($arrPlugin) {
    	// ロゴファイルをhtmlディレクトリにコピーします.
        copy(PLUGIN_UPLOAD_REALDIR . $arrPlugin['plugin_code'] . "/logo.png", PLUGIN_HTML_REALDIR . $arrPlugin['plugin_code'] . "/logo.png");
    }

    /**
     * 削除時に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function uninstall($arrPlugin) {
        
    }
    
    /**
     * 有効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function enable($arrPlugin) {

    }

    /**
     * 無効にした際に実行される処理を記述します.
     * @param array $arrPlugin dtb_pluginの情報配列
     * @return void
     */
    function disable($arrPlugin) {

    }

    /**
     * prefilterコールバック関数
     * テンプレートの変更処理を行います.
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        // SC_Helper_Transformのインスタンスを生成.
        $objTransform = new SC_Helper_Transform($source);
        // 呼び出し元テンプレートを判定します.
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE: // モバイル
            case DEVICE_TYPE_SMARTPHONE: // スマホ
            case DEVICE_TYPE_PC: // PC
                break;
            case DEVICE_TYPE_ADMIN: // 管理画面
            default:
                // テンプレートを変更
                if (strpos($filename, 'customer/index.tpl') !== false) {
                    $objTransform->select('div.btn .btn-normal')->insertBefore(file_get_contents(self::$_template_dir . 'crazy4customer_admin_cutomer_index.tpl'));
                }
                break;
        }

        // 変更を実行します
        $source = $objTransform->getHTML();
    }

    /**
     * outputfilterコールバック関数
     * テンプレートの変更処理を行います.
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @return void
     */
    function outputfilterTransform(&$source, LC_Page_Ex $objPage) {
        if ($objPage->tpl_mainpage === 'customer/index.tpl') {
            $source = Model::getHTML($source, $objPage);
        }
    }

    /**
     * 会員管理ページのコールバック関数
     */
    public function hookAdminCustomer(LC_Page_Ex $objPage) {
        if ($_POST['mode'] === 'crazy4cutomer') Model::save(SC_Query_Ex::getSingletonInstance(), $_POST);
    }

    public function preProcess(LC_Page_Ex $objPage) {
        // 機種判別
        $objPage->device_type_id = SC_Display_Ex::detectDevice();
    }

    /**
     * 全ページに介入
     */
    public function process(LC_Page_Ex $objPage) {
        $objCustomer = new SC_Customer_Ex();
        $objPage->c4c = Model::isTarget($objCustomer);
    }

    /**
     * ログイン処理のコールバック関数
     */
    public function hookLogin(LC_Page_Ex $objPage) {
        // 再判定させる為に初期化
        $_SESSION['crazy4customer'] = null;
    }
}
