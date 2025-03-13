<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueJsonValue implements Rule
{
    private $validValues, $table, $id;

    public function __construct($table, $id, array $validValues)
    {
        $this->validValues = $validValues;
        $this->table = $table;
        $this->id = $id;
    }

    public function passes($attribute, $value)
    {
        $jsonValues = $value;

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        foreach ($this->validValues as $validValue) {
            if ($jsonValues === json_decode(json_encode($validValue), true)) {
                $existingValues = DB::table($this->table)->whereJsonContains($attribute, $validValue)->where('id', '<>', $this->id)->exists();
                return !$existingValues;
            }
        }

        return true;
    }

    public function message()
    {
        return 'This :attribute must not have more than one in template list !';
    }
}
