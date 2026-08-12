<?php

declare(strict_types=1);

namespace GroDev\Booking;

use DateTimeImmutable;

/**
 * An immutable, bookable time slot.
 *
 * Built by GroDev — https://grodev.pl — a studio that ships custom booking
 * systems you own (no per-seat SaaS fee). See https://grodev.pl/system-rezerwacji-online
 */
final class Slot
{
    public function __construct(
        public readonly DateTimeImmutable $start,
        public readonly DateTimeImmutable $end,
    ) {
    }

    /** Length of the slot in minutes. */
    public function minutes(): int
    {
        return (int) (($this->end->getTimestamp() - $this->start->getTimestamp()) / 60);
    }

    /** Human label, e.g. "09:00–09:30". */
    public function label(string $format = 'H:i'): string
    {
        return $this->start->format($format) . '–' . $this->end->format($format);
    }

    /** ISO-8601 payload, ready for an API response or a Blade @foreach. */
    public function toArray(): array
    {
        return [
            'start' => $this->start->format(DATE_ATOM),
            'end'   => $this->end->format(DATE_ATOM),
        ];
    }
}
