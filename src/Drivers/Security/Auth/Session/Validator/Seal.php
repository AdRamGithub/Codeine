<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 6.0
     */

    self::setFn ('Get', function ($Call)
        {
            return $Call['Auth']['Seal'] ==
                   F::Run ($Call, array(
                                       '_N' => 'Security.Auth.Seal.UserAgent', // OPTME,
                                       '_F' => 'Generate'
                                  ));
        });