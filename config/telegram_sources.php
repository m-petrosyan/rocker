<?php

use App\Enums\CityEnum;
use App\Enums\CountryEnum;

return [
    /*
     * Список Telegram-источников (супергруппы с форум-темами),
     * откуда бот ловит новые посты и создаёт Event в статусе pending.
     *
     * Ключ — composite "{chat_id}:{thread_id}" (или "{chat_id}" если без треда).
     */
    'channels' => [
        '-1001583147579:97732' => [
            'name' => 'yerevanmetal',
            'country' => CountryEnum::ARMENIA->value,
            'city' => CityEnum::Yerevan->value,
        ],
        '-1001681397226:1139' => [
            'name' => 'tbilisimetal',
            'country' => CountryEnum::GEORGIA->value,
            'city' => CityEnum::Tbilisi->value,
        ],
        '-1003814367641' => [
            'name' => 'sadsadsasdasadsa',
            'country' => CountryEnum::ARMENIA->value,
            'city' => CityEnum::Yerevan->value,
        ],
    ],

    /*
     * Минимальная длина текста (caption) поста чтобы считать его
     * валидным анонсом события. Помогает отсеять короткие реплаи/смайлы.
     */
    'min_caption_length' => 30,
];
