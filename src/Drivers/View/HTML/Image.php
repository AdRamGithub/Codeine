<?php

    /* Codeine
     * @author BreathLess
     * @description Media includes support 
     * @package Codeine
     * @version 7.x
     */

    setFn('Process', function ($Call)
    {
        $Parsed = F::Run('Text.Regex', 'All',
            [
                'Pattern' => $Call['Image']['Pattern'],
                'Value' => $Call['Output']
            ]);

        if ($Parsed)
        {
            // Image Output

            // Если изображения на отдельном сервере

            if (isset($Call['Image']['Host']) && !empty($Call['Image']['Host']))
                $Host = $Call['Image']['Host'];
            else
                $Host = $Call['RHost'];

            $Call['Images'] = $Parsed[1];

            // Перед вводом картинок
            $Call = F::Hook('beforeImageInput', $Call);

            // Чтение тегов
            foreach ($Call['Images'] as &$Image)
                $Image = F::Merge($Call['Image'],
                    json_decode(
                        json_encode(
                            simplexml_load_string('<image>'.$Image.'</image>'),
                            JSON_NUMERIC_CHECK
                        ), true)
                );

            // После ввода картинок
            $Call = F::Hook('afterImageInput', $Call);

            // Перед выводом картинок
            $Call = F::Hook('beforeImageOutput', $Call);

                foreach ($Call['Images'] as $Call['Current Image'])
                {
                    $Call['Current Image']['Source']['Where'] = ['ID' => $Call['Current Image']['Source']['Where']];

                    if (!F::Run('IO', 'Execute', $Call['Current Image']['Source'],
                    [
                        'Execute' => 'Exist'
                    ]))
                    {
                        $Call['Current Image']['Storage'] = 'Image';
                        $Call['Current Image']['Scope'] = 'Default';

                        $Call['Current Image']['Source']['Storage'] = 'Image';

                        if (isset($Call['Current Image']['Default']))
                        {
                            list($Asset, $ID) =
                                F::Run('View', 'Asset.Route',
                                [
                                    'Value' => $Call['Current Image']['Default']
                                ]);

                            $Call['Current Image']['Source']['Scope'] = $Asset;
                            $Call['Current Image']['Source']['Where'] = ['ID' => $ID];
                        }
                        else
                        {
                            $Call['Current Image']['Source']['Scope'] = 'Default/img';
                            $Call['Current Image']['Source']['Where'] = ['ID' => 'Default.png'];
                        }
                    }

                    $Version = F::Run('IO', 'Execute', $Call['Current Image']['Source'],
                        [
                            'Execute' => 'Version'
                        ]);

                    $ImageCached = $Version.'_'.
                        $Call['Current Image']['Width'].
                        strtr($Call['Current Image']['Source']['Scope'].'.'.$Call['Current Image']['Source']['Where']['ID'], '/', '.');

                    $Write = true;

                    if ($Call['Image']['Caching'])
                    {
                        if (F::Run('IO', 'Execute',
                        [
                            'Storage' => 'Image Cache',
                            'Scope'   => [$Host, 'img'],
                            'Execute' => 'Exist',
                            'Where'   =>
                            [
                                'ID' => $ImageCached
                            ]
                        ]))
                        {
                            F::Log('Cache *hit* '.$ImageCached, LOG_GOOD);
                            $Write = false;
                        }
                        else
                        {
                            F::Log('Cache *miss* *'.$ImageCached.'*', LOG_BAD);
                        }
                    }

                    if ($Write)
                    {
                        $Call['Current Image']['Data'] =
                            F::Run('IO', 'Read',
                                $Call['Current Image']['Source']
                            )[0];

                        $Call = F::Hook('beforeImageWrite', $Call);

                            F::Run ('IO', 'Write',
                            [
                                 'Storage' => 'Image Cache',
                                 'Scope'   => [$Host, 'img'],
                                 'Where'   => $ImageCached,
                                 'Data' => $Call['Current Image']['Data']
                            ]);

                        $Call = F::Hook('afterImageWrite', $Call);
                    }

                    $Call['Image']['Tags'][] = '<img src="'
                            .$Call['Image']['Proto']
                            .$Call['Image']['Host']
                            .$Call['Image']['Pathname']
                            .$ImageCached.'"
                            class="'.$Call['Current Image']['Class'].'"
                            width="'.$Call['Current Image']['Width'].'"
                            height="'.(isset($Call['Current Image']['Height'])? $Call['Current Image']['Height']: "").'" />';
                }

            $Call = F::Hook('afterImageOutput', $Call);

            // После вывода картинок
        }

        if (empty($Call['Image']['Tags']))
            $Call['Image']['Tags'] = '';

        $Call['Output'] = str_replace($Parsed[0], $Call['Image']['Tags'] , $Call['Output']);

        unset($Call['Current Image'], $Call['Image']);

        return $Call;
    });