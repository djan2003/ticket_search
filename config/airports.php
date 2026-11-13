<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Airport Codes
    |--------------------------------------------------------------------------
    |
    | Origin cities. 'passport' selects the visa-free list (config/countries.php)
    | that is searched from this city.
    | 'name_ru' is in the genitive: it is used as "билеты из <name_ru>".
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
            'name_ru' => 'Еревана',
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
        'NQZ' => [
            'name' => 'Astana Nursultan Nazarbayev International Airport',
            'name_ru' => 'Астаны',
            'city' => 'Astana',
            'country' => 'Kazakhstan',
            'passport' => 'KZ',
        ],
        'MOW' => [
            'name' => 'Moscow (all airports)',
            'name_ru' => 'Москвы',
            'city' => 'Moscow',
            'country' => 'Russia',
            'passport' => 'RU',
        ],
        'LED' => [
            'name' => 'Saint Petersburg Pulkovo Airport',
            'name_ru' => 'Санкт-Петербурга',
            'city' => 'Saint Petersburg',
            'country' => 'Russia',
            'passport' => 'RU',
        ],
    ],
];
