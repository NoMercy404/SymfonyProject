# Instrukcja zadania – HIS-System – Moduł Czasu Pracy

## 1. Parametry konfiguracyjne systemu czasu pracy

> Sposób przechowywania parametrów jest dowolny (np. plik konfiguracyjny, tabela w bazie danych).

- **Norma miesięczna godzin:** `40`
- **Stawka godzinowa:** `20 PLN`
- **Stawka nadgodzinowa:** `200% stawki`
  - Nadgodziny są liczone **po przekroczeniu normy miesięcznej**

---

## 2. Encja: `Pracownik`

| Pole                | Typ danych | Opis                      |
|---------------------|------------|---------------------------|
| `id`                | `UUID`     | Unikalny identyfikator    |
| `imie_nazwisko`     | `string`   | Imię i nazwisko pracownika |

---

## 3. Encja: `CzasPracy`

| Pole                 | Typ danych | Opis |
|----------------------|------------|------|
| `pracownik_id`       | `UUID`     | Relacja do pracownika |
| `data_rozpoczecia`   | `datetime` | Data i godzina rozpoczęcia pracy |
| `data_zakonczenia`   | `datetime` | Data i godzina zakończenia pracy |
| `dzien_rozpoczecia`  | `date`     | Dzień pracy, ustalany na podstawie `data_rozpoczecia` |

> Przykład:  
> Jeśli pracownik zarejestrował przedział **01.01.1970 08:00 – 01.01.1970 14:00**,  
> to pole `dzien_rozpoczecia` powinno zawierać wartość **"1970-01-01"**  
> (czyli **zawsze data z początku pracy**).

---

## 4. Zasady projektowe

- Wszystkie wymienione pola są **wymagane**
- Encje mogą być rozszerzane o dodatkowe pola zgodnie z doświadczeniem lub potrzebą
- Typy danych nieokreślone w zadaniu można dobrać według uznania

---

## 5. Endpoint: **Tworzenie pracownika**

- **Metoda:** `POST`
- **Opis:** Tworzy nowego pracownika
- **Odpowiedź:** Zwraca `UUID` nowo utworzonego pracownika

---

## 6. Endpoint: **Rejestracja czasu pracy**

- **Metoda:** `POST`
- **Dane wejściowe:**
  - `pracownik_id` (UUID)
  - `data_rozpoczecia` (datetime)
  - `data_zakonczenia` (datetime)

- **Walidacja:**
  - Pracownik **może mieć tylko jeden przedział czasowy** z tym samym `dzien_rozpoczecia`
  - Maksymalny dozwolony czas jednego przedziału: **12 godzin**

- **Odpowiedź:**
  - Jeśli sukces: `Czas pracy został dodany!`
  - W przypadku błędu: odpowiedni komunikat z informacją o przyczynie

---

## 7. Endpoint: **Podsumowanie czasu pracy (dzień/miesiąc)**

- **Metoda:** `GET`
- **Dane wejściowe:**
  - `pracownik_id` (UUID)
  - `data` (w formacie `YYYY-MM` lub `YYYY-MM-DD`)

- **Odpowiedź:**
  - **Sukces:**
    - Ilość przepracowanych godzin w podanym zakresie
    - Wartość wypracowanych godzin (stawka * czas)
    - Podział:
      - godziny standardowe
      - nadgodziny (liczone po przekroczeniu normy)
  - **Zaokrąglenie czasu pracy:** do **najbliższych 30 minut**
    - Przykłady:
      - `8:10` → `8.0 godz.`
      - `8:17` → `8.5 godz.`
      - `8:35` → `8.5 godz.`
      - `8:48` → `9.0 godz.`

  - **Niepowodzenie:** odpowiedni komunikat błędu

---

## Uwagi dodatkowe

- Możesz zaimplementować system logowania oraz podstawową autoryzację (np. JWT lub session-based).
- Dobrym rozszerzeniem może być dashboard dla użytkownika z wizualizacją danych (np. wykres godzin z Chart.js).
