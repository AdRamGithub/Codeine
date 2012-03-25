<?php

    /* Codeine
     * @author BreathLess
     * @description Exec Parslet 
     * @package Codeine
     * @version 7.2
     */

     self::setFn('Parse', function ($Call)
     {
          foreach ($Call['Parsed'][2] as $Ix => $Match)
          {
              $Match = json_decode(json_encode(simplexml_load_string('<?xml version=\'1.0\'?><exec>'.$Match.'</exec>')), true);

              $Application = F::Run('Code.Flow.Application', 'Run', array('Context' => 'app', 'Run' => (array) $Match));

              $Call['Output'] = str_replace($Call['Parsed'][0][$Ix], $Application['Output'], $Call['Output']);
          }

          return $Call['Output'];
     });