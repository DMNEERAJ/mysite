# VAYUROOP — Favicon Fix Kit (Hinglish)

**Kya hai ye:** website ka tab icon (favicon) **Aurora wale "A" se → tumhare Vayuroop mark** me badalne
ka poora ready package. Tumhari bheji hui image = Vayuroop ka **V diamond mark** hi hai, isliye maine
wahi exact mark use kiya hai (brand kit ke asli vector se banaya gaya — bilkul crisp, koi blur nahi).

---

## ⚠️ Pehle: tumhara favicon kyun nahi badla (asli wajah)

Tumne file ka naam `favicon.svg.png` rakha tha. Layout me ye line hai:

```php
<link rel="icon" href=".../assets/img/favicon.svg" type="image/svg+xml">
```

**Browser ko "ye SVG file hai" bataya gaya hai.** Agar us path pe PNG content rakha to browser use
refuse kar deta hai (ya purana cache dikhata rehta hai) — isliye change "ho hi nahi raha" lagta hai.
Saath hi browser/LiteSpeed favicon ko bahut lambe time tak **cache** karte hain.

Teen cheezein zaroori hain:
1. **Sahi format + sahi naam** (`favicon.svg` me asli SVG, ya `favicon-32.png` me asli PNG).
2. **Sahi jagah** → `public_html/assets/img/` (site root me pade `favicon.svg` ko browser padhta hi nahi,
   kyunki HTML us path ko point karta hai).
3. **Cache bust** → `?v=2` (is kit me already laga hua hai) + browser me **Ctrl+F5** ya incognito.

---

## ✅ Kaam kaise karna hai (2 raaste)

### Raasta A — Recommended: ye poori zip upload kar do (2 minute)

1. cPanel → **File Manager** → apni site ka document root kholo (`public_html` ya `public_html/vayuroop.com`).
2. **Pehle backup:** `views/layouts/store.php` aur `views/layouts/admin.php` ko copy karke `_backup` folder me rakh do.
3. `vayuroop-favicon-update.zip` upload karo → right-click → **Extract** → "Overwrite existing files" maango.
   Zip ka structure site ke structure se exactly match karta hai:

   | Zip ke andar | Server pe kahan jayega |
   |---|---|
   | `assets/img/favicon.svg`, `favicon-16/32/48.png`, `apple-touch-icon.png` | `public_html/assets/img/` |
   | `favicon.ico` | `public_html/favicon.ico` (site root) |
   | `views/layouts/store.php`, `views/layouts/admin.php` | purane files ko overwrite (favicon lines updated) |
   | `README-FAVICON.md`, `backup-original-aurora/favicon.svg` | reference ke liye |
4. Browser me **Ctrl+Shift+R** (hard refresh) ya incognito window me kholo. Tab me ab V mark dikhega.
5. Mobile: iOS pe "Add to Home Screen" karne pe `apple-touch-icon.png` (180×180) use hoga.

**Verify (30 second):** browser me seedha ye URL kholo →
`https://vayuroop.com/assets/img/favicon.svg`
- Agar **V mark** dikhe → upload sahi hua, bas purana cache tab me baitha hai (Ctrl+F5 karo).
- Agar **Aurora "A"** dikhe → file upload nahi hui / galat folder me gayi.
- Agar **404** aaye → path galat hai (`assets/img/` folder check karo).

---

### Raasta B — Manually sirf 2 lines badlo (agar zip overwrite nahi karna chahte)

Dono files me **ek hi line** hai, usko dhundo:

```php
<link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
```

Aur usko is block se replace kar do (ye favicon.ico + PNG + iOS icon, sab handle karta hai):

```php
<link rel="icon" href="<?= url('assets/img/favicon.svg?v=2') ?>" type="image/svg+xml">
<link rel="icon" href="<?= url('assets/img/favicon-32.png?v=2') ?>" sizes="32x32" type="image/png">
<link rel="icon" href="<?= url('assets/img/favicon-16.png?v=2') ?>" sizes="16x16" type="image/png">
<link rel="apple-touch-icon" href="<?= url('assets/img/apple-touch-icon.png?v=2') ?>">
```

Kahan-kahan:
- Live site: `views/layouts/store.php` (line ~45) **aur** `views/layouts/admin.php` (line ~59)
- Local (`C:\VAYUROOP`): same do files

Fir `assets/img/` me ye files daalo: `favicon.svg`, `favicon-16.png`, `favicon-32.png`,
`favicon-48.png`, `apple-touch-icon.png` — aur `favicon.ico` site root me.

---

## 📦 Is kit me kya-kya hai

| File | Kaam |
|---|---|
| `favicon.svg` | **Main favicon** — black rounded square + white V mark (64×64 viewBox, ~3 KB) |
| `favicon.ico` | Purane browsers / root fallback — 16/32/48/64 multi-size |
| `favicon-16.png`, `favicon-32.png`, `favicon-48.png` | Windows/Chrome/Android ke liye PNG versions |
| `apple-touch-icon.png` | iOS home screen (180×180) |
| `android-192.png`, `favicon-512.png` | PWA / Android home screen (optional) |
| `favicon-transparent.svg` + `favicon-transparent-512.png` | **Transparent version** (dark-mode sites/overlays ke liye) |
| `original-aurora/favicon.svg` | Purana file — rollback chahiye to ise wapas use kar lo |
| `patched-layouts/store.php`, `admin.php` | Ready reference copies (jo lines badli wahi dekh sakte ho) |

### Transparent version chahiye?

Favicon line me `favicon.svg` ki jagah `favicon-transparent.svg` kar do:

```php
<link rel="icon" href="<?= url('assets/img/favicon-transparent.svg?v=2') ?>" type="image/svg+xml">
```
(file ko `assets/img/` me upload karna mat bhoolna.)

---

## 🔁 Aage kabhi apna favicon badalna ho to

1. Nayi file ko in **exact naam** se rakho: `favicon.svg` (asli SVG) ya `favicon-32.png` (asli PNG).
2. `assets/img/` me replace karo.
3. Layout me `?v=2` ko `?v=3` kar do (ya sirf Ctrl+F5 karo) — cache turb fresh.

Bas. Koi database setting nahi hai, koi admin panel option nahi — favicon **sirf files + layout lines** se aata hai.

---

## ❌ Ye galtiyaan mat karna

- `favicon.svg` naam ke saath **PNG** content rakhna (yahi tumhare saath hua) — browser reject kar dega.
- Favicon ko site root me `assets` ke bahar rakhna jab HTML `assets/img/...` maang raha ho.
- Cache clear na karna — 90% "change nahi hua" complaints yahi hoti hain. **Ctrl+F5 / incognito** first step.

---

## Bonus: aur bhi 2 files Aurora wali hain

`assets/img/logo.svg` aur `assets/img/og-default.svg` bhi Aurora ke hain. Ye **JSON-LD logo** aur
**WhatsApp/Facebook/Google share preview** me use hote hain. Bolo to unhe bhi Vayuroop mark se
replace kar dunga (same style me) — warna social pe purana mark dikhta rahega.
