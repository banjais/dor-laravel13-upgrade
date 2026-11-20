# DoR Laravel 13 Upgrade — Project Context

Last updated: 2026-11-20

## 1. Project and source of truth

- Project: Department of Roads (DoR), Nepal — dor.gov.np.
- GitHub repository: `banjais/dor-laravel13-upgrade`
- Main branch: `main`
- This repository is the project source of truth for the Laravel 13 upgrade.
- Do not confuse it with the older repository `banjais/dor`; that is not the main upgrade repository.
- Original GitLab repository is separate and must not be changed without explicit instruction.
- Local Windows path: `D:\DoR-Website-Backup\Laravel13-Upgrade`
- Local WSL path: `/mnt/d/DoR-Website-Backup/Laravel13-Upgrade`
- Staging path used previously: `/mnt/d/DoR-Website-Backup/dor-laravel13-upgrade-github-stage`

## 2. Working rules and approvals

- User is not a programmer. Give simple, concise Romanized Nepali instructions.
- Clearly label terminal commands as **WSL Ubuntu** or **Windows PowerShell**.
- Prefer one manageable step at a time; avoid huge command output and unnecessary audits.
- Preserve the existing DoR homepage structure and make incremental, targeted changes.
- Back up files before editing. Do not make broad rebuilds or unrelated changes.
- Never delete database content to solve a presentation problem.
- Never commit `.env`, passwords, API keys, mail credentials, database dumps, or private uploads.
- Do not deploy, run production migrations, or alter production data/configuration without explicit approval.
- Do not assume SSH or VM access. Verify access and environment before using them.
- Before code changes, inspect the current file and relevant CSS/Blade context; do not rely solely on old notes.
- Report exactly which files changed, tests run, and any remaining risks.

## 3. Environments

### Local development

- WSL Ubuntu 22.04.5.
- PHP 8.5.12.
- Laravel 13.40.0 at the last recorded checkpoint.
- MariaDB 10.6.23.
- Local database: `dor_upgrade`.
- Table prefix: `pndc_`.
- Last recorded schema: 188 tables, approximately 126.05 MB.
- Vendor symlink: `/home/doradmin/dor13-vendor`.
- Local test URL used: `http://127.0.0.1:8000/home`; port 8000 has sometimes already been occupied.
- `APP_URL` was set to `https://dor.gov.np` in local config; local browser testing still uses localhost.

### Production snapshot (historical reference only)

- Ubuntu 20.04.6.
- PHP 7.4.26 (CLI and FPM).
- Composer 2.1.12.
- Laravel 8.61.0.
- Database `dor` at 127.0.0.1:3306.
- Production snapshot reported migrations as “No”.
- These are historical snapshot details, not confirmation of the current live server state.

## 4. Previously completed or verified work

The following were recorded as completed or passing at earlier checkpoints. Re-verify before relying on them after further changes.

- Laravel 13 application service provider paginator and view composer updates.
- CORS middleware aliases and Laravel 13 mailer compatibility.
- Download guards and `report()` handling.
- Website theme settings and related configuration.
- Office-hours seasonal setting: winter BS period 8/16–11/15, start time 09:00.
- Circular upload limit: 100 MB.
- OTP policy: roles [2, 12, 13], 5/10 throttle, 5-minute expiry, maximum 5 attempts; role 3 excluded.
- Role 2 Super Admin: global access.
- Role 12 Office Admin/Web Admin: own-office scope.
- Role 13 Circular Admin.
- Role 3 Project Admin: excluded from OTP.
- Circular Admin management rules: roles 2/12 manage type-13; role 13 blocked.
- Office email migration fields: `office_alternative_email`, `office_facebook_page`.
- Historical email migration counts: 234 offices, 232 primary emails, 69 alternative emails, 12 changes, 14 ambiguous, 70 skipped.
- Previously passing tests: OfficeProfileEmailSyncTest (5), UserAccountEmailTest (4), PasswordResetOtpTest (11), CircularAdminManagementTest (8). An XML schema warning was noted.
- Home view uses `home_2021.blade.php`.
- Theme stylesheet: `public/assets/css_2021/website-theme.css`, loaded after base CSS.
- `head_2021` uses filemtime-based asset cache busting.
- Banner description migration: `add_banner_description_to_banner_details`; field `banner_description`; route pattern `{subdomain}/banner/{id}`.

## 5. Homepage and office layout requirements

- Preserve the original homepage structure; improve it incrementally.
- Head Office and Other Offices should have matching header/menu/after-menu/banner/banner-navigation plate styling and heights.
- The “Switch to Head Office” plate should match Head Office height; a subtle grey background is preferred over white. Do not change unrelated dimensions.
- Check whether Other Offices have different CSS and unify only the relevant rules.
- Fix the Other Offices Contact Officers box.
- Officer cards should auto-arrange for 1, 2, 3, or 4 officers.
- Phone, email, and descriptions must not wrap awkwardly or overlap; layout must remain responsive.
- Long text may be visually clamped or hidden, but database content must not be deleted.
- Head Office banner and Contact Officers heights should align, including medium-sized screens.
- “Office Chief upto date's” text should be smaller and auto-arranged.
- On mobile, officer photos must preserve aspect ratio and not stretch.
- Other Offices need a moving banner/slideshow like Head Office, with per-office add/edit/delete/reorder and own-office-only permissions.

### Officer slots

- Head Office: 4 slots — Director General, Spokesperson, Information Officer, Grievance Hearing Officer.
- Other offices: 3 slots.
- Known Head Office URL/record references:
  - DG: `message-from-director-general`, office_id 4, show_in 2.
  - Spokesperson: `dor-spoke-person`, office_id 4, show_in 4.
  - Information Officer: `information-officer`, office_id 4, show_in 4.
- Preserve the Information Officer slot; do not replace it with the unrelated `general-info` entry.

## 6. Latest recorded CSS checkpoint

File: `public/assets/css_2021/website-theme.css`

Backup recorded:
`website-theme.css.backup-20261120-184611`

A desktop-only override was appended:

```css
@media (min-width: 992px) {
    #cssmenu .butlike,
    #cssmenu .back-btn,
    .nav-wrap .navbar-collapse .back-btn,
    #navbarSupportedContent .back-btn {
        padding: 9px 11px !important;
        background-color: #e9edf1 !important;
        border-color: #d5dce3 !important;
    }
    #cssmenu .butlike:hover,
    #cssmenu .back-btn:hover,
    .nav-wrap .navbar-collapse .back-btn:hover,
    #navbarSupportedContent .back-btn:hover {
        background-color: #e1e7ec !important;
    }
}
```

User clarified that the main requirement was matching the Head Office plate height and using a slightly grey background, not changing other heights or dimensions. Verify current CSS before editing.

## 7. PWA and header/footer notes

- PWA files: `public/manifest.webmanifest`, `public/service-worker.js`, `public/assets/pwa/icon-192.png`, `icon-512.png`, and maskable icons.
- QR image: `public/assets/pwa/dor-website-qr.png`, generated with Node qrcode; target URL `dor.gov.np`.
- Service worker cache name recorded: `dor-public-static-v3`.
- Manifest had 192/512 any-purpose and maskable icons; background `#1F2227`.
- Footer entry: “DoR App” / “डोर एप” modal with QR; when installed, show Open rather than Install.
- `RevalidateHtmlResponses` was set to no-store; `head_2021` included favicon, Apple touch icon, and manifest with filemtime query strings.
- A previous 500 from undefined `$nepalidate` in `foot_2021` was fixed by reusing `#headerDateDisplay`; `view:clear` was run.
- Header preferences: compact bold icons, no white boxes behind icons, short hover titles; Sitemap as text; language and calendar together in a globe dropdown; default Bikram Sambat/Nepali date on each load (not persisted); Nepal Sambat disabled/placeholder; one-click font-size toggle; bandwidth icon separate.
- Header/footer text links should change color on hover only; no underline, tooltip, or movement. DoR App should visually match footer text links.
- PWA icon preference: preserve Nepal coat of arms sharply; charcoal/navy square; “DoR” lettering with D navy and R crimson; road curves upward from R; avoid white background.

## 8. Data and local tool notes

- Historical counts: banners 1998, pages 1671, gallery 894, office_chiefs 40, staffs 1791.
- A `public/storage` symlink was missing at one earlier checkpoint while `storage/app/public` existed. Verify before relying on public storage links.
- PHP GD was available; Imagick and PHP QR classes were absent at one checkpoint.
- Windows Node v24.13.0 was available; npm package `qrcode` was installed under `/mnt/d/tmp/dor-qr-tools`.

## 9. VM reproducibility checklist

Before moving from local testing to a VM, record and verify:

- OS version and installed system packages (including PDF text extraction, OCR, and search-index tools).
- PHP version/extensions, Composer version, Node/npm version, and all dependencies.
- Database engine/version, schema, prefix, backup/restore steps.
- Environment variable names and safe configuration notes (never secret values).
- File/folder ownership and permissions; storage symlinks.
- Web server and PHP-FPM configuration; cron, queue workers, and scheduled tasks.
- Search-index creation/rebuild steps and required binaries.
- Build, cache, test, backup, deployment, health-check, and rollback procedures.

Install and test required tools locally first, then reproduce the verified setup on the VM. Do not assume an installation is complete until its version and a functional test are recorded.

## 10. Git and review workflow

- Keep `main` as the shared project source of truth.
- Before editing, inspect repository status and current file content.
- Use a focused branch or pull request for substantial changes when appropriate.
- Avoid destructive Git operations and force-push.
- Review diffs before committing.
- Run targeted tests and relevant asset/view checks.
- Record commit SHA, changed files, tests, results, and remaining issues here after meaningful milestones.
- Do not commit secrets, local environment files, database dumps, or generated private data.
