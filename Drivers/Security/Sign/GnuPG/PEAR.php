<?php

    /* Codeine
     * @author BreathLess
     * @description: 
     * @package Codeine
     * @version 6.0
     * @date 31.08.11
     * @time 5:14
     */

    self::Fn('Do', function ($Call)
    {
        return F::Run(array(
                    '_N'  => 'Engine.Message',
                    '_F'  => 'Send',
                    'To' => 'Event',
                    'Message' => 'Call to unrealized function',
                    'Call' => $Call
                       ));
    });
