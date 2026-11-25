import sys, os, html
S = sys.argv[1]
FONTS = '<link href="https://fonts.googleapis.com/css2?family=Anton&family=Cinzel:wght@500;600&family=JetBrains+Mono:wght@500;700&family=Space+Grotesk:wght@500;600&display=block" rel="stylesheet">'
BASE = """
*{box-sizing:border-box;margin:0;padding:0}
html,body{width:1200px;height:630px;overflow:hidden;background:#0a0a14}
.c{position:relative;width:1200px;height:630px;background:#0a0a14;color:#fff;font-family:'Space Grotesk',sans-serif;overflow:hidden}
.grid{position:absolute;inset:0;background-image:linear-gradient(rgba(195,251,51,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(195,251,51,.045) 1px,transparent 1px);background-size:48px 48px;mask-image:linear-gradient(90deg,#000 0%,#000 45%,transparent 70%)}
.glow{position:absolute;left:-180px;bottom:-260px;width:720px;height:620px;border-radius:50%;background:radial-gradient(closest-side,rgba(195,251,51,.20),transparent);filter:blur(10px)}
.copy{position:absolute;left:64px;top:56px;bottom:56px;width:640px;display:flex;flex-direction:column;z-index:3}
.brand{display:flex;align-items:center;gap:18px}
.logo{font-family:'Cinzel',serif;font-weight:600;font-size:26px;letter-spacing:.2em;text-transform:uppercase}
.sep{width:1px;height:22px;background:rgba(255,255,255,.25)}
.eyebrow{font-family:'JetBrains Mono',monospace;font-size:14px;letter-spacing:.22em;text-transform:uppercase;color:#c3fb33;display:flex;align-items:center;gap:10px}
.eyebrow::before{content:"";width:8px;height:8px;border-radius:50%;background:#c3fb33;box-shadow:0 0 12px #dcff6e}
.mid{margin-top:auto;margin-bottom:auto}
.tag{display:inline-flex;align-items:center;gap:10px;font-family:'JetBrains Mono',monospace;font-weight:700;font-size:17px;letter-spacing:.16em;text-transform:uppercase;padding:11px 20px;border-radius:999px;background:linear-gradient(135deg,#dcff6e,#c3fb33);color:#0a0a14;box-shadow:0 14px 34px -14px #c3fb33,0 0 26px -8px #c3fb33;margin-bottom:22px}
.tag::before{content:"";width:9px;height:9px;border-radius:50%;background:#0a0a14;opacity:.55}
h1{font-family:'Anton',sans-serif;font-weight:400;text-transform:uppercase;line-height:.92;letter-spacing:-.005em}
h1 .a{display:block;color:#fff}
h1 .b{display:block;color:#c3fb33;text-shadow:0 0 40px rgba(195,251,51,.35)}
.chips{display:flex;gap:10px;margin-top:26px;flex-wrap:wrap}
.chip{display:inline-flex;align-items:center;gap:9px;padding:10px 16px;border-radius:999px;background:rgba(195,251,51,.08);border:1px solid rgba(195,251,51,.32);font-family:'JetBrains Mono',monospace;font-weight:700;font-size:15px;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap}
.chip svg{width:19px;height:19px;color:#c3fb33}
.foot{display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1px solid rgba(255,255,255,.12);font-family:'JetBrains Mono',monospace;font-size:14px;letter-spacing:.14em;text-transform:uppercase;color:rgba(220,225,240,.78)}
.foot b{color:#fff;font-weight:700;letter-spacing:.1em}
"""
ICON = {
 'players':'<circle cx="12" cy="8" r="3"/><circle cx="5" cy="10" r="2.2"/><circle cx="19" cy="10" r="2.2"/><path d="M7 20v-1a5 5 0 0 1 10 0v1M1.5 19v-.5a3.5 3.5 0 0 1 4-3.5M22.5 19v-.5a3.5 3.5 0 0 0-4-3.5"/>',
 'age':'<circle cx="12" cy="6" r="2.6"/><path d="M8 21v-6l-2-4h12l-2 4v6M10 21v-4h4v4"/>',
 'games':'<path d="M7 8h10a4 4 0 0 1 4 4l-.8 4.5a2 2 0 0 1-3.6.7L15 15H9l-1.6 2.2a2 2 0 0 1-3.6-.7L3 12a4 4 0 0 1 4-4z"/><path d="M8 11v3M6.5 12.5h3"/>',
 'truck':'<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
 'crew':'<circle cx="12" cy="8" r="3"/><path d="M6 20v-1a6 6 0 0 1 12 0v1"/>',
 'cog':'<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
}
def chip(icon, text):
    return f'<span class="chip"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">{ICON[icon]}</svg>{html.escape(text)}</span>'
def page(css, body):
    return f'<!doctype html><html><head><meta charset="utf-8">{FONTS}<style>{BASE}{css}</style></head><body><div class="c">{body}</div></body></html>'
FOOT = '<div class="foot"><span>Delivered · Set Up · Run By Our Crew</span><b>overworld.com.sg</b></div>'
BRAND = '<div class="brand"><span class="logo">Overworld</span><span class="sep"></span><span class="eyebrow">Event Rental · Singapore</span></div>'

ITEM_CSS = """
.photo{position:absolute;right:0;top:0;width:700px;height:630px;z-index:1}
.photo img{width:100%;height:100%;object-fit:cover;object-position:var(--pos,center)}
.photo::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,#0a0a14 0%,rgba(10,10,20,.88) 16%,rgba(10,10,20,.35) 46%,rgba(10,10,20,.08) 75%,rgba(10,10,20,.25) 100%),linear-gradient(0deg,rgba(10,10,20,.55) 0%,transparent 30%)}
.edge{position:absolute;right:0;top:0;bottom:0;width:6px;background:linear-gradient(180deg,#dcff6e,#c3fb33 60%,transparent);z-index:2}
h1{font-size:118px}
h1 .b{font-size:64px;margin-top:10px;letter-spacing:.005em}
"""
items = [
 ('vr', 'vr.png', 'center', 'Multiplayer VR', 'VR Free Roam', [('players','2–6 Players'),('age','Ages 7+'),('games','20+ Games')]),
 ('fil', 'fil.webp', 'center', 'Crowd-Puller', 'Floor Is Lava', [('players','2–8 Players'),('age','Ages 5+'),('games','30+ Modes')]),
 ('xr', 'xr.png', 'center', 'No Headset Needed', 'XR Party Game', [('players','2–6 Players'),('age','Ages 5+'),('games','6 Game Modes')]),
]
for key, img, pos, tag, name, chips in items:
    body = (f'<div class="photo" style="--pos:{pos}"><img src="../{img}"></div><div class="edge"></div><div class="grid"></div><div class="glow"></div>'
            f'<div class="copy">{BRAND}<div class="mid"><span class="tag">{html.escape(tag)}</span>'
            f'<h1 style="font-size:{108 if len(name)>12 else 118}px"><span class="a">{html.escape(name)}</span><span class="b">Rental For Events</span></h1>'
            f'<div class="chips">{"".join(chip(i,t) for i,t in chips)}</div></div>{FOOT}</div>')
    open(f'{S}/gen/{key}.html','w').write(page(ITEM_CSS, body))

MAIN_CSS = """
.tiles{position:absolute;right:40px;top:0;bottom:0;width:470px;z-index:1}
.t{position:absolute;width:300px;height:200px;border-radius:20px;overflow:hidden;border:1px solid rgba(195,251,51,.45);box-shadow:0 30px 60px -24px rgba(0,0,0,.9),0 0 40px -18px #c3fb33;background:#13131f}
.t img{width:100%;height:100%;object-fit:cover}
.t span{position:absolute;left:12px;bottom:12px;font-family:'JetBrains Mono',monospace;font-weight:700;font-size:13px;letter-spacing:.14em;text-transform:uppercase;padding:7px 12px;border-radius:999px;background:rgba(10,10,20,.85);border:1px solid rgba(195,251,51,.45);color:#c3fb33}
.t1 span{bottom:auto;top:12px}
.t1{right:120px;top:40px;transform:rotate(-4deg)}
.t2{right:-14px;top:208px;transform:rotate(3deg);z-index:2}
.t3{right:110px;top:384px;transform:rotate(-2deg)}
.fade{position:absolute;inset:0;background:radial-gradient(ellipse at 78% 50%,rgba(195,251,51,.12),transparent 60%)}
.copy{width:610px}
h1{font-size:86px}
h1 .b{font-size:52px;margin-top:12px}
.lead{margin-top:20px;font-size:20px;line-height:1.4;color:rgba(220,225,240,.85);max-width:560px}
.chips .chip{font-size:14px;padding:9px 14px}
"""
body = ('<div class="fade"></div><div class="grid"></div><div class="glow"></div>'
        '<div class="tiles"><div class="t t1"><img src="../vr.png"><span>VR Free Roam</span></div>'
        '<div class="t t2"><img src="../fil.webp"><span>Floor Is Lava</span></div>'
        '<div class="t t3"><img src="../xr.png"><span>XR Party Game</span></div></div>'
        f'<div class="copy">{BRAND}<div class="mid">'
        '<h1><span class="a">Interactive Game Rental</span><span class="b">For Events In Singapore</span></h1>'
        '<p class="lead">VR, Floor Is Lava and XR party games for corporate events, family days, schools and roadshows.</p>'
        f'<div class="chips">{chip("truck","Island-wide")}{chip("cog","Setup & Pack-down")}{chip("crew","On-site Crew")}</div></div>'
        '<div class="foot"><span>Corporate · Family Days · Schools</span><b>overworld.com.sg</b></div></div>')
open(f'{S}/gen/main.html','w').write(page(MAIN_CSS, body))
print('ok')
