# Client Site Workflow Playbook — SSH + Git

Standard operating procedure for managing a client WordPress site from a
local Git repository over SSH. Written for Hostinger, but any host with SSH
access works the same way. Replace `CLIENT` / `client-domain.com` throughout.

Proven on: overworld.com.sg (Hostinger, WordPress + Elementor + ACF).

---

## 1. Principles

1. **Git is the source of truth for code.** Every theme/plugin file we touch
   lives in the repo. The live server is a deploy target, never the only copy.
2. **The database is the source of truth for content.** Page content,
   Elementor layouts, and ACF values live in the DB — we patch them with
   wp-cli, never by editing the repo.
3. **Backup before every change.** Files and DB rows get a timestamped
   server-side backup *before* they are modified. No exceptions.
4. **Deploy the smallest unit possible.** Prefer a single-file rsync over a
   full sync. Full syncs are for onboarding only.
5. **Verify on the live site, in a browser,** after every deploy. `200 OK`
   is not verification; rendered output is.
6. **Log every live change** in `docs/live-change-log.md` — what, why, the
   backup path, and how it was verified.
7. **Never commit secrets.** `.env` holds credentials and is gitignored;
   `.env.example` documents the shape.

---

## 2. Repository Layout

```text
client-repo/
├── .env                  # SSH credentials (gitignored — never commit)
├── .env.example          # documented template for .env
├── .rsyncignore          # runtime/user data excluded from sync (uploads, caches, backups)
├── docs/
│   ├── client-access.md      # where credentials live, how to get them (no secrets!)
│   ├── live-site-snapshot.md # host, WP version, themes, plugins — updated when they change
│   ├── live-change-log.md    # newest-first log of every live change
│   └── client-workflow-playbook.md
├── scripts/
│   ├── lib/env.sh            # loads .env, builds SSH_OPTS / SSH_TARGET / RSYNC_SSH
│   ├── ssh-hostinger.sh      # run any shell command on the server
│   ├── remote-wp-cli.sh      # run any wp-cli command in the WP root
│   ├── pull-wp-content.sh    # server -> local sync of wp-content
│   ├── push-wp-content.sh    # local -> server sync (dry-run by default, guarded)
│   └── backup-remote.sh      # full server-side DB + wp-content backup
└── wordpress/
    └── wp-content/           # mirror of the live wp-content (minus .rsyncignore)
```

`.rsyncignore` keeps user data and runtime files out of Git and out of
deploys:

```text
uploads/          cache/            litespeed/        upgrade/
backup*/          backups/          ai1wm-backups/    updraft/
debug.log         *.log             *.sql             *.wpress
advanced-cache.php  db.php  object-cache.php  .litespeed_conf.dat
```

---

## 3. SSH Setup

### 3.1 Collect access details (Hostinger)

hPanel → **Websites → Manage → Advanced → SSH Access**:

- SSH host/IP, username, port (Hostinger default: `65002`)
- Site path, usually `/home/USERNAME/domains/client-domain.com/public_html`

### 3.2 Configure `.env`

```bash
cp .env.example .env
```

```dotenv
HOSTINGER_SSH_HOST=xxx.xxx.xxx.xxx
HOSTINGER_SSH_USER=uXXXXXXXXX
HOSTINGER_SSH_PORT=65002
SSH_KEY_PATH=            # optional; blank = default key or password prompt
REMOTE_WP_PATH=/home/uXXXXXXXXX/domains/client-domain.com/public_html
LOCAL_WP_CONTENT=wordpress/wp-content
```

Prefer **SSH key auth**: add your public key in hPanel (SSH Access → Manage
SSH keys) or `ssh-copy-id`. Passwords are typed at the prompt if unavoidable
— never stored in the repo or in shell history.

### 3.3 First-connection checks

```bash
./scripts/ssh-hostinger.sh 'pwd && ls -la'
./scripts/remote-wp-cli.sh core version
./scripts/remote-wp-cli.sh theme list
./scripts/remote-wp-cli.sh plugin list
```

Record the results in `docs/live-site-snapshot.md`.

---

## 4. Script Reference

| Script | Purpose | Notes |
|---|---|---|
| `ssh-hostinger.sh '<cmd>'` | Arbitrary shell command on the server | Args are shell-quoted safely |
| `remote-wp-cli.sh <args>` | wp-cli inside the WP root | e.g. `post list`, `eval '<php>'`, `cache flush` |
| `pull-wp-content.sh` | Sync server → local `wp-content` | Respects `.rsyncignore` |
| `push-wp-content.sh` | Sync local → server | **Dry-run by default.** Real run: `CONFIRM_PUSH=client-domain.com ./scripts/push-wp-content.sh --apply` |
| `backup-remote.sh` | Full server-side backup | DB export + `wp-content` tarball into `~/CLIENT-backups/` |

Safety guards built into `push-wp-content.sh`:

- No `--apply` → dry run that prints what *would* change.
- `--apply` refuses to run unless `CONFIRM_PUSH=<exact-domain>` is set.
- `--delete` only with `ALLOW_REMOTE_DELETE=1` (almost never needed).

---

## 5. Onboarding a New Client (once)

```bash
# 1. Create the repo
mkdir client-site && cd client-site && git init
# copy scripts/, .env.example, .rsyncignore, docs/ skeleton from a previous project

# 2. Fill .env (section 3.2), verify connection (section 3.3)

# 3. Take a full baseline backup on the server
./scripts/backup-remote.sh

# 4. Pull the code down
./scripts/pull-wp-content.sh

# 5. Baseline commit
git add -A && git commit -m "Baseline snapshot of live wp-content"
git remote add origin git@github.com:AGENCY/client-site.git
git push -u origin main

# 6. Document the site
#    docs/live-site-snapshot.md  — host, versions, themes, plugins, quirks
#    docs/client-access.md      — where credentials are found (never the secrets)
```

---

## 6. Standard Change Workflow (every task)

### 6.1 Code changes (theme/plugin files)

```bash
# 1. EDIT locally in wordpress/wp-content/...

# 2. LINT
php -l wordpress/wp-content/themes/CHILD-THEME/changed-file.php

# 3. BACKUP the live file (server-side, timestamped)
./scripts/ssh-hostinger.sh 'cp $REMOTE_FILE ~/CLIENT-backups/FILE-before-CHANGE-$(date +%Y%m%d-%H%M%S).php'

# 4. DEPLOY just that file
set -a && source .env && set +a
rsync -az -e "ssh -p ${HOSTINGER_SSH_PORT}${SSH_KEY_PATH:+ -i $SSH_KEY_PATH}" \
  wordpress/wp-content/themes/CHILD-THEME/changed-file.php \
  "${HOSTINGER_SSH_USER}@${HOSTINGER_SSH_HOST}:${REMOTE_WP_PATH}/wp-content/themes/CHILD-THEME/"

# 5. FLUSH caches (adjust to the site's stack)
./scripts/remote-wp-cli.sh cache flush
./scripts/remote-wp-cli.sh eval 'if (class_exists("\Elementor\Plugin")) \Elementor\Plugin::$instance->files_manager->clear_cache();'
./scripts/remote-wp-cli.sh litespeed-purge all

# 6. VERIFY in a browser (hard-reload; check the actual rendered change)

# 7. LOG in docs/live-change-log.md (what/why/backup path/verification)

# 8. COMMIT + PUSH
git add <files> docs/live-change-log.md
git commit -m "Short imperative summary"   # body: what leaked/broke, why this fix
git push
```

If CSS changes are involved, **bump the child theme `Version:`** header so
`style.css?ver=` cache-busts for visitors.

### 6.2 Database / Elementor / ACF changes

Content lives in the DB, so the unit of work is a wp-cli patch, not a file:

```bash
# 1. DUMP the target (e.g. an Elementor page) to a file for local inspection
./scripts/remote-wp-cli.sh eval \
  'file_put_contents("/tmp/page-ID.json", get_post_meta(ID, "_elementor_data", true));'

# 2. PATCH locally with a real JSON parser (python), never blind sed:
#    - parse, assert the anchor strings exist exactly once, modify, re-serialize
#    - json.loads() the output as a sanity check before uploading

# 3. APPLY with a guarded eval: backup first, validate JSON, wp_slash on write
./scripts/remote-wp-cli.sh eval '
  $old = get_post_meta(ID, "_elementor_data", true);
  file_put_contents(getenv("HOME")."/CLIENT-backups/page-ID-before-CHANGE-".date("Ymd-His").".json", $old);
  $new = file_get_contents("/tmp/page-ID-patched.json");
  json_decode($new);
  if (json_last_error() === JSON_ERROR_NONE && strlen($new) > EXPECTED_MIN) {
    update_post_meta(ID, "_elementor_data", wp_slash($new)); echo "OK\n";
  } else { echo "ABORT\n"; }
'

# 4. Flush caches, verify in browser, log, commit the changelog entry.
```

Rules of thumb:

- `str_replace` on Elementor JSON only with an **occurrence count check**
  (`abort unless exactly 1 match`), and always `json_decode`-validate before
  writing.
- ACF fields registered in **code** (mu-plugin per feature) beat fields
  created in the UI — they're versioned, reviewable, and can't drift.
- Set both the value meta (`field_name`) and ACF's key reference
  (`_field_name = field_key`) when writing ACF values programmatically.

### 6.3 Rollback

Every change has a timestamped backup on the server:

```bash
./scripts/ssh-hostinger.sh 'ls -t ~/CLIENT-backups/ | head'
# Files:  copy the backup back over the live file, re-flush caches.
# DB rows: read the backup JSON and update_post_meta(wp_slash(...)) it back.
```

---

## 7. Git Conventions

- **Branch:** small sites work directly on `main`; use feature branches when
  a change spans multiple sessions or needs review.
- **One logical change per commit** — the code + its changelog entry
  together, so every commit tells the whole story.
- **Message format:** imperative subject ≤ 72 chars; body explains root
  cause and approach when non-obvious:

  ```text
  Fix pink/navy hover leaks from theme reset; make header sticky

  hello-elementor's reset.css paints links pink (#c36) ... Added a
  neutralizer block to the child stylesheet and made the child CSS
  depend on the parent's reset handle so it loads last.
  ```

- **DB-only changes still get a commit** — the changelog entry is the
  reviewable artifact for work that has no file diff.
- **Push after every completed task** unless the client asks otherwise.
- Never commit: `.env`, uploads, caches, backups, logs (enforced by
  `.gitignore` / `.rsyncignore`).

---

## 8. Change Log Format (`docs/live-change-log.md`)

Newest entry first. Each entry:

```markdown
## YYYY-MM-DD - Short Title

What changed and why (root cause if it was a bug).

- Bullet the concrete changes (files, post IDs, meta keys).

Backup:            ~/CLIENT-backups/<file>
Deploy:            single-file rsync of X; caches flushed (WP, Elementor, LiteSpeed)
Verification:      URLs checked + what was seen in the browser
```

---

## 9. Pre-Flight Checklist (before calling anything done)

- [ ] Server-side backup taken *before* the change
- [ ] PHP linted / JSON validated locally before deploy
- [ ] Deployed the smallest possible unit
- [ ] All caches flushed (object, page builder, LiteSpeed/page cache)
- [ ] Verified rendered output in a browser (hard reload; mobile if layout)
- [ ] `docs/live-change-log.md` updated
- [ ] Committed and pushed with a meaningful message
- [ ] Client-visible URLs return 200 and look right

---

## 10. Security Notes

- SSH keys over passwords; passwords only at the interactive prompt.
- `.env` is per-machine and gitignored. Rotate credentials on handover.
- Repo docs may state *where* credentials live (hPanel, password manager) —
  never the credentials themselves.
- Server-side backups live outside the web root (`~/CLIENT-backups/`, not
  `public_html`), so they are never publicly downloadable.
- Prefer read-only checks (`--dry-run`, `wp post list`) before any write.
