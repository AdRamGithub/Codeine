<?php

    $Timeout = 2000; // Максимальное время выполнения, миллисекунды.
   
    self::$Collection->Query('@All'); // Которые не были обработаны
    self::$Collection->Sort('Priority', 'DESC');
    self::$Collection->Load();

    Timing::Go('Event Queue Server');

    // Обрабатываем по очереди

    $IC = 0;
    $HNDC = 0;

    if (self::$Collection->Length > 0)
        {
            foreach (self::$Collection->_Items as $Item)
                {
                    if (Timing::Lap('Event Queue Server') < $Timeout)
                    {
                        if (($NN = Event::Handle($Item))>0)
                        {
                            $HNDC+= $NN;
                            $IC++;
                        }
                    }
                    else
                        break;
                }
            Page::Add('Всего событий: '.self::$Collection->Length.'<br/> Обработано событий: '.$IC.'<br/>  Сработало слотов: '.$HNDC.'');
        }
    else
        Page::Add('В очереди нет событий');