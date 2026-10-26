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

    // Indikator password cocok (halaman register)
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
});

// Kalau user tekan tombol Back, reset tombol yang sempat "Memproses..."
window.addEventListener('pageshow', e => { if (e.persisted) location.reload(); });