<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class UserStatus extends Enum
{
    const UPDATE_USER =   1;
    const NOT_ACTIVATED =   0;
    const ACTIVATED = 2;
    const BLOCK = 3;
}
