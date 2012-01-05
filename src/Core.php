<?php

    /*
     * @author BreathLess
     * @description: F Class
     * @package Codeine Framework
     * @subpackage Core
     * @version 6.0
     */

    define('Codeine', __DIR__);

    final class F
    {
        protected static $_Options;
        protected static $_Code;
        protected static $_Service = 'Codeine';
        protected static $_Method = 'Do';
        protected static $_Storage;
        protected static $_History = array();

        /**
             * @var SplStack;
             */
        protected static $_Stack;

        public static function Bootstrap ($Call = null)
        {
            self::$_Stack = new SplStack();
            self::$_Stack->push('Core');

            if (isset($Call['Path']))
            {
                if (is_array($Call['Path']))
                    self::$_Options['Path'] = array_merge($Call['Path'], array(Codeine));
                else
                    self::$_Options['Path'] = array($Call['Path'], Codeine);
            }
            else
                self::$_Options['Path'][] = Codeine;

            self::loadOptions();

            register_shutdown_function ('F::Shutdown');
            set_error_handler ('F::Error'); // Instability
        }

        public static function Merge($First, $Second)
        {
            if (is_array($Second) && is_array($First))
            {
                foreach ($Second as $Key => $Value)
                    if (isset($First[$Key]) && is_array($Value))
                        $First[$Key] = self::Merge($First[$Key], $Second[$Key]);
                    else
                        $First[$Key] = $Value;
            }
            else
                $First = $Second;

            return $First;
        }

        public static function findFile($Names)
        {
           $Names = (array) $Names;

           foreach ($Names as $Name)
               foreach (self::$_Options['Path'] as $ic => $Path)
                   if (file_exists($Filenames[$ic] = $Path.'/'.$Name))
                        return $Filenames[$ic];

           //trigger_error($Names[0].' not found'); // FIXME
           return null;
        }

        protected static function _loadSource($Service)
        {
            $Path = strtr($Service, '.', '/');

            if (isset($Call['Filename']))
                $Filename = $Call['Filename'];
            else
                $Filename = self::findFile(self::$_Options['Codeine']['Driver']['Path'].'/'.$Path.self::$_Options['Codeine']['Driver']['Extension']);

            if ($Filename)
                return (include $Filename);
            else
                return null;
        }

        /**
             * @description Проверяет, является ли массив правильно сконструированным вызовом.
             * @param  $Call
             * @return bool
             */
        public static function isCall($Call)
        {
            return (is_array($Call) && isset($Call['Service']) && isset($Call['Method']));
        }

        public static function hashCall($Call)
        {
            if (self::isCall($Call))
                return $Call['Service'].':'.$Call['Method'].'('.sha1(serialize($Call)).')';
            else
                return serialize($Call);
        }

        /**
             * @description Выполняет вызов
             * @param  $Call
             * @return mixed
             */
        public static function Run($Service, $Method, $Call = array())
        {
            // TODO Infinite cycle protection

            if (($sz = func_num_args())>3)
            {
                for($ic = 3; $ic<$sz; $ic++)
                    if (is_array($Argument = func_get_arg ($ic)))
                        $Call = F::Merge($Call, $Argument);
            }

            $ParentNamespace = self::$_Service;

            self::$_Service = $Service;
            self::$_Method  = $Method;
            //self::$_History[sha1($ParentNamespace.self::$_Service)] = array(strtr($ParentNamespace,'.','_'), strtr(self::$_Service,'.','_'));

            $Call = self::Merge(self::loadOptions(), $Call);

           /* if(!isset($Call['NoBehaviours']))
                        foreach (self::$_Options['Codeine']['Behaviours'] as $Behaviour)
                            if (!(is_array($Call) && isset($Call['No'.$Behaviour])))
                                $Call = self::Run('Code.Behaviour.'.$Behaviour, 'beforeRun',
                                    array(
                                        'Value' => $Call,
                                        'NoBehaviours' => true
                                    ));*/

            self::$_Stack->push($Call);

            if (!isset($Call[':Result']))
            {
                if (null === self::getFn($Method) && (null === self::_loadSource ($Service)))
                    $Result = isset($Call['Fallback'])? $Call['Fallback']: null;
                else
                    {
                        $F = self::getFn($Method);

                        if (is_callable($F))
                           $Result = $F(&$Call);
                        else
                           $Result = isset($Call['Fallback'])? $Call['Fallback']: null;
                    }

            /* if(!isset($Call['NoBehaviours']))
                            foreach (self::$_Options['Codeine']['Behaviours'] as $Behaviour)
                                if (!(is_array($Call) && isset($Call['No'.$Behaviour])))
                                    $Call = self::Run('Code.Behaviour.' . $Behaviour, 'afterRun',
                                        array(
                                            'Value' => $Call,
                                            'NoBehaviours' => true
                                        ));*/
            }
            else
                $Result =  $Call[':Result'];

            self::$_Stack->pop();

            self::$_Service = $ParentNamespace;

            return $Result;
        }

        public static function setFn($Function, $Code)
        {
            return self::$_Code[self::$_Service.'.'.$Function] = $Code;
        }

        public static function getFn($Function)
        {
            if (isset(self::$_Code[self::$_Service . '.' . $Function]))
                return self::$_Code[self::$_Service . '.' . $Function];
            else
                return null;

            // Fuckup of IDE hinting
            return function ()
            { };
        }

        public static function Set($Key, $Value)
        {
            return self::$_Storage[self::$_Service][$Key] = $Value;
        }

        public static function Get($Key = null, $Default = null)
        {
            if (null === $Key && isset(self::$_Storage[self::$_Service]))
                return self::$_Storage[self::$_Service];

            if (isset(self::$_Storage[self::$_Service][$Key]))
                return self::$_Storage[self::$_Service][$Key];
            else
                return $Default;
        }

        public static function Live($Variable)
        {
            if (self::isCall($Variable))
                return F::Run($Variable['Service'], $Variable['Method'], $Variable['Call']);
            else
                return $Variable;
        }

        public static function Shutdown()
        {
            /*file_put_contents(Root.'/Data/graph.png', F::Run (
                        array(
                             'Service' => 'View.Generators.Graphviz',
                             'Method' => 'Do',
                             'Graphviz.Layout' => 'dot',
                             'Title' => 'Calls',
                             'Value' => self::$_History,
                             'Format' => 'png'
                        )
                    ));*/

            return null; // TODO onShutdown
        }

        public static function Error($errno , $errstr , $errfile , $errline , $errcontext)
        {
            // FIXME
            echo '<div class="Error"> PHP: '.$errstr.' in <a href="xdebug://'.$errfile.'@'.$errline.'">'.$errfile.':'.$errline.'</a> </div>';
            d(__FILE__, __LINE__, self::$_Stack->top());
        }

        public static function loadOptions($Service = null, $Method = null)
        {
            $Service = ($Service == null)? self::$_Service: $Service;
            $Method = ($Method  == null) ? self::$_Method : $Method;

            if (!isset(self::$_Options[$Service]))
            {
                $Options = array();

                foreach (self::$_Options['Path'] as $Path)
                {
                    if ($Filename = self::findFile ('Options/' . strtr ($Service, '.', '/') . '.json'))
                    {
                        $Options = json_decode(file_get_contents($Filename), true);
                        break;
                    }
                }

                if ($Filename = self::findFile ('Options/' . strtr ($Service, '.', '/') . '/'.$Method.'.json'))
                    $Options = F::Merge($Options, json_decode (file_get_contents ($Filename), true));

                if (empty($Options))
                    $Options = null;

                self::$_Options[$Service] = $Options;
            }

            return self::$_Options[$Service];
        }

        public static function Dump($File, $Line, $Call)
        {
            // FIXME!
            echo '<div class="xdebug-header">'.substr($File, strpos($File, 'Drivers')).' <strong>@'.$Line.'</strong></div>';

            if (is_array($Call))
                krsort($Call);

            var_dump($Call);

            return $Call;
        }

        public static function getOption($Key)
        {
            return isset(self::$_Options[$Key])? self::$_Options[$Key]: null;
        }
    }

    function f()
    {
        return call_user_func_array(array('F','Run'), func_get_args());
    }

    function d()
    {
        return call_user_func_array(array('F','Dump'), func_get_args());
    }