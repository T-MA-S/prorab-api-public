<?php 

namespace app\components\interfaces;

interface BusyInterface 
{
    const BUSY_FREE = 0;
    const BUSY_BUSY = 1;
    const BUSY_SOON = 2;
}