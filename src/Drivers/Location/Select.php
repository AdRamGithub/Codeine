<?php

    /* Sphinx
     * @author BreathLess
     * @description  
     * @package Sphinx
     * @version 7.2
     */

    setFn('Widget', function ($Call)
    {
        $Locations = F::Run('Entity', 'Read', ['Entity' => 'Location', 'NoPage' => true]);

        $Here = F::Run('Entity', 'Read', ['Entity' => 'Location', 'Where' => $Call['Location'], 'One' => true]);

        if ($Call['URL'] == '/')
            $Call['URL'] = '';

        foreach ($Locations as $Location)
        {
            if (null == $Here['Slug'])
                $Location['URL'] = '/'.$Location['Slug'].$Call['URL'];
            else
            {
                if (preg_match('@^/'.$Here['Slug'].'@Ssuu', $Call['URL']))
                    $Location['URL'] = str_replace($Here['Slug'], $Location['Slug'], $Call['URL']);
                else
                    $Location['URL'] = '/'.$Location['Slug'];
            }

            $Call['Output']['Content'][] =
                [
                    'Type' => 'Template',
                    'Scope' => 'Location',
                    'ID' => 'Show/Widget',
                    'Data' => $Location
                ];
        }

        return $Call;
    });

    setFn('Select', function ($Call)
    {
        F::Run('Session', 'Write', $Call, ['Data' => ['Location' => $Call['Location']]]);

        $Call = F::Run('Entity.Redirect', 'Restore', $Call);

        return $Call;
    });