<?php
namespace CharlesAugust44\NubankPHP\Models;

enum NubankStatus: string
{
    case AUTHORIZED = 'Authorized';
    case UNAUTHORIZED = 'Unauthorized';
    case SESSION_LOADED = 'Session Loaded';
    case WAITING_QR = 'Waiting QrCode';
}
