<!DOCTYPE html>
<html>
<head>
    <title>{{ $user->name }}'s Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body class="p-4">
    <div class="container">
        <h2>{{ $user->name }}'s Wishlist</h2>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-3">Back to User List</a>

        <div class="row">
            @forelse ($photos as $item)
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="{{ asset('storage/photos/' . $item->photo->filename) }}" class="card-img-top" alt="Photo">
                    </div>
                </div>
            @empty
                <p>No wishlist photos found.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
