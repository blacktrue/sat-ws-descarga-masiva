<?php

declare(strict_types=1);

namespace PhpCfdi\SatWsDescargaMasiva\Shared;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

class DateTime
{
    /** @var DateTimeImmutable */
    private $value;

    public function __construct($value = null)
    {
        if (is_int($value)) {
            $value = '@' . $value;
        }
        if (null === $value || is_string($value)) {
            $value = new DateTimeImmutable($value ?? 'now');
        }
        if (! $value instanceof DateTimeImmutable) {
            throw new InvalidArgumentException('Unable to create a Datetime');
        }
        $this->value = $value;
    }

    public static function now(): self
    {
        return new self();
    }

    public function formatSat(): string
    {
        return $this->formatTimeZone('Z');
    }

    public function format(string $format, string $timezone = ''): string
    {
        if (empty($timezone)) {
            $timezone = date_default_timezone_get();
        }

        return $this->value->setTimezone(new DateTimeZone($timezone))->format($format);
    }

    public function formatDefaultTimeZone(): string
    {
        return $this->formatTimeZone(date_default_timezone_get());
    }

    public function formatTimeZone(string $timezone): string
    {
        return $this->value->setTimezone(new DateTimeZone($timezone))->format('Y-m-d\TH:i:s.000T');
    }

    public function modify(string $modify): self
    {
        return new self($this->value->modify($modify));
    }

    public function compareTo(self $otherDate): int
    {
        return $this->formatSat() <=> $otherDate->formatSat();
    }

    public function equalsTo(self $expectedExpires): bool
    {
        return $this->formatSat() === $expectedExpires->formatSat();
    }
}
