# PHP-MVC Demo

Yksinkertainen PHP:llä kirjoitettu MVC-frame. Sisältää perustoiminnot kuten käyttäjät ja sisäänkirjautuminen, session seuranta, perus CRUD-operaatiot ja sisällön validaatio sääntöasetuksilla, PDO-kantatoiminta, yksinkertainen käyttöoikeusjärjestelmä, apuskriptejä HTML-lomakkeiden ja -taulujen luontiin.

Käyttää [Bootstrappia](https://getbootstrap.com/) (V5) ulkoasun kaunisteluun ja [Summernote](https://summernote.org/)-editoria artikkelien HTML-sisällön luontiin.

## KÄYTTÖ

Vaatii palvelimen ja kannan alleen (testattu Apachella). Juuressa oleva db.sql sisältää kannan luonnin. Kanta sisältää admin tasoisen käyttäjän (nimi admin, salasama admin) jolla on suuremmat hallinnointioikeudet kuin rekisteröinnin kautta luotavilla käyttäjillä.

Juuren alla olevaan config/config.php tiedostoon tulee asettaa kantayhteystiedot ja URLROOT-määritys tulee asettaa osoittamaan juurihakemistoon. Pitäisi toimia myös PHP:n oman palvelun kautta juoksuttamalla se www-hakemistossa, jolloin URLROOT on pelkkä localhost.

TODO:
* ~~Requestin sisällön käsittely omaan luokkaansa.~~
* Routtauksen parantelu ja asettaminen omaan luokkaansa.
* ~~Form-luokassa liikaa koodin toistoa div-wrappereiden ja label-elementtien rakennuksessa, lyhennetään omiin metodeihin.~~
* Authorisaation uudelleenkirjoitus roolipohjaisiin käyttöoikeuksiin jotta esim. päivitystoiminnot voidaan rajata vain omistajaan. (Nyt kaikki menee läpi kun form-dataa menee sorkkimaan.)
* Modelin validaatiosääntöihin Unique-sääntö estämään esim. käyttäjänimien ja emailien duplikaatio.
* Virheiden näytön parantelu.
