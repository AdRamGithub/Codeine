<?php

    /* Codeine
     * @author BreathLess
     * @description Exec Parslet 
     * @package Codeine
     * @version 6.0
     */

     self::setFn('Parse', function ($Call)
     {
          foreach ($Call['Parsed'][2] as $Ix => $Match)
              $Call['Output'] = str_replace($Call['Parsed'][0][$Ix], '<a class="mailto" href="mailto:'. $Match.'">'. $Match.'</a>', $Call['Output']);

          return $Call['Output'];
     });