# VAYUROOP.rar — kya hai ye? (extract + analysis report)

**Report date:** 3 October 2026
**File:** `VAYUROOP.rar` (repo root) — 2,766,611 bytes, `md5 184c0ecbd3a0e31e5e9826296c5839b1`
**Drive link:** sandbox me Google Drive domain blocked hai, isliye Drive se direct download nahi hua.
Repo me pehle se maujood `VAYUROOP.rar` use kiya gaya — entry list aur file sizes Drive ke raw bytes se
match karte hain, yaani **wahi archive hai**.
**Extracted at:** `/home/user/extracted/VAYUROOP/` (196 files, 3.9 MB)

---

## 1. Ek line mein

Ye **tumhari live website `vayuroop.com` ka pura source code** hai — ek custom-built PHP e-commerce +
CMS + AI blog automation platform, jiska internal naam **"Aurora"** hai
(aur package ka naam bhi `AURORA FINAL v5` likha hai).

100% match ke 3 proof:

1. Live site ka session cookie `aurora_session` **isi code** se set hota hai (`app/Core/Session.php:20`).
2. Audit report me likhe SKUs `AUR-*` (AUR-TEE-CLS, AUR-HDY-NVY…) **installed seed data** me hain.
3. Audit ke 8 products (SVG placeholders ke saath) **installer ki seed list** se exactly match karte hain.

Matlab: ye live site ka **backend/source snapshot** hai, koi malware/random dump nahi.

---

## 2. Technical details

| Cheez | Value |
|---|---|
| Archive format | RAR5, **password/encryption nahi** |
| Entries | 248 (196 files + folders) |
| Uncompressed size | 36,16,107 bytes (~3.6 MB) |
| PHP files | 125 (~11,900 lines) |
| Images | 21 JPG + 12 SVG, 4 woff2 fonts (sab local, koi CDN nahi) |
| Top folder | `VAYUROOP/` |
| Extract tool | `unrar` (PyPI `unrar-cffi` ke sdist se compile kiya — sandbox me koi extractor install nahi tha) |

---

## 3. Andar kya hai — features

**Stack:** PHP 8.2+ (pure PHP, **zero Composer dependency**), MySQL/MariaDB (PDO), Bootstrap 5,
custom MVC. cPanel shared hosting ke liye banaya gaya —
**no Node, no Docker, no Redis, no background daemon**; automation PHP cron + DB job queue se chalti hai.

**Storefront**
Home (hero/featured/new arrivals/bestsellers/categories/blog/newsletter) · Shop with search, filters,
sort, pagination · Product page (size/colour variants, gallery, related products) · Cart · Coupons ·
Checkout: **Razorpay (server-side signature verify) + Cash on Delivery** · Customer accounts ·
Blog with categories/tags/comments · About/Contact/FAQ · Privacy/Terms/Shipping/Return policy ·
`/sitemap.xml`, `/robots.txt`, 404 page.

**Admin dashboard (`/admin`)** — ~90 admin routes
Dashboard stats · Products + categories · Orders + status · Customers · Articles · **AI Hub**
(Run Now, jobs, logs, approvals, content calendar) · Media library · Coupons · Menus · Pages ·
Comments moderation · Contact messages · Redirects · Newsletter list · Users & roles
(super_admin/admin/editor/staff) · Backups (create/download/restore) · Activity logs ·
Settings (general, brand guidelines, SEO, email, payments, shipping/tax, AI).

**AI content automation (approval-based)**
Roz scheduled blog generation (time, daily limit, topic, audience, tone, language, length) →
title, SEO title, meta, slug, excerpt, full HTML article, tags, internal links, image prompt,
alt text, social caption → **AI image generation** → sab kuch `pending approval` rehta hai →
admin approve kare tab publish (now ya scheduled). Reject / edit / regenerate / full history.
Job queue me dedup, retries with backoff, lock (overlapping runs nahi).

**Security (code level pe accha hai)**
PDO prepared statements · har POST pe CSRF (sirf `/payment/webhook/` exempt) · output escaping ·
bcrypt · hardened sessions · login rate limiting · role-based Guard · MIME-validated uploads with
random names · `app/`, `config/`, `storage/` `.htaccess` se blocked · security headers + CSP ·
API keys server-side only · Razorpay HMAC server-side.

**SEO**
Friendly URLs · meta with manual overrides · canonical · OG + Twitter · JSON-LD
(Organization/Product/Article/Breadcrumb) · cached sitemap · robots · redirect manager · per-item noindex.

**Email:** built-in SMTP client (STARTTLS/SSL) + PHP mail fallback, notifications toggle ke saath.

**Database:** `install/schema.sql` me **32 tables** (users, customers, products, variants, inventory ledgers,
orders, payments, articles, AI jobs/logs/approvals, backups, redirects, login_attempts, notifications…).

---

## 4. Folder map (extracted)

```
VAYUROOP/
├── index.php             Front controller (114 routes)
├── cron.php              Scheduler entry (CLI ya /cron.php?token=APP_KEY)
├── install/              Installer wizard + schema.sql (32 tables + seed data)
├── app/                  Core framework + 22 controllers + 11 services   (PRIVATE)
├── views/                72 templates (storefront + admin)
├── assets/               Bootstrap 5, icons, CSS/JS, fonts, imagery
├── uploads/              (khaali — user/AI images yahan aate hain)
├── storage/              logs, backups, cache, install lock               (PRIVATE)
├── config/config.php     Installer-generated (PRIVATE)
└── demo/                 khaali folder (theme preview ke liye tha)
```

---

## 5. Ye snapshot kis machine ka hai? (important)

`config/config.php` + `storage/locks/INSTALLED` kehta hai:

```php
'env' => 'production', 'debug' => true,
'app_url' => 'http://localhost:8081',          // live domain nahi
'db_name' => 'vayuroop_123', 'db_user' => 'root', 'db_pass' => '',   // local MySQL
'timezone' => 'Asia/Kolkata',
'app_key' => '721b5884…'                       // cron token bhi yahi hai
```

Install timestamp: **2026-09-29 12:01:14**, app_url `localhost:8081`.
Logs me Windows path hai: `C:\VAYUROOP\app\...`.

**Yaani:** ye tumhare **local development machine (Windows, `C:\VAYUROOP`, localhost:8081)** ka
snapshot hai — live server ka production config isme nahi hai. `README-DEPLOY.txt` khud kehta hai
"config/config.php ZIP me nahi hai" — lekin is RAR me hai, kyunki ye local folder se banaya gaya.

⚠️ **Note:** is RAR me `app_key` + local DB info hai, aur repo `DMNEERAJ/mysite` **public** hai
jisme `VAYUROOP.rar` committed hai. Local keys hain isliye risk kam hai, par behtar hai ki RAR ko
public repo se hataya jaaye (ya scrub karke rakha jaaye) — especially agar kabhi live config isme aa jaaye.

---

## 6. Bugs / issues jo abhi is code me dikh rahe hain

| # | Issue | Detail |
|---|---|---|
| 1 | **`Class "App\Controllers\Admin\Settings" not found`** | `storage/logs/app.log` me 3 baar (29 Sep, 15:31–15:35), `MarketingController.php:203`. Is snapshot me `use App\Core\Settings;` **already import hai**, matlab log us purane build ka hai — fix ho chuka lagta hai (verify karna baaki, kyunki yahan PHP run nahi ho sakta). |
| 2 | **AI provider fail** | `AI API error: Model not found: gpt-4o-mini / gpt-image-1 … legacy API` — default provider **Pollinations** (`text.pollinations.ai`, `image.pollinations.ai`) apne purane endpoints pe ye models nahi de raha. Yaani AI blog + AI image feature **abhi kaam nahi karta**. |
| 3 | **Cron run fail** | `cron.log`: `published=0 generated=0 images=0 failed=1` — upar wale AI error ka hi nateeja. |
| 4 | **Dev settings production jaisa** | `debug => true`, `app_url = localhost:8081`, `db root / blank password`. |
| 5 | **"Aurora" leftover** | Cookie `aurora_session`, SKU prefix `AUR-`, product names "Aurora Heavy Hoodie / Aurora Cap", cap copy me "embroidered Aurora mark" — audit me bhi yahi point tha. |

---

## 7. Dead / leftover files (theme ka double system)

Controllers sirf ye render karte hain: `home/index`, `shop/product`, `shop/index`, `blog/*` etc.,
aur layout `views/layouts/store.php` load karta hai **bootstrap + store.css + theme.css**.

Iske alawa archive me "**Aurora v2 theme**" ka ek alag drop-in set pada hai jo live pages pe
**use nahi hota**:

- `views/home.php`, `views/product.php`, `views/_helpers.php`
- `views/partials/head.php`, `header.php`, `footer.php`, `drawer.php`, `card-*.php`
- `assets/css/aurora.css` (30 KB), `aurora-tokens.css`, `assets/js/aurora.js`, `motion.js`
- `install-theme.md` (Hinglish integration guide), `demo/` (khaali), `assets/img/demo/*.jpg` (6 stock-ish photos)

Yehi wajah hai ki audit me "Aurora leftover branding / mixed design" dikhta hai — **do themes
side-by-side** hain: purana live theme (`store.css` + `theme.css`) aur naya unused Aurora v2 set.

---

## 8. Local pe kaise chalega (jab chahiye)

1. PHP **8.2+** (extensions: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `json`; optional `gd`, `zip`) + MySQL/MariaDB.
2. Extract karo → `config/config.php` aur `storage/locks/INSTALLED` **delete** karo (ya naya DB banao).
3. Browser me site kholo → **installer wizard** khud chalega: requirements → DB → brand → Super Admin → 32 tables + seed data ban jaate hain.
4. Login: `/admin` → Settings me AI/payment keys daalo.
5. Cron: `*/5 * * * * php /path/cron.php` (ya URL-cron `https://site/cron.php?token=APP_KEY`).

> Is sandbox me PHP/MySQL install nahi hai (aur yahan se package install bhi blocked hai),
> isliye maine site **run karke** verify nahi ki — ye pure **code-level analysis** hai.
> Chaho to tumhare Windows setup pe main instructions de sakta hoon, ya yahan ke liye koi
> offline run plan bana sakta hoon.

---

## 9. Aage kya ho sakta hai (options)

- **A. Bug fix:** AI provider ko working endpoint/model pe shift karna (ya OpenAI key support theek karna), cron failure clear karna.
- **B. Branding cleanup:** `aurora_session`, `AUR-*` SKUs, "Aurora" product names/copy → Vayuroop.
- **C. Design integration:** naya Aurora v2 theme (`aurora.css` + partials) actually live layout me lagana.
- **D. Audit fixes:** `VAYUROOP-WEBSITE-AUDIT.md` ke 10 launch blockers (real photos, reviews, schema, legal pages, indexation).
- **E. Security hygiene:** public repo se RAR hatana / clean karna.
- **F. Frontend-only kaam:** repo ke `index.html` / `download.html` (logo kit pages) aage badhana.

Bolo kaunsa direction chahiye — main us hisaab se kaam shuru karta hoon.
