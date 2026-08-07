# Clutch Control — publicar a l’App Store (iOS)

Bundle ID: `com.clutchcontrol.app`  
Versió actual al projecte: **1.0 (build 1)**  
Servidor que carrega l’app: `https://clutchcontrol.es` (Capacitor `server.url`)

Aquesta guia diguem-ho tot: comptes, diners, Mac, Xcode i què ja està preparat al repo.

---

## 1. Serveis i comptes que HAS de tenir

### Obligatori per penjar a iOS

| Servei | Per a què | Cost aproximat |
|--------|-----------|----------------|
| **Apple Developer Program** | Compte oficial per signar i publicar | **99 USD/any** (individual o company) |
| **Mac amb macOS** | Xcode només funciona a Mac | Compra / lloguer / Mac cloud |
| **Xcode** (App Store del Mac) | Build, Archive, Upload | Gratuït |
| **Apple ID** (el mateix del Developer) | Entrar a App Store Connect | Gratuït |
| **App Store Connect** | Fitxa de l’app, screenshots, review, preus | Inclòs amb el Developer Program |
| **Backend en producció** | L’app carrega `https://clutchcontrol.es` | El teu hosting (Forge/Hetzner/…) |

Sense Apple Developer Program **no** pots penjar res a l’App Store pública.

### Obligatori perquè l’app funcioni (ja els fas servir / necessites)

| Servei | Per a què |
|--------|-----------|
| **Domini + HTTPS** (`clutchcontrol.es`) | Contingut de l’app Capacitor |
| **Hosting Laravel** (Forge + Hetzner o equivalent) | API, auth, DB, cues |
| **Base de dades** (MySQL/Postgres) | Usuaris, rutes, vendes… |
| **Emmagatzematge fotos** (R2 / S3 / disc) | Anuncis, events, manteniment |
| **Correu transaccional** (Resend, Postmark, SES, Mailgun…) | Verificació email, resets |
| **Google Cloud / Google Auth** | Login amb Google (`GOOGLE_*` al `.env`) |
| **Pusher** (o Reverb propi) | Xats en temps real |
| **Firebase Cloud Messaging** | Push (FCM → APNs a iOS) |

### Específic iOS / push (imprescindible si vols notificacions)

| Servei | Per a què |
|--------|-----------|
| **Apple Push Notification service (APNs)** | Push a iPhone |
| **Clau APNs a Apple Developer** (Key `.p8`) | Autenticar pushes |
| **Firebase project** | El backend ja usa FCM; cal configurar APNs dins Firebase |

### Opcional però recomanat

| Servei | Per a què |
|--------|-----------|
| **Stadia Maps** (`VITE_STADIA_API_KEY`) | Tiles de mapa foscos |
| **OpenRouteService** | Planificador de rutes |
| **Cloudflare** | DNS, CDN, R2 |
| **Sentry / similar** | Crash reporting (útil després del llançament) |
| **TestFlight** | Beta abans de review (inclòs amb Developer) |

### Android (si més endavant)

| Servei | Cost |
|--------|------|
| **Google Play Console** | 25 USD **una sola vegada** |

---

## 2. Què ja està preparat al repo (Windows)

Fet al projecte:

- Bundle ID `com.clutchcontrol.app`
- Icona 1024 (`AppIcon-512@2x.png`)
- `Info.plist`: localització, càmera, fotos, background location + remote notifications, `ITSAppUsesNonExemptEncryption = false`
- `PrivacyInfo.xcprivacy` (Privacy Manifest Apple)
- `App.entitlements` amb `aps-environment`
- `AppDelegate` amb forwarding del token de push a Capacitor
- `capacitor.config.json` apuntant a producció + `allowNavigation` (Google OAuth)
- Scripts npm: `ios:prepare`, `cap:ios`
- Privacitat / Termes web: `/privacy-policy`, `/terms-of-service`
- Esborrar compte: Profile → Delete account (requerit per Apple)

**El que NO es pot fer des de Windows:** Archive, Sign, Upload a App Store Connect. Cal Mac + Xcode.

---

## 3. Passos al Mac (ordre real)

### 3.1 Compte Apple

1. Crea / entra a [developer.apple.com](https://developer.apple.com) i paga el **Apple Developer Program**.
2. Entra a [appstoreconnect.apple.com](https://appstoreconnect.apple.com).
3. **My Apps → + → New App**
   - Platform: iOS
   - Name: `Clutch Control`
   - Bundle ID: registra `com.clutchcontrol.app` a Certificates, Identifiers & Profiles si no existeix
   - SKU: `clutch-control-ios` (intern, el que vulguis)
   - Primary language: Catalan o Spanish (Spain)

### 3.2 Capabilities a Apple Developer

Al Identifier `com.clutchcontrol.app` activa:

- **Push Notifications**
- (opcional) Associated Domains si més endavant fas Universal Links

### 3.3 Firebase ↔ APNs

1. Apple Developer → Keys → crea una **APNs Auth Key** (`.p8`). Desa Key ID + Team ID.
2. Firebase Console → Project settings → Cloud Messaging → **Apple app**
3. Puja la clau `.p8` i omple Key ID / Team ID.
4. Afegeix l’iOS app amb bundle `com.clutchcontrol.app` si encara no hi és.

### 3.4 Build al Mac

```bash
git clone <el-teu-repo>
cd clutch-control
npm install
npm run ios:prepare   # cap sync ios
npm run cap:ios       # obre Xcode
```

A Xcode:

1. Selecciona el target **App**
2. **Signing & Capabilities**
   - Team = el teu Apple Developer Team
   - Bundle Identifier = `com.clutchcontrol.app`
   - Assegura’t que surt **Push Notifications** (si no: + Capability)
3. Puja `aps-environment` a **production** quan facis Archive (Xcode sol gestionar-ho en Release; si falla, canvia `App.entitlements`)
4. Dispositiu / scheme: **Any iOS Device (arm64)**
5. Product → **Archive**
6. Organizer → **Distribute App** → App Store Connect → Upload

### 3.5 Fitxa a App Store Connect (obligatori abans de review)

Necessitaràs:

- **Screenshots** iPhone (mínim mida 6.7" i/o les que demani Connect; també 6.1" ajuda)
  - Mínim ~3–5 pantalles reals (Dashboard, Rutes, Events, Sales, GPS)
- **Descripció** (ca/es/en)
- **Keywords**
- **Categoria**: Navigation o Lifestyle / Sports
- **Privacy Policy URL**: `https://clutchcontrol.es/privacy-policy`
- **Support URL**: p.ex. `https://clutchcontrol.es` o un correu de suport
- **Age rating** (qüestionari)
- **App Privacy** (nutrition labels): indica localització, contacte, fotos, identificadors… el que realment reculls
- **Preu**: Free (o el que vulguis)

### 3.6 Notes per a App Review (important)

Enganxa alguna cosa així a **App Review Information → Notes**:

```
Clutch Control is a motorcycle companion app (maintenance, routes, meetups, marketplace).
Login: use [email de prova] / [password de prova]
OR Google Sign-In with the provided test account.

Background location is used only while recording a ride, so GPS
tracking continues with the screen locked. Users grant “Always”
permission explicitly for this feature.

Account deletion: Profile → Delete account.
Privacy: https://clutchcontrol.es/privacy-policy
Terms: https://clutchcontrol.es/terms-of-service
```

Crea un **usuari demo** estable al backend perquè el reviewer no es quedi bloquejat.

### 3.7 TestFlight → Submit for Review

1. Quan el build aparegui a Connect (10–30 min), afegeix-lo a una versió.
2. Prova’l amb TestFlight al teu iPhone.
3. **Submit for Review**.

Primera review: normalment **24–48 h**, a vegades més.

---

## 4. Checklist tècnic abans de Submit

- [ ] `https://clutchcontrol.es` estable (HTTPS, sense 500)
- [ ] Privacitat i Termes accessibles sense login
- [ ] Esborrar compte funciona
- [ ] Login email + Google testats **dins l’app nativa** (no només al navegador)
- [ ] GPS / Free Ride / background recording testats amb pantalla apagada
- [ ] Push: token es registra i arriba una notificació de prova
- [ ] Fotos (anuncis / events) demanen permís i pugen bé
- [ ] Sense `APP_DEBUG=true` a producció
- [ ] Icona i splash correctes
- [ ] Versió `MARKETING_VERSION` / `CURRENT_PROJECT_VERSION` coherents (1.0 / 1)

---

## 5. Costos reals (ordre de magnitud)

| Concepte | Cost |
|----------|------|
| Apple Developer | ~99 USD/any |
| Mac (si no en tens) | 0 si uses un de feina / amic; o Mac Mini / cloud Mac |
| Hosting (Hetzner CX22 + Forge) | ~15–25 €/mes |
| Domini `.es` | ~5–15 €/any |
| Pusher / Firebase / R2 | sovint dins free tier al principi |
| Google Play (després) | 25 USD un cop |

---

## 6. Riscos d’Apple Review (aquest projecte)

1. **App “wrapper” de web** — l’app carrega la web remota. Apple pot demanar valor natiu clar. Ja en tens (GPS background, push, càmera). Explica-ho a les notes.
2. **Background location** — demanen justificació clara (gravació de rutes amb pantalla bloquejada).
3. **Login Google en Browser** — prova’l en device real; si la sessió no torna a la WebView, caldrà ajustar el flux OAuth.
4. **Account deletion** — ja tens UI; assegura’t que el backend esborra de veritat.

---

## 7. Després d’aprovació

1. Copia l’URL pública `https://apps.apple.com/app/idXXXXXXXXX`
2. Posar-la a `.env`:
   ```
   IOS_STORE_URL=https://apps.apple.com/app/clutch-control/idXXXXXXXXX
   ```
3. Actualitza enllaços Open-in-App / share si cal.
4. Cada update: puja `CURRENT_PROJECT_VERSION` (+1 build) i, si cal, `MARKETING_VERSION` (1.0.1, 1.1…).

---

## 8. Ordre pràctic recomanat

1. Pagar Apple Developer  
2. Assegurar backend producció estable  
3. Configurar APNs + Firebase  
4. Mac → `npm run ios:prepare` → Xcode Archive → TestFlight  
5. Screenshots + texts + privacy labels  
6. Submit for Review  
7. Quan estigui live, actualitzar `IOS_STORE_URL`
