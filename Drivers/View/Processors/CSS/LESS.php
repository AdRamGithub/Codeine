<?php

    /* Codeine
     * @author BreathLess
     * @description: LESS Port
     * @package Codeine
     * @version 6.0
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
