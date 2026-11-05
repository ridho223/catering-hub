/*!
 * Dialog konfirmasi (pengganti confirm() bawaan browser).
 * - Otomatis mengubah <form onsubmit="return confirm('...')"> yang sudah ada.
 * - Bisa dipakai langsung: <form data-confirm="Hapus data ini?"> atau window.bjConfirm({...}).
 * Tanpa library tambahan (butuh Bootstrap Icons untuk ikon, yang memang sudah dipakai).
 */
(function () {
    if (window.bjConfirm) { return; }

    var CSS = [
        '.bjc-overlay{position:fixed;inset:0;z-index:2000;display:grid;place-items:center;padding:1rem;background:rgba(16,24,20,.45);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);opacity:0;transition:opacity .18s}',
        '.bjc-overlay.on{opacity:1}',
        '.bjc-dialog{width:min(400px,100%);background:#fff;border-radius:20px;padding:1.6rem 1.5rem 1.3rem;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.25);transform:translateY(8px) scale(.96);transition:transform .2s cubic-bezier(.2,.8,.2,1);font-family:inherit}',
        '.bjc-overlay.on .bjc-dialog{transform:none}',
        '.bjc-icon{width:60px;height:60px;margin:0 auto .9rem;border-radius:50%;display:grid;place-items:center;font-size:1.6rem}',
        '.bjc-icon.danger{background:#fde8e8;color:#dc3545}',
        '.bjc-icon.success{background:#e3f4ea;color:#198754}',
        '.bjc-icon.warning{background:#fff1d6;color:#b7791f}',
        '.bjc-title{margin:0 0 .35rem;font-size:1.15rem;font-weight:800;color:#1f2a24}',
        '.bjc-msg{margin:0 0 1.3rem;color:#5c6a62;font-size:.92rem;line-height:1.45;overflow-wrap:anywhere}',
        '.bjc-actions{display:flex;gap:.6rem}',
        '.bjc-btn{flex:1;border:1px solid transparent;border-radius:12px;padding:.65rem 1rem;font-weight:700;font-size:.92rem;cursor:pointer;transition:background .15s,filter .15s,transform .05s}',
        '.bjc-btn:active{transform:scale(.98)}',
        '.bjc-cancel{background:#fff;border-color:#dbe3dd;color:#34423a}',
        '.bjc-cancel:hover{background:#f3f6f4}',
        '.bjc-ok{background:#198754;color:#fff}',
        '.bjc-ok.danger{background:#dc3545}',
        '.bjc-ok:hover{filter:brightness(.92)}',
        '.bjc-btn:focus-visible{outline:3px solid rgba(25,135,84,.35);outline-offset:2px}',
        '.bjc-ok.danger:focus-visible{outline-color:rgba(220,53,69,.35)}',
        'body.bjc-lock{overflow:hidden}',
        '@media (prefers-reduced-motion:reduce){.bjc-overlay,.bjc-dialog{transition:none}}'
    ].join('');

    var ICONS = { danger: 'bi-trash3', success: 'bi-patch-check', warning: 'bi-exclamation-triangle' };

    function injectStyle() {
        if (document.getElementById('bjc-style')) { return; }
        var s = document.createElement('style');
        s.id = 'bjc-style';
        s.textContent = CSS;
        document.head.appendChild(s);
    }

    function el(tag, cls, text) {
        var e = document.createElement(tag);
        if (cls) { e.className = cls; }
        if (text !== undefined) { e.textContent = text; }
        return e;
    }

    // Menebak jenis dialog dari kalimat pesan lama
    function guess(message) {
        var m = (message || '').toLowerCase();
        if (/hapus/.test(m))            { return { type: 'danger',  title: 'Hapus data?',            ok: 'Ya, hapus' }; }
        if (/kosongkan/.test(m))        { return { type: 'danger',  title: 'Kosongkan keranjang?',   ok: 'Ya, kosongkan' }; }
        if (/tolak/.test(m))            { return { type: 'danger',  title: 'Tolak pembayaran?',      ok: 'Ya, tolak' }; }
        if (/verifikasi|valid/.test(m)) { return { type: 'success', title: 'Verifikasi pembayaran?', ok: 'Ya, verifikasi' }; }
        if (/batalkan/.test(m))         { return { type: 'warning', title: 'Batalkan?',              ok: 'Ya, batalkan' }; }
        return { type: 'warning', title: 'Konfirmasi', ok: 'Ya, lanjutkan' };
    }

    window.bjConfirm = function (opt) {
        injectStyle();
        opt = opt || {};

        var g          = guess(opt.message);
        var type       = opt.type || g.type;
        var title      = opt.title || g.title;
        var okText     = opt.okText || g.ok;
        var cancelText = opt.cancelText || 'Batal';

        return new Promise(function (resolve) {
            var prevFocus = document.activeElement;

            var overlay = el('div', 'bjc-overlay');
            var dialog  = el('div', 'bjc-dialog');
            dialog.setAttribute('role', 'alertdialog');
            dialog.setAttribute('aria-modal', 'true');
            dialog.setAttribute('aria-labelledby', 'bjc-title');
            dialog.setAttribute('aria-describedby', 'bjc-msg');

            var icon = el('div', 'bjc-icon ' + type);
            icon.appendChild(el('i', 'bi ' + (ICONS[type] || ICONS.warning)));

            var h = el('h2', 'bjc-title', title);
            h.id = 'bjc-title';
            var p = el('p', 'bjc-msg', opt.message || '');
            p.id = 'bjc-msg';

            var actions = el('div', 'bjc-actions');
            var cancel  = el('button', 'bjc-btn bjc-cancel', cancelText);
            cancel.type = 'button';
            var ok = el('button', 'bjc-btn bjc-ok' + (type === 'danger' ? ' danger' : ''), okText);
            ok.type = 'button';
            actions.append(cancel, ok);

            dialog.append(icon, h, p, actions);
            overlay.appendChild(dialog);
            document.body.appendChild(overlay);
            document.body.classList.add('bjc-lock');

            function close(result) {
                document.removeEventListener('keydown', onKey, true);
                overlay.classList.remove('on');
                document.body.classList.remove('bjc-lock');
                setTimeout(function () { overlay.remove(); }, 180);
                if (prevFocus && prevFocus.focus) { try { prevFocus.focus(); } catch (e) {} }
                resolve(result);
            }

            function onKey(e) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    close(false);
                } else if (e.key === 'Tab') {
                    // fokus tetap berputar di dalam dialog
                    e.preventDefault();
                    var f = [cancel, ok];
                    var i = f.indexOf(document.activeElement);
                    var next = e.shiftKey
                        ? (i <= 0 ? f.length - 1 : i - 1)
                        : (i === -1 || i === f.length - 1 ? 0 : i + 1);
                    f[next].focus();
                }
            }

            document.addEventListener('keydown', onKey, true);
            overlay.addEventListener('mousedown', function (e) { if (e.target === overlay) { close(false); } });
            cancel.addEventListener('click', function () { close(false); });
            ok.addEventListener('click', function () { close(true); });

            requestAnimationFrame(function () {
                overlay.classList.add('on');
                // untuk aksi berbahaya, fokus awal di "Batal" supaya tidak terpencet Enter tanpa sengaja
                (type === 'danger' ? cancel : ok).focus();
            });
        });
    };

    // ---- Mengubah onsubmit/onclick="return confirm('...')" lama menjadi data-confirm ----
    var LEGACY = /^\s*return\s+confirm\(\s*(['"])([\s\S]*?)\1\s*\)\s*;?\s*$/;

    function upgradeNode(n) {
        ['onsubmit', 'onclick'].forEach(function (attr) {
            if (!n.hasAttribute || !n.hasAttribute(attr)) { return; }
            var m = LEGACY.exec(n.getAttribute(attr));
            if (!m) { return; }   // isinya lebih rumit dari sekadar confirm: biarkan apa adanya
            n.setAttribute('data-confirm', m[2].replace(/\\(['"\\])/g, '$1'));
            n.removeAttribute(attr);
        });
    }

    function upgradeAll() {
        document.querySelectorAll('[onsubmit*="confirm("], [onclick*="confirm("]').forEach(upgradeNode);
    }

    function ask(node) {
        return window.bjConfirm({
            message: node.getAttribute('data-confirm'),
            title:   node.getAttribute('data-confirm-title') || undefined,
            okText:  node.getAttribute('data-confirm-ok') || undefined,
            type:    node.getAttribute('data-confirm-type') || undefined
        });
    }

    // Form: tahan submit, tampilkan dialog, lalu kirim ulang kalau disetujui
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') { return; }

        upgradeNode(form);
        if (!form.hasAttribute('data-confirm')) { return; }

        if (form.__bjOk) { form.__bjOk = false; return; }

        e.preventDefault();
        e.stopPropagation();   // supaya confirm() bawaan tidak ikut jalan
        var submitter = e.submitter || null;

        ask(form).then(function (yes) {
            if (!yes) { return; }
            form.__bjOk = true;
            if (form.requestSubmit) { form.requestSubmit(submitter || undefined); } else { form.submit(); }
        });
    }, true);

    // Tombol atau tautan dengan data-confirm
    document.addEventListener('click', function (e) {
        var t = e.target.closest ? e.target.closest('[data-confirm]') : null;
        if (!t || t.tagName === 'FORM') { return; }

        if (t.__bjOk) { t.__bjOk = false; return; }

        e.preventDefault();
        e.stopPropagation();

        ask(t).then(function (yes) {
            if (!yes) { return; }
            t.__bjOk = true;
            t.click();
        });
    }, true);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', upgradeAll);
    } else {
        upgradeAll();
    }
})();