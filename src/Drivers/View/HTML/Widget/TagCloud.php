<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Make', function ($Call)
    {
        return F::Run('View.HTML.Widget.TagCloud.'.$Call['TagCloud'], 'Make', $Call);
    });