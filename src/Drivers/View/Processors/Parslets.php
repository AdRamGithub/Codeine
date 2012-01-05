<?php

    /* Codeine
     * @author BreathLess
     * @description Apriori Parser 
     * @package Codeine
     * @version 6.0
     */

     self::setFn('Process', function ($Call)
     {
         foreach ($Call['Parslets'] as $Parslet)
         {
             $Tag = strtolower($Parslet);

             if (preg_match_all('@<'.$Tag.'>(.*)<\/'.$Tag.'>@SsUu', $Call['Output'], $Parsed))
             {
                 $Call['Output'] = F::Run('View.Processors.Parslets.'.$Parslet, 'Parse', $Call,
                    array(
                        'Parsed' => $Parsed
                    )
                 );
             }
         }

         return $Call;
     });