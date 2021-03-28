
<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/BasterCz/DB_view_IP">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Prohlížeč databáze</h3>

  <p align="center">
    Řešení projektu na 3. čtvrtletí IP
    <br />
    <a href="http://a2018bartbo.delta-www.cz/">Zobrazit demo</a>
    ·
    <a href="https://github.com/BasterCz/DB_view_IP/issues">Nahlásit chybu</a>
    ·
    <a href="https://github.com/BasterCz/DB_view_IP/issues">Vyžádat funkci</a>
  </p>
</p>

#

<!-- ABOUT THE PROJECT -->
## O projektu

[![Product Name Screen Shot][product-screenshot]](http://a2018bartbo.delta-www.cz/pages/room/mistnosti.php)

Jednoduché webové rozhraní databáze, který má za cíl integrace na SQL databáze s možností prohlížení, úprav a mazní entit.

Proč právě toto řešení?
* Jednoduché UI a validace vstupu databáze
* Automatické mazání cizích klíčů v databázi
* Zabezpečení vstupu neautorizovaných osob
* Přihlášení není plaintext, ale plně hashováno

#
Tento konkrétní příklad kódu je napsán jako čtvrtletní projekt na IP. Proto obsahuje následující funkce, dle zadání:

* Pro přístup ke kterékoli stránce je potřeba, aby se uživatel přihlásil jménem a heslem
* Databáze obsahuje sloupce login a password Uživatelé s oprávněním admin mohou editovat, ostatní jen prohlížet a měnit vlastní heslo, nepřihlášení uživatelé neuvidí nic.
* U každé položky v seznamu zaměstnanců i místností je tlačítko "Editovat". Po kliku na něj se otevře editační okno, ve kterém je možno měnit všechny položky uložené v databázi.
* Každý seznam také obsahuje tlačítko "založit novou místnost/osobu"
  * Editační okno hlídá, aby byly vyplněny všechny povinné položky a nepovolí uložení špatně vyplněného formuláře
  * Předvyplňuje již upravené hodnoty a vypisuje rozumnou formou "seznam chyb ve formuláři".
  * U zaměstnanců navíc bude možné přidávat a odebírat klíče pomocí sérii checkboxů
* Spolu s editací je v seznamu u každé položky možnost ji smazat, detabáze řeší mazání sama, společně s přiřazenými cizími klíči.
* Řešení je objektvoé v příjemném množství.

#

## Využité jazyky a balíčky

### Jazyky:
* [PHP 7.4.9](https://www.php.net/)
* [HTML](https://www.w3schools.com/html/)
* [SQL (MariaDB)](https://mariadb.org/)

### Balíčky:
* [Composer](https://getcomposer.org/)
* [Mustache](https://mustache.github.io/)
* (debug) [Tracy](https://github.com/nette/tracy)


#

<!-- GETTING STARTED -->
## Začínáme

Toto je příklad využití. Napojení na zkušební DB employee - room - key.
Dále je zapotřebí MariaDB na serveru


### Před instalací

Je zapotřebí nejnovější verze NPM k instalaci všech balíčků
  ```sh
  npm install npm@latest -g
  ```

### Instalace


1. Naklonujte repozitář z Gitu
   ```sh
   git clone https://github.com/BasterCz/Composer_Projekt.git
   ```
1. Nainstalovat balíčky
   ```sh
   npm install
   ```
1. V maria DB vytvořte novou databázi
   ```sql
    CREATE DATABASE yourDBNameHere;
   ```
1. V nové databázi spusťte SQL příkaz
    <details>
      <summary>
       <code>SQL kód</code>
      </summary>

        SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
        START TRANSACTION;
        SET time_zone = "+00:00";


        /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
        /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
        /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
        /*!40101 SET NAMES utf8mb4 */;

        --
        -- Database: `yourDBNameHere`
        --

        -- --------------------------------------------------------

        --
        -- Table structure for table `employee`
        --


        DROP TABLE IF EXISTS `employee`;
        CREATE TABLE IF NOT EXISTS `employee` (
        `employee_id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
        `surname` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
        `job` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
        `wage` int(11) NOT NULL,
        `room` int(11) NOT NULL,
        `login` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
        `password` varchar(255) COLLATE utf8mb4_czech_ci DEFAULT NULL,
        `admin` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`employee_id`),
        UNIQUE KEY `login` (`login`),
        KEY `employee_room_IDX` (`room`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

        --
        -- Dumping data for table `employee`
        --

        INSERT INTO `employee` (`employee_id`, `name`, `surname`, `job`, `wage`, `room`, `login`, `password`, `admin`) VALUES
        (1, 'Jan', 'Volhejn', 'ředitel', 69000, 1, 'voljan@example.com', '', 1),
        (3, 'Sebastian', 'Tamáš', 'grafik', 69420, 2, 'tamseb@example.com', '', 1),
        (4, 'Jiřina', 'Hamáčková', 'ekonomka', 32000, 5, 'hamjir@example.com', '', 1),
        (5, 'Stanislav', 'Lorenc', 'skladník', 14000, 8, 'lorsta@example.com', '', 0),
        (6, 'Martina', 'Marková', 'skladnice', 14500, 8, 'marmar@example.com', '', 0),
        (7, 'Tomáš', 'Kalousek', 'technik', 23000, 7, 'kaltom@example.com', '', 0),
        (8, 'Jindřich', 'Holzer', 'technik', 22000, 7, 'holjin@example.com', '', 0),
        (9, 'Alena', 'Krátká', 'technik', 24000, 7, 'kraale@example.com', '', 0),
        (10, 'Stanislav', 'Janovič', 'technik', 22000, 7, 'jansta@example.com', '', 0),
        (11, 'Milan', 'Steiner', 'mistr', 30000, 7, 'stemil@example.com', '', 0),
        (34, 'Test1', 'Netěsný1', 'tester', 12313521, 21, 'test1@test.com', NULL, 0),
        (35, 'Test2', 'Testovskij2', 'tester2', 5486468, 21, 'test2@test.com', NULL, 0);

        -- --------------------------------------------------------

        --
        -- Table structure for table `key`
        --

        DROP TABLE IF EXISTS `key`;
        CREATE TABLE IF NOT EXISTS `key` (
          `key_id` int(11) NOT NULL AUTO_INCREMENT,
          `employee` int(11) NOT NULL,
          `room` int(11) NOT NULL,
          PRIMARY KEY (`key_id`),
          UNIQUE KEY `employee_room` (`employee`,`room`),
          KEY `room` (`room`)
        ) ENGINE=InnoDB AUTO_INCREMENT=567 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

        --
        -- Dumping data for table `key`
        --

        INSERT INTO `key` (`key_id`, `employee`, `room`) VALUES
        (510, 1, 1),
        (511, 1, 3),
        (512, 1, 4),
        (513, 1, 5),
        (514, 1, 6),
        (515, 1, 7),
        (516, 1, 8),
        (517, 1, 11),
        (146, 3, 1),
        (147, 3, 2),
        (148, 3, 3),
        (149, 3, 4),
        (150, 3, 5),
        (151, 3, 6),
        (152, 3, 7),
        (153, 3, 8),
        (154, 3, 11),
        (48, 4, 2),
        (7, 4, 5),
        (36, 4, 6),
        (38, 5, 6),
        (9, 5, 8),
        (50, 5, 11),
        (39, 6, 6),
        (10, 6, 8),
        (51, 6, 11),
        (203, 7, 5),
        (204, 7, 6),
        (205, 7, 7),
        (206, 7, 11),
        (31, 8, 6),
        (2, 8, 7),
        (53, 8, 11),
        (32, 9, 6),
        (3, 9, 7),
        (54, 9, 11),
        (33, 10, 6),
        (4, 10, 7),
        (55, 10, 11),
        (518, 11, 2),
        (519, 11, 6),
        (520, 11, 7),
        (521, 11, 11),
        (561, 34, 6),
        (562, 34, 7),
        (563, 34, 8),
        (564, 34, 11),
        (565, 34, 21),
        (566, 35, 21);

        -- --------------------------------------------------------

        --
        -- Table structure for table `room`
        --

        DROP TABLE IF EXISTS `room`;
        CREATE TABLE IF NOT EXISTS `room` (
          `room_id` int(11) NOT NULL AUTO_INCREMENT,
          `no` varchar(15) COLLATE utf8mb4_czech_ci NOT NULL,
          `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
          `phone` varchar(15) COLLATE utf8mb4_czech_ci DEFAULT NULL,
          PRIMARY KEY (`room_id`),
          UNIQUE KEY `no` (`no`),
          UNIQUE KEY `phone` (`phone`)
        ) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

        --
        -- Dumping data for table `room`
        --

        INSERT INTO `room` (`room_id`, `no`, `name`, `phone`) VALUES
        (1, '101', 'Ředitelna', '2292'),
        (2, '102', 'Kuchyňka', '2293'),
        (3, '104', 'Zasedací místnost', '2294'),
        (4, '201', 'Xerox', '2296'),
        (5, '202', 'Ekonomické', '2295'),
        (6, '203', 'Toalety', NULL),
        (7, '001', 'Dílna', '2241'),
        (8, '002', 'Sklad', '2243'),
        (11, '003', 'Šatna', NULL),
        (21, '123456789', 'Testovací místnost', '121324546897');

        --
        -- Constraints for dumped tables
        --

        --
        -- Constraints for table `employee`
        --
        ALTER TABLE `employee`
          ADD CONSTRAINT `employee_FK` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`) ON DELETE CASCADE;

        --
        -- Constraints for table `key`
        --
        ALTER TABLE `key`
          ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
          ADD CONSTRAINT `fk_room` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`) ON DELETE CASCADE;
        COMMIT;

        /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
        /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
        /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
    </details>
1. Vytvořte soubory a doplňte hodnoty:
  * _includes > db.php
    ```php
      define('CHARSET', '');
      define("DB_HOST", '');
      define('DB', '');
      define('DB_USER', '');
      define('DB_PASS', '');

      function dbConnect() {
          $dsn = "mysql:host=".DB_HOST.";dbname=".DB.";charset=".CHARSET;

          $options = [
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
              PDO::ATTR_EMULATE_PREPARES => false,
          ];
          return new PDO($dsn, DB_USER, DB_PASS, $options);
      }
    ```
    Kód z `./_includes/db.example.php`

  * _includes > LocalConfig.class.php
    ```php
      class LocalConfig
      {

          public const DBSERVER = "";
          public const DBUSER = "";
          public const DBDATABASE = "";
          public const DBPASSWORD = "";
      }
    ```
    Kód z `./_includes/LocalConfig.class.example.php`

<i><strong>Poznámka:</strong></i><br>
<i> Při prvním použití databáze nemá nikdo nastavené heslo.</i><br>
<i> Stačí se přhlásit s prázdným heslem a v následujícím formuláři ho změnit.</i>

#

<!-- USAGE EXAMPLES -->
## Funkce

### Profilové funkce

* Přihlášení

    [![LogIn page screenshot][login-screenshot]]()
  <i>přihlašovací formulář využívá hashovaní už od začátku</i>

     Chyby jsou zobrazovány přímo ve formuláři.

    [![LogIn page screenshot err][login-err-screenshot]]()
        
* Změna hesla a odhlášení

    [![Change password page screenshot][change_pass-screenshot]]()

* Nepřihlášený uživatel na všech stránkách

    [![No Auth screenshot][noAuth-screenshot]]()

### Prohlížecí funkce

* Rozcestník

    [![Nav page screenshot][rozcestnik-screenshot]]()


* Seznam zaměstnanců

    [![People list page screenshot][ppl_list-screenshot]]()

    Uživatel bez adminských práv nebude moct používat editační funkce

    [![People list page no admin view screenshot][ppl_list_noAdmin-screenshot]]()

* Seznam místností

    [![Room list page screenshot][room_list-screenshot]]()

### Editační funkce

* Editace osoby

    [![Person edit page screenshot][edit_ppl-screenshot]]()

* Editace místnosti

    [![Room edit page screenshot][edit_room-screenshot]]()

* Varování před mazáním místnosti

    [![Room warn delete page screenshot][delete_warn-screenshot]]()


#

<!-- CONTACT -->
## Contact

Bořek Bartolšic - logimage110@seznam.cz


[product-screenshot]: images/screenshot.png
[login-screenshot]: images/login.png
[login-err-screenshot]: images/pass_err.png
[change_pass-screenshot]: images/change_pass.png
[noAuth-screenshot]: images/noAuth.png
[ppl_list-screenshot]: images/ppl_list.png
[ppl_list_noAdmin-screenshot]: images/ppl_list_noAdmin.png
[room_list-screenshot]: images/room_list.png
[edit_ppl-screenshot]: images/edit_ppl.png
[edit_room-screenshot]: images/edit_room.png
[delete_warn-screenshot]: images/delete_warn.png
[rozcestnik-screenshot]: images/rozcestnik.png
