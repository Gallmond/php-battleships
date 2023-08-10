<?php

namespace Gavin\GuestlineBattleships\Enums;

enum CellStateEnum : string
{
    case UNKNOWN = 'unknown';
    case HIT = 'hit';
    case MISS = 'miss';
}
