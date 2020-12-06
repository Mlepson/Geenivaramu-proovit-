#Äppi avamise juhend Windowsis.

Samm 1. 
Lae alla zip failina kõik kaustas olevad failid ning paki lahti ja aseta kaust enda valitud kohale.

Samm 2. 
  a) Juhulkui seade ei oma PostgreSQL andmebaasi tõmba alla Postgre andmebaas leheküljelt: https://www.postgresql.org/download/ 
  Töö ajal on kasutatud PostgreSQL 13.1 versiooni. 
  Port: 5432
  User: postgres
  Password: postgres
  Host:localhost
  b) Käivita pgAdmin 4. Logi sisse parooliga: postgres.

Samm 3. Kasuta appi avamiseks XXAMPi. Kui seadmel ei ole seda, lae alla XXAMP: https://www.apachefriends.org/index.html
  a) Käivita xampp-control.exe
  b) 


#Äppi installeerimine (Ubuntu)Linuxis

1) klooni git repositoorium enda arvutisse kausta vabal valikul nt /opt/
$ git clone https://github.com/Mlepson/Geenivaramu-proovit-.git
2) Anna kaustale ning selles sisalduvatele failidele õiged failiõigused
$ sudo chmod -R 755 Geenivaramu-proovit-
$ sudo chown -R root:root Geenivaramu-proovit-
3) Installeeri vajalik tarkvara (veebiserver, php, postgresql)
$ sudo apt install apache2 php php-pgsql postgresql postgresql-contrib
4) Kustuta apache serveri vaikimisi genereeritud veebilehtede konfiguratsioonid
$ sudo rm /etc/apache2/sites-available/*
5) Loo uus veebilehe konfiguratsioon /etc/apache2/sites-available/veebilahendus.conf järgneva sisuga:
<VirtualHost *:80>
    #ServerName geenivaramu.test 
    DocumentRoot /opt/Geenivaramu-proovit-/
    DirectoryIndex index.php
    <Directory /opt/Geenivaramu-proovit-/>
        Options -Indexes +FollowSymLinks +MultiViews
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
6) Luba veebileht ning taaskäivita veebiserver
$ sudo a2ensite veebilahendus.conf
$ sudo service apache2 restart
7) Järgmiseks seadistame postgresql andmebaasi
$ sudo -i -u postgres
# CREATE USER geenivaramu WITH PASSWORD 'halbparool';
# CREATE DATABASE "geenivaramu";
# GRANT ALL ON DATABASE "geenivaramu" TO geenivaramu;
# \q
8) Luba andmebaasiga ühendumine kohalikust arvutist ning taaskäivita postgresql
$ sudo nano /etc/postgresql/12/main/postgresql.conf
#eemalda kommentaari märk järgnevalt realt:
#listen_addresses = 'localhost'
$ sudo service postgresql restart
9) Seadista config.php fail vastavalt andmebaasi seadistustele
#Näidisseadistuse puhul
#pg_connect("host='localhost' port=5432 dbname=geenivaramu user=geenivaramu password=halbparool")
10) Ava veebirakendus kasutades veebilehitsejat  aadressilt: http://{sinu.arvuti.ip.aadress}/
11)???
12) Profit?







# Geenivaramu prooviülesanne
App töötab testitult: Chrome, Mozilla, Edge. 
Ei tööta: internet Explorer

Ajakulu:
- 10-15 minutit läks ülesande lugemise ja arusaamise peale. Uurisin selle ajaga ka  kokkupakitud näidisfaile. 
- 2h Programmide vaatlemine ja analüüsimine. Läks aega, et otsustada, millega teen koostan lahenduse. Lõpuks PHP,Postgre allalaadimine ning rakendamine IntelliJ Idea     abil. 
- 30 min andmetabelite tegemine, Postgre uurimine
- 2-3h PHP ja Postgre ühendamine. Autentimise probleem (10). Ei aktsepteerinud andmebaasi parooli. Viga oli PHP versioonis. 
- 4h neljapäeval + 4h reedel - Funktsioon 1 ehk andmete lugemine. Failide lugemine ja sisestamine andmebaasi. 
- 4h laupäeval + 4h pühapäeval. Funktsioon 2. Andmete lugemine andmebaasist. Readme kirjutamine. 




