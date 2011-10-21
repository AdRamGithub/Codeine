<?php

    /* Codeine
     * @author BreathLess
     * @description: Event Driver
     * @package Codeine
     * @version 6.0
     * @date 29.07.11
     * @time 22:13
     */

    self::Fn('Open', function ($Call)
    {
        return true;
    });

    self::Fn('Send', function ($Call)
    {
        // FIXME Options / Strategy
        $Map = &$Call['Events'];

        if (isset($Map[$Call['Message']]))
        {
            $Results = array();
            
            foreach ($Map[$Call['Message']] as $Name => $Hook)
                $Results[$Name] = F::Run($Call,$Hook);

            return $Results['Result'];
        }
        else
            return null;
    });
