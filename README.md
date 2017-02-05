# Game of life
1. Backend:
- ```composer install```
- Unit teszt futtatása a ./vendor/bin könyvtáron belül:
  - ```phpunit --configuration ../../phpunit.xml --testsuite Tables```
  - a unit tesztek jelenleg hiányosak

2. Frontend:
- ```bower install```
- ```typings install```

Az alkalmazás futtatásához 2 virtual hostra van szükség:
- Frontend: ```http://gameoflife```, aminek a frontend könyvtárra kell mutatnia
- Backend: ```http://be.gameoflife```, aminek a backend könyvtárra kell mutatnia

Mysql adatbázishoz kapcsolódik az alkalmazás. A kapcsolódási paraméterek az index.php fájlban vannak definiálva.
Az adatbázis dump a gyökér könyvtárban található: ```gameoflife.sql```
