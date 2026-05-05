<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lupa Password – Graha Medika</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { min-height: 100vh; background: #0f172a; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; padding: 20px; }
    .card { background: #1e293b; border-radius: 16px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
    .icon-wrap { width: 72px; height: 72px; background: #0f172a; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .icon-wrap svg { width: 36px; height: 36px; color: #0ea5e9; }
    h1 { text-align: center; color: #fff; font-size: 22px; margin-bottom: 8px; }
    .sub { text-align: center; color: #94a3b8; font-size: 13px; line-height: 1.6; margin-bottom: 32px; }
    label { display: block; font-size: 12px; color: #94a3b8; margin-bottom: 6px; letter-spacing: .5px; }
    .input-wrap { position: relative; margin-bottom: 8px; }
    .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: #64748b; pointer-events: none; }
    input[type=email] { width: 100%; background: #0f172a; border: 1.5px solid #334155; border-radius: 10px; padding: 12px 14px 12px 42px; color: #fff; font-size: 14px; outline: none; transition: border-color .2s; }
    input[type=email]:focus { border-color: #0ea5e9; }
    .error-box { font-size: 12px; color: #f87171; margin-bottom: 16px; padding: 10px 14px; background: rgba(248,113,113,.1); border-radius: 8px; border-left: 3px solid #f87171; }
    .btn { width: 100%; background: #0ea5e9; color: #fff; font-size: 15px; font-weight: 700; border: none; border-radius: 10px; padding: 13px; cursor: pointer; margin-top: 20px; transition: background .2s; }
    .btn:hover { background: #0284c7; }
    .back { text-align: center; margin-top: 18px; font-size: 13px; color: #64748b; }
    .back a { color: #0ea5e9; text-decoration: none; }
    .back a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="card">
  <div class="icon-wrap">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
  </div>

  <h1>Lupa Password?</h1>
  <p class="sub">Masukkan email akunmu. Kami akan mengirimkan kode OTP 6 digit ke email tersebut.</p>

  @if(session('error'))
    <div class="error-box">{{ session('error') }}</div>
  @endif
  @error('email')
    <div class="error-box">{{ $message }}</div>
  @enderror

  <form method="POST" action="{{ route('password.send-otp') }}">
    @csrf
    <label for="email">Email</label>
    <div class="input-wrap">
      <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
      </svg>
      <input type="email" id="email" name="email" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus/>
    </div>

    <button type="submit" class="btn">Kirim Kode OTP</button>
  </form>

  <div class="back">
    <a href="{{ route('login') }}">← Kembali ke Login</a>
  </div>
</div>
</body>
</html>
