<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Initialize', function ($Call)
    {
        $Call['SID'] = F::Run('Session.Marker.Cookie', 'Read', $Call);

        $Call = F::Hook('beforeSessionInitialize', $Call);

        // Маркера нет — пользователь чистый гость
        if (null === $Call['SID'])
        {
            F::Log('Session: Marker not set', LOG_DEBUG, 'Security');

            $Call = F::Apply(null, 'Mark', $Call);

            $Call['Session'] = [];
        }
        else
        {
            $Call['Session'] = F::Run('Entity', 'Read',
                [
                    'Entity' => 'Session',
                    'Where' => $Call['SID'],
                    'Time' => rand(),
                    'One' => true
                ]);

                if ($Call['Session'] !== null)
                {
                    F::Log('Session: Channel *'.$Call['Session']['Channel'].'*', LOG_INFO, 'Security');
                /*
                    if (isset($Call['Session']['User']['Locale']))
                    {
                        $Call['Locale'] = $Call['Session']['User']['Locale'];
                        F::Log('User Locale selected: '.$Call['Session']['User']['Locale'], LOG_INFO);
                    }*/
                }
                else
                    $Call['Session'] = [];
        }

        $Call = F::Hook('afterSessionInitialize', $Call);

        $Call = F::Run(null, 'Load User', $Call);

        F::Log($Call['Session'], LOG_DEBUG, 'Security');

        return $Call;
    });

    setFn('Load User', function ($Call)
    {
        if (isset($Call['Session']['Secondary']) && $Call['Session']['Secondary'] != 0)
        {
            $Call['Session']['Primary'] = F::Run('Entity', 'Read', $Call,
                [
                    'Entity' => 'User',
                    'Where' => $Call['Session']['User'],
                    'Time' => microtime(true),
                    'One' => true
                ]);

            $Call['Session']['User'] = F::Run('Entity', 'Read', $Call,
                [
                    'Entity' => 'User',
                    'Where' => $Call['Session']['Secondary'],
                    'Time' => microtime(true),
                    'One' => true
                ]);

            F::Log('Session: Secondary user '.$Call['Session']['User']['ID'].' authenticated', LOG_INFO, 'Security');
        }
        elseif (isset($Call['Session']['User']) && $Call['Session']['User'] != 0)
        {
            $Call['Session']['User'] = F::Run('Entity', 'Read',
                [
                    'Entity' => 'User',
                    'Where' => $Call['Session']['User'],
                    'One' => true
                ]);

            F::Log('Session: Primary user '.$Call['Session']['User']['ID'].' authenticated', LOG_INFO, 'Security');
        }

        if (isset($Call['Session']['User']['Status']) && $Call['Session']['User']['Status'] === 0)
            $Call = F::Hook('ActivationNeeded', $Call);

        return $Call;
    });

    setFn('Write', function ($Call)
    {
        if (!isset($Call['SID']))
            $Call = F::Apply(null, 'Mark', $Call);

        if (!isset($Call['Session']))
            $Call = F::Apply(null, 'Initialize', $Call);

         if (empty($Call['Session']))
        {
            $Call['Session'] = F::Run('Entity', 'Create', $Call,
                [
                    'Entity' => 'Session',
                    'One' => true,
                    'Data' =>
                    [
                        'ID' => $Call['SID']
                    ]
                ]);

            F::Log('Session created '.$Call['SID'], LOG_INFO, 'Security');
        }
        else
        {
            $Call['Session'] = F::Run('Entity', 'Update', $Call,
                [
                    'Entity' => 'Session',
                    'Data' => $Call['Data'],
                    'Where' => $Call['SID'],
                    'One' => true
                ]);

            F::Log('Session updated '.$Call['SID'], LOG_INFO, 'Security');
        }

        $Call = F::Run(null, 'Load User', $Call);

        return $Call;
    });

    setFn('Read', function ($Call)
    {
        if (!isset($Call['Session']))
            $Call = F::Apply(null, 'Initialize', $Call);

        if (isset($Call['Key']))
            return F::Dot($Call['Session'], $Call['Key']) or false;
        else
            return $Call['Session'];
    });

    setFn('Annulate', function ($Call)
    {
        if (isset($Call['Session']['Secondary']) && $Call['Session']['Secondary'] != 0)
            $Call = F::Apply('Session', 'Write', $Call, ['Data!' => ['Secondary' => 0]]);
        else
            $Call = F::Apply('Session', 'Write', $Call, ['Data!' => ['User' => 0]]);

        $Call = F::Hook('afterAnnulate', $Call);

        $Call['Session'] = [];

        return $Call;
    });

    setFn('Mark', function ($Call)
    {
        $Call['SID'] = F::Live($Call['SID Generator']);

        // Вешаем маркер, если включено автомаркирование
        if (F::Run('Session.Marker.Cookie', 'Write', $Call))
            F::Log('Session Marker added', LOG_INFO, 'Security');
        else
            F::Log('Session Marker add failed', LOG_INFO, 'Security');

        return $Call;
    });