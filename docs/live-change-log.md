# Live Change Log

## 2026-06-30 13:13 +08 - Header Book Now Links

Published a live WordPress database update for the header and related Bookeo links.

Requested Book Now targets:

```text
Kallang: https://bookeo.com/overworldkallangwavemall
Orchard: https://bookeo.com/overworldorchardcentral
Funan: https://bookeo.com/overworldfunan
```

What changed:

- Header/Footer Elementor template `29` (`Header`) was restored from targeted backup and safely repatched with `wp_slash()`.
- Replaced remaining exact old Kallang casing `overworldKallangwavemall` with `overworldkallangwavemall` across WordPress tables.
- Flushed WordPress object cache, Elementor CSS cache, and LiteSpeed cache.

Backup:

```text
/home/u146877548/overworld-backups/header-29-elementor-data-before-20260630-050854.json
```

Verification:

```text
exact_upper_postmeta: 0
exact_upper_posts: 0
public homepage Bookeo URLs:
https://bookeo.com/overworldfunan
https://bookeo.com/overworldfunan/buyvoucher
https://bookeo.com/overworldkallangwavemall
https://bookeo.com/overworldkallangwavemall/buyvoucher
https://bookeo.com/overworldorchardcentral
https://bookeo.com/overworldorchardcentral/buyvoucher
```
