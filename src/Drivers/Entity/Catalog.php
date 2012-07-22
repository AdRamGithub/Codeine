<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4
     */

    self::setFn('Do', function ($Call)
    {
        $Call = F::Merge(F::loadOptions('Entity.'.$Call['Entity']), $Call); // FIXME
        $Call = F::Hook('beforeCatalog', $Call);

        $Call['Layouts'][] = array('Scope' => $Call['Entity'],'ID' => 'Main');
        $Call['Layouts'][] = array('Scope' => $Call['Entity'],'ID' => 'Catalog');
        $Call['Locales'][] = $Call['Entity'];

        $Elements = F::Run('Entity', 'Read', array('Entity' => $Call['Entity'], 'Fields' => array($Call['Key'])));

        foreach ($Elements as $Element)
            $Keys[] = $Element[$Call['Key']];

        $Keys = array_count_values($Keys);

        foreach ($Keys as $Value => $Count)
            $Call['Output']['Content'][]=
                array(
                    'Type' => 'Template',
                    'Scope' => $Call['Entity'],
                    'ID' => 'Catalog/Row',
                    'Data' => array(
                        'Count' => $Count,
                        'Value' => $Value
                    )
                );

        $Call = F::Hook('afterCatalog', $Call);

        return $Call;
    });