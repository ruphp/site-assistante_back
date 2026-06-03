<?php

namespace app\Application\Cron;

final class CronLogPeriod
{
    public function monday(string $date): string
    {
        $timestamp = strtotime($date);
        $start = date('w', $timestamp) == 1 ? $timestamp : strtotime('last monday', $timestamp);

        return date('Y-m-d', $start);
    }

    public function sunday(string $date): string
    {
        $timestamp = strtotime($date);
        $start = date('w', $timestamp) == 1 ? $timestamp : strtotime('last monday', $timestamp);

        return date('Y-m-d', strtotime('next sunday', $start));
    }

    public function quarterStart(int $timestamp): string
    {
        $quarter = (int)((date('n', $timestamp) - 1) / 3 + 1);
        $year = date('Y', $timestamp);

        return date('Y-m-d', mktime(0, 0, 0, ($quarter - 1) * 3 + 1, 1, (int)$year));
    }

    public function quarterEnd(int $timestamp): string
    {
        $quarter = (int)((date('n', $timestamp) - 1) / 3 + 1);
        $year = date('Y', $timestamp);

        return date('Y-m-d', mktime(0, 0, 0, $quarter * 3 + 1, 0, (int)$year));
    }
}
