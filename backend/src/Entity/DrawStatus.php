<?php

namespace App\Entity;

enum DrawStatus: string
{
    case IN_PROGRESS = 'IN_PROGRESS';
    case FINISHED = 'FINISHED';
}
