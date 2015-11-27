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

// rus_build_install

namespace RusPostBlank;

class RusPostBlank
{
    public $def = array ( 
        'form' => array('1' => 0, '2' => 1, '1f' => 0, '2f' => 1, '3' => 1, '4' => 1), 
     
        'words' => array(
            '0' => array('', 'десять', '', ''), 
            '1' => array('один', 'одиннадцать', '', 'сто'), 
            '2' => array('два', 'двенадцать', 'двадцать', 'двести'), 
            '1f' => array('одна', '', '', ''), 
            '2f' => array('две', '', '', ''), 
            '3' => array('три', 'тринадцать', 'тридцать', 'триста'), 
            '4' => array('четыре', 'четырнадцать', 'сорок', 'четыреста'), 
            '5' => array('пять', 'пятнадцать', 'пятьдесят', 'пятьсот'), 
            '6' => array('шесть', 'шестнадцать', 'шестьдесят', 'шестьсот'), 
            '7' => array('семь', 'семнадцать', 'семьдесят', 'семьсот'), 
            '8' => array('восемь', 'восемнадцать', 'восемьдесят', 'восемьсот'), 
            '9' => array('девять', 'девятнадцать', 'девяносто', 'девятьсот') 
        ) 
    );

    public static function doit($str, $show_kop = true, $show_rank = true, $rank_only = false) { 
        $num2rub = new RusPostBlank();
        $rank = array(
            0 => '',
            1 => _("shipping.russianpost.thousand_rank"),
            2 => _("shipping.russianpost.million_rank"),
            3 => _("shipping.russianpost.billion_rank"),
            'k' => _("shipping.russianpost.kopeck_rank"),
        );

        $str = number_format($str, 2, '.', ',');
        $rubkop = explode('.', $str);
        $rub = $rubkop[0];
        $kop = (isset($rubkop[1])) ? $rubkop[1] : '00';
        $rub = (strlen($rub) == 1) ? '0' . $rub : $rub;
        $rub = explode(',', $rub);
        $rub = array_reverse($rub);

        $word = array();
        $word[] = __("shipping.russianpost.kopeck_rank", array($kop));

        foreach ($rub as $key => $value) {
            if (intval($value) > 0 || $key == 0) {
                $str_rub = $num2rub->clearDvig($value, $key);

                $str_rank = __($rank[$key], array($value));
                $str_rank = str_replace($value, "", $str_rank);
                $word[] = $str_rub . $str_rank;
            }
        }
        if (!$show_rank) {
            unset($word[0]);
        }

        if($rank_only) {
            $word = array($word[0]);
        }

        $word = array_reverse($word);
        return ucfirst(trim(implode(' ', $word)));
    }

    public function dvig($str, $key, $do_word = true, $show_rank = true) { 
        $def = &$this->def;
        $words = $def['words'];
        $form = $def['form'];

        $sotni = '';
        $num_word = '';
        $rank = '';

        $str = (strlen($str) == 1) ? '0' . $str : $str;
        $dig = str_split($str);
        $dig = array_reverse($dig);

        if (1 == $dig[1]) {
            $num_word = ($do_word) ? $words[$dig[0]][1] : $dig[1] . $dig[0];
        } else {
            if ($key == 1) {
                $rank = 'f';
            }
            if ($dig[0] != 1 && $dig[0] != 2) {
                $rank = '';
            }
            $num_word = ($do_word) ? $words[$dig[1]][2] . ' ' . $words[$dig[0] . $rank][0] : $dig[1] . $dig[0];
            $key = (isset($form[$dig[0]])) ? $form[$dig[0]] : false;
        }

        $sotni = (isset($dig[2])) ? (($do_word) ? $words[$dig[2]][3] : $dig[2]) : '';
        if ($sotni && $do_word)
        {
            $sotni .= ' ';
        }

        return array($sotni . $num_word);
    }

    public static function clearDoit($str) { 
        $num2rub = new RusPostBlank();
        $rank = array(
            0 => _("shipping.russianpost.rub_rank"),
            1 => _("shipping.russianpost.thousand_rank"),
            2 => _("shipping.russianpost.million_rank"),
            3 => _("shipping.russianpost.billion_rank"),
            'k' => _("shipping.russianpost.kopeck_rank"),
        );

        $str = number_format($str, 2, '.', ',');
        $rubkop = explode('.', $str);
        $rub = $rubkop[0];
        $kop = (isset($rubkop[1])) ? $rubkop[1] : '00';
        $rub = (strlen($rub) == 1) ? '0' . $rub : $rub;
        $rub = explode(',', $rub);
        $rub = array_reverse($rub);

        $word = array();
        $word[] = __("shipping.russianpost.kopeck_rank", array($kop));

        foreach($rub as $key => $value) {
            if (intval($value) > 0 || $key == 0) {
                $str_rub = $num2rub->clearDvig($value, $key);
                $str_rank = __($rank[$key], array($value));
                $str_rank = str_replace($value, "", $str_rank);
                $word[] = $str_rub . $str_rank;
            }
        }

        $word = array_reverse($word);

        return $num2rub->mbUcfirst(trim(implode(' ', $word)));
    }

    public function clearDvig($str, $key, $do_word = true) {
        $def =& $this->def;
        $words = $def['words'];
        $form = $def['form'];

        $sotni = '';
        $num_word = '';
        $rank = '';

        $str = (strlen($str) == 1) ? '0' . $str : $str;
        $dig = str_split($str);
        $dig = array_reverse($dig);

        if (1 == $dig[1]) {
            $num_word = ($do_word) ? $words[$dig[0]][1] : $dig[1] . $dig[0];
        } else {
            if ($key == 1) {
                $rank = 'f';
            }
            if ($dig[0] != 1 && $dig[0] != 2) {
                $rank = '';
            }
            $num_word = ($do_word) ? $words[$dig[1]][2] . ' ' . $words[$dig[0] . $rank][0] : $dig[1] . $dig[0];
            $key = (isset($form[$dig[0]])) ? $form[$dig[0]] : false;
        }

        $sotni = (isset($dig[2])) ? (($do_word) ? $words[$dig[2]][3] : $dig[2]) : '';
        if ($sotni && $do_word) {
            $sotni .= ' ';
        }

        return $sotni . $num_word;
    }

    public function mbUcfirst($str, $encoding='UTF-8')
    {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
               mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
}
