# Zgłaszanie podatności

Bezpieczeństwo tego projektu jest dla mnie ważne — jeśli znalazłeś podatność, zgłoś ją **prywatnie** zamiast otwierać publicznego issue.

## Kanały zgłoszenia

- **Preferowany:** [Security Advisory na GitHubie](https://github.com/GronskiDeveloper/booking-slots-php/security/advisories/new) (prywatny, tylko dla mnie do przejrzenia).
- **Alternatywnie:** e-mail bezpośrednio na **dominik@grodev.pl** z tematem `[SECURITY] booking-slots-php`.

## Co warto zawrzeć w zgłoszeniu

- Opis podatności (co jest do wykorzystania, jak).
- Kroki reprodukcji (albo minimalny PoC).
- Ocena wpływu (co atakujący może zrobić — kradzież danych, wykonanie kodu, DoS itd.).
- Ewentualnie sugerowany fix.

## Reakcja

- **Potwierdzenie odbioru:** w ciągu 72h.
- **Wstępna ocena:** w ciągu 7 dni.
- **Fix + release:** zależnie od skali (krytyczne — priorytetowo).

Podziękuję imiennie w release notes / CHANGELOG (o ile nie prosisz o anonimowość).


## Kontekst tego projektu

To biblioteka matematyczna — najkrytyczniejsze podatności to **błędy logiki, które prowadzą do zdublowanych rezerwacji** (dwie osoby na ten sam slot) lub **odmowy usługi** (endless loop w `generate`). Nie jest to typowa 'security' podatność w OWASP-owym znaczeniu, ale w systemie rezerwacji ma dokładnie te same konsekwencje: utracone pieniądze i zaufanie klienta.

Autor: [Dominik Groński / GroDev](https://grodev.pl)
