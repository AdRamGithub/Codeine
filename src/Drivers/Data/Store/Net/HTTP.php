<?php

    /* OSWA Codeine
     * @author BreathLess
     * @type Codeine Driver
     * @description: HTTP Store Driver
     * @package Codeine
     * @subpackage Drivers
     * @version 5.0
     * @date 24.11.10
     * @time 21:39
     */

    self::setFn('Open', function ($Call)
    {
        return curl_init();
    });

    self::setFn('Disconnect', function ($Call)
    {
        curl_close($Call['Link']);
    });

    self::setFn('Load', function ($Call)
    {
        curl_setopt_array($Call['Link'],
          array(
               CURLOPT_HEADER => false,
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_COOKIEJAR => 'cookie.txt',
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_CONNECTTIMEOUT => 15,
               CURLOPT_URL => $Call['ID']));
        
        return curl_exec($Call['Link']);
    });

    self::setFn('Create', function ($Call)
    {

        $Headers = isset($Call['Headers'])? $Call['Headers']: array();
        $UPWD = isset($Call['user:pass'])? $Call['user:pass']:'';

        $Call['Link'] = curl_init();
        curl_setopt_array($Call['Link'],
            array(
                CURLOPT_URL => $Call['ID'],
                CURLOPT_POST => true,
                CURLOPT_COOKIEJAR => 'cookie.txt',
                CURLOPT_HTTPHEADER => $Headers,
                CURLOPT_USERPWD => $UPWD,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $Call['Value']));

        return curl_exec($Call['Link']);
    });

    self::setFn('Update', function ($Call)
    {
        // TODO HTTP Update
    });

    self::setFn('Delete', function ($Call)
    {
        // TODO HTTP Delete
    });
