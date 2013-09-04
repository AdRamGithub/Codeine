<?php

    /* Codeine
     * @author BreathLess
     * @description Activation email 
     * @package Codeine
     * @version 7.x
     */

    setFn('Send', function ($Call)
    {

        if (isset($Call['Data'][0]))
            $Call['Data'] = $Call['Data'][0];

        if (isset($Call['Data']['ID']))
            {
                $Call['Data'] = F::Run('Entity', 'Read', array('Entity' => 'User', 'Where' => $Call['Data']['ID'], 'One' => true)); // FIXME

                $Call['Data']['Code'] = F::Run('Security.UID', 'Get');

                F::Run('IO', 'Write',
                    array(
                         'Storage' => 'Primary',
                         'Scope' => 'Activation',
                         'Data' =>
                             [[
                                 'ID' => (int) $Call['Data']['Code'],
                                 'User' => $Call['Data']['ID']
                             ]]
                    ));

                $Message['Scope'] = '"'.$Call['Data']['Screen'].'" <'.$Call['Data']['EMail'].'>';
                $Message['ID']    = $Call['Subject'];

                $Message['Data']  = F::Run('View', 'Load',
                                                     [
                                                          'Scope' => 'User/Activation',
                                                          'ID' => 'EMail',
                                                          'Data' =>
                                                              F::Merge($Call,[
                                                                   'URLActivation' => $Call['Host'].'/activate/user/'.$Call['Data']['Code']
                                                              ])
                                                     ]);

                $Message['Headers'] = array ('Content-type:' => ' text/html; charset="utf-8"');


                F::Run('IO', 'Write', $Call, $Message,
                    [
                        'Storage' => 'EMail'
                    ]);


                list(,$Call['Data']['Server']) = explode('@', $Call['Data']['EMail']);

                $Call['Output']['Content'] =
                [
                    [
                        'Type'  => 'Template',
                        'Scope' => 'User/Activation',
                        'ID' => 'Needed',
                        'Data'  => $Call['Data']
                    ]
                ];

                if (isset($Call['Second']))
                    $Call['Output']['Message'][] =
                        array(
                            'Type'  => 'Block',
                            'Class' => 'alert alert-success',
                            'Value'  => 'Письмо выслано повторно'
                        );
            }

        return $Call;
    });

    setFn('Check', function ($Call)
    {
        $Activation = F::Run('IO', 'Read',
             array(
                  'Storage' => 'Primary',
                  'Scope' => 'Activation',
                  'Where' => (int) $Call['Code']
             ))[0];

        if ($Activation !== null)
        {
            F::Run('Entity', 'Update', $Call,
                [
                     'Entity' => 'User',
                     'Where' => $Activation['User'],
                     'One' => true,
                     'Data' =>
                         [
                            'Status' => 1
                         ]
                ]);

            F::Run('IO', 'Write',
                array(
                     'Storage' => 'Primary',
                     'Scope' => 'Activation',
                     'Where' => $Call['Code'],
                     'Data' => null
                ));

            if (isset($Call['Activation']['AutoLogin']) && $Call['Activation']['AutoLogin'])
                $Call['Session'] = F::Run('Session', 'Write', $Call, ['Data' => ['User' => $Activation['User']]]);

            $Call = F::Hook('Activation.Success', $Call);
        }
        else
            $Call = F::Hook('Activation.Failed', $Call);

        return $Call;
    });