# Vayuroop Website Audit

**Site:** [https://www.vayuroop.com/](https://www.vayuroop.com/)  
**Audit date:** 1 October 2026  
**Scope:** Technical SEO, on-page SEO, content, e-commerce UX, performance, security, accessibility, legal/trust (India), off-page  
**Method:** Live crawl of all public URLs, HTML source, HTTP headers, robots/sitemap, DNS/WHOIS, W3C validator, securityheaders.com, search-index check

---

## Hindi executive summary

Site abhi **demo / installation stage** pe hai, live brand store nahi. Theme ka naam **Aurora** ab bhi code, SKU, product names aur copy mein dikh raha hai. Google pe **index nahi** ho rahi. Sirf **8 products**, sabki photos **SVG placeholders** hain, blog **khaali** hai, legal pages **template text** hain, aur homepage pe **fake 4.9 rating** hai.

**Overall score: 41 / 100 — launch-ready nahi.**

Pehle ye 10 kaam karo, phir ads / SEO spend mat girao:

1. Placeholder copy hatao (About, FAQ, Privacy, Terms, Shipping, Returns).
2. Asli product photos (JPG/WebP), size chart, fabric/care, reviews.
3. Product schema `OutOfStock` bug fix karo — stock hai phir bhi Google ko out of stock bata rahe ho.
4. Contact pe email, phone, address, GSTIN, company legal name.
5. “4.9 rated” hatao jab tak reviews na hon.
6. Google Search Console + sitemap submit.
7. Search pages `noindex`. HTML pe `Cache-Control: no-store` hatao.
8. HSTS on karo, `X-Powered-By: PHP/8.5.4` hatao.
9. Blog pe 8–12 real style/fit/care articles.
10. Catalog badhao (women sirf 2 items) aur “Aurora” leftover branding hatao.

---

## Scorecard

| Area | Score | Verdict |
| --- | ---: | --- |
| Technical SEO | 58 | Foundations exist; caching, schema, indexation broken |
| On-page SEO | 52 | Titles/canonicals OK; thin H1s, generic metas, SVG OG |
| Content & catalog | 28 | Demo catalog, placeholders, empty journal |
| E-commerce UX / CRO | 38 | Pretty UI, no real photos, no reviews, no size guide |
| Performance | 48 | Tiny HTML but uncacheable; render-blocking CSS/JS |
| Security | 72 | Headers grade A; HSTS missing, PHP leaked, admin public |
| Accessibility | 61 | Viewport/ARIA search OK; heading skips, icon-only nav |
| Trust / legal (India) | 22 | Template policies, no entity, no GST, fake social proof |
| Off-page / authority | 12 | New domain, zero Google results, no social, no backlinks |
| **Overall** | **41** | **Not ready to advertise or rank** |

---

## 1. What this site actually is

This is a **custom PHP storefront** (not Shopify/WooCommerce), hosted on **Hostinger + LiteSpeed**, session cookie `aurora_session`.

| Fact | Detail |
| --- | --- |
| Stack | PHP 8.5.4, LiteSpeed, HTTP/2 + HTTP/3, gzip |
| Hosting | Hostinger (AS47583), IP `82.25.107.79`, IPv6 on |
| Domain | Registered **9 May 2026**, expires **9 May 2027**, registrar Hostinger |
| DNS | `ns1/ns2.dns-parking.com` (Hostinger default). Apex A + AAAA. `www` CNAME → apex |
| Email | MX `mx1/mx2.hostinger.com`, SPF present. **No DMARC / DKIM / CAA** visible |
| Analytics | GTM snippet with ID `GT-M6JN2L37` (Google tag format, not `GTM-…`) |
| Theme leftovers | Cookie `aurora_session`, SKUs `AUR-*`, products “Aurora Cap / Aurora Heavy Hoodie”, cap copy says **“embroidered Aurora mark”** |

HTML comments still say *“Google Tag Manager (free) — configure in Admin → Settings → SEO Controls”* and legal pages say *“Edit in Admin → Pages.”* Crawlers and customers both see that the site is unfinished.

`https://vayuroop.com/` correctly canonicalises to `https://www.vayuroop.com/` (good).

---

## 2. Crawl map (every public URL)

**XML sitemap** (`/sitemap.xml`) lists 21 URLs. **Blog posts: 0.**

| Type | URLs | Status |
| --- | --- | --- |
| Home | `/` | Live |
| Shop | `/shop`, `/shop?sort=newest`, `/shop?on_sale=1`, `/shop?sort=popular` | Live. Filtered URLs keep canonical `/shop` (good) but **Sale title is still “Shop All Products”** |
| Categories | `/category/men` (4), `/women` (2), `/accessories` (2) | Live, thin |
| Products | 8 SKUs | Live, SVG images, thin copy |
| Blog | `/blog` + 4 empty categories | **“No articles yet”** |
| Pages | About, Contact, FAQ, Privacy, Terms, Shipping, Returns, HTML sitemap | Live, mostly templates |
| Account | `/login`, `/register`, `/account` → login | Live |
| Cart | `/cart` empty state. `/checkout` redirects to cart when empty | OK |
| Search | `/search?q=tee` works (2 results) | **Indexable — should not be** |
| Admin | `/admin` → `/admin/login` | Public login form |
| 404 | Custom 404, title “Page Not Found \| Vayuroop” | Good UX |
| Missing | `/manifest.json`, `/ads.txt`, `/llms.txt`, `/.well-known/security.txt` | All 404 as HTML (soft-404 risk if ever linked) |

`robots.txt`:

```
User-agent: *
Disallow: /admin
Disallow: /checkout
Disallow: /cart
Disallow: /account
Sitemap: https://www.vayuroop.com/sitemap.xml
```

**Gaps:** `/search`, `/login`, `/register`, `/admin/login` are crawlable. Filtered shop URLs are crawlable (canonical saves you, but crawl budget still wasted later).

---

## 3. Technical SEO

### 3.1 What is already good

- HTML5, `lang="en"`, charset, responsive viewport
- Unique `<title>` + `<meta name="description">` on home, shop, categories, products
- Absolute `rel="canonical"` on every template checked
- Open Graph + Twitter cards present
- `hreflang` `en` + `x-default`
- Organization JSON-LD on home; Product + BreadcrumbList on PDP
- XML sitemap + HTML sitemap + robots
- `www` as canonical host
- W3C HTML: **only 1 error sitewide** (heading level skip `h2`/`h1` → `h6` in footer/filters)
- Hero image `fetchpriority="high"`; product grid `loading="lazy"`
- CSRF tokens on cart and newsletter forms
- HTTP/2, gzip, IPv6, `alt-svc` HTTP/3

### 3.2 Critical technical issues

**A. Homepage (and likely all HTML) is uncacheable**

```
cache-control: no-store, no-cache, must-revalidate
pragma: no-cache
expires: Thu, 19 Nov 1981 08:52:00 GMT
set-cookie: aurora_session=…; secure; HttpOnly; SameSite=Lax
```

PHP is starting a session on **anonymous** page views. Every Googlebot and visitor hit goes to origin. This hurts TTFB, Hostinger load, and Core Web Vitals. Fix: don’t open a session until cart/login; send `Cache-Control: public, max-age=60, s-maxage=300` for HTML.

**B. Product schema marks in-stock items as OutOfStock**

Classic Crew Tee JSON-LD:

```json
"offers": {
  "@type": "Offer",
  "priceCurrency": "INR",
  "price": "649.00",
  "availability": "https://schema.org/OutOfStock"
}
```

Same product has variant stock S10 / M15 / L20 / XL25. Google Merchant / rich results will treat the catalog as unavailable. Also missing: `brand`, `gtin`/`mpn`, `hasVariant`, `aggregateRating`, `review`, `image` as real photos, `shippingDetails`, `hasMerchantReturnPolicy`.

**C. Search pages are indexable**

`/search?q=tee` has **no `noindex`**. Canonical is `/search` (query stripped). Google can index a thin search hub that duplicates the shop. Add `noindex,follow` and `Disallow: /search` in robots.

**D. OG / Twitter / favicon are SVG**

```
og:image = /assets/img/og-default.svg
twitter:image = …svg
product og:image = …/tee-black.svg
favicon = favicon.svg
```

Facebook, LinkedIn, WhatsApp, iMessage, and many crawlers **do not render SVG** as share images. You will get blank previews. Use 1200×630 JPG/PNG.

**E. Organization schema is empty of trust signals**

```json
{"@type":"Organization","name":"Vayuroop","url":"https://www.vayuroop.com","logo":"…/logo.svg","sameAs":[]}
```

No `sameAs` (Instagram/Facebook), no `contactPoint`, no `address`, no `logo` as PNG. No `WebSite` + `SearchAction` (sitelinks search box). No `FAQPage` on `/faq`. No `ItemList` / `CollectionPage` on shop or categories.

**F. Breadcrumb schema ≠ visible breadcrumbs**

PDP visible: Home → Shop → **Men** → Product  
JSON-LD: Home → Shop → Product  

Mismatch can drop breadcrumb rich results.

**G. Crawler-visible stats are zeros**

Homepage counters render as **0 / 0 / 0** without JS (Googlebot often sees this). JS-enabled snapshot showed 8 / 4 / 1. Don’t animate from 0; print the real numbers in HTML.

**H. Duplicate / thin parameterized titles**

- `/shop?on_sale=1` title = “Shop All Products | Vayuroop” (same as all products)
- Category titles = “Men | Vayuroop” — no keyword depth (“Men’s T-Shirts, Hoodies & Joggers”)
- FAQ / search / many CMS pages reuse the **generic** description: *“Vayuroop — premium clothing, thoughtfully designed…”*

**I. `hreflang` is English-only** on an INR store targeting India. Either add `en-IN` or drop hreflang until you have real locales. Empty hreflang pairs can be ignored; claiming only `en` is fine if intentional.

**J. Information disclosure**

- `x-powered-by: PHP/8.5.4`
- `platform: hostinger` / `panel: hpanel`
- Admin login publicly linked via `/admin`

**K. Sitemap quality**

- Home/shop/blog/about/contact/faq have **no `<lastmod>`**
- All product lastmods are identical (`2026-09-29T05:41:50+05:30`) — looks generated, not real
- No image sitemap
- Empty blog still listed at priority 0.8 daily
- HTML sitemap still lists `/page/about` and `/page/faq` (they 301 to `/about` and `/faq`) plus duplicate FAQ

**L. Indexation**

`site:vayuroop.com` returned **zero Google results** at audit time. Domain is ~5 months old with almost no inbound links. Submit sitemap in **Google Search Console** and **Bing Webmaster**; do not expect rankings until content is real.

---

## 4. On-page SEO by template

### Home — `Vayuroop — Premium Clothing & Everyday Style`

| Element | Status |
| --- | --- |
| Title (52 chars) | Good brand + category |
| Meta (~155 chars) | Decent, a bit generic |
| H1 | “Wear the Moment.” — brand line, **zero search demand** |
| Internal links | Shop, blog, about, 3 categories, products — OK |
| Duplicate modules | New Arrivals + Featured + Best Sellers repeat the **same 8 SKUs** |
| Fake proof | “4.9 rated”, “Vayuroop worldwide”, “Arctic 01 Drop” |
| Categories | Icon placeholders, no photos, no counts |
| Blog CTA | “Read the Journal” → empty blog (dead-end) |
| Section numbers | 05 then **07** (06 missing) |
| Collection banner | `alt=""` empty |

H1 should carry a real query, e.g. **“Premium everyday clothing for men & women | Vayuroop”**, with the poetic line as a subtitle.

### Categories

- H1 is just “Men” / “Women” / “Accessories”
- No unique intro copy (0 words of category SEO text)
- No `CollectionPage` schema
- Women = 2 products, Accessories = 2 — too thin to rank vs Myntra/Ajio/brand.com

### Products (all 8)

| Product | Price | Issues |
| --- | --- | --- |
| Classic Crew Tee | ₹649 (was 799) | Copy says sizes **S–XXL**, variants only S–XL. SVG photo. Schema OutOfStock |
| Essential White Tee | ₹799 | One paragraph, no GSM/fit/care |
| Aurora Heavy Hoodie | ₹1,599 | **Aurora** name. No size S. Winter copy |
| Everyday Joggers | ₹1,299 | Thin |
| Amber Midi Dress | ₹1,799 | Thin, no length/lining |
| Olive Overshirt | ₹1,699 | No size S |
| Aurora Cap | ₹499 | “Aurora mark”. One size |
| Natural Canvas Tote | ₹599 | Thin |

Every PDP is missing: real photos (front/back/on-body/fabric), size chart, fit model stats, fabric GSM, country of origin, care, delivery ETA, reviews, Q&A, wishlist, share, related complete outfits.

Variant JSON is dumped in the DOM (`.variant-json.d-none`). Crawlers extract it as visible text — ugly SERP snippets and a mild info leak of stock numbers.

### Blog

Title is good (*Fashion Journal — Style Guides & Trends*). Body is empty. Four category URLs exist with no posts. This is a **soft-content play** that currently hurts E-E-A-T: you promise a journal and deliver nothing.

---

## 5. Content, brand & E-E-A-T

Google’s quality systems (and Indian shoppers) look for experience, expertise, authority, trust. Current state:

| Signal | Finding |
| --- | --- |
| Real photography | Almost none. Products are **SVG illustrations**. Hero JPG exists |
| Unique copy | Short demo blurbs. About/FAQ/legal are installer templates |
| Authorship | None |
| Brand consistency | Mixed **Vayuroop** vs leftover **Aurora** |
| Social proof | Fake 4.9, zero reviews, empty `sameAs` |
| Company identity | No legal name, address, GSTIN, founders, manufacturing story |
| Expertise | No fabric/fit science, no lookbooks |
| Freshness | Blog empty; sitemap lastmod stamped the same day |

Visible template leaks (must delete before any crawl/ads):

- About: *“This is placeholder copy from the installation.”*
- FAQ: *“Edit these answers in Admin → Pages → faq.”*
- Privacy / Terms / Shipping / Returns: *“Template text — replace with your compliant policy.”*

These strings are a **manual-action / spam** risk if indexed, and they destroy conversion.

---

## 6. E-commerce UX & conversion

**Working well**

- Sticky header, announcement bar (free shipping ₹999)
- Desktop search
- Filters (category, size) on shop
- Size/colour chips, qty picker, CSRF add-to-cart
- Related products
- Empty cart state
- Register/login
- Newsletter (CSRF + AJAX hook)
- 7-day returns mentioned in marquee

**Conversion killers**

1. **No real product photography** — fashion conversion without on-body photos is near zero.
2. **“Please select a size and a colour”** even when only one colour exists — extra friction.
3. **No size guide**, no “how it fits”, no model height.
4. **No reviews / UGC**. Homepage still says 4.9 — deceptive.
5. **Quick look** is a CSS label, not a modal.
6. **Search hidden on mobile** (`d-none d-lg-flex`).
7. **Account/cart icons** have `title` but no visible text; cart has no count badge in HTML.
8. **Contact page** has a form and *zero* email, phone, WhatsApp, or address. Footer `footer-contact` is empty. Social block is empty.
9. **Women’s catalog** is two items — bounce.
10. **Checkout** not auditable without a cart (robots disallow, empty cart redirects). Payment methods never stated on site (UPI / cards / COD?).
11. **Single colour per SKU** — no swatches, no lifestyle variants.
12. Related products for tote/cap = 1 item.

Estimated conversion if you turned ads on today: **very low**. Fix photos + trust + PDP completeness before media spend.

---

## 7. Performance (inferred; PSI UI did not finish)

Direct lab PSI did not return scores in this environment. From source + headers:

| Factor | Impact |
| --- | --- |
| HTML ~5 KB gzip | Positive |
| `Cache-Control: no-store` on HTML | Strong negative (TTFB, TTFB variance) |
| 4 CSS files in `<head>` (bootstrap, icons, store, theme) | Render-blocking |
| 3 JS at end (bootstrap.bundle, store.js, motion.js) | OK placement; tilt/marquee JS extra work |
| No `width`/`height` on images | CLS risk |
| Product images SVG | Tiny files, but not what you want long-term; real photos will need WebP/AVIF + `srcset` |
| No font-display / preload spotted in head | If Google Fonts load from CSS (BuiltWith history), extra RTT |
| Session cookie on every hit | Cache bust |
| Shared Hostinger, no CDN in front of HTML | Geography-sensitive TTFB from India vs US |
| GTM in `<head>` | Extra main-thread if container is heavy |

**Recommendations:** cache anonymous HTML; combine/minify CSS; critical CSS for hero; `width`/`height` or `aspect-ratio`; preload hero + font; defer GTM; serve images via WebP + srcset; consider Cloudflare in front of Hostinger.

---

## 8. Security

[securityheaders.com](https://securityheaders.com/?q=https://www.vayuroop.com/&followRedirects=on) grade **A**.

| Header | Value | Note |
| --- | --- | --- |
| CSP | `upgrade-insecure-requests` only | Not a real CSP. XSS still easy |
| HSTS | **Missing** | Add `max-age=31536000; includeSubDomains; preload` after confirming HTTPS everywhere |
| X-Content-Type-Options | nosniff | Good |
| X-Frame-Options | SAMEORIGIN | Good |
| Referrer-Policy | strict-origin-when-cross-origin | Good |
| Permissions-Policy | camera/mic/geo = () | Good |
| X-XSS-Protection | 1; mode=block | Legacy, harmless |
| Cookie | Secure, HttpOnly, SameSite=Lax | Good. No `__Host-` prefix |
| X-Powered-By | PHP/8.5.4 | Remove in php.ini / .htaccess |

Other:

- CSRF on POST forms — good
- Admin at predictable `/admin/login` — add rate limit, 2FA, optional IP allowlist, `noindex`
- No `security.txt`
- No CAA DNS (anyone can issue a cert if they pass CA checks)
- No DMARC on the domain TXT set we saw — spoofing risk for `@vayuroop.com`
- Newsletter/contact forms: confirm captcha / rate limit (not visible in HTML)

---

## 9. Accessibility

| Issue | Severity |
| --- | --- |
| Footer/filter `h6` skips heading levels | W3C error |
| Search only on large screens | Mobile users can’t search without opening… nothing |
| Account/cart are icon-only | Need visually hidden text, not just `title` |
| No skip-to-content link | Keyboard users hit the full nav every page |
| Marquee is `aria-hidden` | Good (decorative) |
| Hero orbs `aria-hidden` | Good |
| Product images have alt | Good, but alt shouldn’t say “product photo” of an SVG blob |
| Collection banner empty alt | Fix |
| Colour selected only as text “Black” | OK; don’t rely on colour alone |
| Focus states unknown (not lab-tested) | Verify `btn-brand` contrast (accent `#b45309` on black may fail for small text) |
| `details/summary` FAQ | Good native disclosure |

---

## 10. Legal, trust & India compliance

You sell in **INR** with Indian shipping/returns copy. That pulls in:

- Consumer Protection (E-commerce) Rules, 2020  
- Legal Metrology (packed commodities) if you ship packaged goods  
- DPDP Act, 2023  
- GST invoice rules  

**Currently missing / non-compliant looking:**

| Requirement | Site |
| --- | --- |
| Legal name of seller | Not stated |
| Registered office / grievance officer | Not stated |
| GSTIN on site / invoice promise | Not stated |
| Contact email & phone | **Not on Contact or footer** |
| Country of origin on PDP | Missing |
| Accurate return window | Marquee “7-day”; policy says 7 days; FAQ says “the window stated in our Return Policy” |
| Privacy policy | Template; no DPDP purpose limitation, no Data Fiduciary identity, no cookie list |
| Terms | Template; mentions “AI-assisted editorial content” |
| Newsletter | No consent language (DPDP / spam) |
| Cookie / GTM | GTM loads with **no consent banner** |
| Payment / COD / refund TAT | Vague “5–7 business days” |
| Fake rating | “4.9 rated” with no reviews — unfair trade risk |

Do not run Meta/Google ads until identity, policies, and contact are real. Ads accounts get banned for this.

---

## 11. Off-page, brand & competitive reality

- Domain age: **~5 months**
- Google index: **none found**
- Backlinks: none observed
- Social: footer social container **empty**
- Brand search: “Vayuroop” clothing has no established SERP; similar names (Vaayu, Vuori, Veyro) will confuse
- Competitors in “premium everyday India apparel”: Snitch, The Souled Store, H&M, Zara, Uniqlo, local D2C — all have photos, reviews, 100s of SKUs, and blogs

SEO will not move this brand in 30 days. The job now is **indexable trust + catalog + content**, then digital PR / Instagram, then category SEO.

---

## 12. Priority roadmap

### P0 — this week (before any ads or GSC push)

1. Replace every “placeholder / Edit in Admin” string.
2. Publish real Privacy, Terms, Shipping, Returns (DPDP + e-commerce rules). Put **company name, address, email, phone, GSTIN** in footer and Contact.
3. Remove “4.9 rated”, “Vayuroop worldwide”, and Aurora leftovers (product titles, SKU prefix, cap embroidery copy).
4. Upload real product photos (min. 3 angles). Change OG image to 1200×630 JPG.
5. Fix Product `availability` to `InStock` when any variant has stock; add `brand`.
6. Hide admin comments in HTML source.
7. Add `noindex` to `/search`, `/login`, `/register`.

### P1 — next 2 weeks

8. Google Search Console + Bing + sitemap submit. Verify DNS (SPF already; add **DMARC** `p=none` then quarantine).
9. Stop PHP sessions on public pages; enable HTML cache + HSTS; strip `X-Powered-By`.
10. Unique meta descriptions for FAQ, Sale, each category. Keyword H1s.
11. Size guide + care + origin + delivery ETA on every PDP.
12. FAQPage schema; WebSite + SearchAction; CollectionPage ItemList; breadcrumb alignment.
13. Mobile search field.
14. 6–8 journal posts: fabric care, fit, “how to wear X”, monsoon/winter packing. Internal-link to PDPs.
15. Instagram + sameAs. Stop empty social `<div>`.

### P2 — 30–60 days

16. Grow catalog (especially Women). Don’t rank category pages with 2 products.
17. Reviews (even post-purchase email). Never fake stars.
18. Image sitemap, WebP, width/height, CDN.
19. Real CSP, rate-limited admin, 2FA.
20. Look at Merchant Center once photos + availability + GTIN/brand are clean.
21. Hindi landing or `en-IN` only if you will maintain it — don’t add hreflang theatre.

---

## 13. Page-level checklist

| URL | Title | Canonical | Schema | Index? | Main fix |
| --- | --- | --- | --- | --- | --- |
| `/` | Good | www `/` | Organization only | Yes | Real H1, real stats, kill fake 4.9 |
| `/shop` | Good | `/shop` | None | Yes | Intro copy + ItemList |
| `/shop?on_sale=1` | **Wrong title** | `/shop` | None | Canonicalised | Unique Sale title if you want it indexed — or keep canonical |
| `/category/men` | Thin | Self | None | Yes | 150–300w copy + more SKUs |
| `/category/women` | Thin | Self | None | Yes | Catalog too small |
| `/product/*` | `{Name} \| Vayuroop` | Self | Product **OutOfStock** + Breadcrumb | Yes | Photos, schema, depth |
| `/blog` | Good | Self | None | Yes | Publish posts or noindex until then |
| `/about` | OK | `/about` | None | Yes | Real story; delete placeholder |
| `/faq` | OK | `/faq` | **No FAQPage** | Yes | Unique meta + schema + delete admin note |
| `/contact` | OK | Self | None | Yes | Email/phone/address |
| `/search?q=` | Query in title | `/search` | None | **Should noindex** | robots + meta |
| `/page/*` policies | OK | Self | None | Yes | Lawyer/DPDP rewrite |
| `/admin/login` | Sign In | n/a | None | Block | 2FA, noindex, rate limit |

---

## 14. What’s worth keeping

The bones of a D2C theme are fine:

- Clean visual design, brand colour tokens (`#111` / `#b45309`)
- Sensible IA (Shop / New / Sale / Blog / About / Contact)
- Canonical + OG + basic JSON-LD already wired in the CMS
- CSRF, cookie flags, several security headers
- Free-shipping threshold, sale badges, related products
- HTML sitemap and 404

This is **not** a broken site. It is an **unfinished Aurora demo published on a real domain**. Treat it as a staging build that accidentally went live.

---

## 15. Suggested north-star KPIs (after P0/P1)

| KPI | Now | 60-day target |
| --- | --- | --- |
| Indexed pages (GSC) | ~0 | 20–30 quality URLs (no search/login) |
| Product pages with real photos | 0/8 | 8/8 + 3 images each |
| Blog posts | 0 | 8+ |
| Product schema errors | All OutOfStock | 0 |
| Visible company identity | None | Footer + Contact complete |
| Brand SERP for “Vayuroop” | Absent | Site sits #1 with sitelinks |
| Paid traffic | Don’t start | Only after P0 |

---

*Audit based on publicly visible HTML, headers, DNS and on-page content as of 1 Oct 2026. Checkout payment flow, admin, and Core Web Vitals lab numbers were not fully lab-tested (PSI did not complete; origin TLS from some resolvers is IPv6/Hostinger-sensitive). Re-run PSI and GSC URL Inspection after P0.*
