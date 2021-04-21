<?php

/**
 * https://carbon.nesbot.com/laravel/
 */
namespace App\Database\Query\Grammars;

class MySqlGrammar extends \Illuminate\Database\Query\Grammars\MySqlGrammar
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }
}
