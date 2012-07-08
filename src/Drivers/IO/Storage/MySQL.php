<?php

  /* Codeine
     * @author BreathLess
     * @description  MySQL Driver
     * @package Codeine
     * @version 7.4.5
     */

    self::setFn ('Open', function ($Call)
    {
        $Link = new mysqli($Call['Server'], $Call['User'], F::Live($Call['Password']));

        if ($Link->errno != 0)
            return $Link->error;

        $Link->select_db ($Call['Database']);
        $Link->set_charset ($Call['Charset']);
     //   $Link->autocommit ($Call['AutoCommit']);

        return $Link;
    });

    self::setFn ('Read', function ($Call)
    {
        $Query = F::Run('IO.Storage.MySQL.Syntax', 'Read', $Call);

        //if (null === ($Data = F::Get($Query)))
        {
            F::Log($Query);

            $Result = $Call['Link']->query($Query);

            if ($Call['Link']->errno != 0)
                F::Log($Call['Link']->error,'Error');

            $Data = array();

            while($Row = $Result->fetch_assoc())
                $Data[] = $Row;

            F::Set($Query, $Data);
        }
        /*else
            F::Log($Query.' memoized!'); // FIXME*/

        return $Data;
    });

    /**
     * @var mysqli $Call["Link"]
     */
    self::setFn ('Write', function ($Call)
    {
        if (isset($Call['Where']))
        {
            if (isset($Call['Data']))
                $Query = F::Run('IO.Storage.MySQL.Syntax', 'Update', $Call);
            else
                $Query = F::Run('IO.Storage.MySQL.Syntax', 'Delete', $Call);
        }
        else
            $Query = F::Run('IO.Storage.MySQL.Syntax', 'Insert', $Call);

        if (isset($Call['Debug']))
            d(__FILE__, __LINE__, $Query);

        F::Log($Query);

        $Result = $Call['Link']->query($Query);

        if ($Call['Link']->errno != 0)
            F::Log($Call['Link']->error,'Error');

        return $Call['Data'];
    });

    self::setFn ('Close', function ($Call)
    {
        return true;
    });

    self::setFn ('Run', function ($Call)
    {
        return $Call['Link']->query($Call['Run']);
    });

    self::setFn ('Status', function ($Call)
    {
        $Data = explode('  ', $Call['Link']->stat());
        foreach ($Data as &$Row)
            $Row = explode(':', $Row);

        return $Data;
    });

    self::setFn ('Count', function ($Call)
    {
        $Result = $Call['Link']->query(F::Run('IO.Storage.MySQL.Syntax', 'Count', $Call));

        if ($Result)
            $Result = $Result->fetch_assoc();

        return $Result['count(*)'];
    });