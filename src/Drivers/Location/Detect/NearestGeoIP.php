<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Detect', function ($Call)
    {
        $GeoIP = F::Run('System.GeoIP', 'LatLon', ['Value' => F::Run('System.Interface.Web.IP', 'Get')]);

        if (isset($GeoIP['Lat']))
        {
            $Call['Session']['Lat'] = $GeoIP['Lat'];
            $Call['Session']['Long'] = $GeoIP['Lon'];

            $Cities = F::Run('Entity', 'Read', array('Entity' => 'Location'));

            $Sorted = array();

            foreach ($Cities as $Location)
            {
                $Sorted[$Location['ID']] = F::Run('Science.Geography.Distance', 'Calc', array('From' => $Call['Session'], 'To' => $Location));
                F::Log('Distance to '.$Location['Title'].' is '.$Sorted[$Location['ID']].' km', LOG_INFO);
            }

            asort($Sorted);

            list($DeterminedLocation) = each($Sorted);
            F::Log('Location determined', LOG_INFO);
        }
        else
            $DeterminedLocation = 1;

        return $DeterminedLocation;
    });