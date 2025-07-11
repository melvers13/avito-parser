<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use DateTime;

class DateSupport
{
    //****************************************************************
    //************************ Форматы дат ***************************
    //****************************************************************

    /**
     * Основной формат даты 11 янв 2000.
     *
     * @param Carbon|string|null $date
     * @param bool $space_nbsp - пробел между днем и месяцем в виде &nbsp;
     * @param bool $full_month
     * @return string
     */
    public static function mainFormat(Carbon|string|null $date, bool $space_nbsp = false, bool $full_month = false): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        $space = $space_nbsp ? '&nbsp;' : ' ';

        $month = $full_month ? self::getFullMonthNameInCase($date->month, 'genitive') : self::getMonthName($date->month);

        return $date->format('j') . $space . $month . ' ' . $date->format('Y');
    }

    /**
     * Короткий формат 11 янв.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function shortFormat(Carbon|string$date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        return $date->format('j') . ' ' . self::getMonthName($date->month);
    }

    /**
     * Короткий формат 11 янв. в 12:00.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function shortFormatWithTime(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        return $date->format('j') . ' ' . self::getMonthName($date->month) . ' в ' . $date->format('H:i');
    }

    /**
     * Полный формат 11 янв 2000 в 20:00.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function fullFormat(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        return $date->format('j') . ' ' . self::getMonthName($date->month) . ' ' . $date->format('Y в H:i');
    }

    /**
     * Простой формат (ближайшая дата) сегодня/вчера/11 января в 20:00.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function simpleFormat(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        $now = now();

        $when = match ($date->toDateString()) {
            $now->toDateString() => 'сегодня',
            $now->subDay()->toDateString() => 'вчера',
            default => $date->format('j') . ' ' . self::getMonthName($date->month),
        };

        return $when . ' ' . $date->format(' в H:i');
    }

    /**
     * Формат "недавно" - 1 минуту назад, 2 часа назад, 2 дня назад, 1 год назад.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function recentlyFormat(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        $diff = now()->diff($date);

        if ($diff->y > 0) {
            return $diff->y . ' ' . true_word_form($diff->y, 'год', 'года', 'лет') . ' назад';
        }

        if ($diff->m > 0) {
            return $diff->m . ' ' . true_word_form($diff->m, 'месяц', 'месяца', 'месецев') . ' назад';
        }

        if ($diff->d > 0) {
            return $diff->d . ' ' . true_word_form($diff->d, 'день', 'дня', 'дней') . ' назад';
        }

        if ($diff->h > 0) {
            return $diff->h . ' ' . true_word_form($diff->h, 'час', 'часа', 'часов') . ' назад';
        }

        if ($diff->i > 0) {
            return $diff->i . ' ' . true_word_form($diff->i, 'минуту', 'минуты', 'минут') . ' назад';
        }

        return 'только что';
    }

    /**
     * Простой формат "недавно" - 1 минута, 2 часа, 2 дня, 1 год.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function simpleRecentlyFormat(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        $diff = now()->diff($date);

        if ($diff->y > 0) {
            return $diff->y . ' ' . true_word_form($diff->y, 'год', 'года', 'лет');
        }

        if ($diff->m > 0) {
            return $diff->m . ' ' . true_word_form($diff->m, 'месяц', 'месяца', 'месяца');
        }

        if ($diff->d > 0) {
            return $diff->d . ' ' . true_word_form($diff->d, 'день', 'дня', 'дней');
        }

        if ($diff->h > 0) {
            return $diff->h . ' ' . true_word_form($diff->h, 'час', 'часа', 'часов');
        }

        if ($diff->i > 0) {
            return $diff->i . ' ' . true_word_form($diff->i, 'минута', 'минуты', 'минут');
        }

        return 'только что';
    }

    /**
     * Адаптивный формат для чата в админке.
     * 09:21 - только время если дата совпадает с текущей;
     * 09:21 вчера - если вчера;
     * 09:21 1 янв. 2018 - иначе.
     *
     * @param Carbon|string $date
     * @return string
     */
    public static function adaptiveFormat(Carbon|string $date): string
    {
        if (($date = self::checkDate($date)) === false) {
            return '';
        }

        $now = now();

        /**
         * Сегодня.
         */
        if ($date->toDateString() == $now->toDateString()) {
            return $date->format('H:i');
        }

        /**
         * Вчера.
         */
        if ($date->toDateString() == $now->subDay()->toDateString()) {
            return $date->format('H:i ') . __('date.yesterday');
        }

        return $date->format('H:i') . ' ' . $date->format('j') . ' ' . self::getMonthName($date->month) . ' ' . $date->format('Y');
    }

    /**
     * Дополнение времени к дате.
     *
     * @param string $time
     * @param bool $space_nbsp
     * @return string
     */
    public static function concatenateTime(string $time, bool $space_nbsp = false): string
    {
        if (empty($time)) {
            return '';
        }

        $time = Carbon::parse($time);

        $space = $space_nbsp ? json_decode('"\u00A0"') : ' ';

        return $space . __('date.at') . $space . $time->format('H:i');
    }

    //****************************************************************
    //************** Получение составляющих даты *********************
    //****************************************************************

    /**
     * Получение названия месяца по номеру.
     *
     * @param int $number
     * @return string
     */
    public static function getMonthName(int $number): string
    {
        if ($number < 1 || $number > 12)
            return '';

        $months = ['янв.', 'февр.', 'мар.', 'апр.', 'мая', 'июня', 'июля', 'авг.', 'сент.', 'окт.', 'нояб.', 'дек.'];

        return $months[$number - 1];
    }

    /**
     * Получение названия месяца по номеру.
     *
     * @param int $start_index - начальный индекс массива.
     * @return array
     */
    public static function getFullMonths(int $start_index = 0): array
    {
        return [
            $start_index => 'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь',
        ];
    }

    /**
     * Получение полного названия месяца, опционально в сколнении и без.
     *
     * @param int $number
     * @param string $case
     * @return string
     */
    public static function getFullMonthNameInCase(int $number, string $case = 'prepositional'): string
    {
        if ($number < 1 || $number > 12) {
            return '';
        }

        switch ($case) {

            case 'sumple':
                $textMonth = [
                    'январь',
                    'февраль',
                    'март',
                    'апрель',
                    'май',
                    'июнь',
                    'июль',
                    'август',
                    'сентябрь',
                    'октябрь',
                    'ноябрь',
                    'декабрь',
                ];
                break;

            case 'prepositional':
                $textMonth = [
                    "январе",
                    "феврале",
                    "марте",
                    "апреле",
                    "мае",
                    "июне",
                    "июле",
                    "августе",
                    "сентябре",
                    "октябре",
                    "ноябре",
                    "декабре",
                ];
                break;

            case 'genitive':
                $textMonth = [
                    'января',
                    'февраля',
                    'марта',
                    'апреля',
                    'мая',
                    'июня',
                    'июля',
                    'августа',
                    'сентября',
                    'октября',
                    'ноября',
                    'декабря',
                ];

                break;

            case 'eng':
                return lcfirst((DateTime::createFromFormat('!m', $number))->format('F'));

            default:
                return '';
        }

        return $textMonth[$number - 1];
    }

    /**
     * Получение названия дня недели по номеру
     *
     * @param int $number
     * @param bool $is_ISO
     * @param bool $need_full
     * @return string
     */
    public static function getWeekName(int $number, bool $is_ISO = true, bool $need_full = false): string
    {
        if ($is_ISO) {
            $weekday = $need_full
                ? [
                    1 => "Понедельник",
                    2 => "Вторник",
                    3 => "Среда",
                    4 => "Четверг",
                    5 => "Пятница",
                    6 => "Суббота",
                    7 => "Воскресенье"
                ]
                : [
                    1 => "пн",
                    2 => "вт",
                    3 => "ср",
                    4 => "чт",
                    5 => "пт",
                    6 => "сб",
                    7 => "вс"
                ];
            $number = $number == 0 ? 7 : $number;
        } else {
            $weekday = $need_full
                ? [
                    0 => "Воскресенье",
                    1 => "Понедельник",
                    2 => "Вторник",
                    3 => "Среда",
                    4 => "Четверг",
                    5 => "Пятница",
                    6 => "Суббота"
                ]
                : [
                    0 => "вс",
                    1 => "пн",
                    2 => "вт",
                    3 => "ср",
                    4 => "чт",
                    5 => "пт",
                    6 => "сб"
                ];
        }

        return $weekday[$number];
    }

    //****************************************************************
    //************************ Другое ********************************
    //****************************************************************

    /**
     * Проверка даты в формате Carbon
     *
     * @param Carbon|string $date
     * @return bool
     */
    public static function checkCarbonDate(Carbon|string $date): bool
    {
        if ($date instanceof Carbon && $date->timestamp > 0) {
            return true;
        }

        return false;
    }

    /**
     * Вычисление разницы с текущей датой в указанных единицах.
     *
     * @param Carbon|string $date
     * @param string $time
     * @return float|int
     */
    public static function diffWithCurrentDate(Carbon|string$date, string $time = 'day'): float|int
    {
        if (($date = self::checkDate($date)) === false) {
            return 0;
        }

        $now = now();

        $k = $date < $now ? -1 : 1;

        $diff = $now->diff($date);

        return match ($time) {
            'hour' => ($diff->h + $diff->days * 24) * $k,
            default => $diff->days * $k,
        };
    }

    /**
     * Количество лет со дня рождения сервиса.
     *
     * @return int
     */
    public static function yearsFromSiteBirth(): int
    {
        return Carbon::parse('12.04.2012')->diffInYears(now());
    }

    /**
     * Получение списка лет для выбора года в фильтре.
     *
     * @return array
     */
    public static function getYearsSelectList(): array
    {
        $years = [];

        for ($year = 2018; $year <= date('Y'); $year++) {
            $years[$year] = $year;
        }

        return $years;
    }


    /**
     * Получение массива дат по заданным границам.
     *
     * @param Carbon|string $period_begin
     * @param Carbon|string $period_end
     * @return array
     */
    public static function getDaysList(Carbon|string $period_begin, Carbon|string $period_end): array
    {
        $period_begin = Carbon::parse($period_begin);
        $period_end = Carbon::parse($period_end);

        $num_days = $period_begin->diffInDays($period_end);
        $sub_date = $period_end;

        $list = [];

        for ($day = 0; $day <= $num_days; $day++) {

            $list[] = $sub_date->toDateString();

            $sub_date->subDay();
        }

        return $list;
    }

    /**
     * Получение массива периодов по заданным границам.
     *
     * @param Carbon|string $period_begin
     * @param Carbon|string $period_end
     * @param string|null $period_only
     * @return array
     */
     public static function getPeriodsList(Carbon|string $period_begin, Carbon|string $period_end, string $period_only = null): array
    {
        $periods = [];

        $period = Carbon::parse($period_begin)->endOfWeek();
        $end = Carbon::parse($period_end)->endOfWeek();

        while ($period->toDateString() <= $end->toDateString()) {

            $date_filter = self::getWeekPeriod($period);
            $period->addWeek();

            switch (self::isBorderWeekMonth(head($date_filter))) {
                case -1:
                    $periods[] = array_merge(self::getMonthPeriod(head($date_filter)), ['period' => 'month']);
                    $periods[] = array_merge($date_filter, ['period' => 'week']);
                    break;

                case 0:
                    $periods[] = array_merge($date_filter, ['period' => 'week']);
                    break;

                case 1:
                    $periods[] = array_merge(self::getMonthPeriod(Carbon::parse(head($date_filter))->subDays(7)), ['period' => 'month']);
                    $periods[] = array_merge($date_filter, ['period' => 'week']);
                    break;
            }
        }

        if (!empty($period_only)) {
            $periods = Arr::where($periods, function ($value) use ($period_only) {
                return $value['period'] == $period_only;
            });
        }

        return $periods;
    }

    /**
     * Получение периода недели.
     *
     * @param Carbon|string $date_point
     * @return array
     */
     public static function getWeekPeriod(Carbon|string $date_point): array
    {
        $date_point = Carbon::parse($date_point);

        return [
            'period_begin' => (clone $date_point)->startOfWeek()->toDateTimeString(),
            'period_end' => (clone $date_point)->endOfWeek()->toDateTimeString(),
        ];
    }

    /**
     * Получение периода месяца.
     *
     * @param Carbon|string $date_point
     * @return array
     */
     public static function getMonthPeriod(Carbon|string $date_point): array
    {
        $date_point = Carbon::parse($date_point);

        return [
            'period_begin' => (clone $date_point)->startOfMonth()->toDateTimeString(),
            'period_end' => (clone $date_point)->endOfMonth()->toDateTimeString(),
        ];
    }

    /**
     * Является ли неделя граничной в месяце.
     *
     * @param Carbon|string $date_point
     * @return int
     */
     public static function isBorderWeekMonth(Carbon|string $date_point): int
    {
        $week = self::getWeekPeriod($date_point);

        if (Carbon::parse($week['period_begin'])->endOfMonth()->day - 1 <= Carbon::parse($week['period_begin'])->day) {
            return -1;
        } else {
            if (Carbon::parse($week['period_begin'])->day <= 5) {
                return 1;
            }
        }

        return 0;
    }


    /**
     * Получение периода недели.
     *
     * @param array $days
     * @return array
     */
     public static function compilePeriodsByDays(array $days): array
    {
        $periods = [];

        $i = 0;
        while ($i < count($days)) {

            $day = Carbon::parse($days[$i]);

            if (empty($start)) {
                $start = clone $day;
                $last = clone $day;
                $cursor = clone $day;
                $cursor->addDay();
                $i++;
                continue;
            }

            /** @noinspection PhpUndefinedVariableInspection */
            if ($day->toDateString() == $cursor->toDateString()) {
                $cursor->addDay();
            } else {
                $periods[] = $start->toDateString() . ' - ' . $cursor->subDay()->toDateString();
                unset($start);
                continue;
            }

            $last = clone $day;
            $i++;

        }


        if (!empty($start)) {
            /** @noinspection PhpUndefinedVariableInspection */
            $periods[] = $start->toDateString() . ' - ' . $last->toDateString();
        }


        return $periods;
    }

    //****************************************************************
    //************************** Support *****************************
    //****************************************************************

    /**
     * Проверка даты.
     *
     * @param Carbon|string|null $date
     * @return bool|Carbon
     */
    protected static function checkDate(Carbon|string|null $date): bool|Carbon
    {
        if (empty($date)) {
            return false;
        }

        /**
         * Если карбон, проверка на правильную дату.
         */
        if ($date instanceof Carbon) {

            if ($date->timestamp <= 0) {
                return false;
            }

            return $date;
        }

        /**
         * Если не карбон, попытка распарсить дату.
         */
        $date = Carbon::parse($date);

        if ($date->timestamp <= 0) {
            return false;
        }

        return $date;
    }

}
