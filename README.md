# Ranjiva login aplikacija

Namjerno ranjiva aplikacija za lokalne vježbe sa izmišljenim nalozima.

## Baza i ručni unos korisnika

1. Pokreni Apache i MySQL u XAMPP-u.
2. Otvori http://localhost/phpmyadmin/. Ako je potrebno, ručno napravi bazu `ranjiva_app` i tabelu `users`: `id` (INT UNSIGNED, PRIMARY KEY, AUTO_INCREMENT), `username` (VARCHAR 100, UNIQUE, NOT NULL) i `password` (VARCHAR 255, NOT NULL).
3. Izaberi bazu `ranjiva_app`, tabelu `users`, pa karticu **Insert**.
4. Ostavi `id` prazno; unesi `username` i `password` kao običan tekst, pa klikni **Go**. Primjeri: `admin` / `admin123` i `student` / `password123`.

Konekcija u `index.php` koristi `localhost`, korisnika `root` i praznu lozinku. Promijeni te vrijednosti ako MySQL koristi druga podešavanja.

## Prijava

- Otvori http://localhost/ranjiva_app/.
- Ispravni podaci preusmjeravaju na `welcome.php`.
- Nepostojeći username prikazuje „Neispravno korisničko ime.“, a pogrešan password za postojeći username prikazuje „Neispravna lozinka.“
- `welcome.php` je dostupna i direktno, bez sesije.
- Register još nije dodat; **Remember me** nema funkciju.
- `users.json` je stari primjer i više se ne koristi za prijavu.

## Namjerne ranjivosti

Lozinke su plaintext, nema ograničenja pokušaja, a unos se direktno ubacuje u SQL upit. Kada tabela ima bar jednog korisnika, za lokalnu provjeru SQL injection-a unesi `' OR 1=1 -- ` u username (sa razmakom poslije `--`) i bilo koju nepraznu lozinku. Prijava će uspjeti.
