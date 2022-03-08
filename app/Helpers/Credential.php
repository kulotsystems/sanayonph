<?php

namespace App\Helpers;

class Credential
{

    /****************************************************************************************************
     * Determine if credential is username, email, or mobile
     *
     * @param $credential
     * @return string
     */
    public static function field($credential)
    {
        if(is_numeric($credential))
            return 'mobile';
        elseif (filter_var($credential, FILTER_VALIDATE_EMAIL))
            return 'email';
        return 'username';
    }


    /****************************************************************************************************
     * Partially show credential
     * https://stackoverflow.com/questions/20545301/partially-hide-email-address-in-php
     *
     * @param $credential
     * @param $field
     * @return string
     */
    public static function mask($credential, $field)
    {
        if(strlen(''.$credential) > 0) {
            if ($field == 'email') {
                $em = explode("@", $credential);
                $name = implode('@', array_slice($em, 0, count($em) - 1));
                $len = floor(strlen($name) / 2);

                $credential = substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
            }
            else if($field == 'mobile') {
                $credential = substr($credential, 0, 4) . '*****' . substr($credential, -2);
            }
        }
        return $credential;
    }

}
