# AI workflow notes — Booking Slots (PHP)

Kept in the repo because I build with Claude Code (Anthropic) and want the AI/human split legible from the source tree, not just claimed in a README.

## Human vs AI split on this repo

| Layer | Who did it | Why |
|---|---|---|
| The overlap algorithm (`overlapsBusy` — half-open interval test) | **Human** | Booking bugs come from off-by-one errors here. `[start, end)` vs `[blockStart, blockEnd)` is a decision, not a detail, and it has to be right on paper before it goes in code. I wrote it. |
| Value-object structure (`Slot` immutable, `SlotGenerator` stateless) | **Human** | This is API-design. Getting it wrong means every Laravel/Symfony/plain-PHP consumer pays for it. Not delegable. |
| PSR-4 autoload wiring, composer.json boilerplate | **AI-drafted, human-checked** | Copy-paste-shaped work Claude does correctly. I only verified the vendor name (`grodev/booking-slots`) and the license match. |
| Docblocks with `@param list<array{0: DateTimeImmutable, 1: DateTimeImmutable}> $busy` | **AI-drafted** | PHPStan-friendly generics annotations — Claude writes these more consistently than I do. |
| Example (`examples/basic.php`) with an expected-output comment block | **Human** | Made a point of running the example and pasting the *actual* output into the file, not what I hoped it would produce. |
| README with Laravel controller example + backlinks | **Human** | Positioning decision — chose Laravel because that's who buys booking systems from GroDev. |

## What I verified before pushing

- `php -l src/Slot.php src/SlotGenerator.php examples/basic.php` → all clean.
- `php examples/basic.php` and compared to the expected output written in the example (`09:00–09:30, 11:00–11:30, 11:30–12:00, 12:00–12:30, 12:30–13:00`) — matched.
- Walked through the overlap test by hand for the tricky case (booking 10:00–10:30 with 10-min buffer → blocks 09:50–10:40, so 09:30–10:00 is out but 10:40+ is in).
- Confirmed PSR-4 mapping matches directory layout — `GroDev\Booking\` → `src/`.

## Known gotchas for the next AI edit

- **Half-open intervals, not closed.** `[start, end)` — a slot ending exactly at the closing time is *in bounds*, a slot ending after is *out*. Same rule for the busy comparison. Changing this to closed intervals silently breaks edge cases (a booking exactly at the top of the hour becomes ambiguous).
- **`overlapsBusy` uses `<` and `>`, not `<=`/`>=`.** Two bookings that touch at exactly one instant (`10:00` and `10:00–10:30`) do not overlap — that's the whole point of half-open. Don't "helpfully" tighten it.
- **`readonly` on `Slot` — do not add setters.** The value-object contract lets consumers pass slots around without defensive copies. Adding a setter breaks it silently.
- Buffer default is 0, slot default is 30 minutes. Both are documented in the constructor comment. Don't change defaults — downstream code depends on them.

## When to reach for Claude on this project vs code it yourself

- **Reach for Claude:** adding staff/resource dimension (multiple concurrent bookings), adding recurring bookings, adding a Doctrine bridge.
- **Do it yourself:** anything touching `overlapsBusy` or the loop invariant in `generate`. This is the code that determines whether a real booking succeeds — the boring math has to be exact.
