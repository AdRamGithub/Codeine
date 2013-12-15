<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Length', function ($Call)
    {
        $Length = mb_strlen($Call['View']['HTML']['Keywords']);

        if ($Length > $Call['SEO']['Audit']['Keywords']['Length']['Maximum'])
        {
            $Call = F::Hook('SEO.Audit.Keywords.TooLong', $Call);
            F::Log('SEO Keywords is too long *'.$Length.' chars* over *'
                .$Call['SEO']['Audit']['Keywords']['Length']['Maximum'].'*', LOG_WARNING, 'SEO');
        }
        elseif ($Length < $Call['SEO']['Audit']['Keywords']['Length']['Minimum'])
        {
            $Call = F::Hook('SEO.Audit.Keywords.TooShort', $Call);
            F::Log('SEO Keywords is too short *'.$Length.' chars* over *'
                .$Call['SEO']['Audit']['Keywords']['Length']['Minimum'].'*', LOG_WARNING, 'SEO');
        }
        else
        {
            F::Log('SEO Keywords length is normal ', LOG_GOOD, 'SEO');
        }

        return $Call;
    });