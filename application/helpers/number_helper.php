<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

function format_currency($amount)
{
    global $CI;
    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
    }
}

function format_amount($amount = NULL)
{
    if ($amount!==NULL) {
        $CI =& get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    return NULL;
}

function standardize_amount($amount)
{
    $CI =& get_instance();
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}

function get_tax_amount($amount, $percent){
    $CI =& get_instance();
    $tax_part = $percent / 100;

    if ($CI->mdl_settings->setting('tax_excluded')){
        $tax_amount = $amount / (1 - $tax_part) - $amount;
    } else {
        $tax_amount = $amount * $tax_part;
    }

    return $tax_amount;
}

function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}