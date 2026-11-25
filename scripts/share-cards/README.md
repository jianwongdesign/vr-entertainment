# Share cards (Open Graph images), 1200x630

`build.py <dir>` writes main/vr/fil/xr HTML into `<dir>/gen/`. It expects the
photos as `<dir>/vr.png`, `fil.webp` and `xr.png` (the rental items' cover
photos, downloaded from the site).

Render each one with headless Chrome:

    "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --headless=new \
      --hide-scrollbars --force-device-scale-factor=1 --window-size=1200,630 \
      --virtual-time-budget=8000 --allow-file-access-from-files \
      --screenshot=out.png "file://<dir>/gen/vr.html"

Convert to JPG (`sips -s format jpeg -s formatOptions 86`), upload, and set it
as the page's **Share Image** (`ow_seo_image`). Live since 2026-11-25:
/event-rental/ #1981, VR Free Roam #1982, Floor Is Lava #1983, XR Party Game
#1984. For a new rental item, add a row to `items` in build.py.
