<?php

    /* Codeine
     * @author BreathLess
     * @description: Base converter
     * @package Codeine
     * @version 6.0
     * @date 31.08.11
     * @time 3:41
     */

    self::Fn('Convert', function ($Call)
    {
        return F::Run(array(
                    '_N'  => 'Engine.Message',
                    '_F'  => 'Send',
                    'To' => 'Event',
                    'Message' => 'Call to unrealized function',
                    'Call' => $Call
                       ));
    });
