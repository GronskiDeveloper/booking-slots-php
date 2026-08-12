# Booking Slots (PHP)

[![License: MIT](https://img.shields.io/badge/License-MIT-1D9E75.svg?style=flat-square)](LICENSE) [![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net) [![Laravel-ready](https://img.shields.io/badge/Laravel-ready-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com) [![GroDev](https://img.shields.io/badge/by-GroDev-534AB7?style=flat-square)](https://grodev.pl/system-rezerwacji-online)


A tiny, **dependency-free** available-slot generator for booking systems. Give it opening hours, a slot length, an optional turnaround buffer and the bookings you already have — get back the free slots a customer can book.

Framework-agnostic (plain PHP, **Laravel**, Symfony…), PHP 8.1+, immutable, fully typed.

Built by **[GroDev](https://grodev.pl)** — a studio that builds **custom booking systems you own** (no per-booking commission, no per-seat SaaS fee). See [grodev.pl/system-rezerwacji-online](https://grodev.pl/system-rezerwacji-online).

## Why

Every booking product needs the same core question answered: *given my hours and my existing bookings, what's still free?* This library answers exactly that — correctly handling slot length, a buffer around bookings (cleaning/turnaround), a lead time (`notBefore`), and overlap edge cases — and nothing else. Wire it into any storage, any framework.

## Install

```bash
composer require grodev/booking-slots
```

Or just copy `src/Slot.php` and `src/SlotGenerator.php` into your project.

## Usage

```php
use GroDev\Booking\SlotGenerator;

// Salon open 09:00–13:00, 30-min slots, 10-min turnaround buffer.
$generator = new SlotGenerator(slotMinutes: 30, bufferMinutes: 10);

$slots = $generator->generate(
    opensAt:  new DateTimeImmutable('2026-09-01 09:00'),
    closesAt: new DateTimeImmutable('2026-09-01 13:00'),
    busy: [
        [new DateTimeImmutable('2026-09-01 10:00'), new DateTimeImmutable('2026-09-01 10:30')],
    ],
    notBefore: new DateTimeImmutable('now'), // optional lead time
);

foreach ($slots as $slot) {
    echo $slot->label() . "\n"; // "09:00–09:30"
}
```

Output (the 10-minute buffer correctly frees only what's truly bookable):

```
09:00–09:30
11:00–11:30
11:30–12:00
12:00–12:30
12:30–13:00
```

Run it yourself: `php examples/basic.php`.

## Laravel example

```php
// app/Http/Controllers/AvailabilityController.php
use GroDev\Booking\SlotGenerator;

public function __invoke(Request $request)
{
    $date = CarbonImmutable::parse($request->query('date'));

    $busy = Booking::whereDate('starts_at', $date)
        ->get(['starts_at', 'ends_at'])
        ->map(fn ($b) => [
            new DateTimeImmutable($b->starts_at),
            new DateTimeImmutable($b->ends_at),
        ])
        ->all();

    $slots = (new SlotGenerator(slotMinutes: 30, bufferMinutes: 10))->generate(
        opensAt:   $date->setTime(9, 0),
        closesAt:  $date->setTime(17, 0),
        busy:      $busy,
        notBefore: now()->addHours(2), // no bookings in the next 2h
    );

    return response()->json(array_map(fn ($s) => $s->toArray(), $slots));
}
```

Your frontend (or a Three.js/vanilla booking widget) then renders the JSON slots.

## API

**`new SlotGenerator(int $slotMinutes = 30, int $bufferMinutes = 0)`**

**`generate(DateTimeImmutable $opensAt, DateTimeImmutable $closesAt, array $busy = [], ?DateTimeImmutable $notBefore = null): Slot[]`**
- `$busy` — list of `[start, end]` `DateTimeImmutable` pairs.
- `$notBefore` — earliest bookable moment (lead time).

**`Slot`** — readonly value object: `->start`, `->end`, `->minutes()`, `->label()`, `->toArray()`.

## Production note

This handles availability. A real booking system also needs: atomic booking (to avoid double-booking under concurrency — wrap the insert in a transaction with a uniqueness constraint), deposits/prepayment against no-shows, reminders, and per-staff/per-resource calendars. That's exactly what a custom system gives you — and you own it.

## Own your booking system

Booksy/Reservio take 5–15% commission or a per-seat fee — thousands per year, forever, and you never own it. A custom system is a one-time build that pays for itself in months. See [grodev.pl/system-rezerwacji-online](https://grodev.pl/system-rezerwacji-online) or reach out at **[grodev.pl](https://grodev.pl)**.

## License

MIT.

---

*Made by [Dominik Groński / GroDev](https://grodev.pl) · Poznań, Poland · Laravel · PHP · booking systems*
