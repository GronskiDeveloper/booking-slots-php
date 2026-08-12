---
description: Weryfikacja niezmienników matematycznych po każdej zmianie w SlotGenerator
---

Recenzujesz zmianę w `src/SlotGenerator.php` albo `src/Slot.php`. To jest kod, który decyduje, czy realna rezerwacja się uda — off-by-one tutaj kosztuje klienta zdublowaną rezerwację albo utraconą sprzedaż.

Zanim zaakceptujesz diff, upewnij się, że wszystkie sześć poniższych warunków zachodzi:

1. **Przedziały są półotwarte, `[start, end)`, nie zamknięte.** Slot kończący się dokładnie o `closesAt` jest *w zakresie*. Slot kończący się po jest *poza*. Ta sama zasada dla porównania busy.
2. **`overlapsBusy` używa `<` i `>`, nie `<=`/`>=`.** Dwie rezerwacje stykające się w jednej chwili (booking 10:00–10:30 i wolny slot 10:30–11:00) się **nie** nakładają. Jeśli diff używa `<=` — odrzucić.
3. **`readonly` na `Slot` — brak setterów, brak metod mutujących.** Value-object kontrakt musi trzymać. Cokolwiek dodaje setter — odrzucić.
4. **Bufor stosuje się symetrycznie** — `busyStart->sub(buffer)` i `busyEnd->add(buffer)`. Nie „tylko przed" ani „tylko po".
5. **`generate` zwraca `list<Slot>`, nie asocjacyjny array.** Kolejność ma znaczenie (kalendarz UI zakłada chronologię).
6. **Testowany przypadek graniczny w komentarzu `examples/basic.php`** — jeśli diff zmienia algorytm, expected output musi być przeliczony ręcznie i zaktualizowany. Automatyczne generowanie „oczekiwanego" wyniku przez ten sam kod, który zmieniasz, nie jest testem.

Jeśli którykolwiek warunek pęka — zablokuj zmianę i wróć z konkretnym przypadkiem, który się złamie.

Bonus: jeśli diff dodaje wymiar (staff, resource, cyklika), sprawdź, czy nie melduje starych rezerwacji z nowym wymiarem — czyli czy migracja danych jest przewidziana.
