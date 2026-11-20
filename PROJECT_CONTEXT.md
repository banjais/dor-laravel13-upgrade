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


## 11. User-confirmed requests and working decisions

Keep this section as a decision log, not just a technical status summary. It helps future ChatGPT sessions remember what the user explicitly requested, clarified, approved, or rejected. Record the date when known. Distinguish confirmed decisions from ideas still under discussion. Do not silently reinterpret a request or mark a proposal as approved.

### Confirmed working preferences

- The user wants continuity across ChatGPT chats; consult this file before asking them to repeat project background or previously confirmed decisions.
- Give simple Romanized Nepali explanations and clearly labelled terminal steps (WSL Ubuntu / Windows PowerShell).
- Avoid breaking work into unnecessary long pauses or asking for very large terminal outputs. Ask for the smallest useful output.
- Do not make broad changes when the user requested a specific adjustment. In particular, when the user says a component's height should match another, do not change unrelated dimensions.
- Preserve the existing homepage structure and make focused incremental changes.
- For homepage styling, match the Head Office appearance where requested; the “Switch to Head Office” plate should have the same height, with a slightly grey rather than white background.
- Keep user-confirmed constraints and corrections even if a later chat starts without the original conversation.

### Explicit project decisions to preserve

- The upgrade repository is `banjais/dor-laravel13-upgrade`, branch `main`; do not confuse it with the older `banjais/dor` repository.
- The user confirmed the GitHub repository is Public.
- GitHub's ChatGPT Codex Connector installation is configured for **Only select repositories**, with `banjais/dor-laravel13-upgrade` selected.
- The connector installation page reports read access to checks, commit statuses, and metadata, and read/write access to actions, code, issues, pull requests, and workflows.
- The user wants relevant prior requests and confirmations recorded here so future sessions can remember them and work more consistently.

### Decision-log maintenance

- Add new user-confirmed requirements, corrections, and approvals here as work progresses.
- For each important decision, record: date (if known), exact requested outcome, scope/constraints, and whether it is confirmed, pending, or rejected.
- When a decision changes, preserve the old decision as superseded and record the new one; do not erase history that explains why code is the way it is.
- Do not record secrets, personal sensitive information, or unnecessary private details.
- Keep technical implementation status separate from user intent: a requested feature is not “completed” until implementation and testing are verified.


## 12. User-requested fast autonomous development workflow (confirmed 2026-11-20)

The user explicitly requests that, when they ask for project work, the assistant should follow this order without waiting for repeated instructions:

1. **Update project memory first:** record the new request, constraints, and intended outcome in `PROJECT_CONTEXT.md` on GitHub before starting implementation.
2. **Read it back:** fetch and read the updated `PROJECT_CONTEXT.md` from GitHub in the same work session, then use it as the working brief.
3. **Inspect and analyze:** inspect the relevant repository files, related Blade/CSS/JS/routes/controllers/tests as appropriate, and current GitHub state before editing.
4. **Implement promptly:** make focused changes in the related files. The user permits the assistant to make reasonable implementation decisions and does not require asking about every minor detail. Ask only when a decision is consequential, ambiguous, risky, or could affect data/security/production.
5. **Verify:** review the diff and run available targeted tests/checks. Never claim a test or browser check was run unless it actually was.
6. **Publish:** commit and push approved, verified code changes to the GitHub repository/branch. Prefer a focused branch and PR for substantial or risky work; do not force-push or overwrite unrelated work.
7. **Keep the user informed:** state what changed, commit SHA, tests, and what still needs local visual verification.

### Today's requested sync scope and limits

- For the current work session, the desired shared workflow is **ChatGPT conversation + GitHub + the user's own PC/local live site**.
- The user wants to view the local live website on their PC while the assistant works.
- The assistant can read and write the GitHub repository through the connected GitHub app, subject to the granted repository permissions.
- The assistant cannot directly operate the user's PC, local WSL filesystem, browser, Git working tree, or local server from an ordinary chat. A GitHub commit does not itself synchronize files into the PC working copy. Local synchronization requires a configured Git client/automation or a user-run `git pull`/sync step. Do not claim the PC is synced until verified.
- The user also wants changes sent to a new VM eventually. That is a later stage; no VM deployment or production action is authorized merely by this workflow request. First verify VM access, deployment method, backups, environment, and obtain explicit approval before any VM deployment.
- Do not store credentials, tokens, SSH keys, or private connection details in this file or the repository.
- Do not create insecure auto-deploy credentials or expose a local development server publicly just to achieve synchronization.


## 13. Local PC Git status checkpoint — 2026-11-20

User supplied the WSL output of `git status --short --branch` from:
`/mnt/d/DoR-Website-Backup/Laravel13-Upgrade`

Observed facts:
- Local checked-out branch is `master`, while the GitHub project handover/source branch is `main`.
- The local working tree is heavily modified and has many untracked files/directories, including application code, controllers, models, migrations, tests, Blade views, CSS/JS, PWA assets, backup files, and temporary/audit artifacts.
- The output includes multiple environment backup files (for example `.env.bak`, `.env.before-local-db`, `.env.pre-...`). These must be treated as sensitive and must never be staged, committed, uploaded, or printed.
- There are deleted tracked files as well as modified tracked files.
- This local tree contains substantial work not yet reconciled with GitHub. Do not assume that GitHub `main` is a complete mirror of this local tree.

Mandatory safety before syncing:
- Do NOT run `git pull`, `git reset`, `git clean`, `git checkout` over the working tree, `git stash` (unless carefully scoped and verified), or bulk `git add .` / `git add -A`.
- Do not merge or overwrite local work until remotes, branch tracking, commit divergence, and a complete safe backup are understood.
- Never commit any `.env*`, credentials, database dumps, private uploads, temporary command-output files, or backup artifacts merely because they are untracked.
- Next diagnostic should be read-only and concise: `git remote -v`, `git branch -vv`, `git log -1 --oneline`, `git diff --stat`, and counts of modified/untracked/deleted files. Avoid printing secrets.
- Before any reconciliation, create and verify a full local backup outside the repository, or another equally reliable recovery point. Then compare local `master` with GitHub `main` and plan a non-destructive integration.
- The user wants PC + ChatGPT + GitHub working together today, while they view the local live site. Do not claim PC synchronization until a real local pull/sync is performed and verified.


## 14. Local Git remote and working-tree inspection — 2026-11-20

User supplied additional read-only diagnostics from the local WSL checkout at `/mnt/d/DoR-Website-Backup/Laravel13-Upgrade`.

Verified output:
- Current branch: `master`.
- Latest local commit: `2e44a75 fix: use Nepal timezone for Laravel`.
- `git branch -vv` showed no upstream/tracking branch for local `master`.
- Local `origin` fetch/push URL is `git@gitlab.com:sangamu/dor-website-8.git` (GitLab), not the GitHub upgrade repository.
- Tracked diff summary: 137 files changed, 10,310 insertions, 5,661 deletions.
- Status summary command reported 135 modified/added, 604 untracked, and 2 deleted.
- The untracked list includes many backup copies, temporary audit/output files, private/environment backup files, uploads, and some stray files with names resembling shell output fragments. Treat all as untrusted/unreviewed; do not bulk-stage.
- This confirms the local checkout's Git origin and branch do not currently match the GitHub upgrade repository/branch recorded above. The relationship between this GitLab checkout and the GitHub upgrade repository is not yet established.

Next safe steps:
1. Do not change the local origin, pull, merge, reset, clean, stash, or push yet.
2. Confirm whether the user intends this WSL folder to become a working copy of `banjais/dor-laravel13-upgrade`, or whether it is a separate original GitLab project whose Laravel 13 work needs selective migration.
3. Inspect GitHub repository history/tree and compare with the local base commit before choosing any integration method.
4. Create and verify a complete local backup outside the repository before any operation that changes the working tree.
5. Establish a safe allowlist of intended source files; explicitly exclude `.env*`, backup artifacts, dumps, uploads/private data, temporary outputs, and credentials.
6. Only after user intent and repository relationship are clear, configure a non-destructive sync path. Never claim local PC sync until verified.


## 15. User confirms canonical local working folder — 2026-11-20

User explicitly confirmed that the existing local WSL folder
`/mnt/d/DoR-Website-Backup/Laravel13-Upgrade` (Windows:
`D:\DoR-Website-Backup\Laravel13-Upgrade`) is intended to become the main local working folder for the GitHub upgrade repository
`banjais/dor-laravel13-upgrade` on branch `main`.

This confirms the desired end state, but does NOT authorize destructive replacement of the current local working tree. The local folder currently has a GitLab `origin`, branch `master`, base commit `2e44a75`, and extensive uncommitted work. Proceed with a preservation-first migration:
- Keep all existing local work intact.
- Do not overwrite or discard any local changes.
- Do not immediately replace the GitLab remote or run pull/reset/clean/checkout/merge.
- First inspect GitHub main's current commit and tree, then compare the local base/history and identify the exact source files that belong in the GitHub working copy.
- Create and verify a complete backup outside the repository before any local working-tree mutation.
- Keep `.env*`, credentials, database dumps, private uploads, backup copies, temporary outputs, and other sensitive/local-only files out of GitHub.
- Plan a non-destructive integration so the local site can continue running. Only report PC sync after the user runs the safe local sync operation and the resulting branch/status/commit are verified.
- Desired final state: this same local folder tracks GitHub `main`; the user can keep the local development server/browser running to inspect the site while development proceeds.


## 16. GitHub main inspection after folder confirmation — 2026-11-20

After the user confirmed the existing local folder should become the canonical GitHub working folder:
- Updated and re-read this context file; confirmation is recorded in section 15.
- GitHub `main` currently points to commit `5d382724506dc4f628a5b7537f163e0a0fa2c005` (the latest context documentation commit at this checkpoint).
- GitHub `main` has a populated Laravel application tree, including app/controllers, migrations, views, tests and assets. The root README is still the generic Laravel README and is not a reliable project-specific handover.
- A GitHub compare request using local base commit `2e44a75` against this repository's `main` returned 404. Therefore that local commit is not currently resolvable as a commit in this GitHub repository; no common history has been established.
- Do not infer that the local and GitHub trees are identical just because file paths overlap. Compare actual files and histories before reconciliation.
- Next user-side step should remain read-only: check available disk space and current folder size so a full external backup can be planned. Do not modify Git remotes or the working tree yet.


## 17. D: and E: inspected files transfer remains outstanding — 2026-11-20

User clarified that transferring the files previously inspected on both D: and E: drives is still unfinished and must be included in the migration. Do not treat the Laravel project folder sync as the only transfer task. Maintain a separate inventory/manifest of inspected D: and E: source paths, destination paths, sizes, file counts, and verification results before moving/copying. Preserve source files until destination copies are verified (counts, sizes, and preferably hashes for critical files). Avoid duplicate/ambiguous overwrites; resolve conflicts before copying.

Current local storage diagnostics:
- Laravel project folder at `/mnt/d/DoR-Website-Backup/Laravel13-Upgrade`: 17 GB excluding `.git`.
- D: mounted at `/mnt/d`: 463 GB total, 427 GB used, 37 GB available (93% used).
- Backup parent `/mnt/d/DoR-Website-Backup` exists.
- Do not create a second full 17 GB backup on D: without checking capacity and planning. First inspect E: available space and retrieve/compile the prior D:/E: inspected-path inventory. Do not start bulk transfers yet.


## 18. D:/E: drive inventory checkpoint — 2026-11-20

Latest WSL read-only diagnostics:
- D: (`/mnt/d`): 463 GB total, 427 GB used, 37 GB available (93%). Root entries include `Dirves`, `DoR-Website-Backup`, `Recovery`, `tmp`, and root-level `ChatbotSettingController.php`. `System Volume Information` permission warning is expected; do not try to access protected system files.
- E: (`/mnt/e`): 469 GB total, 353 GB used, 117 GB available (76%). Root entries include `Dirve`, `DoR-Website-Backup`, and `DoR-Website-Backup-Upgrade-Data`; Windows protected files such as `pagefile.sys`, `DumpStack.log.tmp`, and `System Volume Information` are inaccessible and must be excluded.
- D: project folder `/mnt/d/DoR-Website-Backup/Laravel13-Upgrade` exists and is the current working project (17 GB excluding `.git`). Prior check on 2026-11-15 found `E:\DoR-Website-Backup\Laravel13-Upgrade` did not exist.
- Prior conversation lookup confirms the D:/E: root folder names and the D: project path, but did not recover nested file inventories, sizes, timestamps, exact intended transfer destinations, or exclusions. Do not claim those nested contents have already been inspected or decide transfer scope by guessing.
- `D:\Dirves` and `E:\Dirve` are differently named folders; do not assume they are duplicates or rename/merge them without inspecting contents.
- `E:\DoR-Website-Backup-Upgrade-Data` is a separate E: root folder and must be inventoried as a potential source of upgrade data before transfer.
- Next: read-only inventory of the named user-data folders on D: and E: (names, sizes, file counts, timestamps); exclude Windows protected/system entries. Compare manifests and available destinations before any copy. Preserve all source files until destination verification.


## 19. Recovered 2026-11-20 D:/E: inspection details (approximately 3 hours; do not repeat or lose)

The user spent approximately three hours inspecting D: and E: data on 2026-11-20. The following findings were recovered from that session and are authoritative context.

### GitHub staging / source boundaries
- Staging folder: `D:\DoR-Website-Backup\dor-laravel13-upgrade-github-stage`; at an earlier inspection it was about 20 MB, 1,814 files / 476 directories, with 13 MB under `public`.
- It was pushed to private GitHub repo `banjais/dor-laravel13-upgrade`, branch `main`; later push report recorded commit `daa289f`, 56.87 MiB / 2,969 files, then a second push reported “Everything up-to-date.” Treat these as distinct recorded checkpoints; do not conflate their counts.
- GitHub repo contains code/history only. Private uploads and database dumps were excluded from the GitHub push. Original GitLab repo and live VM were not changed.
- No uploads, SQL backups, or private user data should be pushed to GitHub.

### Active local Laravel project and upload storage
- Active local project: `D:\DoR-Website-Backup\Laravel13-Upgrade` (WSL: `/mnt/d/DoR-Website-Backup/Laravel13-Upgrade`).
- Active project `public/uploads` is a real D: directory, approximately 16 GB, with 14,926 files.
- Its major subfolders/sizes at inspection: notice 6.3 GB; publication 4.0 GB; circular 1.8 GB; pages 1.5 GB; ckfiles 556 MB; banner 519 MB; gallery 339 MB; download 337 MB.
- Also present locally: `uploads-x` approximately 3.3 MB and `uploads_tmp` approximately 868 KB.
- `uploads002` is a symlink to `E:\DoR-Website-Backup-Upgrade-Data\public\uploads002` (about 7.8 GB).
- `uploads_20220429b4_existing_working` is a symlink to `E:\DoR-Website-Backup-Upgrade-Data\public\uploads_20220429b4_existing_working` (about 7.4 GB).
- Both E: symlink targets exist. Do not move, rename, replace, or break either symlink until application references and active use have been verified.

### E: backup uploads comparison
- Comparison source: `E:\DoR-Website-Backup-Upgrade-Data\public\uploads`.
- E: copy contained 15,165 files, versus 14,926 in active D: uploads.
- E: major folders observed: notice 6.3 GB; publication 4.9 GB; circular 1.8 GB; pages 1.5 GB; ckfiles 556 MB; banner 560 MB; tender 101 MB; press 1.9 MB; staffs about 295 MB.
- Relative-path comparison: E-only 264 files; D-only 25 files. E-only breakdown: publication 123, tender 104, notice 29, press 3, testimonial 2, pages 1, ckfiles 1, banner 1. D-only breakdown: office_chiefs 10, pages 5, download 3, banner 4, staffs 1, circular 1, album 1.
- A sample of E-only material included banner assets, ckfiles/images/bumn.jpg, notice JPG/PDFs, pages/Documents_1734781862.jpg, press PDFs/JPGs, and publication PDFs.
- These are not safe to bulk-merge blindly: the folder versions differ, and both E-only and D-only files exist. Preserve both sides and compare conflicts before copying. No transfer/deletion was done in this inspection session.

### Database backups / PWA assets
- Database backup files inspected by path: `D:\DoR-Website-Backup\Database\dor.sql` (about 61,300 KB) and `dor_web.sql` (about 18,200 KB). These are private database backups; never commit to GitHub. Do not restore/import/overwrite without explicit approval and database-target verification.
- Original PWA assets were located under active project `public\assets\pwa`, including QR, emblem, icons, and logo-review materials. Preserve these source assets.

### Publication upload implementation
- Inspected `app\Http\Controllers\Admin\PublicationController.php`.
- Publication uploads are stored at relative path `uploads/publication/`; accepted extensions jpg/jpeg/pdf/ppt/pptx; max file size 130 MB; generated names use `publication_<time>.<ext>`.
- Project-root `uploads/` does not exist; actual active folder is `public\uploads\publication`.
- At that checkpoint, local server was `/usr/bin/php8.5 -S 0.0.0.0:8000 .../server.php`, working directory `.../Laravel13-Upgrade/public`. User reported no code, file, or database changes from the inspection.

### Transfer decisions / safeguards
- User accepted an inspect-first plan to consolidate only necessary uploads/database backups from E: to D: while retaining old backups; later explicitly confirmed no files had yet been moved or deleted and E: originals remained intact.
- Preserve current D: active files and E: originals. No bulk copy, merge, move, delete, overwrite, symlink change, database restore, or production action without a verified manifest and explicit approval.
- The later drive-space check: D: 463 GB total / 37 GB free (93% used); E: 469 GB total / 117 GB free (76% used). E: has more available space, but this alone does not decide transfer destinations.
- The latest 2026-11-20 root inventory also showed D: `Dirves`, `DoR-Website-Backup`, `Recovery`, `tmp`, root-level `ChatbotSettingController.php`; E: `Dirve`, `DoR-Website-Backup`, `DoR-Website-Backup-Upgrade-Data`. Do not assume `Dirves` and `Dirve` are the same folder.
- Earlier read-only inspection covered the active upload folders and E: backup upload comparison above. Do not ask the user to repeat those three hours of work. Only request new read-only output for genuinely uninspected remaining directories or to verify a planned transfer.
