# Memoria final - redaccio bloc 1

## Diagnostic inicial

La guia demana una memoria amb aquests blocs principals: portada, introduccio i objectius, pla d'empresa, analisi/disseny/construccio, requeriments, estructura de dades, interfícies, seguretat, copies de seguretat, errors, seguiment diari, conclusions, bibliografia i comentari personal.

La memoria actual ja te bastant avancats els apartats 1, 2 i 3. Sobretot esta be encaminada la part d'introduccio, metodologia, tecnologies, arquitectura i explicacio general de la implementacio. Tambe ja apareixen funcionalitats importants com rutes, mapes, gravacio, xats, notificacions, offline, idiomes i desplegament.

El problema principal es que a partir de l'apartat 4 gairebe tot esta en blanc. Per arribar a mes de 50 pagines, el mes logic no es omplir amb text generic, sino desenvolupar be aquests apartats que falten:

- 1. Requeriments
- 1. Estructura de dades
- 1. Interfícies i interaccio amb l'usuari
- 1. Seguretat i acces a dades
- 1. Copies de seguretat
- 1. Errors i problemes durant el desenvolupament
- 1. Seguiment diari
- 1. Conclusions
- 1. Bibliografia
- 1. Comentari personal

També convé ampliar una mica els apartats 1, 2 i 3, pero no els tocaria primer. Ara mateix el mes urgent es omplir l'esquelet buit amb contingut real del projecte.

## Index final recomanat

1. Introduccio i objectius

1.1. Descripcio general  
1.2. Motivacio  
1.3. Objectius  
1.4. Metodologia de desenvolupament  
1.5. Alternatives considerades  
1.6. Tria de tecnologies  

1. Pla d'empresa

2.1. Estudi de mercat  
2.2. DAFO  
2.3. Pressupost  
2.4. Financament  

1. Analisi, disseny i construccio

3.1. Metodologia de treball  
3.2. Fases  
3.3. Temporitzacio  
3.4. Gantt  
3.5. Planificacio i seguiment  
3.6. Implementacio tecnica  

1. Requeriments

4.1. Ambit i camp del projecte  
4.2. Requeriments funcionals  
4.3. Requeriments no funcionals  
4.4. Casos d'us  
4.5. Fitxes de casos d'us  
4.6. Diagrames d'activitat  
4.7. Diagrama de classes  
4.8. Disseny modular i dependencies  

1. Estructura de dades

5.1. Model entitat-relacio  
5.2. Model relacional  
5.3. Explicacio de les taules principals  

1. Interficies i interaccio amb l'usuari

6.1. Estructura general de la interfície  
6.2. Pantalles principals  
6.3. Navegacio  
6.4. Adaptacio mobil  

1. Seguretat i acces a dades

7.1. Autenticacio  
7.2. Verificacio de correu  
7.3. Autoritzacio i permisos  
7.4. Validacions  
7.5. Proteccio de dades  

1. Copies de seguretat
2. Errors i problemes durant el desenvolupament
3. Seguiment diari
4. Conclusions
5. Bibliografia
6. Comentari personal

---

# 4. Requeriments

## 4.1. Ambit i camp del projecte

L'ambit del projecte es el desenvolupament d'una aplicacio web i mobil enfocada al mon de la moto. La idea principal es juntar en una mateixa plataforma diferents necessitats que normalment un motorista te repartides en aplicacions o eines diferents.

El projecte no es limita nomes a una funcionalitat concreta, sino que intenta cobrir una experiencia mes completa. L'usuari pot registrar les seves motos, portar un control basic del manteniment, crear rutes, consultar rutes d'altres usuaris, gravar recorreguts, participar en quedades, publicar motos en venda i parlar amb altres usuaris mitjancant xat.

El camp del projecte es, per tant, una combinacio entre aplicacio de gestio personal, aplicacio social i aplicacio de mobilitat. A nivell tecnic tambe barreja diferents parts: backend, frontend, base de dades, geolocalitzacio, notificacions, xat i adaptacio a dispositius mobils.

Els usuaris principals del sistema son:

- Motoristes que volen portar un control de les seves motos.
- Usuaris que volen planificar o guardar rutes.
- Grups de motoristes que volen organitzar quedades.
- Persones que volen vendre o comprar motos de segona ma.
- Administradors que poden controlar contingut i usuaris.

L'aplicacio esta pensada com un prototip funcional, pero amb una estructura bastant completa per poder continuar ampliant-la en el futur. Per aquest motiu s'ha fet servir una arquitectura modular, separant les parts principals del projecte en controladors, models, vistes i rutes.

## 4.2. Requeriments funcionals

Els requeriments funcionals defineixen que ha de poder fer l'aplicacio. En aquest projecte, els requeriments s'han anat concretant a mesura que avançava el desenvolupament, ja que s'ha treballat de forma incremental.

### RF01 - Registre i autenticacio d'usuaris

El sistema ha de permetre que un usuari es pugui registrar, iniciar sessio i tancar sessio. Tambe ha de poder verificar el seu correu electronic i recuperar la contrasenya en cas de perdre-la.

Aquesta part es important perque la majoria de funcionalitats del projecte necessiten saber quin usuari esta fent cada accio. Per exemple, una moto, una ruta, una quedada o un missatge sempre han d'estar relacionats amb un usuari concret.

### RF02 - Gestio del perfil d'usuari

L'usuari ha de poder modificar les dades del seu perfil, actualitzar informacio personal i gestionar elements relacionats amb el seu compte. Dins d'aquesta part tambe s'ha inclos la possibilitat de guardar el token del dispositiu per poder enviar notificacions push.

El perfil no esta bloquejat per la verificacio de correu, ja que si un usuari s'equivoca escrivint el correu ha de poder entrar i corregir-lo.

### RF03 - Gestio de motocicletes

El sistema ha de permetre afegir, editar, consultar i eliminar motos. Cada moto esta associada al seu propietari, que es l'usuari autenticat.

Les motos tenen dades com marca, model, any, quilometratge i altres camps necessaris per poder fer servir el manteniment, les rutes i les vendes. Tambe es pot actualitzar el quilometratge, cosa important per tenir un control mes real de l'estat de la moto.

### RF04 - Gestio de manteniment, reparacions i millores

L'aplicacio ha de permetre registrar tasques de manteniment d'una moto. Aixo inclou manteniments previstos, reparacions i millores.

L'usuari ha de poder:

- Crear una tasca de manteniment.
- Consultar les tasques pendents.
- Marcar tasques com a fetes.
- Consultar l'historic de manteniment.
- Veure un historic global de la moto.

Aquesta funcionalitat te sentit perque molts motoristes porten el manteniment en notes, papers o mentalment, i tenir-ho dins de la mateixa aplicacio dona mes control.

### RF05 - Creacio i consulta de rutes

El sistema ha de permetre crear rutes de moto amb informacio geografica. L'usuari pot definir punts de pas, indicar una descripcio, dificultat, distancia, durada i si la ruta sera publica o privada.

Les rutes publiques formen part d'una zona comunitaria, on altres usuaris les poden veure. Les rutes privades queden guardades nomes per l'usuari que les ha creat.

També s'ha de poder editar, eliminar i clonar rutes. La clonacio es util per agafar una ruta d'un altre usuari i guardar-ne una copia propia per modificar-la o fer-la mes endavant.

### RF06 - Visualitzacio de mapes i punts de ruta

L'aplicacio ha de mostrar les rutes sobre un mapa. Per aquesta part s'ha utilitzat Leaflet, que permet representar punts, recorreguts i mapes interactius.

Els punts de ruta es guarden com a waypoints, de manera que el sistema pot reconstruir el recorregut o mostrar els punts principals. Aquesta part es important perque una ruta de moto no es pot explicar nomes amb text, necessita una representacio visual.

### RF07 - Gravacio de recorreguts

El sistema ha de permetre gravar recorreguts reals utilitzant la geolocalitzacio del dispositiu. Aixo permet que l'usuari pugui sortir amb la moto i guardar el trajecte fet.

Durant la gravacio es poden calcular dades com distancia i temps. Aquest modul ha estat una de les parts mes complexes, perque depen del dispositiu, dels permisos de localitzacio i del funcionament en mobil.

### RF08 - Funcionament offline parcial

L'aplicacio ha de contemplar que durant una ruta l'usuari pot quedar-se sense connexio. Per aquest motiu s'ha preparat una part de rutes pendents i sincronitzacio offline.

La idea es que un recorregut es pugui guardar temporalment i sincronitzar-se mes tard quan torni la connexio. Aquesta funcionalitat es especialment util en el context del projecte, perque quan es fan rutes de moto es normal passar per zones amb poca cobertura.

### RF09 - Quedades i esdeveniments

El sistema ha de permetre crear quedades o esdeveniments relacionats amb el mon de la moto. Un usuari pot crear una quedada, indicar dades com titol, descripcio, ubicacio, dates i rutes associades.

Altres usuaris poden apuntar-se o sortir de la quedada. Aixo converteix l'aplicacio no nomes en una eina individual, sino tambe en una eina social.

### RF10 - Venda de motos

L'aplicacio ha de permetre publicar motos en venda. Un usuari pot crear un anunci a partir d'una de les seves motos, afegir imatges, descripcio, preu i estat de l'anunci.

Els usuaris poden consultar el mercat, veure anuncis, marcar-los com a favorits i contactar amb el venedor. Tambe es pot marcar una moto com a venuda i consultar l'historic public si l'usuari ho permet.

### RF11 - Sistema de xat

El sistema ha de permetre converses entre usuaris. Aquest xat s'utilitza especialment per dues situacions:

- Contactar amb un venedor d'una moto.
- Comunicar-se en relacio amb una quedada o grup.

El xat suporta converses directes i converses de grup. Tambe marca missatges com a llegits i mostra missatges pendents.

A nivell tecnic s'ha fet servir un model amb temps real quan esta disponible, i tambe un sistema de polling com a alternativa. Aixo fa que el xat sigui mes robust, perque no depen nomes d'un canal WebSocket.

### RF12 - Notificacions push

Quan un usuari rep un missatge, el sistema pot enviar una notificacio push al dispositiu corresponent mitjancant Firebase Cloud Messaging.

Aquesta funcionalitat millora molt l'experiencia d'usuari, ja que no obliga a tenir sempre la pantalla del xat oberta. Tot i aixo, depen de permisos del dispositiu i de la configuracio del token FCM.

### RF13 - Compartir rutes i esdeveniments

El sistema ha de permetre compartir rutes i quedades mitjancant enllacos publics. Aquests enllacos utilitzen tokens, de manera que no cal exposar directament funcionalitats internes.

Quan un usuari obre un enllac compartit, pot veure una previsualitzacio o continuar al navegador/app. Aquesta part es important per poder moure contingut fora de la plataforma i facilitar que altres persones accedeixin a una ruta o quedada.

### RF14 - Cerca per codi o token

L'aplicacio inclou una cerca per token que permet trobar rutes o esdeveniments compartits. Aixo facilita que un usuari pugui entrar un codi i accedir al contingut corresponent.

### RF15 - Panell d'administracio

El sistema ha de tenir una zona d'administracio protegida per rol. Aquesta zona permet gestionar usuaris, rutes, esdeveniments, vendes, motos i manteniment.

Aquest requeriment es necessari per tenir control sobre el contingut de la plataforma, sobretot si en el futur l'aplicacio tingues mes usuaris reals.

## 4.3. Requeriments no funcionals

Els requeriments no funcionals no expliquen tant que fa l'aplicacio, sino com ho ha de fer. En aquest projecte son importants perque hi ha moltes parts connectades entre elles i l'experiencia de l'usuari ha de ser clara.

### RNF01 - Usabilitat

L'aplicacio ha de ser facil d'utilitzar. Com que esta orientada a motoristes, no pot ser una eina massa complicada. Les accions principals han de ser visibles: crear ruta, veure motos, entrar al xat, consultar vendes o apuntar-se a una quedada.

S'ha intentat que la navegacio sigui directa, amb pantalles separades per funcionalitat i una navegacio adaptada a l'us mobil.

### RNF02 - Adaptacio a mobil

El projecte ha d'estar preparat per funcionar en dispositius mobils. Aixo es especialment important en funcionalitats com gravar rutes, consultar mapes o rebre notificacions.

Per aquesta rao s'ha treballat amb Capacitor, que permet portar una aplicacio web a un entorn mes proper a una app nativa per Android i iOS.

### RNF03 - Rendiment acceptable

L'aplicacio ha de carregar les pantalles en un temps raonable i permetre treballar amb llistats de rutes, motos, vendes i xats sense bloquejar-se.

El projecte encara es un prototip academic, pero s'ha intentat mantenir una estructura eficient, carregant dades relacionades quan es necessiten i separant funcionalitats per controladors.

### RNF04 - Seguretat

El sistema ha de protegir les dades dels usuaris. No qualsevol usuari pot editar o eliminar informacio d'un altre.

Per exemple, una moto nomes la pot modificar el seu propietari, una ruta privada nomes l'ha de controlar el seu creador i una conversa nomes pot ser vista pels participants.

### RNF05 - Mantenibilitat

El codi ha d'estar organitzat per poder seguir ampliant el projecte. Per aquest motiu s'ha utilitzat l'estructura propia de Laravel, separant models, controladors, migracions i rutes.

Al frontend tambe s'han separat les pantalles en diferents carpetes, com Routes, Events, Sales, Chats, Motorcycles o Auth. Aixo ajuda a localitzar cada part i continuar treballant-hi.

### RNF06 - Escalabilitat

Tot i que el projecte es academic, s'ha pensat per poder creixer. La base de dades separa les entitats principals i fa servir relacions entre taules.

Per exemple, els usuaris poden tenir moltes motos, les motos poden tenir manteniments, les rutes poden tenir punts, els anuncis poden tenir imatges i els xats poden tenir molts missatges.

### RNF07 - Compatibilitat

El sistema ha de funcionar tant en entorn web com en entorn mobil. Aixo ha fet necessari tenir en compte diferencies entre navegador, Android i iOS, sobretot en permisos de localitzacio, notificacions i gravacio de recorreguts.

### RNF08 - Disponibilitat

Per poder provar l'aplicacio fora de l'entorn local, el projecte s'ha desplegat a AlwaysData. Aixo permet accedir-hi des d'internet i fer proves mes realistes.

## 4.4. Casos d'us

Els casos d'us representen les accions principals que poden fer els actors del sistema. En aquest projecte hi ha tres actors principals:

- Usuari no autenticat
- Usuari autenticat
- Administrador

### Actor: Usuari no autenticat

L'usuari no autenticat pot veure la pagina inicial, registrar-se, iniciar sessio, recuperar contrasenya, consultar pagines legals i obrir enllacos publics de rutes o esdeveniments.

Aquest actor te acces limitat, perque la majoria de funcionalitats necessiten identificar l'usuari.

### Actor: Usuari autenticat

L'usuari autenticat es l'actor principal del sistema. Pot gestionar motos, manteniments, rutes, recorreguts, quedades, vendes, favorits, xats i perfil.

Tambe pot veure contingut public d'altres usuaris, com rutes publiques o anuncis de venda.

### Actor: Administrador

L'administrador pot entrar al panell d'administracio i gestionar dades globals del sistema. Aquesta part no esta pensada per a l'usuari normal, sino per controlar contingut i mantenir la plataforma.

## 4.5. Fitxes de casos d'us

### Cas d'us 1: Registrar usuari

Actor principal: Usuari no autenticat  
Objectiu: Crear un compte nou a l'aplicacio.  
Precondicio: L'usuari no ha iniciat sessio.  
Flux principal:

1. L'usuari entra a la pantalla de registre.
2. Introdueix les seves dades.
3. El sistema valida la informacio.
4. Es crea el compte.
5. El sistema pot demanar verificacio de correu.

Postcondicio: L'usuari queda registrat i pot accedir a l'aplicacio segons l'estat de verificacio.

### Cas d'us 2: Crear una moto

Actor principal: Usuari autenticat  
Objectiu: Registrar una moto propia dins del sistema.  
Precondicio: L'usuari ha iniciat sessio i te el correu verificat.  
Flux principal:

1. L'usuari entra a l'apartat de motos.
2. Selecciona l'opcio de crear una nova moto.
3. Omple les dades de marca, model, any i quilometres.
4. El sistema valida les dades.
5. La moto queda guardada i associada a l'usuari.

Postcondicio: La moto apareix al panell de l'usuari i es pot utilitzar en manteniments, rutes o vendes.

### Cas d'us 3: Crear una ruta

Actor principal: Usuari autenticat  
Objectiu: Crear una ruta amb punts al mapa.  
Precondicio: L'usuari ha iniciat sessio.  
Flux principal:

1. L'usuari entra a l'apartat de rutes.
2. Selecciona crear ruta.
3. Introdueix titol, descripcio, dificultat i altres dades.
4. Marca punts sobre el mapa.
5. Decideix si la ruta sera publica o privada.
6. El sistema guarda la ruta i els punts associats.

Postcondicio: La ruta queda disponible a les meves rutes i, si es publica, tambe pot aparèixer a la comunitat.

### Cas d'us 4: Gravar un recorregut

Actor principal: Usuari autenticat  
Objectiu: Guardar un recorregut real fet amb la moto.  
Precondicio: L'usuari ha iniciat sessio i ha concedit permisos de localitzacio.  
Flux principal:

1. L'usuari entra al mode de ruta o ruta lliure.
2. Inicia la gravacio.
3. El sistema llegeix la posicio del dispositiu.
4. Es calcula temps i distancia.
5. L'usuari finalitza la gravacio.
6. El sistema guarda la ruta o el recorregut.

Postcondicio: El recorregut queda guardat i pot quedar vinculat a una moto o ruta.

### Cas d'us 5: Crear una quedada

Actor principal: Usuari autenticat  
Objectiu: Organitzar una sortida o event de motos.  
Precondicio: L'usuari ha iniciat sessio.  
Flux principal:

1. L'usuari entra a l'apartat de quedades.
2. Selecciona crear una nova quedada.
3. Omple les dades de la quedada.
4. Opcionalment associa rutes.
5. El sistema guarda l'esdeveniment.

Postcondicio: La quedada queda visible i altres usuaris s'hi poden apuntar.

### Cas d'us 6: Publicar una moto en venda

Actor principal: Usuari autenticat  
Objectiu: Crear un anunci de venda.  
Precondicio: L'usuari te una moto registrada.  
Flux principal:

1. L'usuari entra a l'apartat de vendes.
2. Selecciona crear anunci.
3. Tria una moto propia.
4. Afegeix preu, descripcio i imatges.
5. El sistema publica l'anunci.

Postcondicio: La moto apareix al mercat i altres usuaris poden veure-la.

### Cas d'us 7: Enviar missatge

Actor principal: Usuari autenticat  
Objectiu: Comunicar-se amb un altre usuari.  
Precondicio: L'usuari participa en una conversa.  
Flux principal:

1. L'usuari entra al xat.
2. Obre una conversa.
3. Escriu un missatge.
4. El sistema valida que no estigui buit.
5. El missatge es guarda a la base de dades.
6. Si es possible, s'envia en temps real i es genera notificacio push.

Postcondicio: Els participants poden veure el missatge.

### Cas d'us 8: Administrar contingut

Actor principal: Administrador  
Objectiu: Controlar dades generals de la plataforma.  
Precondicio: L'usuari te rol d'administrador.  
Flux principal:

1. L'administrador inicia sessio.
2. Accedeix al panell admin.
3. Consulta usuaris, rutes, motos, vendes o manteniments.
4. Pot modificar o eliminar dades segons necessitat.

Postcondicio: El contingut queda gestionat des del panell intern.

## 4.6. Diagrames d'activitat

Com que en aquesta memoria es poden afegir diagrames visuals, jo proposaria incloure com a minim tres diagrames d'activitat:

- Creacio d'una ruta.
- Gravacio d'un recorregut.
- Enviament d'un missatge de xat.

### Activitat: creacio d'una ruta

El proces comenca quan l'usuari entra a l'apartat de rutes i selecciona crear una nova ruta. Despres introdueix les dades generals i marca els punts al mapa. El sistema valida que els camps obligatoris siguin correctes i guarda la ruta a la base de dades. Si hi ha punts de pas, tambe els guarda com a waypoints.

Si la ruta es publica, queda disponible per la comunitat. Si es privada, nomes apareix a les rutes de l'usuari.

### Activitat: gravacio d'un recorregut

El proces comenca quan l'usuari entra a una ruta o al mode de ruta lliure i inicia la gravacio. L'aplicacio demana o utilitza els permisos de localitzacio. Mentre la gravacio esta activa, va capturant punts GPS i calculant dades com distancia i temps.

Quan l'usuari atura la gravacio, el sistema intenta guardar el recorregut. Si hi ha connexio, es guarda directament al servidor. Si no hi ha connexio, pot quedar pendent per sincronitzar mes tard.

### Activitat: enviament d'un missatge

El proces comenca quan l'usuari entra a una conversa i escriu un missatge. El sistema comprova que l'usuari sigui participant de la conversa. Si no ho es, no pot accedir-hi. Si ho es, valida el missatge, el guarda i intenta enviar l'event en temps real.

Despres tambe intenta enviar notificacions push als altres participants. Si aquesta part falla, el missatge igualment queda guardat, pero pot no arribar la notificacio.

## 4.7. Diagrama de classes

El diagrama de classes del projecte es pot representar a partir dels models principals de Laravel. Les classes principals son:

- User
- Motorcycle
- MaintenanceTask
- MaintenanceLog
- Route
- RouteWaypoint
- RouteCategory
- RouteReview
- Trip
- Event
- SaleListing
- SaleImage
- Conversation
- Message

La classe User es una de les mes importants, perque gairebe tot el sistema parteix d'un usuari autenticat. Un usuari pot tenir moltes motos, moltes rutes i pot participar en converses o guardar anuncis com a favorits.

La classe Motorcycle representa una moto registrada dins del sistema. Esta relacionada amb un usuari i pot tenir manteniments, historics i rutes associades.

La classe Route representa una ruta de moto. Pot tenir molts punts de pas, una categoria, ressenyes, likes i tambe es pot vincular a esdeveniments. Aquesta classe es central en la part de mapes i comunitat.

La classe Event representa una quedada. Esta relacionada amb l'usuari creador, amb participants i amb rutes associades.

La classe SaleListing representa un anunci de venda. Esta vinculada a una moto i pot tenir imatges i usuaris que l'han marcat com a favorit.

La classe Conversation representa una conversa de xat. Pot ser directa o de grup i te molts missatges. Cada Message pertany a una conversa i a un usuari emissor.

## 4.8. Disseny modular i dependencies

El projecte esta dividit en moduls funcionals. Aquesta divisio ajuda a mantenir el codi mes ordenat i tambe facilita explicar el projecte.

### Modul d'autenticacio

Gestiona registre, login, logout, verificacio de correu, recuperacio de contrasenya i login amb Google. Depen del model User i de les rutes d'autenticacio.

### Modul de perfil

Permet editar dades de l'usuari, avatar, eliminacio de compte i token FCM del dispositiu. Depen de l'usuari autenticat.

### Modul de motos

Gestiona les motos de cada usuari. Depen del model User i es relaciona amb manteniments, rutes i vendes.

### Modul de manteniment

Gestiona tasques de manteniment, reparacions, millores i historics. Depen del modul de motos, perque totes les tasques estan associades a una moto concreta.

### Modul de rutes

Gestiona creacio, edicio, consulta, clonacio, rutes publiques, rutes privades, gravacio, rutes pendents i sincronitzacio offline. Depen dels models Route, RouteWaypoint, RouteCategory, Motorcycle i User.

### Modul de quedades

Gestiona esdeveniments i participants. Depen del model Event, User i Route, perque una quedada pot tenir rutes associades.

### Modul de vendes

Gestiona anuncis de motos, imatges, favorits i estat de venda. Depen de Motorcycle, SaleListing, SaleImage i User.

### Modul de xat

Gestiona converses i missatges. Depen dels models Conversation, Message i User. Tambe es relaciona amb motos o esdeveniments quan la conversa neix d'una venda o d'una quedada.

### Modul d'administracio

Gestiona dades globals de la plataforma. Depen del rol de l'usuari i dels diferents models del sistema.

---

# 5. Estructura de dades

## 5.1. Model entitat-relacio

El model entitat-relacio del projecte esta format per diverses entitats connectades entre elles. Com que l'aplicacio cobreix diferents funcionalitats, la base de dades no es limita a una sola area, sino que uneix motos, rutes, manteniments, vendes, quedades i xats.

L'entitat central es User, ja que representa els usuaris registrats. A partir d'aquesta entitat es relacionen moltes de les altres:

- Un usuari pot tenir moltes motos.
- Un usuari pot crear moltes rutes.
- Un usuari pot crear quedades.
- Un usuari pot participar en quedades.
- Un usuari pot enviar missatges.
- Un usuari pot marcar anuncis com a favorits.

L'entitat Motorcycle representa les motos de cada usuari. Aquesta entitat esta relacionada amb MaintenanceTask, MaintenanceLog, Route i SaleListing.

L'entitat Route representa les rutes creades o gravades. Una ruta pot tenir molts RouteWaypoint, pot pertanyer a una RouteCategory, pot rebre RouteReview i pot estar associada a Event mitjancant una taula intermitja.

L'entitat Event representa una quedada. Es relaciona amb User com a creador, amb altres usuaris com a participants i amb Route per poder associar rutes a l'esdeveniment.

L'entitat SaleListing representa un anunci de venda. Esta relacionada amb Motorcycle, SaleImage i User a traves dels favorits.

L'entitat Conversation representa les converses. Es relaciona amb User mitjancant conversation_user i amb Message per guardar els missatges.

## 5.2. Model relacional

El model relacional es pot descriure amb les taules principals següents:

### users

Guarda la informacio dels usuaris. Inclou dades com nom, email, contrasenya, rol, dades de Google login i token FCM per notificacions push.

Relacions principals:

- users 1:N motorcycles
- users 1:N routes
- users N:M conversations
- users N:M sale_listings a traves de sale_favorites
- users N:M events a traves de event_participants

### motorcycles

Guarda les motos dels usuaris. Cada moto pertany a un usuari.

Relacions principals:

- motorcycles N:1 users
- motorcycles 1:N maintenance_tasks
- motorcycles 1:N maintenance_logs
- motorcycles 1:N routes
- motorcycles 1:1 sale_listings

### maintenance_tasks

Guarda tasques pendents o planificades de manteniment, reparacio o millora. Cada tasca pertany a una moto.

### maintenance_logs

Guarda l'historic de manteniment realitzat. Serveix per consultar que s'ha fet a una moto al llarg del temps.

### routes

Guarda les rutes del sistema. Inclou dades com titol, descripcio, dificultat, distancia, durada, si es publica, si es gravada, coordenades inicials i informacio geografica.

Relacions principals:

- routes N:1 users
- routes N:1 motorcycles
- routes N:1 route_categories
- routes 1:N route_waypoints
- routes 1:N route_reviews
- routes N:M users a traves de route_likes
- routes N:M events a traves de event_routes

### route_waypoints

Guarda els punts de pas d'una ruta. Cada waypoint te latitud, longitud, ordre i nom. Aquesta taula es important per reconstruir una ruta o mostrar-ne els punts al mapa.

### route_categories

Guarda categories de rutes. Serveix per classificar-les i fer el sistema mes ordenat.

### route_reviews

Guarda ressenyes o valoracions d'usuaris sobre rutes. Cada ressenya pertany a una ruta i a un usuari.

### trips

Guarda recorreguts realitzats per l'usuari. Pot estar vinculat a una moto i opcionalment a una ruta. Aquesta taula es util per diferenciar una ruta planificada d'un recorregut fet realment.

### events

Guarda les quedades. Inclou dades de l'esdeveniment i el creador.

Relacions principals:

- events N:1 users
- events N:M users a traves de event_participants
- events N:M routes a traves de event_routes

### event_participants

Taula intermitja que guarda quins usuaris participen en cada quedada.

### event_routes

Taula intermitja que guarda quines rutes estan associades a cada quedada.

### sale_listings

Guarda anuncis de venda. Cada anunci esta vinculat a una moto. Inclou preu, descripcio, estat, visites i opcions d'historic.

### sale_images

Guarda les imatges associades a un anunci de venda.

### sale_favorites

Taula intermitja que guarda quins usuaris han marcat un anunci com a favorit.

### conversations

Guarda converses de xat. Una conversa pot ser directa o de grup. Tambe pot estar associada a una moto o a un event.

### conversation_user

Taula intermitja que guarda els participants d'una conversa.

### messages

Guarda els missatges enviats dins d'una conversa. Cada missatge te un emissor i pot tenir data de lectura.

## 5.3. Decisions sobre la base de dades

S'ha triat una base de dades relacional perque el projecte te moltes entitats connectades. Si s'hagues fet amb dades no relacionals, algunes parts haurien estat mes dificils de controlar, sobretot permisos, ownership i relacions entre usuaris, motos, rutes i xats.

Laravel facilita molt aquesta part amb Eloquent, ja que cada model pot definir les seves relacions. Per exemple, un usuari te moltes motos, una ruta te molts punts i una conversa te molts missatges.

També s'han utilitzat taules intermitges quan la relacio no era d'un a molts, sino de molts a molts. Per exemple:

- event_participants per usuaris apuntats a quedades.
- event_routes per rutes associades a quedades.
- sale_favorites per anuncis favorits.
- conversation_user per participants de converses.
- route_likes per likes de rutes.

Aquesta estructura dona flexibilitat i permet que el projecte pugui créixer.

---

# 6. Interficies i interaccio amb l'usuari

## 6.1. Estructura general de la interfície

La interfície del projecte esta feta amb Vue 3 i Inertia. Aixo permet que l'aplicacio funcioni com una web moderna, amb pantalles dinamiques, pero mantenint Laravel com a backend principal.

L'objectiu de la interfície ha estat que l'usuari pugui accedir rapidament a les parts importants: motos, rutes, quedades, vendes i xats. Com que l'aplicacio esta pensada per usuaris de moto, es important que les pantalles siguin clares i adaptades al mobil.

L'aplicacio te una pagina inicial publica, pantalles d'autenticacio, un dashboard i despres diferents apartats interns. La navegacio s'ha anat millorant durant el projecte, especialment al final, per fer que els botons i rutes fossin mes accessibles.

## 6.2. Pagina inicial

La pagina inicial serveix com a entrada a l'aplicacio. Des d'aqui l'usuari pot entendre la idea general del projecte i accedir al registre o inici de sessio.

Aquesta pantalla es important perque es el primer contacte amb l'aplicacio. En un projecte real, tambe tindria una funcio de presentacio comercial, explicant els avantatges de tenir motos, rutes, quedades, vendes i xat en una mateixa plataforma.

## 6.3. Autenticacio

Les pantalles de login i registre permeten accedir al sistema. S'ha mantingut un flux bastant estandard:

- Registrar-se.
- Iniciar sessio.
- Verificar correu.
- Recuperar contrasenya.
- Entrar amb Google.

Aquesta part ha de ser simple, perque si l'usuari troba problemes abans d'entrar, no arriba a utilitzar la resta de funcionalitats.

## 6.4. Dashboard i motos

El dashboard mostra la informacio principal de l'usuari i dona acces a les seves motos. La gestio de motos es una de les bases del projecte, ja que moltes altres funcionalitats poden dependre d'una moto.

Des de la interfície l'usuari pot crear una moto, editar-la, consultar-la i sumar quilometres. Tambe pot accedir a manteniments, reparacions, millores i historics.

La interaccio aqui esta pensada per ser practica: l'usuari no nomes guarda una moto com a dada estatica, sino que la pot anar actualitzant mentre utilitza l'aplicacio.

## 6.5. Pantalles de manteniment

Les pantalles de manteniment separen tasques, reparacions i millores. Aquesta separacio ajuda a que l'usuari no barregi tots els registres.

Per exemple, no es el mateix un manteniment periodic com canviar oli, una reparacio per una avaria, o una millora com instal·lar un accessori. Separar-ho fa que l'historic sigui mes facil d'entendre.

## 6.6. Pantalles de rutes

El modul de rutes es un dels mes importants visualment. La interfície ha de permetre veure rutes, crear-les i interactuar amb mapes.

Les pantalles principals son:

- Rutes publiques.
- Les meves rutes.
- Crear ruta.
- Editar ruta.
- Veure ruta.
- Ruta lliure.
- Rutes pendents.

L'usuari pot veure el mapa, punts de pas, distancia, durada, dificultat i altres dades. Tambe pot clonar una ruta o compartir-la.

En aquest modul la interaccio amb el mapa es clau. Per aixo s'ha utilitzat Leaflet, que permet mostrar mapes interactius i treballar amb coordenades.

## 6.7. Gravacio i mode mobil

La gravacio de recorreguts esta pensada sobretot per dispositiu mobil. L'usuari pot iniciar una gravacio, moure's amb la moto i despres guardar el recorregut.

Aquesta pantalla requereix una interaccio clara per evitar confusions. L'usuari ha de saber quan esta gravant, quina distancia porta i quan pot finalitzar.

Tambe s'han tingut en compte notificacions locals i geolocalitzacio en segon pla, tot i que aquesta part depen molt del sistema operatiu i dels permisos.

## 6.8. Pantalles de quedades

Les quedades permeten consultar esdeveniments, crear-ne, editar-los i apuntar-s'hi. Aquesta part dona un component social al projecte.

La interfície ha de mostrar clarament les dades de la quedada, com titol, descripcio, data, ubicacio i participants. Tambe ha de permetre associar rutes, cosa que encaixa molt amb l'us real: una quedada de motos normalment te una ruta prevista.

## 6.9. Pantalles de vendes

El modul de vendes funciona com un petit mercat de motos. L'usuari pot veure anuncis, crear-ne, editar-los, marcar favorits i contactar amb el venedor.

Les imatges tenen molta importancia en aquesta part, ja que en una venda de moto l'aspecte visual es basic. Tambe es important mostrar be el preu, l'estat i la informacio de la moto.

## 6.10. Pantalles de xat

El xat te una pantalla de llistat de converses i una pantalla de conversa concreta. La pantalla de llistat mostra les converses de l'usuari, ultim missatge i missatges no llegits.

La pantalla de conversa permet enviar i rebre missatges. Tambe es marca com a llegit quan l'usuari entra a la conversa.

Durant el desenvolupament aquesta part ha tingut bastantes millores d'estil i funcionalitat, ja que el xat es una pantalla on la experiencia d'usuari es nota molt.

## 6.11. Panell d'administracio

El panell d'administracio esta separat de la zona normal d'usuari. Esta protegit per autenticacio, verificacio de correu i rol d'administrador.

Permet gestionar dades com usuaris, rutes, esdeveniments, vendes, motos i manteniments. Aquesta part no es l'objectiu principal per a l'usuari final, pero es important per mantenir la plataforma.

## 6.12. Adaptacio a idiomes

La interfície incorpora sistema multiidioma. S'han preparat fitxers de traduccio per diferents idiomes, com catala, castella i angles.

Aquesta decisio fa que l'aplicacio pugui ser mes flexible i que en el futur no calgui canviar tots els textos manualment. Encara que el projecte sigui academic, tenir aquesta estructura ajuda a fer-lo mes professional.

---

# 7. Seguretat i acces a dades

## 7.1. Autenticacio

La seguretat del projecte comenca amb l'autenticacio d'usuaris. Per accedir a la majoria de funcionalitats, l'usuari ha d'haver iniciat sessio.

Laravel proporciona una base bastant segura per aquesta part, ja que gestiona sessions, contrasenyes encriptades, recuperacio de contrasenya i proteccio CSRF en formularis.

En el projecte tambe s'ha afegit login amb Google mitjancant Socialite. Aixo dona una alternativa a l'usuari i facilita l'acces, tot i que tambe obliga a tenir ben configurades les credencials externes.

## 7.2. Verificacio de correu

El sistema utilitza verificacio de correu. Aixo vol dir que, abans d'accedir a les funcionalitats principals, l'usuari ha de validar el seu email.

Aquesta decisio ajuda a reduir comptes falsos i dona mes fiabilitat a la plataforma. Es especialment important perque l'aplicacio inclou comunicacio entre usuaris, vendes i quedades.

El perfil queda accessible encara que l'usuari no estigui verificat, perque si s'ha equivocat posant el correu pugui corregir-lo.

## 7.3. Autoritzacio per propietat

No totes les dades poden ser modificades per qualsevol usuari. Moltes accions comproven que l'usuari autenticat sigui el propietari del recurs.

Per exemple:

- Una moto nomes l'hauria de modificar el seu propietari.
- Una ruta nomes la pot editar o eliminar el seu creador.
- Una conversa nomes la poden veure els participants.
- Una ruta lliure nomes es pot iniciar sobre una moto propia.

Aquest control es molt important per evitar que un usuari accedeixi o modifiqui dades d'un altre.

## 7.4. Rol d'administrador

El projecte inclou un rol d'administrador. Les rutes d'administracio estan protegides per middleware admin.

Aixo vol dir que no n'hi ha prou amb estar autenticat, sino que l'usuari ha de tenir el rol correcte. Aquesta separacio evita que un usuari normal pugui entrar al panell intern.

## 7.5. Validacio de dades

Els formularis i accions principals tenen validacions. Per exemple, quan es crea una ruta es valida el titol, la dificultat, les distancies, la moto associada i la imatge. Quan s'envia un missatge, es valida que el text sigui obligatori i que no superi una mida maxima.

Les validacions son importants per dues raons:

- Eviten errors a la base de dades.
- Redueixen dades incorrectes o manipulades.

## 7.6. Proteccio de converses

El xat te controls especifics. Abans de mostrar una conversa o retornar missatges, el sistema comprova que l'usuari formi part de la conversa.

Aixo es una mesura necessaria, perque els missatges son informacio privada. Tambe s'utilitzen canals privats per al xat en temps real, de manera que nomes els participants autoritzats poden escoltar el canal corresponent.

## 7.7. Proteccio de dades amb rutes publiques i privades

Les rutes poden ser publiques o privades. Aquesta diferencia permet que l'usuari decideixi si vol compartir una ruta amb la comunitat o guardar-la nomes per ell.

Tambe existeixen enllacos compartits per token. Aixo permet compartir una ruta o event sense haver de fer visible tota la informacio interna del sistema.

## 7.8. Acces a fitxers i imatges

El projecte permet pujar imatges, per exemple en rutes o anuncis de venda. Aquestes pujades es validen per tipus de fitxer i mida.

Aixo evita que l'usuari pugui pujar qualsevol fitxer sense control. En un entorn real, aquesta part seria encara mes important i es podria reforçar amb mes comprovacions, optimitzacio d'imatges o escaneig de fitxers.

## 7.9. Notificacions i tokens FCM

Per enviar notificacions push, el sistema guarda el token FCM del dispositiu de l'usuari. Aquest token no es una contrasenya, pero igualment es una dada sensible relacionada amb el dispositiu.

Per aquest motiu s'ha de tractar com una dada interna i no mostrar-la publicament. El seu us queda limitat a enviar notificacions quan hi ha missatges nous.

## 7.10. Limitacions de seguretat

Com que es tracta d'un projecte academic, hi ha punts que es podrien reforçar en una versio futura:

- Revisar millor permisos en totes les pantalles.
- Afegir proves automatitzades de seguretat.
- Millorar el control d'imatges pujades.
- Afegir logs d'accions administratives.
- Revisar politiques de privacitat i retencio de dades.

Tot i aixo, la base de seguretat es coherent amb el tipus de projecte: autenticacio, verificacio, rols, validacions i control d'accessos per usuari.