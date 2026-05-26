<?php

namespace app\helpers;


class ManagerHelpers
{

    public static function createFiles($public_key){
        if(!file_exists(__DIR__ . '/../web/custom/custom_'.$public_key.'.css')){
            $custom_css_default = '';
            $custom_css = __DIR__ . '/../web/custom/custom_'.$public_key.'.css';
            file_put_contents($custom_css,$custom_css_default);
        }
        if(!file_exists(__DIR__ . '/../web/custom/logo' . $public_key . '.svg')) {
            $logo_svg_default = '<svg width="38" height="38" viewBox="0 0 38 24" fill="#3d8af5" xmlns="http://www.w3.org/2000/svg"><path d="M15.7171 15.4784L14.1244 14.6367L22.5398 0L24.1022 0.871922L15.7171 15.4784Z"></path><path d="M11.5063 3.48226V1.66556L0 7.23656L11.5063 13.1106V11.3544L3.67137 7.29702L11.5063 3.48226Z"></path><path d="M26.4945 3.48223V1.66553L38 7.23653L28.4112 12.1414V19.4473C25.4823 23.0427 19.2231 24.0681 14.3907 22.5266L15.3548 20.847V20.8455C19.1386 21.9701 23.723 21.2687 26.5069 18.7358L26.5076 13.1151L24.0725 14.3607V12.6045L34.3286 7.29698L26.4945 3.48223Z"></path></svg>';
            $logo_svg = __DIR__ . '/../web/custom/logo' . $public_key . '.svg';
            file_put_contents($logo_svg, $logo_svg_default);
        }
    }
    public static function getLogoSvgCode($public_key): false|string
    {
        return file_get_contents(__DIR__ . '/../web/custom/logo'.$public_key.'.svg', true);
    }
    public static function getCustomCssCode($public_key): false|string
    {
        return file_get_contents(__DIR__ . '/../web/custom/custom_'.$public_key.'.css', true);
    }
    public static function rolesTreeSel($roles)
    {
        if (is_array($roles)) {
            $tree = '';
            foreach ($roles as $k => $v) {
                $tree .= '<option value="' . $k . '">' . $v . '</option>';
            }
        }
        else return null;
        return $tree;
    }
    public static function getRores($arr)
    {
        $arrroles = [];
        $strroles = '';
        foreach ($arr as $k => $v) {
            $arrroles[] = $v->name;
        }
        if ($arrroles) {
            $strroles = implode(', ', $arrroles);
        }
        else {
            $strroles = 'Нет ролей';
        }
        return $strroles;
    }

    public static function getLinks($arr)
    {
        $arrlinks = [];
        $strlinks = '';
        foreach ($arr as $k => $v) {
            $arrlinks[] = $v->uri;
        }
        if ($arrlinks) {
            $strlinks = implode(',', $arrlinks);
        }
        else {
            $strlinks = 'Нет привязок';
        }
        return $strlinks;
    }

    public static function debug($arr,$exit=false)
    {

        echo '<pre>' . print_r($arr, true) . '</pre>';
        if($exit) {
            exit();
        }
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}