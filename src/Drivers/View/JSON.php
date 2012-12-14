<?php

   /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn ('Render', function ($Call)
    {
       $Call['Headers']['Content-type:'] = 'text/json';
       $Call['Output'] = json_encode($Call['Output'], JSON_UNESCAPED_UNICODE);

       return $Call;
    });