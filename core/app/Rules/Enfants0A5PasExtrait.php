<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Enfants0A5PasExtrait implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $ageEnfant0A5 = request('ageEnfant0A5'); // Obtenez la valeur de ageEnfant0A5 depuis la requête

        // Assurez-vous que enfantsPasExtrait est inférieur ou égal à ageEnfant0A5
        return $value <= $ageEnfant0A5;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Le nombre d\'enfants de 0 à 5 n\' ayant pas  d\'extraits doit être inférieur ou égal au nombre d\'enfants de 0 à 5 ans du ménage.';
    }
}
