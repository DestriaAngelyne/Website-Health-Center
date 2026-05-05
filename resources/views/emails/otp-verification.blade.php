<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verifikasi OTP – Graha Medika</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { min-height: 100vh; background: #0f172a; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; padding: 20px; }
    .card { background: #1e293b; border-radius: 16px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
    .icon-wrap { width: 72px; height: 72px; background: #0f172a; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .icon-wrap svg { width: 36px; height: 36px; color: #22c55e; }
    h1 { text-align: center; color: #fff; font-size: 22px; margin-bottom: 8px; }
    .sub { text-align: center; color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 8px; }
    .email-badge { text-align: center; color: #0ea5e9; font-size: 13px; font-weight: 600; margin-bottom: 32px; }
    .error-box { font-size: 12px; color: #f87171; margin-bottom: 20px; padding: 10px 14px; background: rgba(248,113,113,.1); border-radius: 8px; border-left: 3px solid #f87171; }
    .success-box { font-size: 12px; color: #4ade80; margin-bottom: 20px; padding: 10px 14px; background: rgba(74,222,128,.1); border-radius: 8px; border-left: 3px solid #4ade80; }
    .otp-label { font-size: 12px; color: #94a3b8; margin-bottom: 14px; letter-spacing: .5px; text-align: center; }
    .otp-row { display: flex; gap: 10px; justify-content: center; margin-bottom: 28px; }
    .otp-row input { width: 52px; height: 60px; background: #0f172a; border: 2px solid #334155; border-radius: 12px; text-align: center; font-size: 26px; font-weight: 800; color: #fff; outline: none; transition: border-color .2s, transform .1s; font-family: 'Courier New', monospace; }
    .otp-row input:focus { border-color: #0ea5e9; transform: scale(1.05); }
    .otp-row input.filled { border-color: #0ea5e9; }
    .btn { width: 100%; background: #0ea5e9; color: #fff; font-size: 15px; font-weight: 700; border: none; border-radius: 10px; padding: 13px; cursor: pointer; transition: background .2s; }
    .btn:hover { background: #0284c7; }
    .btn:disabled { background: #1e3a5f; cursor: not-allowed; color: #64748b; }
    .resend-row { text-align: center; margin-top: 16px; font-size: 13px; color: #64748b; }
    .resend-row a { color: #0ea5e9; text-decoration: none; cursor: pointer; }
    .resend-row a:hover { text-decoration: underline; }
    .expire { text-align: center; margin-top: 12px; font-size: 12px; color: #475569; }
  </style>
</head>
<body>
<div class="card">
  <div class="icon-wrap">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
  </div>

  <h1>Masukkan Kode OTP</h1>
  <p class="sub">Kode OTP 6 digit telah dikirim ke</p>
  <p class="email-badge">{{ $email }}</p>

  @if(session('error'))
    <div class="error-box">{{ session('error') }}</div>
  @endif
  @if(session('success'))
    <div class="success-box">{{ session('success') }}</div>
  @endif
  @error('otp')
    <div class="error-box">{{ $message }}</div>
  @enderror

  <form method="POST" action="{{ route('password.verify-otp') }}" id="otp-form">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}"/>
    <input type="hidden" name="otp" id="otp-hidden"/>

    <p class="otp-label">KODE OTP</p>
    <div class="otp-row">
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
      <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off"/>
    </div>

    <button type="submit" class="btn" id="verify-btn" disabled>Verifikasi OTP</button>
  </form>

  <div class="resend-row">
    Tidak menerima kode?
    <form method="POST" action="{{ route('password.resend-otp') }}" style="display:inline">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}"/>
      <button type="submit" style="background:none;border:none;padding:0;font-size:13px;" class="resend-row">
        <a>Kirim ulang</a>
      </button>
    </form>
  </div>
  <p class="expire">⏱ Kode berlaku selama 10 menit</p>
</div>

<script>
  const inputs  = document.querySelectorAll('.otp-input');
  const hidden  = document.getElementById('otp-hidden');
  const btn     = document.getElementById('verify-btn');

  inputs.forEach((inp, i) => {
    inp.addEventListener('input', e => {
      // Hanya izinkan angka
      inp.value = inp.value.replace(/\D/g, '');
      if (inp.value && i < 5) inputs[i + 1].focus();
      updateOtp();
    });
    inp.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !inp.value && i > 0) {
        inputs[i - 1].focus();
      }
    });
    inp.addEventListener('paste', e => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData)
        .getData('text').replace(/\D/g, '').slice(0, 6);
      paste.split('').forEach((char, idx) => {
        if (inputs[idx]) inputs[idx].value = char;
      });
      if (inputs[paste.length - 1]) inputs[paste.length - 1].focus();
      updateOtp();
    });
  });

  function updateOtp() {
    const otp = Array.from(inputs).map(i => i.value).join('');
    hidden.value = otp;
    btn.disabled = otp.length < 6;
    inputs.forEach(i => {
      i.classList.toggle('filled', i.value !== '');
    });
  }
</script>
</body>
</html>
