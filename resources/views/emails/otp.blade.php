<!-- resources/views/emails/otp.blade.php -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:20px; background:#f4f4f4; font-family: sans-serif;">
  <div style="max-width:520px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden;">
    
    <div style="background:#1a1a2e; padding:32px; text-align:center;">
      <span style="color:#fff; font-size:20px; letter-spacing:1px;">● ResQ email verification</span>
    </div>

    <div style="padding:32px 40px;">
      <h2 style="margin:0 0 8px; font-size:20px; font-weight:500;">Verify your email address</h2>
      <p style="color:#666; line-height:1.7;">Hey there! Use the code below to complete your registration. It expires in <strong>10 minutes</strong>.</p>
      
      <div style="background:#f8f8f8; border-radius:8px; padding:24px; text-align:center; margin:24px 0;">
        <div style="font-size:36px; font-weight:500; letter-spacing:12px; font-family:monospace;">{{ $otp }}</div>
        <div style="font-size:12px; color:#999; margin-top:8px;">Your one-time verification code</div>
      </div>

      <p style="color:#666; line-height:1.7;">If you didn't request this, you can safely ignore this email.</p>
      <hr style="border:none; border-top:1px solid #eee; margin:24px 0;">
      <p style="font-size:13px; color:#888;">🔒 Never share this code with anyone. Our team will never ask for it.</p>
    </div>

    <div style="padding:20px 40px; border-top:1px solid #eee; text-align:center; font-size:12px; color:#aaa;">
      © 2026 ResQ Team. All rights reserved.
    </div>

  </div>
</body>
</html>