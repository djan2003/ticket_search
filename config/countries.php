<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Visa-Free Countries for Russian Citizens
    |--------------------------------------------------------------------------
    |
    | List of countries that don't require a visa for Russian citizens
    | with their IATA country codes and popular destinations
    |
    */

    'visa_free' => [
        'AM' => [
            'name' => 'Armenia',
            'cities' => ['EVN'], // Yerevan
        ],
        'BY' => [
            'name' => 'Belarus',
            'cities' => ['MSQ'], // Minsk
        ],
        'KZ' => [
            'name' => 'Kazakhstan',
            'cities' => ['ALA', 'TSE', 'GUW'], // Almaty, Astana, Atyrau
        ],
        'KG' => [
            'name' => 'Kyrgyzstan',
            'cities' => ['FRU', 'OSS'], // Bishkek, Osh
        ],
        'MD' => [
            'name' => 'Moldova',
            'cities' => ['KIV'], // Chisinau
        ],
        'TJ' => [
            'name' => 'Tajikistan',
            'cities' => ['DYU'], // Dushanbe
        ],
        'UZ' => [
            'name' => 'Uzbekistan',
            'cities' => ['TAS', 'SKD'], // Tashkent, Samarkand
        ],
        'RS' => [
            'name' => 'Serbia',
            'cities' => ['BEG'], // Belgrade
        ],
        'TR' => [
            'name' => 'Turkey',
            'cities' => ['IST', 'SAW', 'AYT', 'ADB'], // Istanbul, Sabiha Gokcen, Antalya, Izmir
        ],
        'TH' => [
            'name' => 'Thailand',
            'cities' => ['BKK', 'HKT', 'CNX'], // Bangkok, Phuket, Chiang Mai
        ],
        'MV' => [
            'name' => 'Maldives',
            'cities' => ['MLE'], // Male
        ],
        'AE' => [
            'name' => 'United Arab Emirates',
            'cities' => ['DXB', 'AUH'], // Dubai, Abu Dhabi
        ],
        'IL' => [
            'name' => 'Israel',
            'cities' => ['TLV'], // Tel Aviv
        ],
        'MA' => [
            'name' => 'Morocco',
            'cities' => ['CMN', 'RAK'], // Casablanca, Marrakech
        ],
        'TN' => [
            'name' => 'Tunisia',
            'cities' => ['TUN'], // Tunis
        ],
        'CU' => [
            'name' => 'Cuba',
            'cities' => ['HAV'], // Havana
        ],
        'VN' => [
            'name' => 'Vietnam',
            'cities' => ['HAN', 'SGN'], // Hanoi, Ho Chi Minh
        ],
        'ID' => [
            'name' => 'Indonesia',
            'cities' => ['CGK', 'DPS'], // Jakarta, Bali
        ],
    ],
];
