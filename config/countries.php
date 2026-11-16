<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Visa-Free Countries per Passport
    |--------------------------------------------------------------------------
    |
    | Countries that don't require a visa, keyed by passport (ISO country code),
    | with their IATA country codes and popular destinations.
    | Which passport an origin city searches for is set in config/airports.php.
    |
    */

    // Passport names in the genitive, for "паспорт России"
    'passports' => [
        'RU' => 'России',
        'KZ' => 'Казахстана',
        'UZ' => 'Узбекистана',
    ],

    'visa_free' => [
        'RU' => [
            // Checked 13.11.2026 against the Russian MFA consular table
            // https://www.kdmid.ru/info-for-traveling-abroad/reference-information-for-traveling-abroad/list-of-countries-with-simplified-entry/
            // Deliberately absent: Israel (paid ETA-IL before travel), Indonesia (visa on
            // arrival), Moldova (visa-free on paper, frequent refusals at the border).
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
                'cities' => ['ALA', 'NQZ', 'GUW'], // Almaty, Astana, Atyrau
            ],
            'KG' => [
                'name' => 'Kyrgyzstan',
                'cities' => ['BSZ', 'OSS'], // Bishkek, Osh
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
            'GE' => [
                'name' => 'Georgia',
                'cities' => ['TBS', 'KUT'], // Tbilisi, Kutaisi
            ],
            'AZ' => [
                'name' => 'Azerbaijan',
                'cities' => ['GYD'], // Baku
            ],
            'JO' => [
                'name' => 'Jordan',
                'cities' => ['AMM'], // Amman
            ],
            'QA' => [
                'name' => 'Qatar',
                'cities' => ['DOH'], // Doha
            ],
            'OM' => [
                'name' => 'Oman',
                'cities' => ['MCT'], // Muscat
            ],
            'MY' => [
                'name' => 'Malaysia',
                'cities' => ['KUL'], // Kuala Lumpur
            ],
            'HK' => [
                'name' => 'Hong Kong',
                'cities' => ['HKG'], // Hong Kong
            ],
        ],
        'KZ' => [
            // Source: MFA of Kazakhstan, "Визовый режим для граждан Республики Казахстан"
            // https://www.gov.kz/memleket/entities/mfa/press/article/details/6816?lang=ru (rev. 21.08.2026)
            'TR' => [
                'name' => 'Turkey',
                'cities' => ['IST', 'SAW', 'AYT'], // Istanbul, Sabiha Gokcen, Antalya
            ],
            'AE' => [
                'name' => 'United Arab Emirates',
                'cities' => ['DXB', 'AUH', 'SHJ'], // Dubai, Abu Dhabi, Sharjah
            ],
            'GE' => [
                'name' => 'Georgia',
                'cities' => ['TBS', 'KUT'], // Tbilisi, Kutaisi
            ],
            'AM' => [
                'name' => 'Armenia',
                'cities' => ['EVN'], // Yerevan
            ],
            'AZ' => [
                'name' => 'Azerbaijan',
                'cities' => ['GYD'], // Baku
            ],
            'KG' => [
                'name' => 'Kyrgyzstan',
                'cities' => ['BSZ'], // Bishkek
            ],
            'UZ' => [
                'name' => 'Uzbekistan',
                'cities' => ['TAS', 'SKD'], // Tashkent, Samarkand
            ],
            'TJ' => [
                'name' => 'Tajikistan',
                'cities' => ['DYU'], // Dushanbe
            ],
            'RU' => [
                'name' => 'Russia',
                'cities' => ['MOW', 'LED'], // Moscow, Saint Petersburg
            ],
            'BY' => [
                'name' => 'Belarus',
                'cities' => ['MSQ'], // Minsk
            ],
            'RS' => [
                'name' => 'Serbia',
                'cities' => ['BEG'], // Belgrade
            ],
            'CN' => [
                'name' => 'China',
                'cities' => ['URC', 'BJS'], // Urumqi, Beijing
            ],
            'HK' => [
                'name' => 'Hong Kong',
                'cities' => ['HKG'], // Hong Kong
            ],
            'MN' => [
                'name' => 'Mongolia',
                'cities' => ['UBN'], // Ulaanbaatar
            ],
            'QA' => [
                'name' => 'Qatar',
                'cities' => ['DOH'], // Doha
            ],
            'OM' => [
                'name' => 'Oman',
                'cities' => ['MCT'], // Muscat
            ],
            'TH' => [
                'name' => 'Thailand',
                'cities' => ['BKK'], // Bangkok
            ],
            'VN' => [
                'name' => 'Vietnam',
                'cities' => ['HAN', 'CXR'], // Hanoi, Nha Trang
            ],
            'MY' => [
                'name' => 'Malaysia',
                'cities' => ['KUL'], // Kuala Lumpur
            ],
            'MV' => [
                'name' => 'Maldives',
                'cities' => ['MLE'], // Male
            ],
        ],
        'UZ' => [
            // Source: MFA of Uzbekistan, "Визовые требования для граждан Республики Узбекистан"
            // https://gov.uz/ru/mfa/pages/o-zbekiston-respublikasi-fuqarolari-uchun-viza-talablari (rev. 29.12.2025)
            // Deliberately absent: Thailand (listed there, but a visa is required from 15.09.2026),
            // Iran and Ukraine (no usable flights), Kenya/Sri Lanka (tourist registration, not visa-free).
            // Only destinations that have a landing page are listed.
            'TR' => ['name' => 'Turkey', 'cities' => ['IST', 'SAW', 'AYT']],
            'AE' => ['name' => 'United Arab Emirates', 'cities' => ['DXB', 'AUH', 'SHJ']],
            'GE' => ['name' => 'Georgia', 'cities' => ['TBS', 'KUT']],
            'AM' => ['name' => 'Armenia', 'cities' => ['EVN']],
            'AZ' => ['name' => 'Azerbaijan', 'cities' => ['GYD']],
            'KZ' => ['name' => 'Kazakhstan', 'cities' => ['ALA', 'NQZ']],
            'KG' => ['name' => 'Kyrgyzstan', 'cities' => ['BSZ']],
            'TJ' => ['name' => 'Tajikistan', 'cities' => ['DYU']],
            'RU' => ['name' => 'Russia', 'cities' => ['MOW', 'LED']],
            'BY' => ['name' => 'Belarus', 'cities' => ['MSQ']],
        ],
    ],
];
