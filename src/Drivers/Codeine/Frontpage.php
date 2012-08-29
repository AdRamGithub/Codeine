<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    self::setFn ('Do', function ($Call)
    {
        $Call['Output']['Content'] =
            [
                [
                    'Type' => 'Heading',
                    'Level' => 1,
                    'Value' => 'Codeine '.$Call['Version']['Codeine']['Major'].' works!'
                ],
                [
                    'Type'  => 'Heading',
                    'Level' => 2,
                    'Value' => $_SERVER['HTTP_HOST']
                ]
            ];

        return $Call;
    });