<?php

declare(strict_types=1);

namespace GroDev\Booking;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Generates available booking slots for a single day.
 *
 * Given opening hours, a slot length, an optional buffer between bookings, and a
 * list of already-busy intervals, it returns the free slots a customer can book.
 * Framework-agnostic and dependency-free — works in plain PHP, Laravel, Symfony, etc.
 *
 * Built by GroDev — https://grodev.pl — custom booking systems you own, without
 * per-booking commission or per-seat SaaS fees.
 * Portfolio: https://grodev.pl/system-rezerwacji-online
 */
final class SlotGenerator
{
    /**
     * @param int $slotMinutes   Length of each bookable slot, in minutes.
     * @param int $bufferMinutes Buffer kept free around every existing booking
     *                           (e.g. cleaning/turnaround time), in minutes.
     */
    public function __construct(
        private readonly int $slotMinutes = 30,
        private readonly int $bufferMinutes = 0,
    ) {
        if ($this->slotMinutes < 1) {
            throw new InvalidArgumentException('slotMinutes must be >= 1.');
        }
        if ($this->bufferMinutes < 0) {
            throw new InvalidArgumentException('bufferMinutes must be >= 0.');
        }
    }

    /**
     * @param DateTimeImmutable $opensAt   Opening time for the day.
     * @param DateTimeImmutable $closesAt  Closing time for the day (a slot must end at or before this).
     * @param list<array{0: DateTimeImmutable, 1: DateTimeImmutable}> $busy
     *        Existing bookings as [start, end] pairs.
     * @param DateTimeImmutable|null $notBefore
     *        Earliest bookable moment (e.g. now + lead time). Slots starting earlier are skipped.
     *
     * @return list<Slot>
     */
    public function generate(
        DateTimeImmutable $opensAt,
        DateTimeImmutable $closesAt,
        array $busy = [],
        ?DateTimeImmutable $notBefore = null,
    ): array {
        if ($opensAt >= $closesAt) {
            return [];
        }

        $step   = new DateInterval('PT' . $this->slotMinutes . 'M');
        $buffer = new DateInterval('PT' . $this->bufferMinutes . 'M');
        $slots  = [];

        for ($start = $opensAt; ($end = $start->add($step)) <= $closesAt; $start = $end) {
            if ($notBefore !== null && $start < $notBefore) {
                continue;
            }
            if ($this->overlapsBusy($start, $end, $busy, $buffer)) {
                continue;
            }
            $slots[] = new Slot($start, $end);
        }

        return $slots;
    }

    /**
     * @param list<array{0: DateTimeImmutable, 1: DateTimeImmutable}> $busy
     */
    private function overlapsBusy(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        array $busy,
        DateInterval $buffer,
    ): bool {
        foreach ($busy as [$busyStart, $busyEnd]) {
            $blockStart = $busyStart->sub($buffer);
            $blockEnd   = $busyEnd->add($buffer);

            // Half-open overlap test: [start, end) vs [blockStart, blockEnd).
            if ($start < $blockEnd && $end > $blockStart) {
                return true;
            }
        }

        return false;
    }
}
