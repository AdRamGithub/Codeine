<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    self::setFn('Test', function ($Call)
    {
        $Cycles = 150000;

        $Array = [];

        for ($a = 1; $a<24; $a++)
            $Array[$a] = $a*$a;

        $Start = microtime(true);
            for ($a = 1; $a <$Cycles; $a++)
                foreach ($Array as $Key => $Value)
                    {
                        $Value; $Key;
                    }

        $Stop = microtime(true);
        return round(1000/($Stop-$Start));
    });