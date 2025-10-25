<?php

namespace App\Enums;

enum NewsSource: string
{
    case NEWS_API = 'newsapi';
    case THE_GUARDIAN = 'theguardian';
    case NEWS_DATA_IO = 'newsdataio';
    case NEW_YORK_TIMES = 'newyorktimes';
    // case NEWS_CRED = 'newscred';
    // case BBC_NEWS = 'bbc';

    public function getLabel(): string
    {
        return match($this) {
            self::NEWS_API => 'NewsAPI',
            self::NEWS_CRED => 'NewsCred',
            self::NEWS_DATA_IO => 'NewsDataIo',
            self::NEW_YORK_TIMES => 'NewYorkTimes',
            // self::THE_GUARDIAN => 'The Guardian',
            // self::BBC_NEWS => 'BBC News',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn($case) => $case->getLabel(), self::cases());
    }
}