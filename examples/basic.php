<?php

declare(strict_types=1);

use GroDev\Booking\SlotGenerator;

require __DIR__ . '/../src/Slot.php';
require __DIR__ . '/../src/SlotGenerator.php';

// A salon open 09:00–13:00, 30-minute slots, 10-minute turnaround buffer.
$generator = new SlotGenerator(slotMinutes: 30, bufferMinutes: 10);

$opensAt  = new DateTimeImmutable('2026-09-01 09:00');
$closesAt = new DateTimeImmutable('2026-09-01 13:00');

// One existing booking 10:00–10:30.
$busy = [
    [new DateTimeImmutable('2026-09-01 10:00'), new DateTimeImmutable('2026-09-01 10:30')],
];

$slots = $generator->generate($opensAt, $closesAt, $busy);

echo "Available slots for 2026-09-01:\n";
foreach ($slots as $slot) {
    echo '  ' . $slot->label() . "\n";
}

// Expected: 09:00–09:30, 09:30–10:00 blocked-by-buffer? (09:30 ends 10:00, buffer pushes
// block to 09:50–10:40) => 09:30–10:00 overlaps 09:50 block, so it's removed.
// Free: 09:00–09:30, 10:40+ ... => 11:00–11:30, 11:30–12:00, 12:00–12:30, 12:30–13:00.
