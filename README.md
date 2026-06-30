# Overworld WordPress Operations

Local operations repo for `https://overworld.com.sg`, hosted on Hostinger and running WordPress.

This repo is for SSH access, controlled file sync, Git history, and deployment notes. It intentionally keeps WordPress secrets, uploads, cache files, and backups out of Git.

## Setup

1. Copy `.env.example` to `.env`.
2. Fill in the Hostinger SSH details and `REMOTE_WP_PATH`.
3. Test SSH:

   ```bash
   ./scripts/ssh-hostinger.sh 'pwd && ls -la'
   ```

4. Pull the editable WordPress content:

   ```bash
   ./scripts/pull-wp-content.sh
   ```

5. Make edits under `wordpress/wp-content/`, then commit them in Git.

6. Check a dry-run deploy:

   ```bash
   ./scripts/push-wp-content.sh
   ```

7. Deploy only after reviewing the dry-run:

   ```bash
   CONFIRM_PUSH=overworld.com.sg ./scripts/push-wp-content.sh --apply
   ```

## Safety Rules

- Run `./scripts/backup-remote.sh` before any meaningful live-site change.
- Keep `.env`, `wp-config.php`, database exports, and backups out of Git.
- Do not track `wp-content/uploads/`; it is large user media, not source code.
- Use the dry-run deploy first. The deploy script does not delete remote files unless `ALLOW_REMOTE_DELETE=1` is explicitly set.

## Useful Commands

```bash
./scripts/remote-wp-cli.sh core version
./scripts/remote-wp-cli.sh theme list
./scripts/remote-wp-cli.sh plugin list
```
