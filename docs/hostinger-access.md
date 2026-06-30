# Hostinger Access Notes

## Details Needed

To connect this local repo to the live WordPress site, add these values to `.env`:

- `HOSTINGER_SSH_HOST`
- `HOSTINGER_SSH_USER`
- `HOSTINGER_SSH_PORT`
- `REMOTE_WP_PATH`
- `SSH_KEY_PATH` if using a specific private key

Hostinger commonly uses port `65002`, but hPanel is the source of truth.

## Where To Find Them

In Hostinger hPanel:

1. Open the website for `overworld.com.sg`.
2. Go to `Manage`.
3. Open `SSH Access`.
4. Copy the SSH IP or host, username, and port.
5. Confirm the site path. It is often:

   ```text
   /home/USERNAME/domains/overworld.com.sg/public_html
   ```

## Recommended Access Method

Use SSH key access where possible. Do not save Hostinger passwords inside this repo. If password login is the only option, use the SSH password prompt in the terminal.

## First Connection Checks

After `.env` is filled:

```bash
./scripts/ssh-hostinger.sh 'pwd'
./scripts/ssh-hostinger.sh 'ls -la'
./scripts/ssh-hostinger.sh 'ls -la ~/domains/overworld.com.sg/public_html'
./scripts/remote-wp-cli.sh core version
```

If `wp` is not installed on the server, SSH and rsync can still be used, but database exports and plugin/theme inspection will need another route through hPanel or phpMyAdmin.
