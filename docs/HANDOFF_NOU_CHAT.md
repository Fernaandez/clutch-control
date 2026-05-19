# Handoff per nou chat — Clutch Control

Copia aquest fitxer (o enganxa el resum de sota) al primer missatge d'un chat nou.

**Repo:** `https://github.com/Fernaandez/clutch-control`  
**Producció:** `https://clutchcontrol.es`  
**Stack:** Laravel 12 + Vue 3 + Inertia 2 + Forge + Hetzner CX23 + Cloudflare R2 + Mailtrap + Pusher

---

## ✅ Ja fet (infra + deploy)

- Migració AlwaysData → **Hetzner + Laravel Forge** (~17 €/mes)
- Domini **clutchcontrol.es** (DNS Cloudflare, SSL Let's Encrypt)
- **Cloudflare R2** per fotos (`media.clutchcontrol.es`) — fix `storageUrl` a `HandleInertiaRequests.php`
- **Mailtrap** SMTP (recordatoris email funcionen)
- **Google OAuth** (botó restaurat a Login/Register)
- **Push-to-deploy** via GitHub webhook + Deploy hook Forge
- **Pusher** (xat web temps real) — `BROADCAST_CONNECTION=pusher`
- **Firebase FCM** — Cloud Messaging habilitat; JSON service account pendent de pujar al servidor si cal; **no provat al mòbil** (apartat)
- SSH al servidor: `ssh forge@178.105.116.105`
- Guia deploy: [`docs/DEPLOY_HETZNER.md`](DEPLOY_HETZNER.md)

### Commits recents rellevants
- R2: `league/flysystem-aws-s3-v3`, disk `public` → S3 quan `AWS_BUCKET` definit
- `storageUrl` usa `Storage::disk('public')->url('')` (no `asset('storage')`)
- Google login buttons restaurats

---

## 🔜 Següent pas: **Fase 1 — Seguro + ITV**

Pla complet guardat a:  
`C:\Users\janfc\.cursor\plans\clutch_control_improvements_plan_163cee54.plan.md`

### Fase 1 — Implementar

**Migració** `add_insurance_and_itv_to_motorcycles_table`:
- `insurance_company`, `insurance_policy_number`, `insurance_expires_at`
- `itv_expires_at`, `itv_last_passed_at`

**Model** `Motorcycle`: fillable + accessors `insurance_status` / `itv_status` (`ok|expiring_soon|expired`)

**Backend:**
- Validació a `MotorcycleController` store/update
- Job `SendExpiryReminderJob` + command `motorcycles:check-expirations`
- Scheduler diari 09:00 a `bootstrap/app.php` (Laravel 12)
- Recordatoris: 30 dies, 7 dies, dia venciment (FCM si token; opcional email)

**Frontend:**
- Secció "Documentació" a `Motorcycles/Create.vue` i `Edit.vue`
- Badges a `Motorcycles/Index.vue`
- Widget "Pròxims venciments" a `Dashboard.vue`

---

## 📋 Pla complet (5 fases)

| Fase | Feature | Estat |
|------|---------|-------|
| 0 | Infra (Hetzner, R2, mail, OAuth, Pusher) | ✅ Fet |
| **1** | **Seguro + ITV** | ⏳ **SEGÜENT** |
| 2 | Aplicació retroactiva de rutes (`POST /routes/{id}/apply-to-motorcycle`, trips manual_entry) | Pendent |
| 3 | Recomanador (`RouteRecommendationService`, Haversine + likes/categoria) | Pendent |
| 4 | Planificador auto (A→B + loop 1h, leaflet-routing-machine + OSRM) | Pendent |
| 5 | Monetització (Stripe/Cashier, Premium + destacats Sales) | Pendent |

---

## 📝 Prompt per enganxar al nou chat

```
Continuem Clutch Control. Llegeix docs/HANDOFF_NOU_CHAT.md i el pla a .cursor/plans/clutch_control_improvements_plan_163cee54.plan.md.

Infra ja està feta (clutchcontrol.es en producció). Implementa la Fase 1: Seguro + ITV segons el pla. Sè eficient amb tokens — canvis mínims, una fase per sessió.
```

---

## ⚠️ Notes tècniques

- `.env` producció només a Forge (no al repo)
- `AWS_ENDPOINT` ha de ser `.r2.cloudflarestorage.com` (no `.12.`)
- Usuaris Google: `password = null`, sense formulari canvi contrasenya si `google_id`
- Forge scheduler + queue daemon ja configurats
- Composer 2.5 / Auto per desenvolupament (estalviar quota API)
