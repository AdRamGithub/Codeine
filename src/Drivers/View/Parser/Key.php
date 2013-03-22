<?php

    /* Codeine
     * @author BreathLess
     * @description <k> tag 
     * @package Codeine
     * @version 7.x
     */

    setFn('Parse', function ($Call)
    {
        if (preg_match_all('@<k>(.*)</k>@SsUu', $Call['Value'], $Pockets))
        {
            $Pockets[1] = array_unique($Pockets[1]);

            foreach ($Pockets[1] as $IX => $Match)
            {
                if (($Matched = F::Live(F::Dot($Call['Data'], $Match))) !== null)
                {
                    if ((array) $Matched === $Matched)
                        $Matched = array_shift($Matched);

                    if (($Matched === false) or ($Matched === 0) )
                        $Matched = '0';

                    $Call['Value'] = str_replace($Pockets[0][$IX], $Matched, $Call['Value']);
                }
                else
                    $Call['Value'] = str_replace($Pockets[0][$IX], '', $Call['Value']);
            }
        }

        return $Call['Value'];
    });