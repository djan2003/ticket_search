<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Airport Codes
    |--------------------------------------------------------------------------
    |
    | Origin cities. 'passport' selects the visa-free list (config/countries.php)
    | that is searched from this city.
    |
    */

    'origins' => [
        'BUS' => [
            'name' => 'Batumi International Airport',
            'name_ru' => 'Батуми',
            'city' => 'Batumi',
            'country' => 'Georgia',
            'passport' => 'RU',
        ],
        'TBS' => [
            'name' => 'Tbilisi International Airport',
            'name_ru' => 'Тбилиси',
            'city' => 'Tbilisi',
            'country' => 'Georgia',
            'passport' => 'RU',
        ],
        'EVN' => [
            'name' => 'Yerevan Zvartnots Airport',
            'name_ru' => 'Ереван',
            'city' => 'Yerevan',
            'country' => 'Armenia',
            'passport' => 'RU',
        ],
        'ALA' => [
            'name' => 'Almaty International Airport',
            'name_ru' => 'Алматы',
            'city' => 'Almaty',
            'country' => 'Kazakhstan',
            'passport' => 'KZ',
        ],
    ],
];
