{{-- WayWay Loading Screen Component --}}
<div 
    id="ww-loading"
    style="display:none;"
    class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-white"
>
    {{-- Ombak background bawah --}}
    <div class="absolute bottom-0 left-0 right-0 h-24 overflow-hidden pointer-events-none">
        <svg style="width:200%; animation: ww-wave 4s linear infinite;" 
             viewBox="0 0 800 70" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 40 Q100 10 200 40 Q300 70 400 40 Q500 10 600 40 Q700 70 800 40 
                     Q900 10 1000 40 Q1100 70 1200 40 Q1300 10 1400 40 Q1500 70 1600 40 
                     L1600 70 L0 70 Z" fill="rgba(91,200,212,0.15)"/>
            <path d="M0 50 Q120 25 240 50 Q360 75 480 50 Q600 25 720 50 Q840 75 960 50 
                     Q1080 25 1200 50 Q1320 75 1440 50 Q1560 25 1680 50 L1680 70 L0 70 Z" 
                  fill="rgba(43,127,168,0.10)"/>
        </svg>
    </div>

    {{-- Kompas + Waybot --}}
    <div class="relative w-40 h-40 flex items-center justify-center">
        {{-- Ring luar berputar --}}
        <div class="absolute w-40 h-40 rounded-full" 
             style="border:2.5px solid transparent; border-top-color:#4BA8C8; border-right-color:#E8C98A; animation:ww-spin 2s linear infinite;"></div>
        {{-- Ring dalam dashed --}}
        <div class="absolute w-28 h-28 rounded-full" 
             style="border:1.5px dashed rgba(91,200,212,0.4); animation:ww-spin-r 3.5s linear infinite;"></div>

        {{-- Titik kompas --}}
        <div class="absolute top-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full" style="background:#E8C98A;"></div>
        <div class="absolute bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full" style="background:#5BC8D4;"></div>
        <div class="absolute left-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#2B7FA8;"></div>
        <div class="absolute right-1 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full" style="background:#2B7FA8;"></div>

        {{-- Waybot --}}
        <div style="animation:ww-pulse 2.5s ease-in-out infinite; position:relative; z-index:2;">
            {{-- Antena --}}
            <div style="position:absolute; top:-14px; left:50%; transform:translateX(-50%); width:3px; height:10px; background:#1B3A5C; border-radius:2px;">
                <div style="position:absolute; top:-6px; left:50%; transform:translateX(-50%); width:8px; height:8px; border-radius:50%; background:#4BA8C8; border:1.5px solid #2B7FA8;"></div>
            </div>
            {{-- Body --}}
            <div style="width:68px; height:58px; background:white; border:2.5px solid #1B3A5C; border-radius:16px; display:flex; align-items:center; justify-content:center; position:relative; box-shadow:0 2px 12px rgba(43,127,168,0.15);">
                {{-- Telinga kiri --}}
                <div style="position:absolute; left:-8px; top:50%; transform:translateY(-50%); width:9px; height:16px; background:#DCE8EF; border:2px solid #1B3A5C; border-radius:3px;"></div>
                {{-- Telinga kanan --}}
                <div style="position:absolute; right:-8px; top:50%; transform:translateY(-50%); width:9px; height:16px; background:#DCE8EF; border:2px solid #1B3A5C; border-radius:3px;"></div>
                {{-- Visor --}}
                <div style="width:48px; height:26px; background:#1B3A5C; border-radius:8px; display:flex; align-items:center; justify-content:center; gap:9px;">
                    <div style="width:13px; height:11px; border:2.5px solid #5BC8D4; border-bottom:none; border-radius:7px 7px 0 0; animation:ww-blink 4s ease-in-out infinite; transform-origin:center bottom;"></div>
                    <div style="width:13px; height:11px; border:2.5px solid #5BC8D4; border-bottom:none; border-left:none; border-radius:0 7px 0 0; animation:ww-blink 4s ease-in-out infinite 0.15s; transform-origin:center bottom;"></div>
                </div>
                {{-- Ekor bubble chat --}}
                <div style="position:absolute; bottom:-9px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:7px solid transparent; border-right:7px solid transparent; border-top:9px solid #1B3A5C;"></div>
                <div style="position:absolute; bottom:-6px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:5px solid transparent; border-right:5px solid transparent; border-top:7px solid white;"></div>
            </div>
            {{-- Bintang sparkel --}}
            <span style="position:absolute; top:6px; right:-18px; color:#E8C98A; font-size:10px; animation:ww-float 2s ease-in-out infinite;">✦</span>
            <span style="position:absolute; top:24px; left:-20px; color:#4BA8C8; font-size:7px; animation:ww-float 2.5s ease-in-out infinite 0.4s;">✦</span>
        </div>
    </div>

    {{-- Brand name --}}
    <div class="mt-6 flex items-center gap-2">
        <div class="w-2 h-2 rounded-full" style="background:#5BC8D4;"></div>
        <span class="font-semibold text-base" style="color:#1B3A5C;">WayWay</span>
    </div>

    {{-- Dots --}}
    <div class="flex gap-2 mt-3">
        <div style="width:8px;height:8px;border-radius:50%;background:#4BA8C8;animation:ww-dot 1.4s ease-in-out infinite;"></div>
        <div style="width:8px;height:8px;border-radius:50%;background:#5BC8D4;animation:ww-dot 1.4s ease-in-out infinite 0.2s;"></div>
        <div style="width:8px;height:8px;border-radius:50%;background:#E8C98A;animation:ww-dot 1.4s ease-in-out infinite 0.4s;"></div>
    </div>

    {{-- Pesan rotating (ganti teks sesuai konteks via JS) --}}
    <div id="ww-loading-msg" class="mt-3 text-sm font-medium" style="color:#2B7FA8; min-height:20px;">
        Loading...
    </div>
</div>

{{-- CSS Keyframes --}}
<style>
    @keyframes ww-spin    { to { transform: rotate(360deg); } }
    @keyframes ww-spin-r  { to { transform: rotate(-360deg); } }
    @keyframes ww-pulse   { 0%,100%{transform:scale(1);}50%{transform:scale(1.07);} }
    @keyframes ww-blink   { 0%,80%,100%{transform:scaleY(1);}87%{transform:scaleY(0.08);} }
    @keyframes ww-dot     { 0%,100%{opacity:.25;}50%{opacity:1;} }
    @keyframes ww-float   { 0%,100%{transform:translateY(0);}50%{transform:translateY(-6px);} }
    @keyframes ww-wave    { from{transform:translateX(0);}to{transform:translateX(-50%);} }
</style>

{{-- JS Helper --}}
<script>
    // Panggil WayWayLoading.show('login') / .show('logout') / .show('itinerary') / .hide()
    window.WayWayLoading = {
        messages: {
            login:     ['Welcome back, explorer!', 'Loading your trips...', 'Almost there ✦'],
            logout:    ['Signing you out...', 'See you next journey!', 'Goodbye for now...'],
            itinerary: ['Finding best routes...', 'Calculating distances...', 'Building your itinerary...'],
            default:   ['Loading...', 'Please wait...', 'Almost there...'],
        },
        _interval: null,
        show(type = 'default') {
            const el = document.getElementById('ww-loading');
            const msgEl = document.getElementById('ww-loading-msg');
            if (!el) return;
            el.style.display = 'flex';
            // Rotating messages
            const msgs = this.messages[type] || this.messages.default;
            let i = 0;
            msgEl.textContent = msgs[0];
            clearInterval(this._interval);
            this._interval = setInterval(() => {
                i = (i + 1) % msgs.length;
                msgEl.textContent = msgs[i];
            }, 1500);
        },
        hide() {
            const el = document.getElementById('ww-loading');
            if (el) el.style.display = 'none';
            clearInterval(this._interval);
        }
    };
</script>