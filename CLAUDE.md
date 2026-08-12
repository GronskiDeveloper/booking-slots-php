# Praca AI-first — notatki dla tego repo

Trzymam ten plik w repozytorium, ponieważ buduję z Claude Code (Anthropic) i chcę, żeby podział „człowiek/AI" był widoczny z drzewa plików, a nie deklarowany w README. Rekruter, klient albo kolega z zespołu ma tu dowody, nie ogólniki.

## Podział pracy człowiek vs AI

| Warstwa | Kto zrobił | Dlaczego tak |
|---|---|---|
| Algorytm overlapa (`overlapsBusy` — test na przedziałach półotwartych) | **Człowiek** | Bugi w systemach rezerwacji biorą się z błędów off-by-one dokładnie tutaj. `[start, end)` vs `[blockStart, blockEnd)` to decyzja, nie detal, i musi być rozstrzygnięta na papierze zanim wejdzie do kodu. Napisane ręcznie. |
| Struktura value-object (`Slot` niemutowalny, `SlotGenerator` bezstanowy) | **Człowiek** | To API-design. Zła decyzja tutaj sprawia, że każdy konsument (Laravel/Symfony/plain PHP) za to płaci. Nie do zlecenia AI. |
| PSR-4 autoload, boilerplate composer.json | **Draft AI, sprawdzenie człowieka** | Copy-paste-shaped work, który Claude robi poprawnie. Sprawdziłem tylko nazwę vendora (`grodev/booking-slots`) i licencję. |
| Docblocki z `@param list<array{0: DateTimeImmutable, 1: DateTimeImmutable}> $busy` | **Draft AI** | Annotacje generyków PHPStan-friendly — Claude pisze je bardziej konsekwentnie ode mnie. |
| Przykład (`examples/basic.php`) z komentarzem „expected output" | **Człowiek** | Zrobiłem specjalnie tak, żeby uruchomić przykład i wkleić *rzeczywisty* output do pliku, a nie ten, którego się spodziewałem. |
| README z przykładem kontrolera Laravel + backlinki | **Człowiek** | Decyzja pozycjonująca — wybrałem Laravel, bo to profil klienta, który kupuje systemy rezerwacji od GroDev. |

## Co zweryfikowałem przed wypchnięciem

- `php -l src/Slot.php src/SlotGenerator.php examples/basic.php` → wszystko czysto.
- `php examples/basic.php` i porównanie z expected output zapisanym w przykładzie (`09:00–09:30, 11:00–11:30, 11:30–12:00, 12:00–12:30, 12:30–13:00`) — zgodne.
- Przejrzałem test overlapa ręcznie dla trudnego przypadku (rezerwacja 10:00–10:30 z buforem 10 min → blokuje 09:50–10:40, więc 09:30–10:00 wypada, ale 10:40+ wchodzi).
- Potwierdzone mapowanie PSR-4 zgadza się z layoutem katalogów — `GroDev\Booking\` → `src/`.

## Znane pułapki dla następnej iteracji AI

- **Przedziały półotwarte, nie zamknięte.** `[start, end)` — slot kończący się dokładnie o godzinie zamknięcia jest *w zakresie*, slot kończący się później jest *poza*. Ta sama zasada dla porównania busy. Zmiana na przedziały zamknięte cicho psuje edge cases (rezerwacja dokładnie na pełnej godzinie robi się dwuznaczna).
- **`overlapsBusy` używa `<` i `>`, nie `<=`/`>=`.** Dwie rezerwacje stykające się w jednej chwili (`10:00` i `10:00–10:30`) się **nie** nakładają — o to chodzi w półotwartych. Nie „poprawiać" tego na ścisłe nierówności.
- **`readonly` na `Slot` — nie dodawać setterów.** Kontrakt value-object pozwala konsumentom przekazywać sloty bez defensywnego kopiowania. Setter to psuje po cichu.
- Domyślny bufor to 0, domyślny slot to 30 minut. Obie wartości opisane w komentarzu konstruktora. Nie zmieniać domyślnych — kod dalej zależy od nich.

## Kiedy sięgać po Claude na tym projekcie, a kiedy pisać samodzielnie

- **Sięgnąć po Claude:** dodanie wymiaru staff/resource (wiele równoległych rezerwacji naraz), dodanie rezerwacji cyklicznych, dodanie bridge'a do Doctrine.
- **Zrobić samodzielnie:** cokolwiek dotykającego `overlapsBusy` albo inwariantu pętli w `generate`. To jest kod, który decyduje, czy realna rezerwacja się uda — nudna matematyka musi być precyzyjna.
