# Clutch Control — Pla mestre per Fable 5.0

**Objectiu:** arreglar, revisar i millorar l'app fins que sigui competitiva — **sense cremar la quota en una sola sessió**.

**Com usar aquest document:**
1. Obre un **xat nou** per cada sessió (S0, S1, S2…).
2. Selecciona el model **Fable 5.0** només per sessions marcades `[FABLE]`.
3. Copia el **prompt de la sessió** (al final de cada bloc) com a primer missatge.
4. Quan acabis una sessió, marca `[x]` els ítems fets i actualitza la secció **Progrés**.
5. **Mai** demanis "ho fes tot" en un sol xat.

**Context ràpid:** Laravel 12 + Vue 3 + Inertia 2 + Capacitor + Leaflet + Pusher + Firebase FCM. Producció: `https://clutchcontrol.es`. Veure també `docs/HANDOFF_NOU_CHAT.md` i `PROJECT_ROADMAP.md`.

---

## Regles d'or (obligatòries per Fable)

### Què SÍ fer
- **Una sessió = un àmbit o un bug concret.** Exemple: "S3 — Copiar ruta + edició de rutes".
- Llegir **només** els fitxers necessaris per a la tasca (no escanejar tot el repo).
- Canvis **mínims**: arreglar el bug o la feature demanada, res més.
- Reutilitzar patrons existents (botó enrere, `smartBack`, traduccions `$t()`, colors `brand-neon`).
- Marcar al final de cada sessió: fitxers tocats, què queda pendent, com provar-ho.
- Actualitzar `PROJECT_ROADMAP.md` només si l'usuari ho demana o si acabes una tasca del roadmap.

### Què NO fer (estalvia tokens)
- No refactoritzar arquitectura global ni "netejar tot el codi".
- No reescriure components que ja funcionen.
- No afegir tests, docs ni README llevat que la sessió ho demani.
- No explorar carpetes senceres "per curiositat".
- No implementar més d'**1 feature nova** per sessió (bugs en batch sí, dins del mateix àmbit).
- No tocar `.env`, credencials, Forge ni deploy llevat que la sessió ho indiqui explícitament.
- No obrir subagents ni Task explore llevat que calgui per un bug concret amb stack trace.

### Patró UI unificat (referència ràpida)
Botó tornar estàndard (ja usat a Routes, Chats, Sales…):
```vue
<button type="button" @click="goBack"
  class="inline-flex items-center justify-center w-10 h-10 flex-shrink-0 rounded-full bg-brand-dark border border-brand-neon/50 text-brand-neon hover:bg-brand-neon hover:text-brand-black transition shadow-[0_0_10px_rgba(12,225,181,0.2)]"
  aria-label="Enrere">
```
Navegació: `smartBack(route('...'))` des de `@/Composables/navigationStack.js`.

---

## Criteris "app competitiva" (prioritat de negoci)

Per decidir què afegir/eliminar, Fable ha de prioritzar això:

| Prioritat | Criteri | Exemples a Clutch Control |
|-----------|---------|---------------------------|
| P0 | **Funciona** — res trencat en producció | Xats no s'envien, OAuth Google, editar rutes |
| P1 | **Valor únic** — diferencia de Calimoto/Wikiloc genèric | Historial moto compartible, quedades, mercat integrat |
| P2 | **Retenció** — l'usuari torna cada setmana | Seguro/ITV + recordatoris, rutes recurrents, recomanador |
| P3 | **Polish UX** — coherència visual i navegació | Títols minimalistes, botons enrere, flux quedada→ruta |
| P4 | **Monetització** — després de P0–P2 | Premium, destacats Sales (Fase 5 del pla) |

**Eliminar** coses que confonen o no aporten: filtres buits, textos obsolets ("Sense telèfon"), descripcions redundants als títols.

**Afegir** només si compleix P0–P2 o és polish ràpid (<30 min) dins una sessió de UI.

---

## Mapa de sessions (ordre recomanat)

```
S0 Auditoria (Ask, barat)     → llista prioritzada, zero codi
S1 Bugs crítics               → login + xats
S2 Rutes — bugs               → copiar, editar, mapa ubicació
S3 Rutes — UX + polish        → títols, enrere, show
S4 Quedades                   → guardar, navegació, targetes
S5 Vendes                       → visibilitat, navegació, favorits
S6 Fase 1 Seguro + ITV          → feature nova (pla existent)
S7 Fase 2 Rutes retroactives
S8 Fase 3 Recomanador
S9 Fase 4 Planificador auto
S10 Fase 5 Monetització
S11 Recorreguts (feature gran)  → només després de S2 estable
S12 Passada final UX            → unificar títols/enrere restants
```

**Estimació quota:** ~1 sessió = 1 xat = un bloc de treball. No en facis 3 seguides sense provar l'app tu mateix.

---

## S0 — Auditoria ràpida `[ASK]` (no Fable Agent encara)

**Durada objectiu:** 5–10 min de quota. **Sense escriure codi.**

Fable ha de:
1. Llegir `PROJECT_ROADMAP.md`, `docs/HANDOFF_NOU_CHAT.md` i aquest fitxer.
2. Llistar P0/P1 pendents agrupats per àmbit.
3. Proposar ordre S1–S12 confirmant dependències.
4. **No** implementar res.

### Prompt S0
```
Mode Ask. Llegeix docs/FABLE_MASTER_PLAN.md, PROJECT_ROADMAP.md i docs/HANDOFF_NOU_CHAT.md.
Fes una auditoria: llista P0/P1/P2 pendents per àmbit (Login, Rutes, Quedades, Vendes, Xats, Features noves).
Proposa ordre de sessions S1–S12. ZERO codi, ZERO exploració profunda del repo.
Màxim 40 línies de resposta.
```

---

## S1 — Bugs crítics `[FABLE]`

**Àmbit:** Login Google + Xats que no s'envien.

| # | Tasca | Fitxers probables |
|---|-------|-------------------|
| 1.1 | OAuth `redirect_uri_mismatch` — documentar URI correcta a Google Console + verificar `config/services.php`, routes callback | `.env.example`, Socialite controller |
| 1.2 | Missatges xat no s'envien — Pusher, broadcasting, `Chats/Show.vue`, controller store | `routes/channels.php`, Chat controller, Echo config |

**Criteri d'èxit:** login Google funciona (o instruccions clares si cal canvi a Google Console); enviar missatge funciona en web.

**No incloure:** grups, header sticky del xat (S5/S12).

### Prompt S1
```
Agent. Llegeix docs/FABLE_MASTER_PLAN.md — sessió S1 només.
Arregla: (1) Google OAuth redirect_uri_mismatch, (2) xats que no envien missatges.
Llegeix només fitxers implicats. Canvis mínims. No toquis UI de xats excepte si cal per l'enviament.
Al final: fitxers tocats + com provar + què queda per S12.
```

**Progrés S1:** [ ] 1.1 OAuth  [ ] 1.2 Xats

---

## S2 — Rutes: bugs `[FABLE]`

**Àmbit:** funcionalitat trencada a rutes.

| # | Tasca | Notes |
|---|-------|-------|
| 2.1 | Botó "Copiar ruta" no funciona | |
| 2.2 | Error en editar rutes | |
| 2.3 | Punt blau ubicació actual invisible al mapa genèric (iOS/general) | Comparar amb mapa "iniciar ruta" |

**No incloure:** Recorreguts, planificador, títols minimalistes (S3).

### Prompt S2
```
Agent. Sessió S2 de docs/FABLE_MASTER_PLAN.md.
Arregla: copiar ruta, editar rutes, ubicació actual al mapa genèric.
Només resources/js/Pages/Routes/* i controllers/routes relacionats. Canvis mínims.
Prova mentalment fluxos Create/Edit/Show/MyRoutes. Resum final curt.
```

**Progrés S2:** [ ] 2.1 Copiar  [ ] 2.2 Editar  [ ] 2.3 Mapa

---

## S3 — Rutes: UX i polish `[FABLE]` o Composer

| # | Tasca |
|---|-------|
| 3.1 | Títols minimalistes (una paraula, sense descripció) — Index, MyRoutes, Create, Show |
| 3.2 | Botó sortir Show ruta: turquesa + fletxa (no creueta sola) |
| 3.3 | MyRoutes: botó enrere visible, mateix patró que Dashboard |
| 3.4 | Punt d'inici fora del waypoint — UX clara (copy + botó "Iniciar des d'aquí" o similar) |

**Consell quota:** S3 es pot dividir en S3a (títols+enrere) i S3b (punt d'inici) si cal.

### Prompt S3
```
Agent. Sessió S3 docs/FABLE_MASTER_PLAN.md.
Polish UX rutes: títols minimalistes, botons enrere unificats (patró brand-neon del doc), show ruta, MyRoutes.
Punt d'inici fora del waypoint: proposta UX mínima i implementació.
No toquis bugs ja resolts a S2. Canvis mínims per fitxer.
```

**Progrés S3:** [ ] 3.1 Títols  [ ] 3.2 Show  [ ] 3.3 MyRoutes  [ ] 3.4 Punt inici

---

## S4 — Quedades `[FABLE]`

| # | Tasca |
|---|-------|
| 4.1 | Guardar edició quedada trencat |
| 4.2 | Validació: max assistents ≥ assistents apuntats |
| 4.3 | Navegació ruta des de quedada → tornar a quedada (no a Rutes) |
| 4.4 | Targeta dia visible sobre imatge fons |
| 4.5 | KM totals + quantitat rutes a llista |
| 4.6 | Títols minimalistes + botó tornar/mapa X |

### Prompt S4
```
Agent. Sessió S4 docs/FABLE_MASTER_PLAN.md — només Events/Quedades.
Fix guardar edició, validació assistents, smartBack des de ruta oberta des de quedada.
UI: targeta dia, KM/rutes a llista, títols i enrere. Canvis mínims.
```

**Progrés S4:** [ ] 4.1–4.6

---

## S5 — Vendes / Mercat `[FABLE]`

| # | Tasca |
|---|-------|
| 5.1 | Estat visibilitat "Oculta" quan és pública |
| 5.2 | Detall meus anuncis: enrere → "Els meus anuncis" (no Mercat) |
| 5.3 | Cerca marca/model amb BD (com crear moto) |
| 5.4 | Favorits: sense filtres, missatge buit correcte, títol minimal |
| 5.5 | Els meus anuncis: sense filtres, UI detall com targeta mercat |
| 5.6 | Crear anunci: treure avís telèfon; privacitat historial/factures (avís UX) |
| 5.7 | Show venda: fitxa tècnica (ull + ubicació, sense icona moto redundant) |

### Prompt S5
```
Agent. Sessió S5 docs/FABLE_MASTER_PLAN.md — només Sales/* i controllers.
Roadmap PROJECT_ROADMAP secció Vendes. Prioritat: bug visibilitat i navegació enrere.
Cerca marca/model reutilitzant component de motos si existeix. Canvis mínims.
```

**Progrés S5:** [ ] 5.1–5.7

---

## S6 — Fase 1: Seguro + ITV `[FABLE]`

Implementació segons `docs/HANDOFF_NOU_CHAT.md` i pla a `.cursor/plans/clutch_control_improvements_plan_163cee54.plan.md`.

**Entregables:**
- Migració + model `Motorcycle`
- Job + command + scheduler 09:00
- UI Create/Edit/Index + widget Dashboard

### Prompt S6
```
Agent. Sessió S6 docs/FABLE_MASTER_PLAN.md.
Implementa Fase 1 Seguro + ITV segons docs/HANDOFF_NOU_CHAT.md (especificació completa allà).
Una feature, canvis mínims, seguir convencions Laravel 12 del repo.
No Fase 2 encara.
```

**Progrés S6:** [ ] Migració  [ ] Backend  [ ] Frontend  [ ] Scheduler

---

## S7 — Fase 2: Rutes retroactives `[FABLE]`

Veure pla detallat (trips `manual_entry`, `POST apply-to-motorcycle`, modal a Show).

### Prompt S7
```
Agent. Sessió S7 docs/FABLE_MASTER_PLAN.md + pla Fase 2 al plan file.
Aplicació retroactiva de rutes a moto. Només àmbit trips/routes/motorcycle km.
```

**Progrés S7:** [ ] Backend  [ ] Modal Show  [ ] Recurrents (opcional dins sessió)

---

## S8 — Fase 3: Recomanador `[FABLE]`

`RouteRecommendationService` + endpoint + secció a Routes/Index.

### Prompt S8
```
Agent. Sessió S8 docs/FABLE_MASTER_PLAN.md — Fase 3 recomanador.
Servei + endpoint + UI carousel "Recomanades". Algoritme del plan file, sense ML.
Cache 1h per usuari.
```

**Progrés S8:** [ ] Service  [ ] API  [ ] UI

---

## S9 — Fase 4: Planificador automàtic `[FABLE]`

Tabs Manual / A→B / Loop a Create.vue + leaflet-routing-machine.

### Prompt S9
```
Agent. Sessió S9 docs/FABLE_MASTER_PLAN.md — Fase 4 planificador.
Tabs a Routes/Create.vue, OSRM públic per MVP. Zero canvis model Route backend.
Loop: 3 propostes simples. Canvis mínims al tab Manual existent.
```

**Progrés S9:** [ ] A→B  [ ] Loop  [ ] Desar ruta

---

## S10 — Fase 5: Monetització `[FABLE]` (2 xats si cal)

Cashier + Stripe test + Premium + destacats Sales.

### Prompt S10a
```
Agent. Sessió S10a docs/FABLE_MASTER_PLAN.md — setup Cashier, migracions, Billable, webhooks, middleware EnsurePremium.
```

### Prompt S10b
```
Agent. Sessió S10b docs/FABLE_MASTER_PLAN.md — UI Billing + destacats Sales + badges. Stripe test mode.
```

**Progrés S10:** [ ] Cashier  [ ] Premium  [ ] Destacats

---

## S11 — Recorreguts (feature gran) `[FABLE]` — després de S2 estable

**Objectiu:** enregistrar múltiples passades d'una mateixa ruta i comparar-les al mapa.

**Abans de codar:** 1 missatge Ask per disseny (taula `route_passes` vs reutilitzar `trips`, UI a MyRoutes).

### Prompt S11-design (Ask)
```
Ask. Sessió S11 design — docs/FABLE_MASTER_PLAN.md Recorreguts.
Proposta mínima: model, endpoints, UI MyRoutes/Show. 30 línies màx. Sense codi.
```

### Prompt S11-impl (Agent)
```
Agent. Implementa Recorreguts segons el disseny acordat a la sessió anterior. Només aquest àmbit.
```

**Progrés S11:** [ ] Disseny  [ ] Backend  [ ] Mapa compare  [ ] MyRoutes accés

---

## S12 — Passada final UX + Xats `[COMPOSER]` o Fable

**Baixa prioritat, batch únic:**
- Xats: títol minimal, grups integrants, header/footer fixos al scroll
- Títols/enrere restants no fets a S3–S5
- Revisió coherència i textos obsolets

### Prompt S12
```
Agent. Sessió S12 docs/FABLE_MASTER_PLAN.md — polish final.
Xats: header/footer sticky, grups integrants, títol minimal.
Repassa PROJECT_ROADMAP ítems UI encara [ ]. No noves features. Canvis petits per fitxer.
```

**Progrés S12:** [ ] Xats UI  [ ] Títols restants  [ ] Textos obsolets

---

## Checklist revisió qualitat (final de cada sessió)

Fable ha d'auto-verificar abans de tancar:

- [ ] El canvi resol **només** el scope de la sessió
- [ ] Traduccions ca/es/en si s'afegeix text visible (`resources/js/locales/`)
- [ ] Navegació enrere correcta (`smartBack` / ruta explícita)
- [ ] Sense errors linter als fitxers tocats
- [ ] Instruccions de prova manual (3 passos màx.)
- [ ] `PROJECT_ROADMAP.md`: marcar `[x]` només tasques completades aquesta sessió

---

## Progrés global (actualitza manualment)

| Sessió | Estat | Data | Notes |
|--------|-------|------|-------|
| S0 Auditoria | [ ] | | |
| S1 Crítics | [ ] | | |
| S2 Rutes bugs | [ ] | | |
| S3 Rutes UX | [ ] | | |
| S4 Quedades | [ ] | | |
| S5 Vendes | [ ] | | |
| S6 Seguro/ITV | [ ] | | |
| S7 Retroactives | [ ] | | |
| S8 Recomanador | [ ] | | |
| S9 Planificador | [ ] | | |
| S10 Monetització | [ ] | | |
| S11 Recorreguts | [ ] | | |
| S12 Polish final | [ ] | | |

---

## Prompt "emergència" (quota baixa)

Si et queden poc tokens avui:

```
Agent. Quota limitada. Llegeix docs/FABLE_MASTER_PLAN.md.
Fes NOMÉS el primer ítem P0 pendent del progrés global. Màxim 3 fitxers.
Explica en 5 línies. Para.
```

Substitueix "primer ítem P0" pel concret (ex. "copiar ruta").

---

## Què esperar de "app competitiva" al acabar S1–S12

- **Cap flux trencat** (login, xats, rutes, quedades, vendes)
- **Features diferenciadores** en marxa: historial moto, quedades, mercat, seguro/ITV, recomanador, planificador
- **UX coherent** (navegació, títols, botons)
- **Base monetització** preparada (Premium + destacats)
- **Recorreguts** si S11 completada

Això posa Clutch Control al nivell d'una app de moto moderna integrada (gestió + rutes + social + mercat), sense intentar competir amb Calimoto només en navegació GPS offline — el valor és l'**ecosistema tot-en-un**.
