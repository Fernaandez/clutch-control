# Clutch Control — Guía de auditoría total (SOLO LECTURA)

> **Para el modelo / agente de IA:** este documento define tu misión. Léelo entero antes de hacer nada.
>
> **Regla absoluta:** NO modifiques ningún archivo. NO crees commits. NO propongas parches todavía. Solo explora, lee, entiende y escribe un informe.

---

## 1. ¿Qué es este proyecto?

**Clutch Control** es una aplicación para motoristas. Combina en un solo producto:

- **Gestión de motos** (garaje, kilometraje, mantenimientos, reparaciones, mejoras, documentación)
- **Rutas** (planificar, grabar, historial, rutas habituales, mapas con Leaflet)
- **Quedadas / eventos** (organizar salidas en grupo)
- **Mercado de venta** (anuncios de motos, favoritos, historial compartible)
- **Chat en tiempo real** (Pusher)
- **App móvil** (Capacitor — iOS y Android) además de web

**Producción:** https://clutchcontrol.es  
**Repo:** https://github.com/Fernaandez/clutch-control

---

## 2. Tu misión en una frase

> Examinar **absolutamente todo** el proyecto para entender qué hay, cómo funciona, qué infraestructura paga el usuario, cuál es la filosofía del producto, qué está bien hecho y qué habría que cambiar en un remodel completo — **sin tocar una sola línea de código**.

---

## 3. Filosofía del producto (lo que debes entender)

El usuario no quiere una app genérica de rutas (tipo Wikiloc/Calimoto sueltos). Quiere un **ecosistema del motorista**:

| Prioridad | Criterio | Qué significa |
|-----------|----------|---------------|
| **P0** | Que funcione | Bugs en producción son inaceptables (OAuth, chats, editar rutas…) |
| **P1** | Valor único | Historial de moto compartible, quedadas, mercado integrado — cosas que la competencia no junta |
| **P2** | Retención | Seguro/ITV con recordatorios, rutas recurrentes, recomendador de rutas |
| **P3** | Polish UX | Títulos minimalistas, navegación coherente (`smartBack`), botones de volver unificados (turquesa/neon) |
| **P4** | Monetización | Premium, destacados en ventas (fase posterior) |

**Patrón UI unificado** que el proyecto ya intenta seguir:
- Botón volver: círculo, borde turquesa (`brand-neon`), icono flecha
- Navegación: composable `smartBack()` en `@/Composables/navigationStack.js`
- Estilo: dark + acentos turquesa/neon (`brand-neon`, `brand-dark`)

**Eliminar** lo que confunde: filtros vacíos, textos obsoletos, descripciones redundantes bajo títulos.  
**Conservar** lo que aporta valor único aunque esté imperfecto.

Al final de la auditoría debes poder explicar si el código actual refleja esta filosofía o se ha desviado.

---

## 4. Stack técnico (referencia rápida)

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3 + Inertia 2 + Vite 6 |
| Estilos | Tailwind CSS 3/4 |
| Estado | Pinia (+ persisted state) |
| i18n | vue-i18n (ca, es, en) |
| Mapas | Leaflet, leaflet-routing-machine, OSRM |
| Tiempo real | Pusher + Laravel Echo |
| Push móvil | Firebase FCM (Capacitor) |
| Auth | Laravel Breeze + Sanctum + Google OAuth (Socialite) |
| Storage fotos | Cloudflare R2 (S3-compatible) |
| Email | Mailtrap (prod) / Resend en deps |
| Deploy | Laravel Forge + Hetzner CX23 (~17 €/mes) |
| DNS/SSL | Cloudflare + Let's Encrypt |
| Mobile | Capacitor 8 (iOS + Android) |

---

## 5. Infraestructura que el usuario YA PAGA (evaluar si se usa bien)

Debes mapear **qué servicio existe, para qué sirve en la app, si está bien integrado y si merece conservarse** en el remodel:

| Servicio | Coste aprox. | Uso en la app |
|----------|--------------|---------------|
| **Hetzner Cloud** (VPS CX22/CX23) | ~4–5 €/mes | Servidor producción |
| **Laravel Forge** | ~12 $/mes | Deploy, Nginx, PHP, MySQL, queues, scheduler, SSL |
| **Dominio clutchcontrol.es** | ~10 €/año | Producción |
| **Cloudflare** | Gratis / bajo | DNS, posiblemente R2 |
| **Cloudflare R2** | Gratis hasta 10 GB | Fotos (`media.clutchcontrol.es`) |
| **Mailtrap** | Según plan | SMTP recordatorios email |
| **Pusher** | Según plan | Chat tiempo real |
| **Firebase** | Gratis tier | Push notifications móvil |
| **Stadia Maps** (opcional) | Free tier | Tiles de mapa oscuros (`VITE_STADIA_API_KEY`) |

**Archivos clave de infra:**
- `docs/DEPLOY_HETZNER.md`
- `docs/HANDOFF_NOU_CHAT.md`
- `.env.example` / `.env.production.example`
- `config/filesystems.php`, `config/broadcasting.php`, `config/services.php`
- `capacitor.config.json`
- `android/`, `ios/`

**Preguntas que debes responder:**
1. ¿La infra actual es la adecuada o hay redundancias?
2. ¿Hay servicios configurados pero no usados?
3. ¿Hay funcionalidades que dependen de servicios no configurados en local?
4. ¿Qué se puede simplificar sin perder capacidades?

---

## 6. Documentos que DEBES leer primero

Antes de explorar código al azar, lee estos archivos en orden:

1. `docs/HANDOFF_NOU_CHAT.md` — estado actual, qué está hecho, qué falta
2. `PROJECT_ROADMAP.md` — bugs y mejoras pendientes por módulo
3. `docs/FABLE_MASTER_PLAN.md` — plan de sesiones y criterios de prioridad
4. `docs/DEPLOY_HETZNER.md` — arquitectura de producción
5. `memoria-redaccio-bloc-1.md` (y blocs 2–3 si existen) — visión académica/negocio del producto
6. `.cursor/plans/` — planes guardados en Cursor (si existen)

Estos documentos son la **voz del usuario**. Contrasta lo que dice con lo que encuentras en el código.

---

## 7. Mapa del repositorio — qué examinar

Recorre **cada área** y documenta qué hay, calidad, deuda técnica y coherencia con la filosofía.

### 7.1 Backend — Laravel

```
app/
├── Http/Controllers/     → Lógica de cada módulo
├── Http/Middleware/      → Auth, admin, Inertia shared data
├── Http/Requests/        → Validaciones
├── Models/               → 17 modelos (ver lista abajo)
├── Services/             → Lógica de negocio extraída (si existe)
├── Jobs/                 → Colas (recordatorios, etc.)
├── Events/               → Broadcasting
├── Policies/             → Autorización
└── Providers/            → Bootstrapping

routes/
├── web.php               → Rutas Inertia principales
├── api.php               → API móvil / externa
└── auth.php              → Login, registro, OAuth

database/
├── migrations/           → Esquema completo de BD
├── seeders/              → Datos de prueba
└── factories/            → Factories para tests

config/                   → Toda la configuración
bootstrap/app.php         → Scheduler, middleware (Laravel 12)
tests/                    → Cobertura de tests
```

**Modelos a entender:**
`User`, `Motorcycle`, `Route`, `RouteWaypoint`, `RouteCategory`, `RouteReview`, `Trip`, `HabitualRoute`, `Event`, `MaintenanceTask`, `MaintenanceLog`, `SaleListing`, `SaleImage`, `Conversation`, `Message`, `Report`

### 7.2 Frontend — Vue + Inertia

```
resources/js/
├── Pages/                → 68+ páginas Vue (por módulo)
│   ├── Dashboard.vue
│   ├── Motorcycles/
│   ├── Routes/           → Hub, Create, Edit, Show, FreeRide, Plan, Habitual, MyRoutes, Pending
│   ├── Events/           → Quedadas
│   ├── Sales/            → Mercado
│   ├── Chats/
│   ├── Trips/
│   ├── Maintenance/, Repairs/, Upgrades/
│   ├── Admin/
│   ├── Auth/
│   └── Legal/
├── Components/           → Componentes reutilizables
├── Composables/          → navigationStack, etc.
├── Layouts/              → AppLayout
├── locales/              → ca.json, es.json, en.json
└── app.js / bootstrap.js → Entry points
```

### 7.3 Mobile — Capacitor

```
capacitor.config.json
android/
ios/
```

Plugins usados: geolocation, background-geolocation, push-notifications, local-notifications, browser.

### 7.4 Build y assets

```
vite.config.js
tailwind.config.js
public/build/             → Assets compilados (no editar, solo saber que existe)
```

### 7.5 Lo que NO necesitas leer línea a línea

- `vendor/`, `node_modules/`
- `storage/framework/views/` (cache)
- `public/build/assets/*.js` (bundles minificados)
- `.git/`

Sí debes **saber que existen** y si el flujo de build/deploy es correcto.

---

## 8. Módulos funcionales — checklist de auditoría

Para **cada módulo**, responde:

- ¿Qué hace según el código?
- ¿Qué hace según la documentación del usuario?
- ¿Coinciden?
- ¿Qué funciona bien?
- ¿Qué está roto o incompleto?
- ¿Qué cambiarías en un remodel?
- ¿Nivel de deuda técnica? (baja / media / alta)

### Módulos obligatorios:

| # | Módulo | Archivos principales |
|---|--------|----------------------|
| 1 | **Auth & perfil** | `Auth/*`, Google OAuth, verificación email |
| 2 | **Dashboard** | `Dashboard.vue`, widget moto activa |
| 3 | **Motos / garaje** | `Motorcycles/*`, modelo `Motorcycle` |
| 4 | **Mantenimiento / reparaciones / mejoras** | `Maintenance/*`, `Repairs/*`, `Upgrades/*` |
| 5 | **Rutas** | `Routes/*`, Leaflet, routing, waypoints |
| 6 | **Viajes / trips** | `Trips/*`, grabación GPS, historial |
| 7 | **Quedadas / eventos** | `Events/*`, modelo `Event` |
| 8 | **Mercado / ventas** | `Sales/*`, favoritos, visibilidad |
| 9 | **Chats** | `Chats/*`, Pusher, conversaciones |
| 10 | **Admin** | `Admin/*`, moderación, reports |
| 11 | **Legal & reports** | Privacy, Terms, `Report` |
| 12 | **Notificaciones** | FCM, local notifications, email jobs |
| 13 | **i18n** | Traducciones ca/es/en, cobertura |
| 14 | **Mobile** | Capacitor, permisos GPS, push |

### Bugs conocidos (verificar en código si siguen presentes):

Según `PROJECT_ROADMAP.md`:

- [ ] Google OAuth: `redirect_uri_mismatch`
- [ ] Chats: mensajes NO se envían
- [ ] Rutas: botón "Copiar ruta" roto
- [ ] Rutas: error al editar
- [ ] Mapa: punto azul de ubicación no visible (iOS)
- [ ] Quedadas: botón Guardar roto; validación asistentes máximos
- [ ] Ventas: visibilidad marca "Oculta" estando pública
- [ ] Ventas: navegación "mis anuncios" → vuelve a mercado en vez de mis anuncios

---

## 9. Preguntas estratégicas que el informe debe responder

### Sobre el producto
1. ¿Cuál es la propuesta de valor real hoy vs. la visionada?
2. ¿Qué módulos son el núcleo y cuáles son secundarios?
3. ¿La UX actual es coherente entre módulos?
4. ¿Qué features del roadmap (Fases 1–5) tienen base en el código actual?

### Sobre el código
5. ¿La arquitectura Laravel es limpia o hay lógica en controllers gordos?
6. ¿El frontend está bien organizado o hay duplicación masiva?
7. ¿Hay patrones consistentes (navegación, formularios, validación, errores)?
8. ¿Los tests existen y cubren algo relevante?
9. ¿Hay código muerto, dependencias sin usar, archivos duplicados?

### Sobre infra y costes
10. ¿Los ~17 €/mes de infra se justifican para el estado actual?
11. ¿Hay alternativas más simples para algún servicio?
12. ¿El flujo deploy (Forge + GitHub webhook) está documentado y es reproducible?

### Sobre el remodel
13. ¿Refactorizar in-place o reescribir módulos enteros?
14. ¿Qué se conserva tal cual, qué se rehace, qué se elimina?
15. ¿Orden recomendado de remodel (dependencias)?
16. ¿Riesgos principales (datos en prod, usuarios activos, etc.)?

---

## 10. Formato del informe de salida

Al terminar la auditoría, entrega un informe con esta estructura:

```markdown
# Informe de auditoría — Clutch Control
Fecha: [fecha]
Modo: Solo lectura (sin cambios)

## Resumen ejecutivo (5–10 líneas)
[Qué es la app, estado general, veredicto rápido]

## Mapa del proyecto
[Árbol simplificado de carpetas + propósito de cada área]

## Infraestructura y costes
[Tabla: servicio → uso → estado → recomendación conservar/cambiar/eliminar]

## Módulos funcionales
### [Nombre módulo]
- Propósito:
- Estado: ✅ Bien / ⚠️ Mejorable / ❌ Roto / 🚧 Incompleto
- Lo bueno:
- Lo malo / deuda técnica:
- Cambios propuestos (sin implementar):

[Repetir por cada módulo]

## Bugs confirmados vs. documentados
[Tabla: bug → confirmado en código sí/no → gravedad → causa probable]

## Coherencia con la filosofía del producto
[¿El código refleja P0–P4? ¿Dónde se desvía?]

## Roadmap cruzado
[PROJECT_ROADMAP vs. realidad del código vs. FABLE_MASTER_PLAN]

## Propuesta de remodel (solo plan, sin código)
### Conservar tal cual
### Mejorar / refactorizar
### Reescribir desde cero
### Eliminar
### Orden de ejecución recomendado

## Riesgos y dependencias
[Prod, usuarios, migraciones, servicios externos]

## Preguntas abiertas para el usuario
[Lo que no pudiste deducir del código y necesitas que confirme]
```

---

## 11. Reglas de conducta para el agente

### ✅ SÍ hacer
- Leer archivos ampliamente — explora todo lo necesario
- Usar subagentes de exploración si hace falta (Grok Explore, Task explore)
- Seguir imports, rutas, relaciones entre modelos
- Leer migraciones para entender el esquema de BD completo
- Comparar docs del usuario vs. código real
- Ser honesto: si algo está mal, dilo claro
- Priorizar por impacto (P0 primero)

### ❌ NO hacer
- **Modificar, crear o borrar archivos**
- **Ejecutar migraciones, npm install, composer update**
- **Hacer commits o push**
- **Proponer código todavía** — solo diagnóstico y plan
- **Inventar funcionalidades** que no existen en el repo
- **Ignorar la infra** — el usuario paga servidor y dominio, eso importa
- **Auditoría superficial** — el usuario pide repaso a fondo de absolutamente todo

---

## 12. Prompt listo para copiar y pegar

Usa este prompt en un chat nuevo (modelo recomendado: **Opus 5** o **Sonnet 5**):

```
Modo SOLO LECTURA. NO modifiques ningún archivo.

Lee docs/GUIA_AUDITORIA_TOTAL.md y sigue sus instrucciones al pie de la letra.

Tu misión: auditoría completa de Clutch Control — examinar todo el repositorio,
entender la filosofía del producto, mapear infraestructura (Hetzner, Forge, R2,
Pusher, Firebase, dominio clutchcontrol.es), analizar cada módulo funcional,
confirmar bugs del PROJECT_ROADMAP.md, y entregar el informe con el formato
definido en la sección 10.

Explora a fondo: backend Laravel, frontend Vue/Inertia, Capacitor mobile,
rutas, modelos, migraciones, config, tests, docs del proyecto.

Al final: qué está bien, qué cambiarías, plan de remodel (sin escribir código).

ZERO cambios. ZERO commits. Solo mirar y reportar.
```

---

## 13. Después de la auditoría

Cuando el informe esté listo, el usuario lo leerá y decidirá:

1. Validar el diagnóstico
2. Responder preguntas abiertas
3. Lanzar el remodel en un chat separado (ahí sí se podrá tocar código)

**Este documento es la fase 0. La fase 1 (implementación) viene después.**

---

*Documento creado para Clutch Control — auditoría previa al remodel completo.*
