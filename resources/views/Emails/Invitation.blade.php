<!DOCTYPE html>
<html>
<head>
    <title>Invitation EasyColoc</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f8fafc;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 40px; border-radius: 10px;">
        <h2 style="color: #4f46e5;">EasyColoc</h2>

        <p>Vous avez été invité à rejoindre la colocation :</p>
        <p style="font-size: 18px; font-weight: bold; color: #1e293b;">{{ $invitation->colocation->name }}</p>

        <p>Cliquez sur le bouton ci-dessous pour accepter l'invitation :</p>

        <a href="{{ url('/invitations/' . $invitation->token) }}"
           style="display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: white; text-decoration: none; border-radius: 8px; margin: 20px 0;">
            Accepter l'invitation
        </a>

        @if($invitation->expires_at)
        <p style="color: #64748b; font-size: 14px;">
            Ce lien expire le {{ $invitation->expires_at->format('d/m/Y à H:i') }}
        </p>
        @endif
    </div>
</body>
</html>
