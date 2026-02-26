<!DOCTYPE html>
<html>
<head>
    <title>Colocation Invitation</title>
</head>
<body>
    <h1>You've been invited to join {{ $invitation->colocation->name }}</h1>

    @if (Auth::check())
        <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
            @csrf
            <button type="submit">Accept Invitation</button>
        </form>
    @else
        <p>Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to accept this invitation.</p>
    @endif
</body>
</html>
