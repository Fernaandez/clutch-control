# Memoria final - redaccio bloc 3

# 11. Conclusions

El resultat final del projecte es una aplicacio bastant completa orientada al mon de la moto. Al principi la idea era crear una plataforma que ajuntes diferents necessitats dels motoristes, i durant el desenvolupament aquesta idea s'ha anat convertint en una aplicacio amb molts moduls connectats entre ells.

El projecte ha acabat incloent gestio d'usuaris, motos, manteniment, rutes, gravacio de recorreguts, quedades, compravenda, xat, notificacions, multiidioma, desplegament i adaptacio mobil. Aixo fa que no sigui nomes una practica petita, sino un projecte amb bastanta complexitat tecnica.

## 11.1. Compliment dels objectius

En general, els objectius principals s'han complert. L'aplicacio permet registrar usuaris, gestionar motos i treballar amb diferents funcionalitats relacionades amb elles.

Un dels objectius era crear una aplicacio funcional amb autenticacio. Aquest objectiu s'ha assolit, ja que el sistema inclou registre, login, verificacio de correu, recuperacio de contrasenya i login amb Google.

Un altre objectiu era implementar gestio de motos i manteniment. Aquesta part tambe s'ha complert, ja que l'usuari pot crear motos, actualitzar quilometres, crear manteniments, reparacions, millores i consultar historics.

El modul de rutes tambe s'ha desenvolupat de forma bastant completa. No nomes permet crear rutes, sino tambe visualitzar-les en mapa, guardar punts, clonar rutes, compartir-les, gravar recorreguts i treballar amb suport offline parcial.

La part social del projecte tambe esta present a traves de quedades i xats. Les quedades permeten organitzar sortides, i el xat permet comunicacio entre usuaris. Aixo dona mes sentit a l'aplicacio, perque el mon de la moto no es nomes gestio individual, sino tambe comunitat.

La part de venda de motos tambe s'ha implementat. L'usuari pot publicar anuncis, afegir imatges, gestionar favorits, marcar com venut i contactar amb altres usuaris.

## 11.2. Valoracio tecnica

A nivell tecnic, la tria de Laravel, Vue i Inertia ha estat encertada. Laravel ha facilitat la part de backend, rutes, validacions, autenticacio i base de dades. Vue ha ajudat a fer pantalles mes dinamiques i Inertia ha permes unir backend i frontend sense haver de fer una API REST completa per cada pantalla.

La base de dades relacional tambe ha estat adequada, perque el projecte te moltes dades relacionades entre elles. Usuaris, motos, rutes, quedades, anuncis i xats necessiten relacions clares, i amb Eloquent ha estat mes facil representar-les.

Capacitor ha estat una part important per apropar el projecte al mobil. Tot i que ha portat problemes, sobretot amb permisos i iOS, tambe ha permes treballar amb geolocalitzacio, notificacions i funcionalitats mes properes a una app real.

## 11.3. Valoracio de la metodologia

La metodologia incremental ha estat adequada pel projecte. Si hagues intentat tancar cada modul al 100% abans de passar al següent, segurament hauria estat mes dificil arribar a una aplicacio tan completa.

Treballar per iteracions ha permes tenir primer versions simples i despres millorar-les. Aixo es veu molt en rutes i xats, que han anat canviant i millorant durant el projecte.

El punt negatiu d'aquesta metodologia es que a vegades pot donar sensacio de desordre, perque es toca una funcionalitat, despres una altra, i despres es torna a la primera. Pero en aquest projecte m'ha anat be perque moltes parts depenien entre elles.

## 11.4. Dificultats principals

Les dificultats principals han estat les parts que depenien de tecnologies externes o comportaments del dispositiu.

La geolocalitzacio i la gravacio de recorreguts han estat complicades perque no funcionen igual en navegador, Android i iOS. A mes, cal tenir permisos correctes i controlar situacions com perdre connexio.

El xat tambe ha estat una part complexa, ja que no consisteix nomes en guardar missatges. Cal controlar participants, missatges no llegits, temps real, polling, notificacions i experiencia d'usuari.

El desplegament i els builds tambe han donat feina. Una cosa es que el projecte funcioni en local, i una altra es fer que funcioni en un entorn extern o dispositiu mobil.

## 11.5. Millores futures

Encara que el projecte esta bastant complet, hi ha millores que es podrien fer en el futur.

Una millora seria reforçar les proves automatitzades. Ara el projecte s'ha anat provant manualment, pero en una aplicacio gran seria millor tenir tests per assegurar que una funcionalitat nova no trenca una anterior.

També es podria millorar el sistema offline, fent una cua de sincronitzacio mes clara, amb estat visual de pendent, sincronitzat o error.

Una altra millora seria optimitzar el xat i notificacions, sobretot si hi hagues molts usuaris. En una versio real caldria revisar rendiment, limitacions de Pusher/Firebase i tractament d'errors.

També es podria millorar el sistema de recomanacions o filtres. Per exemple, filtrar rutes per zona, dificultat, distancia o tipus de moto. En vendes, es podrien afegir mes filtres per preu, marca, cilindrada o quilometres.

Finalment, es podria publicar l'aplicacio de forma mes completa en stores mobils, amb una preparacio mes professional de privacitat, termes, builds i versions.

## 11.6. Conclusio final

Com a conclusio, el projecte ha complert l'objectiu principal: crear una aplicacio completa i funcional per motoristes, unint funcionalitats que normalment estan separades.

El projecte m'ha servit per aprendre molt mes que nomes programar pantalles. He hagut de treballar amb arquitectura, base de dades, mapes, permisos, temps real, notificacions, mobil, desplegament i documentacio.

També he vist que en un projecte real moltes vegades el mes dificil no es fer una funcionalitat una vegada, sino fer que funcioni be en diferents situacions i que sigui usable per l'usuari.

---

# 12. Bibliografia

Per al desenvolupament del projecte s'han consultat diferents recursos oficials i documentacio tecnica. La majoria de tecnologies utilitzades tenen documentacio propia, que ha servit per resoldre dubtes i implementar funcionalitats.

## 12.1. Documentacio de Laravel

Laravel Documentation.  
[https://laravel.com/docs](https://laravel.com/docs)

S'ha utilitzat per consultar rutes, controladors, models, migracions, validacions, autenticacio, middleware i funcionament general del framework.

## 12.2. Documentacio d'Inertia.js

Inertia.js Documentation.  
[https://inertiajs.com/](https://inertiajs.com/)

S'ha utilitzat per entendre la comunicacio entre Laravel i Vue sense crear una API separada per cada pantalla.

## 12.3. Documentacio de Vue

Vue.js Documentation.  
[https://vuejs.org/](https://vuejs.org/)

S'ha utilitzat per crear components, pantalles, reactivitat i logica del frontend.

## 12.4. Documentacio de Vite

Vite Documentation.  
[https://vite.dev/](https://vite.dev/)

S'ha consultat per la part de compilacio del frontend i configuracio de desenvolupament.

## 12.5. Documentacio de Tailwind CSS

Tailwind CSS Documentation.  
[https://tailwindcss.com/](https://tailwindcss.com/)

S'ha utilitzat per treballar amb estils i disseny de la interfície.

## 12.6. Documentacio de Capacitor

Capacitor Documentation.  
[https://capacitorjs.com/docs](https://capacitorjs.com/docs)

S'ha utilitzat per la part d'aplicacio mobil, Android, iOS, permisos, geolocalitzacio, notificacions i sincronitzacio amb projectes natius.

## 12.7. Documentacio de Leaflet

Leaflet Documentation.  
[https://leafletjs.com/](https://leafletjs.com/)

S'ha utilitzat per la part de mapes, marcadors i visualitzacio de rutes.

## 12.8. Documentacio de Firebase Cloud Messaging

Firebase Cloud Messaging Documentation.  
[https://firebase.google.com/docs/cloud-messaging](https://firebase.google.com/docs/cloud-messaging)

S'ha utilitzat per la part de notificacions push als dispositius.

## 12.9. Documentacio de Pusher

Pusher Channels Documentation.  
[https://pusher.com/docs/channels/](https://pusher.com/docs/channels/)

S'ha utilitzat per la part de comunicacio en temps real del xat.

## 12.10. Documentacio de Git

Git Documentation.  
[https://git-scm.com/doc](https://git-scm.com/doc)

S'ha utilitzat com a referencia general pel control de versions.

## 12.11. GitHub

GitHub Docs.  
[https://docs.github.com/](https://docs.github.com/)

S'ha utilitzat per allotjar el repositori remot i mantenir una copia del projecte.

## 12.12. AlwaysData

AlwaysData Documentation.  
[https://help.alwaysdata.com/](https://help.alwaysdata.com/)

S'ha utilitzat com a referencia pel desplegament del projecte en un servidor accessible des d'internet.

## 12.13. OpenStreetMap

OpenStreetMap.  
[https://www.openstreetmap.org/](https://www.openstreetmap.org/)

S'ha utilitzat com a base cartografica per treballar amb mapes i rutes.

---

# 13. Comentari personal

Aquest projecte ha estat un repte bastant gran per mi, sobretot per la quantitat de parts diferents que he hagut d'unir. Al principi podia semblar una aplicacio de motos amb algunes funcionalitats, pero a mesura que avançava em vaig adonar que cada modul obria nous problemes.

Una cosa que he apres es que fer una aplicacio completa no es nomes programar una pantalla. Tambe cal pensar en base de dades, rutes, permisos, usuaris, errors, dispositius, estils, desplegament i manteniment.

El modul que mes m'ha costat segurament ha estat el de rutes i gravacio, perque treballar amb localitzacio i dispositius mobils no sempre es previsible. En local pot semblar que tot funciona, pero despres en Android o iOS poden sortir problemes diferents.

El xat tambe m'ha costat, sobretot per fer que no nomes envies missatges, sino que tingues converses, participants, missatges no llegits, notificacions i una interfície usable.

També he vist la importancia de fer commits. Encara que alguns commits tinguin noms poc formals, m'han servit per guardar el proces i veure com ha avançat el projecte. Sense Git seria molt mes dificil recordar tot el que he fet.

Una altra cosa important ha estat aprendre a no esperar que tot surti be a la primera. Moltes vegades he hagut de repetir, corregir, provar i tornar a modificar. Al principi aixo pot frustrar, pero al final forma part del desenvolupament real.

Estic content amb el resultat perque crec que el projecte no s'ha quedat en una idea molt basica. Te bastants moduls i algunes funcionalitats que no son simples, com offline, notificacions, xat o gravacio de recorreguts.

Si tingues mes temps, m'agradaria polir mes la part visual, afegir mes proves i deixar mes estable la part mobil. Tambe m'agradaria millorar el sistema offline i preparar millor la publicacio com a app real.

En conclusio, aquest projecte m'ha ajudat a aplicar coneixements apresos durant el curs i tambe a aprendre coses noves pel meu compte. Ha estat exigent, pero tambe m'ha servit per veure millor com es construeix una aplicacio real amb moltes parts connectades.