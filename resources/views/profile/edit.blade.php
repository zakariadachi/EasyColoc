<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
</head>
<body>
    <h1>Profile</h1>

    @if (session('status') === 'profile-updated')
        <p>Profile updated successfully.</p>
    @endif

    <div>
        <h2>Profile Information</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span>{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span>{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label>Role</label>
                <input type="text" value="{{ $user->is_admin ? 'Admin' : 'Member' }}" readonly>
            </div>

            <div>
                <label>Account Status</label>
                <input type="text" value="{{ $user->is_banned ? 'Banned' : 'Active' }}" readonly>
            </div>

            <button type="submit">Save</button>
        </form>
    </div>

    <div>
        <h2>Update Password</h2>
        <p>Password update functionality available.</p>
    </div>
</body>
</html>
