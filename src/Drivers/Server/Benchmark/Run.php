<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Test', function ($Call)
    {
        $Start = microtime(true);

        for ($a = 0; $a<$Call['Cycles']; $a++)
            F::Run('Dummy', 'Do', $Call);

        $Stop = microtime(true);

        return $Call['Cycles']/($Stop-$Start);
    });