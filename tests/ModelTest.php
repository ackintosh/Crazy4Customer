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
 * プラグイン のテストクラス.
 *
 * @package Crazy4Customer
 * @author ackintosh
 * @version $Id: $
 */
require 'phpunit_bootstrap.php';
require '../classes/Model.php';

class ModelTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    public function testGetSettings()
    {
        $this->assertEquals(array(), Model::getSetting(('search_cond')));
    }

    public function testSave()
    {
        $search_cond = array(
            'search_customer_id' => 1234, 
            'search_name' => 'テスト会員', 
            );

        $scQuery = $this->getMock('SC_Query_Ex', array('update'));
        $scQuery->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo('dtb_plugin'), 
                $this->equalTo(
                    array(
                    'free_field1' => serialize($search_cond),
                    'update_date' => 'CURRENT_TIMESTAMP',)
                    ), 
                $this->equalTo('plugin_code = ?'),
                $this->equalTo(array('Crazy4Customer'))
                );
        Model::save($scQuery, $search_cond);
    }

    public function testFiltering()
    {
        $expected_array = array(
                'search_customer_id' => 1, 
                'search_name' => "試験", 
                'search_kana' => "テスト", 
                'search_pref' => 2, 
                'search_b_start_year' => 2013, 
                'search_b_start_month' => 10, 
                'search_b_start_day' => 30, 
                'search_b_end_year' => 2014, 
                'search_b_end_month' => 12, 
                'search_b_end_day' => 20, 
                'search_birth_month' => 8, 
                'search_email' => 'test@example.com', 
                'search_email_mobile' => 'test@example.ne.jp', 
                'search_tel' => '00-0000-0000', 
                'search_buy_total_from' => 10000, 
                'search_buy_total_to' => 50000, 
                'search_buy_times_from' => 5, 
                'search_buy_times_to' => 10, 
                'search_start_year' => 2000, 
                'search_start_month' => 3, 
                'search_start_day' => 10, 
                'search_end_year' => 2010, 
                'search_end_month' => 7, 
                'search_end_day' => 15, 
                'search_buy_start_year' => 2008, 
                'search_buy_start_month' => 2, 
                'search_buy_start_day' => 12, 
                'search_buy_end_year' => 2004, 
                'search_buy_end_month' => 4, 
                'search_buy_end_day' => 14, 
                'search_buy_product_code' => 'testcode', 
                'search_buy_product_name' => 'テスト商品', 
                'search_category_id' => 20, 
                'search_sex' => 1, 
                'search_status' => 2, 
                'search_job' => 3, 
            );

        $test_array = array(
                'search_customer_id' => 1, 
                'search_name' => "試験", 
                'search_kana' => "テスト", 
                'search_pref' => 2, 
                'search_b_start_year' => 2013, 
                'search_b_start_month' => 10, 
                'search_b_start_day' => 30, 
                'search_b_end_year' => 2014, 
                'search_b_end_month' => 12, 
                'search_b_end_day' => 20, 
                'search_birth_month' => 8, 
                'search_email' => 'test@example.com', 
                'search_email_mobile' => 'test@example.ne.jp', 
                'search_tel' => '00-0000-0000', 
                'search_buy_total_from' => 10000, 
                'search_buy_total_to' => 50000, 
                'search_buy_times_from' => 5, 
                'search_buy_times_to' => 10, 
                'search_start_year' => 2000, 
                'search_start_month' => 3, 
                'search_start_day' => 10, 
                'search_end_year' => 2010, 
                'search_end_month' => 7, 
                'search_end_day' => 15, 
                'search_buy_start_year' => 2008, 
                'search_buy_start_month' => 2, 
                'search_buy_start_day' => 12, 
                'search_buy_end_year' => 2004, 
                'search_buy_end_month' => 4, 
                'search_buy_end_day' => 14, 
                'search_buy_product_code' => 'testcode', 
                'search_buy_product_name' => 'テスト商品', 
                'search_category_id' => 20, 
                'search_sex' => 1, 
                'search_status' => 2, 
                'search_job' => 3, 
                /* must be filtered. from here. */
                'search_customer_id_test' => 1, 
                'search_name_test' => "試験", 
                'search_kana_test' => "テスト", 
                'search_pref_test' => 2, 
                'search_b_start_year_test' => 2013, 
                'search_b_start_month_test' => 10, 
                'search_b_start_day_test' => 30, 
                'search_b_end_year_test' => 2014, 
                'search_b_end_month_test' => 12, 
                'search_b_end_day_test' => 20, 
                'search_birth_month_test' => 8, 
                'search_email_test' => 'test@example.com', 
                'search_email_mobile_test' => 'test@example.ne.jp', 
                'search_tel_test' => '00-0000-0000', 
                'search_buy_total_from_test' => 10000, 
                'search_buy_total_to_test' => 50000, 
                'search_buy_times_from_test' => 5, 
                'search_buy_times_to_test' => 10, 
                'search_start_year_test' => 2000, 
                'search_start_month_test' => 3, 
                'search_start_day_test' => 10, 
                'search_end_year_test' => 2010, 
                'search_end_month_test' => 7, 
                'search_end_day_test' => 15, 
                'search_buy_start_year_test' => 2008, 
                'search_buy_start_month_test' => 2, 
                'search_buy_start_day_test' => 12, 
                'search_buy_end_year_test' => 2004, 
                'search_buy_end_month_test' => 4, 
                'search_buy_end_day_test' => 14, 
                'search_buy_product_code_test' => 'testcode', 
                'search_buy_product_name_test' => 'テスト商品', 
                'search_category_id_test' => 20, 
                'search_sex_test' => 1, 
                'search_status_test' => 2, 
                'search_job_test' => 3, 
                /* must be filtered. so far. */
            );

        $this->assertEquals($expected_array, Model::filtering($test_array));
    }
}
