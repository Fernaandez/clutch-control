# Clutch Control — publicar a l'App Store (iOS)

Bundle ID: `com.clutchcontrol.app`
Versió al projecte: **1.1.0 (build 1)** — alineada amb Android (`versionName 1.1.0`)
Servidor que carrega l'app: `https://clutchcontrol.es` (Capacitor `server.url`)
Capacitor **8.3.0**, integració iOS per **Swift Package Manager** (no hi ha Podfile)

---

## 0. Llegeix això primer: què queda per fer i qui ho ha de fer

### Ja arreglat al repo (no ho toquis)

| Què | Detall |
|-----|--------|
| **Icona i splash reals** | Abans eren el logo blau per defecte de Capacitor. Ara es generen del logo de Clutch Control, **opacs i sense canal alpha** (Apple rebutja icones amb transparència). |
| **`Package.swift` trencat** | Es va generar a Windows amb barres invertides (`..\..\..\node_modules\...`). En Swift, `\n` de `\node_modules` és un salt de línia: **el projecte iOS no compilava**. Ara té barres normals. |
| **Plugin `@capacitor/browser` no enllaçat** | Estava al `package.json` però no a iOS ni a Android. És el que obre el login de Google. Ara està enllaçat a les dues plataformes. |
| **`aps-environment`** | Era `development`. Ara `production`. |
| **Versions** | iOS anava a 1.0 (1) i Android a 1.1.0 (5). iOS ja és 1.1.0. |
| **Flag `-D COCOAPODS`** | Sobrava (no fem servir CocoaPods). Tret. |

### Ho has de fer tu al Mac (no es pot fer des de Windows)

1. **Archive + Sign + Upload** — requereix Xcode. Veure §3.4.
2. **`GoogleService-Info.plist`** — s'ha de baixar de Firebase. Veure §3.3.
3. **Decidir què fas amb el push a iOS** — hi ha un problema real, explicat a §2. **Llegeix-lo abans de fer l'Archive.**
4. **Fitxa d'App Store Connect** (screenshots, textos, privacy labels). Veure §3.5.

### Avís de seguretat que has de resoldre tu (Android, urgent)

La clau de signatura d'Android (`android/app/clutch-release.jks`) i la seva contrasenya
(`clutch2026`, que estava escrita dins de `android/app/build.gradle`) **estaven totes dues
pujades al repositori de git**.

Què he fet jo:
- `build.gradle` ja no conté cap contrasenya: les llegeix de `keystore.properties`
  (ignorat per git) o de variables d'entorn `CC_KEYSTORE_*`.
- He tret el `.jks` i `database/database.sqlite` del seguiment de git (`git rm --cached`).
  **Els fitxers segueixen al disc**, la teva build local continua funcionant.
- `.gitignore` ja bloqueja `*.jks`, `*.p8`, `*.p12`, `keystore.properties`.

Què has de fer tu:
1. **Fes una còpia de seguretat de `android/app/clutch-release.jks` i de la contrasenya
   ara mateix** (gestor de contrasenyes). Si perds aquesta clau no pots tornar a
   publicar cap actualització de l'app a Google Play.
2. Si el repositori és **públic** o l'ha vist algú que no siguis tu: la clau i la
   contrasenya segueixen a l'**historial** de git (treure-la del seguiment no purga
   l'historial). Amb Play App Signing pots demanar un *upload key reset* a Google;
   si no fas servir Play App Signing, no es pot canviar la clau de l'app publicada.
3. Canvia la contrasenya del keystore per una de nova i llarga, i actualitza
   `keystore.properties`.

---

## 1. Serveis i comptes que HAS de tenir

### Obligatori per penjar a iOS

| Servei | Per a què | Cost aproximat |
|--------|-----------|----------------|
| **Apple Developer Program** | Compte oficial per signar i publicar | **99 USD/any** |
| **Mac amb macOS** | Xcode només funciona a Mac | Compra / lloguer / Mac cloud |
| **Xcode** (App Store del Mac) | Build, Archive, Upload | Gratuït |
| **App Store Connect** | Fitxa de l'app, screenshots, review, preus | Inclòs amb el Developer Program |
| **Backend en producció** | L'app carrega `https://clutchcontrol.es` | El teu hosting |

Sense Apple Developer Program **no** pots penjar res a l'App Store pública.

### Obligatori perquè l'app funcioni

| Servei | Per a què |
|--------|-----------|
| **Domini + HTTPS** (`clutchcontrol.es`) | Contingut de l'app Capacitor. iOS bloqueja `http://` (ATS). |
| **Hosting Laravel** | API, auth, DB, cues |
| **Base de dades** (MySQL/Postgres) | Usuaris, rutes, vendes… |
| **Emmagatzematge fotos** (R2 / S3 / disc) | Anuncis, events, manteniment |
| **Correu transaccional** | Verificació email, resets |
| **Google Cloud / Google Auth** | Login amb Google (`GOOGLE_*` al `.env`) |
| **Pusher** (o Reverb propi) | Xats en temps real |
| **Firebase Cloud Messaging** | Push |

### Opcional però recomanat

| Servei | Per a què |
|--------|-----------|
| **Stadia Maps** (`VITE_STADIA_API_KEY`) | Tiles de mapa foscos |
| **OpenRouteService** | Planificador de rutes |
| **TestFlight** | Beta abans de review (inclòs amb Developer) |
| **Sentry / similar** | Crash reporting |

---

## 2. El push a iOS: problema real i com resoldre'l

**Això no impedeix que Apple aprovi l'app**, però si no ho arregles les notificacions
push **no funcionaran a iOS** (a Android sí funcionen).

### Per què no funciona

- A iOS, `@capacitor/push-notifications` retorna un **token d'APNs** (d'Apple).
- El backend (`ConversationController`, `SendExpiryReminderJob`) envia amb Kreait
  Firebase: `CloudMessage::withTarget('token', $user->fcm_token)`. Això espera un
  **token de registre d'FCM**, que no és el mateix.
- A Android funciona perquè `google-services.json` fa que el token ja sigui d'FCM.
- Resultat a iOS: es guarda un token d'APNs a `users.fcm_token`, FCM el rebutja i el
  push falla en silenci (queda al log, no trenca res).

### Opció A — recomanada: fer que iOS també doni tokens d'FCM

Afegeix el SDK de Firebase a iOS, així el token que arriba ja és d'FCM i **el backend
no s'ha de tocar**. Al Mac:

1. Firebase Console → afegeix una **app iOS** amb bundle `com.clutchcontrol.app`.
2. Baixa `GoogleService-Info.plist` i arrossega'l a Xcode dins del target `App`
   (marca *Copy items if needed*). **No el pugis a git.**
3. Instal·la el plugin que ja gestiona FCM a les dues plataformes:
   ```bash
   npm install @capacitor-firebase/messaging
   npx cap sync ios
   ```
4. A `resources/js/Layouts/AppLayout.vue`, on ara es fa
   `PushNotifications.register()` i s'escolta `registration`, fes servir
   `FirebaseMessaging.getToken()` per obtenir el token i envia'l al mateix endpoint
   (`profile.device-token`). La resta del flux ja és correcta.
5. Apple Developer → Keys → crea una **APNs Auth Key** (`.p8`), i puja-la a
   Firebase Console → Project settings → Cloud Messaging (Key ID + Team ID).

### Opció B — enviar a APNs directament des de Laravel

Mantens FCM per Android i afegeixes un enviament a APNs per iOS. Implica guardar la
plataforma de cada token i afegir una llibreria d'APNs al backend. Més feina i més codi
per mantenir; només té sentit si vols treure Firebase del tot.

### Si decideixes deixar-ho per a més endavant

L'app s'aprova igual. Simplement no anunciïs notificacions push com a funcionalitat
iOS fins que estigui provat. Tot el que hi ha (capability, entitlement,
`UIBackgroundModes: remote-notification`, forwarding del token a l'`AppDelegate`) ja
està al seu lloc i no molesta.

---

## 3. Passos al Mac (ordre real)

### 3.1 Compte Apple

1. Crea / entra a [developer.apple.com](https://developer.apple.com) i paga el **Apple Developer Program**.
2. Certificates, Identifiers & Profiles → registra l'Identifier `com.clutchcontrol.app`.
3. [appstoreconnect.apple.com](https://appstoreconnect.apple.com) → **My Apps → + → New App**
   - Platform: iOS
   - Name: `Clutch Control`
   - Bundle ID: `com.clutchcontrol.app`
   - SKU: `clutch-control-ios` (intern)
   - Primary language: Catalan o Spanish (Spain)

### 3.2 Capabilities a Apple Developer

A l'Identifier `com.clutchcontrol.app` activa:

- **Push Notifications**
- (opcional) Associated Domains, si més endavant fas Universal Links

### 3.3 Firebase ↔ APNs

Només si vols push a iOS (§2, opció A):

1. Apple Developer → Keys → crea una **APNs Auth Key** (`.p8`). Desa Key ID + Team ID.
   El `.p8` només es pot baixar **una vegada**: guarda'l bé (i mai a git).
2. Firebase Console → Project settings → Cloud Messaging → **Apple app** → puja el `.p8`.
3. Baixa `GoogleService-Info.plist` i afegeix-lo al target `App` a Xcode.

### 3.4 Build al Mac

```bash
git clone <el-teu-repo>
cd clutch-control
npm install
npm run build          # genera els assets de Vite
npm run ios:prepare    # cap sync ios
npm run cap:ios        # obre Xcode
```

> `npx cap sync ios` regenera `ios/App/CapApp-SPM/Package.swift`. Executat **al Mac**
> el genera bé. Si algun dia el tornes a generar des de Windows, comprova que els
> `path:` tinguin barres normals (`/`) i no invertides (`\`), perquè si no el projecte
> no compila. Ja està arreglat al repo.

A Xcode:

1. Selecciona el target **App**.
2. **Signing & Capabilities**
   - Team = el teu Apple Developer Team (al repo no hi ha `DEVELOPMENT_TEAM`, és normal)
   - Bundle Identifier = `com.clutchcontrol.app`
   - Comprova que hi surt **Push Notifications**; si no, `+ Capability`.
3. Espera que Xcode resolgui els Swift Packages (barra de progrés a dalt). Han de
   sortir 5 plugins de Capacitor: background-geolocation, browser, geolocation,
   local-notifications, push-notifications.
4. Scheme / dispositiu: **Any iOS Device (arm64)**.
5. Product → **Archive**.
6. Organizer → **Distribute App** → App Store Connect → Upload.

Si l'Archive falla per signatura, revisa que el Team estigui posat i que l'Identifier
tingui Push Notifications activat.

### 3.5 Fitxa a App Store Connect (obligatori abans de review)

- **Screenshots** iPhone 6.7" (i 6.1" ajuda): 3–5 pantalles reals
  (Dashboard, Rutes, Detall de ruta amb mapa, Quedades, Compra/Venda)
- **Descripció** (ca/es/en)
- **Keywords**
- **Categoria**: Navigation, o Sports / Lifestyle
- **Privacy Policy URL**: `https://clutchcontrol.es/privacy-policy`
- **Support URL**: `https://clutchcontrol.es` o un correu de suport
- **Age rating** (qüestionari)
- **App Privacy** (nutrition labels) — declara de veritat el que reculls:
  - Location (precise) → *App Functionality* (gravació de rutes)
  - Contact Info (email, nom) → *App Functionality*
  - User Content (fotos) → *App Functionality*
  - Identifiers → només si acabes fent servir Firebase/analytics
  - `PrivacyInfo.xcprivacy` ja declara les *required reason APIs*, però les
    nutrition labels s'omplen a mà a Connect.
- **Preu**: Free

### 3.6 Notes per a App Review (important)

A **App Review Information → Notes**:

```
Clutch Control is a motorcycle companion app (maintenance, routes, meetups, marketplace).

Login: [email de prova] / [password de prova]
(or Google Sign-In with the provided test account)

Background location is used only while recording a ride, so GPS tracking
continues with the screen locked. The user explicitly grants "Always"
permission from the ride recording screen.

Account deletion: Profile -> Delete account (works for both password and
Google accounts).
Privacy: https://clutchcontrol.es/privacy-policy
Terms: https://clutchcontrol.es/terms-of-service
```

Crea un **usuari demo** estable al backend, amb una moto i alguna ruta ja creades,
perquè el reviewer no vegi pantalles buides.

### 3.7 TestFlight → Submit for Review

1. Quan el build aparegui a Connect (10–30 min), afegeix-lo a una versió.
2. Prova'l amb TestFlight al teu iPhone (§4).
3. **Submit for Review**.

Primera review: normalment **24–48 h**.

---

## 4. Checklist de proves al dispositiu real (abans de Submit)

Marca-ho tot en un iPhone de veritat, no al simulador (el GPS i el push no es proven bé al simulador):

- [ ] `https://clutchcontrol.es` estable (HTTPS, sense 500), i `APP_DEBUG=false`
- [ ] L'app arrenca i es veu la splash de Clutch Control (no la de Capacitor)
- [ ] La icona a l'escriptori és la de Clutch Control
- [ ] Registre amb email + verificació de correu
- [ ] **Login amb Google dins de l'app nativa** (això depèn del plugin Browser que
      acabo d'enllaçar: és el punt més probable de fallada, prova'l bé)
- [ ] Crear una moto, afegir km, manteniment
- [ ] Free Ride: gravar amb **pantalla bloquejada** i comprovar que els km surten
- [ ] Sortir de la pantalla mentre grava i tornar: el recorregut s'ha de desar a
      pendents i el GPS s'ha d'aturar (arreglat en aquesta revisió)
- [ ] Pujar fotos a un anunci (demana permís de càmera / fotos)
- [ ] Xat en temps real entre dos comptes
- [ ] Push: si has fet §2 opció A, comprova que arriba una notificació
- [ ] Privacitat i Termes s'obren **sense login**
- [ ] Esborrar compte funciona (prova-ho també amb un compte de Google)
- [ ] Rotació: l'app és portrait a iPhone

---

## 5. Costos reals (ordre de magnitud)

| Concepte | Cost |
|----------|------|
| Apple Developer | ~99 USD/any |
| Mac (si no en tens) | Mac Mini, un de prestat, o cloud Mac per hores |
| Hosting (Hetzner + Forge) | ~15–25 €/mes |
| Domini `.es` | ~5–15 €/any |
| Pusher / Firebase / R2 | sovint dins free tier al principi |
| Google Play (ja pagat) | 25 USD un cop |

---

## 6. Riscos d'Apple Review (aquest projecte)

1. **App "wrapper" de web** — l'app carrega la web remota amb `server.url`. Apple pot
   demanar valor natiu clar. En tens (GPS en segon pla, push, càmera): explica-ho a les
   notes de review.
2. **Background location** — és el punt que més miren. Cal que la justificació sigui
   evident: gravació de rutes amb pantalla bloquejada. Els textos del `Info.plist` ja ho diuen.
3. **Login Google via Browser** — prova'l en device real; si en tornar del navegador la
   sessió no arriba a la WebView, caldrà revisar el flux OAuth.
4. **Account deletion** — Apple ho exigeix si hi ha registre. Ja hi és i funciona
   també per comptes de Google.

---

## 7. Després d'aprovació

1. Copia l'URL pública `https://apps.apple.com/app/idXXXXXXXXX`.
2. Posa-la al `.env` de producció:
   ```
   IOS_STORE_URL=https://apps.apple.com/app/clutch-control/idXXXXXXXXX
   ```
   La fan servir les pantalles de compartir (`Shared/OpenInApp`).
3. Cada actualització: puja `CURRENT_PROJECT_VERSION` (+1 build sempre) i, si canvia la
   versió pública, `MARKETING_VERSION`. Mantén-ho alineat amb Android.

---

## 8. Regenerar icones i splash

Si canvies el logo (`resources/images/logo.svg`):

```bash
node scripts/build-app-assets.mjs        # crea assets/icon.png, splash.png, splash-dark.png
npx capacitor-assets generate --ios --iosProject ios/App
npx capacitor-assets generate --android --androidProject android
```

El script deixa les imatges **opaques a propòsit**: una icona d'iOS amb canal alpha fa
que App Store Connect rebutgi el binari amb *"Invalid Image - can't contain an alpha
channel or transparency"*.

---

## 9. Ordre pràctic recomanat

1. Guardar còpia del keystore d'Android i canviar-ne la contrasenya (§0)
2. Pagar Apple Developer
3. Assegurar backend de producció estable
4. Decidir sobre el push (§2)
5. Mac → `npm install` → `npm run build` → `npm run ios:prepare` → Xcode Archive → TestFlight
6. Passar el checklist del §4 en un iPhone real
7. Screenshots + textos + privacy labels
8. Submit for Review
9. Quan estigui live, actualitzar `IOS_STORE_URL`
