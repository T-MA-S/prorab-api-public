<?php 

namespace app\components\interfaces;

interface StatusInterface
{
    const AWAITING = 0;
    const APPROVED = 1;
    const REJECTED = 2;
    const DEACTIVATED = 3;
    const DELETED = 4;

    const MODERATION_PROCESS = 1;
}