Instrukcja zadania:
1. Parametry konfiguracyjne systemu czasu pracy (sposób przechowywania
parametrów jest dowolny):
  a. norma miesięczna godzin - 40
  b. stawka - 20 PLN
  c. stawka nadgodzinowa - 200% stawki
    i. liczone po przekroczeniu normy miesięcznej

2. Encja Pracownik
  a. unikalny identyfikator - uuid
  b. imię i nazwisko

3. Encja Czas pracy
  a. relacja z pracownikiem
  b. data i godzina rozpoczęcia - datetime
  c. data i godzina zakończenia - datetime
  d. dzień rozpoczęcia - date
    i. na podstawie tego pola będziemy określać z jakiego dnia są godziny
      niezależnie od daty zakończenia,
    ii. np. jeśli zostanie zarejestrowany przedział 01.01.1970 08:00 -
      01.01.1970 14:00, wtedy pole “dzień rozpoczęcia” powinno zawierać
      dzień z daty rozpoczęcia tj. “01.01.1970”

4. Pola wymienione są wymagane, natomiast każdą encję można rozszerzać zgodnie z
własnym upodobaniem/doświadczeniem o dodatkowe pola, jeśli uzna się to za
stosowne. Jeśli typ danych nie został określony wyżej, może wtedy zostać wybrany
również w oparciu o doświadczenie.

5. Endpoint: (tworzenie użytkownika)
  a. tworzy pracownika
  b. zwraca unikalny identyfikator

6. Endpoint: (rejestracja czasu pracy)
  a. Przyjmuje dane:
    i. unikalny identyfikator pracownika
    ii. data i godzina rozpoczęcia
    iii. data i godzina zakończenia
  b. Zwraca odpowiedź:
    i. jeśli sukces, ”Czas pracy został dodany!”
    ii. jeśli niepowodzenie, to odpowiedni komunikat błędu.
  c. Sprawdzenie poprawności podanego czasu pracy
    i. Pracownik może posiadać tylko 1 przedział z tym samym dniem
      rozpoczęcia
    ii. Pracownik nie może zarejestrować więcej niż 12 godzin w jednym
      przedziale
   
7. Endpointy: (podsumowanie czasu pracy dzień/miesiąc)
  a. Przyjmuje dane:
    i. Unikalny identyfikator pracownika
    ii. data w formacie (‘YYYY-MM’ lub ‘YYYY-MM-DD’)
  b. Zwraca odpowiedź:
    i. jeśli sukces,
      1. to otrzymujemy podsumowanie w przedstawionej postaci,
        ilości godzin w podanej dacie oraz wartość wypracowanych
        godzin z odpowiednim uwzględnieniem stawek oraz podziałem
        na godziny standardowe i nadgodziny
      2. czas pracy zaokrąglamy do 30 minut, np.
        a. 8:10 = 8 godz.
        b. 8:17 = 8,5 godz.
        c. 8:35 = 8,5 godz.
        d. 8:48 = 9 godz.

    ii. jeśli niepowodzenie, to odpowiedni komunikat błędu.
