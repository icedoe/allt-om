Redovisningar
================
Kmom01:PHP-baserade och MVC-inspirerade ramverk
-------------------------------------------------
Min utvecklingsmiljö består av Windows 8, Xampp, Sublime text 3 och Firefox med div tillägg.

Jag har inte någon tidigare erfarenhet av ramverk. Hitintills är jag någonstans mitt emellan förvirrad och förtjust. Samtidigt som jag velat dela upp koden hårdare även tidigare, har utvecklingen plötsligt blivit väldigt abstrakt, och det är inte längre alldeles självklart vad som egentligen är hemsida, och vad som är ren kod. Förmodligen bara min vanliga ovana som spökar.

MVC-begreppen är nya, medan jag åtminstone hört talas om kodbegreppen.

Det kommer ta en del tid att lära sig hitta i Anax-strukturen. Många mappar, och det har tagit några genombläddringar bara att få något av en känsla för var saker finns. När jag vant mig kommer jag säkert uppskatta namespaces, men så här i början har det kanske framförallt saboterat mitt vanliga sätt att hitta filer.

Kmom02: Kontroller och modeller
--------------------------------
Anax kräver ett helt nytt sätt att tänka för min del. Till en början kändes det inte som jag egentligen sa åt servern att göra något; sen gjorde den det ändå. Till det kommer lite av samma problem som jag hade med PHP från start; att ramverket sköter saker som jag instinktivt söker hantera själv. Känslan av att sväva omkring i ingenting la sig dock en bit in i uppgiften, och när väl hjärnan ställt om kändes ramverket riktigt smidigt att arbeta i.

Composer var lite överraskande. Funkade hur bra som helst, och även om jag ännu inte installerat fler paket hittade jag flera som jag ska titta närmare på. Allt från mail till debug.

Det tog även den här gången några genomläsningar att begripa, både av artiklar och forumtrådar. I slutändan var det genom trial and error jag kunde dechiffrera texterna, och efter det gick allt mycket enklare.

Jag delade upp min controller-metod i två. Dels controlAction som används med en redirect på redovisningssidan; dels en indexAction som anropas direkt från navmenyn, och använder controlAction som en vanlig function. Jag behövde skicka runt lite variabler mellan funktionerna, och de förändringar som gjorts i koden har berott på det. Införde bl a variabler för redirect och "kommentarskonto", för att kunna hantera flera sidor.

Passade också på att flytta ut webroot från Anax-mappen, bara för att det känns tveksamt att ha det på något annat sätt, och meningslöst att ladda upp hela ramverket med varje uppgift.

Kmom03: Bygg ett eget tema
-----------------------------
Klart blandade känslor. Medan LESS gör css till i princip exakt vad det i början tog mig ett antal bittra googlingar att acceptera att det inte var, har felsökningen blivit något av en plåga, och eftersom jag inte hunnit sätta mig in i språket, faller jag oftast tillbaka på vanlig css när något strular. Jag kunde exempelvis inte få igång stöd för unit(), i vare sig hem- eller skolmiljö, och hårdkodade till slut fontstorlekar och leadings. Bara att räkna ut att det var unit som var problemet tog gräsliga mängder tid i anspråk. Jag har inte heller ordnat all typografi, utan bara sett till att det jag använder mest fungerar, och tänkte fylla på vartefter behovet uppstår.

Jag har inte stött på några css-ramverk tidigare, men har som sagt saknat mycket av funktionaliteten i LESS, och ser klart fram emot att sätta mig in mer i det framöver. Lessphp gjorde vad jag förstår precis vad det lovade, och det är lätt att förstå vilken lättnad det måste innebära för vem som helst som använt LESS utan det. Även semantic var en trevlig ny bekantskap. Jag har ofta svårt att gissa mig till vad slags layout som kan funka, så gridlösningen är som gjord för mig. Gillade klart den horisontella lösningen. Den vertikala var mer av pilljobb, men jag är medveten om att jag har nytta av det, så det är väl bara att stå ut.

Valde att använda script, snarare än klass som kontroller. Detta mest för att när övningen var gjord hade jag också kontrollern färdig. Insåg för övrigt när jag la till kontrollern i navbar, att jag gjorde en rejäl tankevurpa förra övningen, och har återgått till att låta webroot ligga kvar i Anaxkatalogen.

Mycket tacksam för Font Awesome. Har saknat ett bra ikonverktyg, och det här tycks åtminstone än så länge göra allt jag kan komma på att behöva av ett sånt.

Jag känner mig för pressad för utsvävningar, och har egentligen bara lagt lite bakgrundsfärg på övningsresultatet. Av samma orsak backade jag från extauppgiften.

Kmom04: Databasdrivna modeller
-------------------------------
Det här momentet bjöd på sina svårigheter. Det tog till exempel ett helt gäng misslyckanden för min del, att fatta hur callbacks och check samverkade. Gjorde också en vända in i CForm för att se om det gick att stänga av valideringen för enskilda submitknappar, men om jag läste koden rätt vet inte klassen vilken submit som tryckts förrän efter att valideringen gjorts. Kanske går att lösa, men jag har tillräckligt ont om tid som det är.

När det väl gått upp för mig hur det fungerade trivdes jag alldeles utmärkt med arrayformulär. Saknade lite funktioner --ovan bekrivs en; en annan var disable-- men så länge det klassen gör räcker, är den faktiskt smått lysande.

Föredrar helt klart att hantera databaser med php. Med klasserna som utvecklades blev väldigt mycket väldigt mycket smidigare. Det lilla som skiljer mina klasser från exemplen, är att jag lämnar öppet för att välja andra kolumner än id. Detta görs alltså lämpligen i de förlängande klasserna. I fallet med kommentarerna fick jag också utöka någon funktion för att hantera fler kolumner än bara en.

Kunde kapa bort några routes i kontrollerklassen för kommentarer. Med formulären igång kändes det klart smidigare att låta check jobba mot databasen än att skicka runt kontrollen i onödan. I övrigt var det mest en fråga om att följa det ursprungliga flödet, och styra om det från sessionen till databasen.

Kmom05: Bygg ut ramverket
---------------------------
Jag gjorde en html-tabellbyggare, fick inspiration från inspirationstexten, och byggde den från ingenting.

Jag började med funktion, och jobbade från src-mappen. Inga större problem att utveckla koden så, men så här i efterhand kan jag känna att jag borde börjat med att få in den via github och composer. Förvirrade mig några gånger bland arrayer och kolumnnamn, men gjorde egentligen ingenting avancerat, så bortsett från sporadiska hjärnsläpp flöt kodandet ganska smärtfritt.

Både github och packagist var nya upplevelser för mig, men allt fungerade som det skulle i båda fallen. Har undvikit att publicera tidigare, och måste säga att det är en ganska knepig känsla att se sitt eget arbete utlagt, även om det bara är basic.

Hade fel mappstruktur, och hade inte riktigt greppat vad autoloadern skulle leta efter var. Ödade en massa tid på att inte få ordning på det innan jag till slut vände mig till forumet, och Mos kunde berätta vad som var fel. Borde be om hjälp tidigare, men har fruktansvärt svårt att sluta jaga mina misstag själv. Efter att jag väl fått ordning på det, kunde jag övergå till att rätta till lite småfel, ta bort debug-utskrifter och skriva readme. Dokumentationen var inga större problem. Fastnade dock lite i funderingar kring funktioner med flera möjliga returtyper, och hur de bäst dokumenteras.

Sluttestet blev som föreslaget att installera modulen i en ren Anaxinstallation och det är den som länkas i me-sidans navmeny.

Kmom06: Verktyg och CI
------------------------
Jag hade inte testat någon av teknikerna tidigare. Känner förstås till unit-tester, men inte till mer än namnet. Travis och scrutinizer var helt nya.

Min modul från förra momentet var inte särskilt lämplig att skriva tester till. Nästan inga returvärden, och till och med felhanteringen sköttes internt. Till en början fick jag alltså skriva om den; framförallt med införandet av Exceptions, vilket gav mig något att fokusera testfallen på. Själva testfallen var inga som helst problem. Hoppas dock få tid att sätta mig in i hur mer komplicerade testfall utformas. Nu räckte det med ett ett till ettförhållande av förväntat utfall och exekvering.

Travis krånglade lite. Framförallt använder jag Anax-ramverket, och fick ta omvägen via de texterna för att få det att fungera. Sen visade det sig att mina custom exceptions inte fungerade i Travis, och jag fick övergå till standardversionen. Till slut gjorde jag en hel massa meningslösa ändringar i koden bara för att badgen inte uppdaterades med resten av sidan.

Scrutinizer var lite som travis, fast snällare. Riktigt bra att få en kvick genomgång av koden. Hittade både oanvänd kod och tvetydigheter. Många av anmärkningarna var dock mer av sånt slag att jag kommer tänka annorlunda nästa gång, snarare än ändra i den här modulen nu.

Det var lite knepigt att komma igång med verktygen, men väl på plats var de smått fantastiska. Jag vet inte hur mycket jag kommer publicera framöver, men gör jag det är Travis och scrutinizer precis vad jag behöver för att inte börja fulkoda av ren slöhet. Phpunit är dock ett omedelbart tillskott i min arsenal, och jag är ganska övertygad om att jag kommer ha testning i bakhuvudet från start i mina framtida klassbyggen.

Gjorde inte extrauppgiften.