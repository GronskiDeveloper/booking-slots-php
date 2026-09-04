# Case study - jak powstał `booking-slots-php`

Krótka retrospektywa: dlaczego postawiłem osobną bibliotekę na jeden problem, którego 90% frameworków „załatwia" pluginem, i gdzie AI faktycznie pomogło.

## Brief (30 sekund)

Generator wolnych slotów rezerwacyjnych dla systemów bookingu (gabinet medyczny, salon beauty, warsztat). Dwa wejścia, jedno wyjście:

- **Wejście 1:** godziny pracy (business hours) - np. „pon-pt 9-17, sob 10-14"
- **Wejście 2:** istniejące rezerwacje (Slot value objects z start/duration)
- **Wyjście:** tablica wolnych slotów o zadanej długości i granularity

Trzy twarde wymagania:

1. **Zero runtime dependencies.** Composer `require:` tylko `php >=8.1`. Żadnego `nesbot/carbon`, `symfony/*`, `guzzle/*`. Dla PSR-log opcjonalnie via dependency injection, jeżeli klient sam wciśnie.
2. **Framework-agnostic.** Ma działać w Laravel, w Symfony, w plain PHP i w moim proxy z `claude-chat-widget`. Klasa `SlotGenerator` przyjmuje POPO value objects; nie ma zewnętrznych I/O ani statica.
3. **Poprawność, nie „szybkość".** Domyślnie liczy z uwzględnieniem DST, śmiesznie krótkich slotów (5 min), i krawędzi typu „ostatni slot dnia kończy się dokładnie w minutę zamknięcia".

## Gdzie faktycznie zdarzyła się praca człowieka

### 1. Model domenowy - `Slot` jako value object (człowiek)

`Slot` to `readonly class` z konstruktorem `(DateTimeImmutable $start, int $durationMinutes)` i wyliczonym `$end`. Namespace `Grodev\BookingSlots\Slot`. Zero setterów.

Ta decyzja jest oczywista *jeśli* wiesz, że rezerwacje muszą być porównywalne wartością (nie referencją), i że przypadkowo zmutowany slot w środku pętli generatora to godzina debuggu. Nie każdy dev z automatu wybiera immutability; ja wybieram, bo booking to domena gdzie mutacja = fałszywe wolne miejsce = klient nie może zarezerwować.

### 2. Algorytm - sortowanie + skanowanie liniowe (człowiek)

Naiwna implementacja: dla każdego kandydackiego slotu, iteruj po wszystkich istniejących - `O(n²)`. Nie skaluje przy 300+ rezerwacjach dziennych.

Właściwa: sortuj istniejące po `start`, potem jednym pass-em skanuj kandydatów - `O(n log n)` przez sort + `O(n+m)` przez skan. To decyzja, którą podejmujesz zanim napiszesz linijkę - jak nie, kod się refactoruje w bólach dwa tygodnie później.

### 3. AI pomogło z testami krawędziowymi (Claude, ~30 min)

Kod produkcyjny napisałem sam - kilka klas, ~200 linii. Ale case study testowe to coś, gdzie AI shines: „wygeneruj mi PHPUnit test data providery dla 20 krawędzi typu:

- pierwszy slot dnia
- ostatni slot dnia
- slot przecinający południe (jeśli byłaby przerwa)
- slot na styku dwóch rezerwacji
- rezerwacja dokładnie w środku okna
- rezerwacja pokrywająca całe okno pracy
- DST spring-forward (utracona godzina)
- DST fall-back (podwójna godzina)
- ..."

Claude wygenerował, ja odrzuciłem 3 błędne (halucynacje o metodach), zostawiłem 17. To *wielokrotnie* szybsze niż napisanie ręcznie i realnie testy złapały 2 błędy w pierwszym mojej implementacji.

## Gdzie to się sprawdza

- Systemy rezerwacji na jedno stanowisko (dentysta, fryzjer, jedno łóżko fizjoterapii)
- Booking urządzeń (rezerwacja sali konferencyjnej, projektora, samochodu firmowego)
- Dowolny widget rezerwacji `<Calendar />` gdzie backend musi zwrócić „które godziny są wolne dziś?"

## Kiedy nie ta biblioteka

- **Multi-resource** (jednoczesne rezerwacje wielu pracowników / stanowisk) - to wymaga rozszerzenia albo drugiej warstwy scheduling. Ta biblioteka celowo pilnuje jednego zasobu.
- **Skomplikowana logika biznesowa** (długość zależna od typu usługi, oscylacja czasu przygotowania) - biblioteka jest low-level, wysokopoziomowa reguła po Twojej stronie.
- **Timezone-heavy applications** (klient rezerwuje z Nowego Jorku, salon w Warszawie) - działa, ale przekazujesz `DateTimeImmutable` z odpowiednim `DateTimeZone`. Biblioteka nie konwertuje.

## Kontekst szerszy

Ta biblioteka jest **kręgosłupem** mojego demo [`booking-ai-demo`](https://github.com/GronskiDeveloper/booking-ai-demo) (system rezerwacji z asystentem AI), a to demo jest kręgosłupem moich wdrożeń klienckich dla gabinetów medycznych i salonów beauty. To nie „coś co zrobiłem raz i wrzuciłem" - to kod, którego sam używam.

## Wersje i kontakt

Composer `grodev/booking-slots-php` (za chwilę na Packagist). MIT. Repo utrzymywane.

Wdrożenie systemu rezerwacji pod branding klienta (nie tylko silnik slotów - całe UX): [dominik@grodev.pl](mailto:dominik@grodev.pl) · [grodev.pl/system-rezerwacji-online](https://grodev.pl/system-rezerwacji-online).
