# Deploy Clutch Control a Hetzner Cloud + Laravel Forge

Guia pas a pas per migrar l'app de AlwaysData a una infraestructura escalable
amb **Hetzner Cloud + Laravel Forge + Cloudflare R2** per a les fotos.

Cost mensual aproximat: ~17 €/mes (VPS + Forge + backups + R2 dins quota gratuïta).

---

## Resum del que farem

```
                  Cloudflare R2 (fotos)
                          ▲
                          │
   Usuari ───►  Domini (Cloudflare DNS) ───►  Hetzner CX22
                                                  │
                                                  ├── Nginx + PHP 8.3
                                                  ├── MySQL
                                                  ├── Queue workers (supervisord)
                                                  └── Scheduler (cron)
                                                  
                  Laravel Forge gestiona tot l'anterior
```

---

## Part 1 — Comptes a crear (10 minuts)

### 1.1 Hetzner Cloud
1. Vés a [https://www.hetzner.com/cloud](https://www.hetzner.com/cloud) i crea un compte.
2. Verifica el correu i afegeix mètode de pagament (targeta o PayPal).
3. Un cop dins de la consola, crea un **Project** nou anomenat `clutch-control`.
4. **No** creïs el servidor manualment. Ho farà Forge per tu.

### 1.2 Laravel Forge
1. Vés a [https://forge.laravel.com](https://forge.laravel.com) → Sign Up.
2. Tria el pla **Hobby** (12 $/mes, gestiona 1 servidor i servidors de DB il·limitats).
3. A Forge: **Settings → Server Providers → Hetzner Cloud → Connect**.
   - Genera una API Token a Hetzner: `Project → Security → API Tokens → Generate API Token` amb permís **Read & Write**.
   - Enganxa el token a Forge.

### 1.3 Cloudflare (DNS + R2)
1. Crea compte a [https://dash.cloudflare.com](https://dash.cloudflare.com).
2. Activa **R2** a `R2 Object Storage`. Et demanarà afegir mètode de pagament (per damunt dels 10 GB gratuïts).
3. (Més endavant, a la Part 4) afegirem el domini i el bucket.

### 1.4 Domini
Si encara no en tens:
- Recomanats per preu: [Porkbun](https://porkbun.com), [Namecheap](https://www.namecheap.com), [Cloudflare Registrar](https://dash.cloudflare.com) (al cost, sense marge).
- Mira de comprar un `.com` (~10 €/any) o `.app` / `.es`.

---

## Part 2 — Provisionar el servidor amb Forge (15 minuts)

1. A Forge: **Create Server** → tria **Hetzner Cloud**.
2. Configuració recomanada per començar:
   - **Server Name:** `clutch-control-prod`
   - **Type:** CX22 (2 vCPU shared, 4 GB RAM) — `~4 €/mes`
   - **Region:** `Falkenstein` o `Nuremberg` (latència mínima a Espanya)
   - **PHP version:** 8.3
   - **Database:** MySQL 8
   - **DB name:** `clutch_control`
3. Clica **Create Server**. Forge provisionarà i instal·larà tot (~5-10 min).
4. Quan acabi, guarda les credencials que et mostrarà (root password, sudo password). Les necessitaràs un cop.
5. Activa **Hetzner backups** a Hetzner Console → Servers → clutch-control-prod → Backups (`~1 €/mes`, snapshots diaris).

---

## Part 3 — Connectar el repositori i fer primer deploy (10 minuts)

### 3.1 Configurar el site
1. A Forge → el teu servidor → **Sites**. Per defecte ja hi ha `default`. Crea'n un de nou:
   - **Root Domain:** `your-domain.com` (o subdomini de prova mentre no tinguis el domini definitiu)
   - **Project Type:** General PHP / Laravel
   - **Web Directory:** `/public`
2. Clica **Add Site**.

### 3.2 Connectar Git
1. Al nou site → **Apps** → **Git Repository**.
2. Provider: **GitHub** / **GitLab** / Bitbucket → connecta el compte (un cop).
3. Selecciona el repositori `clutch-control` i la branca (probablement `main` o `master`).
4. **Marca** "Install Composer Dependencies".
5. Deploy.

### 3.3 Configurar `.env`
1. Al site → **Environment** → obre l'editor.
2. Copia el contingut de [`.env.production.example`](../.env.production.example) i substitueix els placeholders amb els valors reals (deixa buits els que encara no tens — Pusher, R2, etc., els omplirem ara).
3. Genera APP_KEY:
   - Al servidor (via Forge → Server → Commands o per SSH):
     ```bash
     cd /home/forge/your-domain.com
     php artisan key:generate --show
     ```
   - Copia la clau resultant a `APP_KEY=base64:...`.

### 3.4 Configurar el deploy script
A Site → **Deployments** → Edit Deploy Script:

```bash
cd /home/forge/your-domain.com
git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Build frontend assets
if [ -f package-lock.json ]; then
    npm ci
else
    npm install
fi
npm run build

# Laravel housekeeping
( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

$FORGE_PHP artisan migrate --force
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache
$FORGE_PHP artisan view:cache
$FORGE_PHP artisan event:cache
$FORGE_PHP artisan storage:link
```

Activa **Quick Deploy** perquè cada push a la branca dispari un deploy automàtic.

### 3.5 Migracions inicials
Com ens dius que les dades actuals són irrellevants, fem deploy net:
1. Al servidor: `php artisan migrate:fresh --force` (només un cop, després ja serà `migrate` automàtic).
2. Si tens seeders importants (categories de rutes, marques de motos): `php artisan db:seed --force`.

---

## Part 4 — Cloudflare R2 per a les fotos (15 minuts)

### 4.1 Crear bucket
1. Cloudflare Dashboard → **R2** → **Create bucket**.
2. Nom: `clutch-control-media`.
3. Location: `EU (Eastern Europe)` — més a prop d'Espanya que `EU (Western Europe)`. (Pots fer servir el que vagi millor de latència segons proves.)

### 4.2 Token d'accés
1. R2 → **Manage R2 API Tokens** → **Create API token**.
2. Permission: **Object Read & Write**.
3. Specify bucket(s): `clutch-control-media`.
4. TTL: forever.
5. Crea i **copia immediatament** les claus que mostra (no es tornen a mostrar):
   - `Access Key ID`
   - `Secret Access Key`
   - `Endpoint URL` — té la forma `https://<ACCOUNT_ID>.r2.cloudflarestorage.com`

### 4.3 Domini públic per al bucket
Les R2 no s'accedeixen directament des d'Internet per `cloudflarestorage.com`. Cal o bé:
- **Opció A (recomanada):** subdomini propi connectat al bucket → URLs com `https://media.your-domain.com/sales/abc.jpg`.
- Opció B: utilitzar el `pub-...r2.dev` (per a testing).

Per a A:
1. Al teu domini a Cloudflare (cal tenir el domini gestionat per Cloudflare per a aquesta opció — més avall ho fem a la Part 5).
2. R2 → el bucket → **Settings** → **Public access** → **Connect Domain** → `media.your-domain.com`.
3. Cloudflare crearà automàticament el registre DNS i el certificat.

### 4.4 Posar credencials a Forge
A Forge → Site → Environment, omple:

```
AWS_ACCESS_KEY_ID=<el-que-has-copiat>
AWS_SECRET_ACCESS_KEY=<el-que-has-copiat>
AWS_DEFAULT_REGION=auto
AWS_BUCKET=clutch-control-media
AWS_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com
AWS_URL=https://media.your-domain.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Desa i fes deploy. A partir d'aquest moment **tota foto pujada va al R2 automàticament** (cap canvi de codi). El truc està a [`config/filesystems.php`](../config/filesystems.php) on el disk `public` salta a S3 si `AWS_BUCKET` està definit.

### 4.5 Test ràpid
SSH al servidor:
```bash
cd /home/forge/your-domain.com
php artisan tinker
>>> Storage::disk('public')->put('test.txt', 'hello R2');
>>> Storage::disk('public')->url('test.txt');
```

Si la URL retornada s'obre i veus "hello R2", funciona.

---

## Part 5 — Domini, DNS i SSL (10 minuts)

### 5.1 Apuntar el domini a Cloudflare
1. Compra el domini (si encara no).
2. Al registrar, **canvia els nameservers** als de Cloudflare (te'ls dirà Cloudflare quan afegeixis el domini al teu compte: Cloudflare Dashboard → Add Site → segueix l'assistent).

### 5.2 Crear el registre A
A Cloudflare DNS:
- **Tipus:** A
- **Name:** `@` (arrel) i un segon registre amb `Name: www`.
- **IPv4 address:** IP pública del servidor Hetzner (te la mostra Forge a la pàgina del servidor).
- **Proxy status:** **DNS only (núvol gris)** durant la primera configuració, després el pots passar a **Proxied (núvol taronja)** un cop SSL configurat.

### 5.3 SSL
A Forge → Site → **SSL** → **Let's Encrypt** → afegeix `your-domain.com` i `www.your-domain.com` → Obtain Certificate.

Quan estigui actiu, ja pots posar Cloudflare en mode **Proxied** i configurar:
- **SSL/TLS mode:** Full (Strict).
- **Always Use HTTPS:** ON.

---

## Part 6 — Queues, Scheduler i Storage Link (5 minuts)

### 6.1 Scheduler
A Forge → Server → **Scheduler** → ja inclou per defecte `php artisan schedule:run` cada minut. Verifica que la tasca està **enabled**. Això és imprescindible per a la Fase 1 del pla (recordatoris ITV/seguro).

### 6.2 Queue worker
A Forge → Server → **Daemons** → **New Daemon**:
- **Command:** `php /home/forge/your-domain.com/artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- **User:** `forge`
- **Directory:** `/home/forge/your-domain.com`
- **Processes:** 1 (puja a 2-3 quan creixi el tràfic)

Forge ho gestiona via supervisord, així que els jobs corren sempre i es reinicien sols si peten.

### 6.3 Storage link
Ja és part del deploy script, però la primera vegada:
```bash
php artisan storage:link
```

(Amb R2 com a disk `public`, aquest enllaç ja no és necessari per a les fotos — ara mateix no farà mal però en serveix per a coses locals de futur.)

---

## Part 7 — Migració dels secrets i serveis externs (variable)

Aquests són els valors que has de portar dels serveis externs al `.env` de Forge:

| Servei | On treure les credencials | Variables `.env` |
|---|---|---|
| **Pusher** (chat) | dashboard.pusher.com → la teva app | `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER` |
| **Firebase** (FCM push) | console.firebase.google.com → Project settings → Service accounts → Generate new private key (JSON) | Puja el JSON via Forge `Files` o SCP a `storage/app/firebase-credentials.json`, configura `FIREBASE_CREDENTIALS` |
| **Google OAuth** | console.cloud.google.com → APIs & Services → Credentials | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |
| **Mail** | Resend, Brevo o Mailgun (3 plans free decent) | `MAIL_*` |

**Important per a Google OAuth:** afegeix el nou redirect URI `https://your-domain.com/auth/google/callback` a la Google Cloud Console abans del primer test.

---

## Part 8 — Capacitor (app mòbil)

L'app de Capacitor té la URL de l'API hardcoded a `capacitor.config.json` o similar. Cal:

1. Buscar a `resources/` o arrel: `grep -r "always" .` per veure URLs antigues.
2. Substituir per `https://your-domain.com`.
3. Recompilar l'APK / IPA i pujar als stores (o distribuir internament).
4. Provar que el login + FCM token segueix funcionant amb la nova URL.

---

## Part 9 — Comprovacions finals

Llista de verificació un cop tot llest:

- [ ] `https://your-domain.com` carrega la home.
- [ ] Login amb email funciona.
- [ ] Login amb Google funciona.
- [ ] Pujar una foto a un perfil/ruta/sale es desa a R2 i la URL és `https://media.your-domain.com/...`.
- [ ] `php artisan schedule:list` (SSH) mostra les tasques registrades.
- [ ] `tail -f storage/logs/laravel.log` no mostra errors crítics.
- [ ] Push notification de prova arriba al mòbil (`php artisan tinker` → enviar test FCM).
- [ ] Chat en temps real funciona (broadcast → Pusher → vue listener).
- [ ] HTTPS forçat i certificat vàlid (test a [https://www.ssllabs.com/ssltest/](https://www.ssllabs.com/ssltest/)).

---

## Costos resum

| Servei | Cost/mes |
|---|---|
| Hetzner CX22 | ~4-5 € |
| Hetzner backups | ~1 € |
| Laravel Forge Hobby | ~11 € (12 $) |
| Cloudflare R2 (≤10 GB) | 0 € |
| Domini | ~0,80 € (anualitzat) |
| Pusher free tier | 0 € |
| Firebase FCM | 0 € |
| **Total** | **~17 €/mes** |

---

## Quan creixis (referència futura)

- **Tràfic alt:** puja a CX32 (4 vCPU, 8 GB) o CX42 (8 vCPU, 16 GB) amb un click a Hetzner.
- **DB separat:** prepara un segon servidor només per a MySQL, o passa a DigitalOcean Managed Database.
- **Redis:** Forge té un toggle per instal·lar Redis. Canvia `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` a `redis`.
- **CDN davant del web:** Cloudflare Proxied ja en fa una mica; per a més, posa Bunny.net o Cloudflare Pro davant.
- **Monitoring:** Forge té integració amb Sentry, Honeybadger, Flare. Per al teu volum, Sentry free tier sobra.
