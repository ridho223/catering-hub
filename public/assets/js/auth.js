document.addEventListener('DOMContentLoaded', () => {

    // Lihat / sembunyikan password
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const icon = btn.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // Tombol loading saat submit (cegah klik ganda)
    document.querySelectorAll('.auth-form form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('.btn-auth');
            if (!btn) return;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        });
    });

    // Indikator password cocok
    const pw = document.querySelector('input[name="password"]');
    const cf = document.querySelector('input[name="password_confirmation"]');
    const hint = document.getElementById('matchHint');
    if (pw && cf && hint) {
        const check = () => {
            if (!cf.value) { hint.textContent = ''; hint.className = 'field-hint'; return; }
            const ok = pw.value === cf.value;
            hint.textContent = ok ? 'Password cocok' : 'Password belum sama';
            hint.className = 'field-hint ' + (ok ? 'ok' : 'bad');
        };
        pw.addEventListener('input', check);
        cf.addEventListener('input', check);
    }

    // Indikator kekuatan password (daftar & reset password)
    const meter = document.getElementById('pwMeter');
    const meterText = document.getElementById('pwText');
    if (pw && meter) {
        const labels = ['Minimal 8 karakter', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
        const score = v => {
            if (!v) return 0;
            let s = 0;
            if (v.length >= 8) s++;
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
            if (/\d/.test(v)) s++;
            if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) s++;
            return Math.max(1, s);
        };
        pw.addEventListener('input', () => {
            const lv = score(pw.value);
            meter.dataset.level = lv;
            if (meterText) meterText.textContent = labels[lv];
        });
    }

    // Efek parallax foto di panel kiri (hanya perangkat dengan mouse, hormati reduced-motion)
    const side = document.querySelector('.auth-side');
    const canHover = window.matchMedia('(hover: hover)').matches;
    const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (side && canHover && !calm) {
        let raf = null;
        document.addEventListener('mousemove', e => {
            if (raf) return;
            raf = requestAnimationFrame(() => {
                side.style.setProperty('--mx', ((e.clientX / window.innerWidth) - 0.5).toFixed(3));
                side.style.setProperty('--my', ((e.clientY / window.innerHeight) - 0.5).toFixed(3));
                raf = null;
            });
        });
    }

    // Jeda sebelum boleh kirim ulang email (tombol dengan data-cooldown="detik")
    document.querySelectorAll('[data-cooldown]').forEach(btn => {
        let left = parseInt(btn.dataset.cooldown, 10) || 0;
        if (left <= 0) return;
        const label = btn.textContent.trim();
        btn.disabled = true;
        const tick = () => {
            if (left <= 0) { btn.disabled = false; btn.textContent = label; return; }
            btn.textContent = 'Kirim ulang dalam ' + left + ' dtk';
            left--;
            setTimeout(tick, 1000);
        };
        tick();
    });
});

// Kalau user tekan tombol Back, reset tombol yang sempat "Memproses..."
window.addEventListener('pageshow', e => { if (e.persisted) location.reload(); });