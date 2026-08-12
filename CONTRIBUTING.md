# Jak współtworzyć — Booking Slots (PHP)

Chętnie przyjmę pull requesty i sensowne issue.

## Zanim otworzysz issue

1. **Sprawdź [otwarte issue](https://github.com/GronskiDeveloper/booking-slots-php/issues)** — może już ktoś to zgłosił.
2. **Sprawdź [`CLAUDE.md`](CLAUDE.md)** — sekcja *Znane pułapki* i *Kiedy sięgać po Claude* wyjaśnia projekt design decisions. Jeśli Twoje zgłoszenie idzie wbrew którejś z tych zasad, opisz dlaczego — nie zamykam takich propozycji z automatu, ale trzeba to uzasadnić.
3. **Zgłoszenia bezpieczeństwa** → NIE otwieraj publicznego issue, patrz [`SECURITY.md`](SECURITY.md).

## Pull requesty

- **Małe, skupione zmiany** — jeden PR = jeden temat. Refactor + fix + feature w jednym PR trudno zrecenzować.
- **Trzymaj się konwencji z repo** — spójrz na istniejący kod przed napisaniem swojego. Style zgodne z resztą projektu (bez własnego formatowania „na siłę").
- **Test manualny obowiązkowo, jeśli zmieniasz działanie** — opisz w opisie PR co uruchomiłeś i jaki był wynik. Sam `lint` przechodzący nie wystarczy.
- **Aktualizuj `CLAUDE.md`, jeśli zmieniasz założenia projektu** — np. dodajesz nowy niezmiennik do sekcji *Known gotchas*.

## Praca z AI (dowolny model — Claude, GitHub Copilot, Cursor)

Nie ukrywaj tego. Jeśli używałeś AI do wygenerowania draftu:

- **Uczciwie o tym napisz** w opisie PR: „Draft wygenerowany przez [narzędzie], zaudytowany ręcznie przed pushem".
- **Zweryfikuj każdą linię, którą podpisujesz swoim commit.** LLM potrafi napisać kod prawdopodobnie wyglądający, który sypie w runtime — Twoja odpowiedzialność jako autora PR to złapanie tego przed pushem.
- **Nie mieszaj wygenerowanego draftu z ręcznymi zmianami w tym samym commicie** — trudno zrecenzować, co pochodzi skąd.

Ten projekt sam jest budowany [AI-first](CLAUDE.md) — więc dokumentowany workflow jest tu wartością, nie problemem.

## Kontakt

Pytania: dominik@grodev.pl.

Autor: [Dominik Groński / GroDev](https://grodev.pl)
