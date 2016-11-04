<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Process', function ($Call)
    {
        if ($Call['Locale'] !== $Call['Default']['Locale'])
            if (F::Dot($Call, 'LocalizedURLs.Enabled') && preg_match_all('@a href="(/.*)"@SsUu',$Call['Output'], $Links))
            {
                $Pair = [];
               
                foreach ($Links[1] as $IX => $Link)
                {
                    $Localize = true;
    
                    if (in_array($Link, F::Dot($Call, 'LocalizedURLs.Excluded')))
                        $Localize = false;
    
                    foreach ($Call['Locales']['Available'] as $Locale)
                        if (preg_match('@^/'.$Locale.'/@Ssu', $Link))
                            $Localize = false;
                    
                    if ($Link == '/')
                        $Localize = false;
    
                    if ($Localize)
                        $Pair['href="'.$Link] = 'href="/'.$Call['Locale'].$Link;

                }
                
                $Call['Output'] = strtr($Call['Output'], $Pair);
            }

        return $Call;
    });