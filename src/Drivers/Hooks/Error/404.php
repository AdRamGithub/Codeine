<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 6.0
     */

    self::setFn ('Show', function ($Call)
        {
            header ("HTTP/1.0 404 Not Found");

            $Call['Widgets'] = array(array(
                                         'Place'   => 'Content',
                                         'Type'    => 'Heading',
                                         'Level'   => 3,
                                         'Value'   => '404',
                                         'Subtext' => '<l>' . $Call['_N'] . '.Subtext</l>'
                                     ));

            return $Call;
        });