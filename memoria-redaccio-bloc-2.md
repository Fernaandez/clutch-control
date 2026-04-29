# Memoria final - redaccio bloc 2

# 8. Copies de seguretat

Les copies de seguretat han estat una part important del projecte, encara que no sempre s'hagin fet com una copia manual tradicional. En aquest projecte, la forma principal de tenir seguretat sobre el treball ha estat utilitzar Git i GitHub.

Cada vegada que es feien canvis importants, es pujaven commits al repositori. Aixo permetia tenir un historic del projecte i poder recuperar versions anteriors si alguna cosa deixava de funcionar.

El repositori remot utilitzat es GitHub, i ha servit com a copia externa del projecte. Aixo es important perque si hi hagues algun problema amb l'ordinador local, el codi no es perdria completament.

## 8.1. Control de versions amb Git

Git s'ha utilitzat per guardar l'evolucio del projecte. Cada commit representa un punt del desenvolupament, amb canvis concrets o una fase concreta.

Aquest sistema m'ha ajudat en diferents situacions:

- Recuperar codi si algun canvi trencava una funcionalitat.
- Veure l'evolucio real del projecte.
- Justificar el seguiment del treball.
- Tenir una copia externa a GitHub.
- Separar canvis grans en diferents moments.

La part positiva de Git es que no nomes guarda l'estat final, sino tambe tot el cami que s'ha fet. Aixo en una memoria es util, perque es pot demostrar que el projecte ha anat avançant per fases i no s'ha fet tot de cop al final.

## 8.2. Copies del codi font

El codi font principal esta guardat al projecte local i tambe al repositori remot de GitHub. Les parts mes importants que queden cobertes per aquest sistema son:

- Controladors Laravel.
- Models.
- Migracions de base de dades.
- Rutes web i API.
- Components i pantalles Vue.
- Configuracio del projecte.
- Fitxers de Capacitor per Android i iOS.

Aquestes copies permeten reconstruir l'aplicacio si cal tornar a instal·lar el projecte en un altre entorn.

## 8.3. Copies de base de dades

La base de dades com a dades reals no queda completament guardada nomes amb Git, perque Git guarda el codi, no el contingut viu de la base de dades.

Tot i aixo, el projecte te les migracions, que permeten reconstruir l'estructura de la base de dades. Aixo vol dir que, encara que es perdessin les dades, es podria tornar a crear l'esquema de taules amb Laravel.

Per una versio real en produccio, seria necessari fer copies periodiques de la base de dades. Per exemple:

- Exportacions SQL programades.
- Copies manuals abans de canvis importants.
- Backups del servidor AlwaysData.
- Copies dels fitxers pujats pels usuaris, com imatges de motos o rutes.

En aquesta fase academica, l'objectiu principal era conservar el codi i l'estructura, pero en una empresa real s'hauria de donar molta mes importancia a les dades dels usuaris.

## 8.4. Copies dels fitxers pujats

L'aplicacio pot guardar imatges, com fotos de rutes o imatges d'anuncis de venda. Aquests fitxers no sempre queden coberts pel repositori, perque normalment els fitxers pujats pels usuaris formen part de l'emmagatzematge del servidor.

Per a una versio futura, seria recomanable fer copies d'aquests fitxers juntament amb la base de dades. Si no es fa, podria passar que la base de dades tingues registres d'imatges que despres no existeixen fisicament.

## 8.5. Copies en desplegament

El desplegament a AlwaysData tambe funciona com una forma de tenir una versio externa del projecte. No substitueix GitHub ni una copia de base de dades, pero ajuda a tenir una versio accessible fora de l'ordinador local.

Aixo tambe m'ha permes provar el projecte en condicions mes reals, amb acces des d'altres dispositius i no nomes des de localhost.

## 8.6. Millores futures en copies de seguretat

Si el projecte continues mes enlla de la memoria, les millores principals serien:

- Automatitzar backups de base de dades.
- Guardar copies dels fitxers pujats.
- Documentar un proces de restauracio.
- Fer copies abans de desplegaments importants.
- Separar dades de prova i dades reals.

La conclusio d'aquest apartat es que el projecte ha tingut una bona seguretat del codi gracies a Git i GitHub, pero que en una versio real s'hauria d'afegir un sistema mes complet de backup de dades i fitxers.

---

# 9. Errors i problemes durant el desenvolupament

Durant el desenvolupament han aparegut diferents problemes. Alguns han estat errors de codi normals, altres han estat problemes de configuracio, i altres han vingut per la dificultat d'integrar parts mes avançades com geolocalitzacio, xat en temps real, notificacions o iOS.

Com que el projecte ha crescut bastant, no ha estat una aplicacio simple d'una sola funcionalitat. Cada modul nou podia afectar parts anteriors, i per aixo moltes vegades ha calgut tornar enrere, corregir, provar i tornar a pujar canvis.

## 9.1. Problemes inicials de configuracio

Al principi del projecte, una part important va ser deixar preparat l'entorn. Laravel, Vue, Inertia, Vite, base de dades i Capacitor havien de funcionar junts.

Aquest tipus de configuracio pot semblar secundaria, pero si alguna cosa no esta ben preparada, despres qualsevol funcionalitat falla. Per exemple, una ruta pot estar ben feta al backend, pero si Vite no compila be o Inertia no carrega la pagina, l'usuari no ho pot veure.

També hi va haver feina amb fitxers compilats, public build i configuracions de produccio. En diferents commits apareixen ajustos de build, cosa que reflecteix que aquesta part va necessitar proves.

## 9.2. Problemes amb rutes i navegacio

El modul de rutes ha estat un dels mes importants i tambe un dels mes complicats. Treballar amb mapes no es igual que fer un formulari normal, perque s'han de guardar punts, coordenades, distancies, mapes i representacio visual.

Alguns problemes que van aparèixer:

- Guardar be els punts de ruta.
- Diferenciar rutes planificades i rutes gravades.
- Controlar si una ruta era publica o privada.
- Editar rutes sense perdre els punts anteriors.
- Clonar rutes.
- Fer que els enllacos i botons portessin al lloc correcte.

Al final del projecte hi ha bastants commits relacionats amb navegacio, rutes, links i botons. Aixo demostra que una part important del poliment final va ser fer que l'usuari pogues moure's millor per l'aplicacio.

## 9.3. Problemes amb geolocalitzacio i gravacio

La gravacio de recorreguts ha estat una de les parts mes delicades. En navegador es pot fer servir geolocalitzacio, pero en mobil la situacio canvia, perque depen dels permisos del dispositiu i del sistema operatiu.

En Android i iOS el comportament no sempre es igual. Per exemple, iOS es mes estricte amb permisos de localitzacio i notificacions. Aixo va provocar que calgues fer canvis especifics per iOS, com ajustar permisos i utilitzar APIs de Capacitor en lloc d'algunes APIs del navegador.

Aquest problema es important perque una aplicacio de rutes necessita localitzacio estable. Si la localitzacio falla o es congela, l'usuari no pot gravar correctament.

## 9.4. Problemes amb funcionament offline

El suport offline tambe ha tingut dificultat. La idea era que si l'usuari perd connexio durant una ruta, no es perdi tota la informacio.

El repte principal es sincronitzar despres les dades. No es nomes guardar un text, sino guardar punts GPS, distancia, durada, moto associada i moment de creacio. Si alguna dada no esta ben validada, la sincronitzacio pot fallar.

En el projecte es va preparar una pantalla de rutes pendents i un endpoint de sincronitzacio. Aixo deixa una base funcional, tot i que en una versio futura es podria fer mes complet, amb millor gestio d'errors i estat de sincronitzacio.

## 9.5. Problemes amb xats

El sistema de xat va requerir moltes iteracions. A nivell simple, enviar un missatge podria semblar facil, pero quan es vol fer be apareixen mes detalls:

- Crear converses sense duplicar-les.
- Comprovar que l'usuari pertany a la conversa.
- Mostrar ultim missatge.
- Comptar missatges no llegits.
- Marcar missatges com a llegits.
- Enviar missatges en temps real.
- Tenir un sistema alternatiu si el temps real falla.
- Enviar notificacions push.

Els commits del mes d'abril mostren bastanta feina dedicada al xat, tant funcional com d'estils. Aixo te sentit, perque el xat es una funcionalitat que necessita estar molt polida per semblar usable.

## 9.6. Problemes amb notificacions push

Les notificacions push depenen de Firebase Cloud Messaging i dels tokens dels dispositius. Aixo vol dir que no nomes cal programar el backend, sino tambe registrar el dispositiu i guardar el token.

Un problema habitual d'aquesta part es que si el token no existeix, ha caducat o el dispositiu no dona permisos, la notificacio no arriba. En el projecte s'ha intentat que, encara que la notificacio falli, el missatge no es perdi. Per aixo el missatge queda guardat a la base de dades i els errors externs es registren.

## 9.7. Problemes amb iOS

iOS ha estat una part complicada. A diferencia d'una web normal, una app amb Capacitor ha de tenir permisos ben configurats. Localitzacio en segon pla i notificacions necessiten claus i configuracio especifica.

Durant el projecte es van fer canvis per solucionar problemes de permisos i comportaments estranys en iOS. Aquesta part va ser una de les mes lentes perque no sempre l'error es veu clarament, i moltes vegades cal provar en dispositiu o build real.

## 9.8. Problemes de base de dades i migracions

Com que el projecte te moltes entitats, tambe hi ha hagut ajustos en migracions i camps. Per exemple, durant el desenvolupament es van afegir camps nous com estat d'anuncis, visites, tokens compartits, dades de Google, FCM, trips i altres.

Aixo es normal en una metodologia incremental: al principi es crea una estructura, pero quan apareixen noves necessitats s'han d'afegir camps o taules.

El risc d'aixo es que alguna part del codi esperi un camp que encara no existeix o que un seeder tingui noms de columnes incorrectes. De fet, en els commits apareixen correccions de seeders i noms de columnes, cosa que mostra aquest tipus de problema.

## 9.9. Problemes d'interfície i responsive

Un altre bloc de problemes ha estat l'estil i la experiencia d'usuari. Encara que una funcionalitat funcioni tecnicament, si la pantalla no es clara, l'usuari la percep com mal feta.

Durant el projecte s'han fet millores d'estils en xats, navegacio, botons, marges i adaptacio visual. Aquesta part ha estat important sobretot al final, quan ja hi havia moltes funcionalitats i calia fer-les mes coherents.

## 9.10. Aprenentatge dels errors

La part positiva dels errors es que han ajudat a millorar el projecte. Moltes funcionalitats han passat per una primera versio, proves, errors i millores.

Alguns aprenentatges importants han estat:

- No deixar la navegacio pel final, perque afecta tota l'experiencia.
- Provar abans les parts mobils, ja que Android i iOS no sempre funcionen igual.
- Fer commits sovint per no perdre avenços.
- Separar funcionalitats en moduls per entendre millor el codi.
- Validar be les dades abans de guardar-les.
- Tenir alternatives quan una integracio externa pot fallar.

---

# 10. Seguiment diari

El seguiment del projecte s'ha fet principalment a traves dels commits de Git. Aixo permet veure l'evolucio general del desenvolupament i reconstruir les fases del projecte.

Tot i aixo, cal tenir en compte que un commit no sempre significa que tota la feina s'hagi fet exactament aquell dia. A vegades un commit pot ser una pujada de feina feta abans, una correccio petita, un build o una reorganitzacio del que ja estava avançat. Per aixo aquest seguiment combina l'historic de Git amb la realitat del proces de treball.

No tots els dies tenen la mateixa quantitat de feina. Hi ha dies amb molts commits petits i altres dies sense activitat. Aixo es normal en un projecte academic, perque el treball depen de classes, proves, errors, disponibilitat personal i tambe imprevistos.

## 10.1. Gener - inicialitzacio

### 29 de gener

Es va fer la inicialitzacio del projecte. En aquesta fase es va preparar la base tecnica: backend, base de dades i instal·lacio de Capacitor.

Aquesta etapa va ser important per deixar el projecte llest per començar a desenvolupar funcionalitats reals. Sense aquesta base, no es podien crear encara els moduls de motos, rutes o usuaris.

Tasques principals:

- Creacio del projecte.
- Configuracio inicial de Laravel.
- Preparacio de base de dades.
- Instal·lacio de Capacitor.
- Primera estructura general.

## 10.2. Febrer - primers moduls funcionals

### 4 de febrer

Es va avançar en funcionalitats inicials del projecte. Aquesta etapa va servir per començar a donar forma a l'aplicacio i provar que l'estructura funcionava.

### 10 de febrer

Es va treballar en el modul de motos i les primeres parts de rutes. Aquest moment marca l'inici de dues funcionalitats centrals: gestio de motos i planificacio de rutes.

### 17 de febrer

Es va continuar amb motos i rutes. La feina es va centrar en millorar el que ja hi havia i fer que les dues parts estiguessin mes connectades.

### 18 de febrer

Es van fer proves amb quedades. Aquesta funcionalitat amplia el projecte cap a una part mes social, permetent organitzar sortides o events.

### 20 de febrer - operacio i parada

El dia 20 de febrer em van operar dels creuats. A partir d'aquest moment hi va haver una parada real del ritme de treball. Per aquest motiu, encara que a Git apareguin commits posteriors dins de febrer, no s'han d'interpretar com dies normals de desenvolupament.

En aquesta part del projecte ja hi havia feina feta abans en motos, rutes i quedades, pero la recuperacio va afectar la planificacio i va fer que el desenvolupament no pogues seguir el ritme previst.

### 23-28 de febrer - commits de registre, builds i ajustos

Durant aquests dies apareixen commits relacionats amb deixar constancia de l'estat del projecte, pujar canvis, provar builds i ajustar parts de rutes, Android i produccio.

No ho explicaria com una setmana normal de feina, perque venia just despres de l'operacio. Es mes correcte entendre aquesta part com una etapa de treball molt irregular, amb pujades de codi i ajustos puntuals sobre feina que ja estava bastant encaminada.

En aquesta fase el projecte ja tenia una primera base funcional, pero encara faltava molt per polir. Es començaven a veure moduls importants com motos, rutes, quedades i vendes, pero no era encara una versio final.

També es van fer proves amb Android i fitxers de build. Aixo va ajudar a comprovar que el projecte podia començar a funcionar fora de l'entorn local, encara que quedessin errors i millores pendents.

## 10.3. Primera setmana de març - recuperacio, build i correccions

Durant la primera setmana de març el ritme encara va estar condicionat per la recuperacio de l'operacio. A Git apareixen diferents commits el dia 1 de març, sobretot relacionats amb build, correu i correccions.

### 1 de març

Es van fer diversos commits relacionats amb build, correu i correccions. Aixo indica una etapa de proves i ajustos.

També hi va haver feina en verificacio o refresc de correu, una part important per l'autenticacio.

## 10.4. Parada per motius personals

La parada principal ve marcada per l'operacio dels creuats del dia 20 de febrer i la recuperacio posterior. Aquesta situacio va afectar el ritme del projecte i va fer que despres calgues reprendre el treball amb mes intensitat.

Aquesta situacio tambe explica per que la planificacio real no coincideix al 100% amb la planificacio estimada. En un projecte real poden aparèixer imprevistos, i en aquest cas es va haver d'adaptar el calendari.

## 10.5. Març - app, gravacio, offline i legal

### 18 de març

Es va reprendre el projecte amb la part d'aplicacio connectada amb backend i gravacio de rutes. Aquesta etapa va ser important per portar el projecte cap a un us mes mobil i real.

### 24 de març

Es van fer millores generals. Aquesta fase va servir per corregir problemes i afegir detalls a funcionalitats ja existents.

### 25 de març

Es va treballar en inici, login i pantalla show. Aixo indica una millora de pantalles basiques i fluxos d'entrada.

### 26 de març

Es va treballar en rutes, vores/estils i funcionalitat offline. Aquest dia es veu clarament la intencio de millorar el modul de rutes i preparar-lo per situacions sense connexio.

### 29 de març

Es van afegir idiomes i pagines legals. Aquesta fase es important perque apropa el projecte a una aplicacio mes completa, no nomes funcional. Les pagines legals tambe son necessaries si es pensa en publicacio o proves en entorns com Google Play.

### 30 de març

Es va treballar en l'eliminacio de compte. Aquesta funcionalitat es important per privacitat i control de dades de l'usuari.

## 10.6. Abril - xats, iOS, tracks i poliment final

### 1 d'abril

Es va començar una etapa important de xats i codis. Tambe es va fer build. El xat es un dels moduls amb mes interaccio entre usuaris.

### 2 d'abril

Es van afegir dades demo i es van corregir diferents problemes de seeders i columnes. Tambe es va corregir un error de metode PUT en l'edicio de rutes.

Aquesta etapa reflecteix problemes tipics quan la base de dades evoluciona i cal ajustar dades de prova.

### 4 d'abril

Es van solucionar problemes d'iOS relacionats amb background location, notificacions i permisos. Aquesta part va ser important perque iOS requereix configuracions especifiques.

### 5 d'abril

Es van fer canvis generals, builds i treball amb tracks. Aquesta fase va reforçar la part de recorreguts gravats.

### 6 d'abril

Hi ha molts commits relacionats amb tracks, meetings, xats i botons. Va ser un dia de molta feina en funcionalitats socials i millores d'interfície.

### 7 d'abril

Es va treballar en tutorial i actualitzacio del xat. Aixo ajuda a millorar la comprensio de l'app i l'experiencia d'usuari.

### 9 i 10 d'abril

Es va dedicar molta feina al xat: diferents intents, estils, ajustos de layout, responsive i correccions. Aquesta part mostra que el xat va ser un modul que va necessitar moltes proves fins quedar mes estable.

### 11 d'abril

Es va fer una revisio o millora general del projecte.

### 14 d'abril

Es va treballar molt en navegacio, rutes, links i botons. Aquesta fase representa el poliment final, fent que l'aplicacio fos mes facil d'utilitzar.

### 24 i 26 d'abril

Es van afegir fitxers i es va treballar en la memoria. Aixo correspon a la fase final del projecte, documentacio i tancament.

## 10.7. Resum del seguiment

El seguiment mostra una evolucio clara:

- Gener: preparacio tecnica.
- Febrer: motos, rutes, quedades, vendes i primera versio funcional.
- Març: app mobil, gravacio, offline, login, legal i idiomes.
- Abril: xats, iOS, tracks, navegacio, botons, build i memoria.

La metodologia real ha estat incremental. No s'ha fet una funcionalitat sencera i despres s'ha oblidat, sino que s'han anat tocant diferents moduls a mesura que sortien necessitats.

Aixo es veu especialment en rutes i xats, que han tingut moltes iteracions fins arribar a una versio mes estable.