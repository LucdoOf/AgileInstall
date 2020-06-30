<?php

use AgileCore\Models\Command;
use AgileCore\Utils\Dbg;

/**
 * @return bool
 */
function isDev() {
    return true;
}

function getIpAddress() {

    // Check for shared Internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && checkIpAddress($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // Check for IP addresses passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        // Check if multiple IP addresses exist in var
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (checkIpAddress($ip)) {
                    return $ip;
                }
            }
        } else {
            if (checkIpAddress($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && checkIpAddress($_SERVER['HTTP_X_FORWARDED'])) {
        return $_SERVER['HTTP_X_FORWARDED'];
    }
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && checkIpAddress($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && checkIpAddress($_SERVER['HTTP_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (!empty($_SERVER['HTTP_FORWARDED']) && checkIpAddress($_SERVER['HTTP_FORWARDED'])) {
        return $_SERVER['HTTP_FORWARDED'];
    }

    // Return unreliable IP address since all else failed
    if (isset($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }

    return null;
}

/**
 * Ensures an IP address is both a valid IP address and does not fall within
 * a private network range.
 *
 * @param string $ip
 * @param array $whitelist
 * @param array $blacklist
 * @param bool $allow_private
 * @return bool
 */
function checkIpAddress($ip, $whitelist = [], $blacklist = [], $allow_private = true) {

    if (strtolower($ip) === 'unknown') {
        return false;
    }

    if (!is_string($ip) || in_array($ip, $blacklist)) {
        return false;
    }

    if (in_array($ip, $whitelist)) {
        return true;
    }

    $filter_flag = FILTER_FLAG_NO_RES_RANGE;

    if (!$allow_private) {
        if (preg_match('/^127\.$/', $ip)) {
            return false;
        }
        $filter_flag |= FILTER_FLAG_NO_PRIV_RANGE;
    }

    if (!filter_var($ip, FILTER_VALIDATE_IP, $filter_flag)) {
        return false;
    }

    return true;
}

// Function to check string starting
// with given substring
function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

// Function to check the string is ends
// with given substring or not
function endsWith($string, $endString)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

function n_digit_random($digits) {
  return rand(pow(10, $digits - 1) - 1, pow(10, $digits) - 1);
}

function validateReference($string){
    return preg_match('/[A-Z]{3}_[1-9]{4}/', $string);
}

function filterStrictPositiveFloat($float){
    return floatval($float) > 0 ? floatval($float) : null;
}

function filterStrictPositiveInt($int){
    return intval($int) > 0 ? intval($int) : null;
}

function filterPositiveFloat($float){
    return floatval($float) >= 0 ? floatval($float) : null;
}

function filterDate($date){
    if($date instanceof DateTime){
        return $date;
    } else if(is_int($date)){
        try {
            return new DateTime($date);
        } catch (Exception $e) {
            Dbg::error($e->getMessage());
            return null;
        }
    } else if(is_string($date)){
        return createDateFromStandards($date);
    }
    return null;
}

function filterIp($ip){
    if(checkIpAddress($ip)){
        return $ip;
    }
    return null;
}

function filterCommandStatus($status){
    if(in_array($status, Command::STATUS)){
        return $status;
    }
    return null;
}

function filterString($string){
    if(!empty((string)$string)){
        return (string)$string;
    }
    return null;
}

function filterZipcode($string){
    if(preg_match("/^[0-9]{5}$/", $string)){
        return $string;
    }
    return null;
}

function filterCountry($string){
    if(preg_match('/^[A-Z]{2}$/',$string)){
        return $string;
    }
    return null;
}

function filterUrl($url){
    if(filter_var($url, FILTER_VALIDATE_URL) !== false){
        return $url;
    }
    return null;
}

function createDateFromStandards($date){
    $created = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if(!$created){
        $created = DateTime::createFromFormat('Y-m-d', $date);
    }
    return $created ? $created : null;
}

/**
 * @param $date DateTime
 */
function parseDate($date){
    return $date->format("Y-m-d H:i:s");
}

function parsePrice($price, $currency = 'EUR'){
    return round($price,2) . " " . ($currency == 'EUR' ? '€' : '');
}

function parsePercentage($percentage){
    return round($percentage, 2) . " %";
}

function public_url(){
    return getenv("PUBLIC_DOMAIN") . getenv('RELATIVE_DIR_PUBLIC');
}
