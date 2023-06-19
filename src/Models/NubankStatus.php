<?php
namespace CharlesAugust44\NubankPHP\Models;

enum NubankStatus
{
    case AUTHORIZED;
    case UNAUTHORIZED;
    case SESSION_LOADED;
    case WAITING_QR;
}
