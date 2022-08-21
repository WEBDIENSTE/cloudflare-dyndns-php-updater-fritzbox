# cloudflare-dyndns-php-updater-fritzbox
Simple PHP script to privovide dyndns on cloudflares DNS platform.
 
1. Pull https://github.com/WEBDIENSTE/cloudflare-dyndns-php-updater-fritzbox in Deinen Webspace.
2. Ändere die Parameter/Zugangsdaten in der update.php.
3. Logge Dich in Deiner Fritzbox ein .
 
4. Eingabe der Variablen, wie gewünscht:
 
Wenn nur IPv4 verwendet werden soll: 
URL: https://<deine-übelst-krasse-subdomain.de>.<deine-übelst-krasse-domain>.<deine-übelst-krasse-tld>/update.php?domain=<domain>&ipv4=<ipaddr>&user=<username>&pass=<pass> 

Wenn nur IPv6 verwendet werden soll: 
URL: https://<deine-übelst-krasse-subdomain.de>.<deine-übelst-krasse-domain>.<deine-übelst-krasse-tld>/update.php?domain=<domain>&ipv6=<ip6addr>&user=<username>&pass=<pass> 
 
Wenn IPv4 und IPv6 verwendet werden soll: 
URL: https://<deine-übelst-krasse-subdomain.de>.<deine-übelst-krasse-domain>.<deine-übelst-krasse-tld>/update.php?domain=<domain>&ipv4=<ipaddr>&ipv6=<ip6addr>&user=<username>&pass=<pass> 
 
Beispiel:
https://butioc76uzgoihtrd5iougihpiguut.keineangabe.de/unterverzeichnis/weiteres-unterverzeichnis/update.php?domain=<domain>&ipv4=<ipaddr>&user=<username>&pass=<pass> 
 
 
... Fertig. Easy, oder?

 
 
 ### WARNUNG ###
 Dieses Script sollte gut versteckt werden, da es potentiell alle Zoneneinträge eines Accounts ändern kann.
 Ich werde noch eine angepasste Version veröffentlichen, bei der dies verhindert wird.
 Bis dahin gilt: Vorsicht walten lassen!
 
 Nutzung auf eigene Gefahr! Eltern haften für Ihre Kinder! Usw.
