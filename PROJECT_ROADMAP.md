# Full de Ruta - Millores Clutch Control

## 🔐 Log In
- [ ] OAUTH: Arreglar l'inici de sessió amb Google. Error: `Error 400: redirect_uri_mismatch` ("Acceso bloqueado: la solicitud de esta aplicación no es válida").

## 🏠 Dashboard
- [x] Manteniments/Reparacions/Millores: Afegir funcionalitat de "show" (inspeccionar la targeta sense editar-la).
- [x] Historial: Poder inspeccionar les targetes i compartir-les a interessats en comprar la moto.
- [x] Botó "Registrar Volta en moto": Fer-lo més visible.
- [x] Sumar KM: Treure el botó (es suma amb les rutes i es pot editar directament editant la moto).
- [x] Botons de "Tornar" / "Cancel·lar": Unificar disseny. Han de ser a dalt a l'esquerra, estil botó "Afegir" però en blau turquesa i amb fletxa cap a l'esquerra.
- [x] Botó de "Canviar moto": Fer-lo més visible i/o canviar-lo de lloc.
- [x] Crear moto - Marca/Model: Posar un desplegable amb cerca (base de dades) per la marca i model per evitar errors humans.
- [x] Crear moto - CC/CV: Autocompletar dades reals de cilindrada i cavalls segons el model escollit (que es puguin editar després per si hi ha errors).

## 🗺️ Rutes
- [ ] Títols de les pantalles: Més minimalistes, una paraula fixa, treure la descripció.
- [ ] Punt d'inici: Millorar la UX/UI a l'hora d'iniciar la ruta des d'un altre lloc (dilema: "com gestionar que estiguis fora del punt d'inici i que sigui fàcil d'entendre").
- [ ] Botó "Copiar ruta": Arreglar-lo perquè no funciona.
- [ ] Mapa - Ubicació actual: Revisar (iOS/General) que el punt blau de la ubicació actual no es veu al mapa genèric (es veu a l'iniciar la ruta però).
- [ ] Show de ruta - Botó sortir (Creueta): Fer-lo del color blau turquesa i icona de fletxa com al dashboard.
- [ ] Les Meves Rutes - Llista:
    - [ ] Botó de tornar: Més visible, fletxa blava com al dashboard.
    - [ ] Títol: Minimalista i sense descripció.
- [ ] Nova funcionalitat "Recorreguts": Poder enregistrar i veure al mapa les diferents passades/recorreguts d'una mateixa ruta per comparar per on vas anar cada cop. Afegir accés ràpid des de les meves rutes.
- [ ] Edició de Rutes: Arreglar error a l'editar rutes.

## 🤝 Quedades
- [ ] Títols de les pantalles: Minimalistes, una sola paraula, treure descripció.
- [ ] Llista de quedades: Afegir "KM totals" i "Quantitat de rutes".
- [ ] Targeta Quedada: Millorar visibilitat del requadre del dia (no es veu amb la imatge de fons).
- [ ] Edició de Quedades: Arreglar el botó/acció de "Guardar". Validar que no es puguin abaixar els assistents màxims a un número inferior als assistents actualment apuntats.
- [ ] Navegació Ruta->Quedada: Si s'obra una ruta des d'una quedada, al sortir-ne ha de tornar a la quedada, no a l'apartat de Rutes.
- [ ] Botó "Tornar": Fer-lo més visible al detall d'una quedada (igual que dashboard/rutes).
- [ ] Mapa de la quedada: Botó "X" per sortir més visible (igual que dashboard).

## 💰 Vendes
- [ ] Títol principal: Minimalista, treure la descripció.
- [ ] Detall de Vendes (Show):
    - [ ] Fitxa tècnica: Treure la icona de la moto; deixar només la vista de l'ull (vistes) i el *pivote* de la ubicació.
    - [ ] Estat de visibilitat: Revisar perquè marca "Oculta" tot i estar pública.
    - [ ] Historial de la Moto en l'anunci: Mostrar el manteniment/historial (Integrar avís/UIX prèvia perquè els usuaris decideixin o tinguin garanties abans de penjar factures amb dades personals).
- [ ] Cerca de motos al mercat: Utilitzar base de dades per buscar marca i model (igual que al crear la moto, sense errors humans).
- [ ] Favorits:
    - [ ] Títol: Minimalista, sense descripció.
    - [ ] Botó tornar: Fletxa blau turquesa a dalt a la dreta (unificar o seguir criteris de tots els de sortir, però has demanat "a dalt a la dreta" en aquest cas per tancar modal/show).
    - [ ] Filtres: Treure els filtres (no calen per poques motos).
    - [ ] Missatge "Cap moto trobada": Treure caixes grosses i el botó text "Veure-ho tot" si no hi ha favorits. Millorar-ho a "No tens motos a favorits".
- [ ] Els meus anuncis:
    - [ ] Títol: Minimalista.
    - [ ] Botó tornar: Fletxa blava amunt a l'esquerra.
    - [ ] Filtres: Treure'ls completament.
    - [ ] Detall dels MEUS anuncis: Adaptar UI semblant a la targeta del mercat principal.
    - [ ] Botó de tornar del detall (Dels meus anuncis): Ha de tornar a "Els meus anuncis", ara torna a "Mercat directe" i és un error.
- [ ] Posar a la venda (Crear anunci):
    - [ ] Títol: Minimalista.
    - [ ] Botó tornar: Fletxa blava.
    - [ ] Text telèfon: Treure l'avís de "Sense telèfon", perquè ja no es fa servir número de telèfon.
    - [ ] UIX/Privacitat documents: Garantir la privacitat del tema factures de l'historial quan es posa a la venda.

## 💬 Xats
- [ ] Safata d'entrada: Títol minimalista i sense descripció. (Els dissenys dels xats estan bé).
- [ ] Grups: Si el xat és un grup, poder veure'n els integrants.
- [ ] Header i Footer del xat actual: Fixar el textarea a sota (footer) i el títol del xat a dalt (header) sense que es moguin al fer scroll pels missatges.
- [ ] Enviament: Els missatges NO s'envien. Revisar protocols (Pusher/WebSockets, backend, o permisos).
