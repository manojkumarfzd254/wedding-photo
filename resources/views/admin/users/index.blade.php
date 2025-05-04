<!DOCTYPE html>
<html>
<head>
    <title>All Users & Wishlists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>All Users</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Access Link</th>
                    <th>Wishlist Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>
                            <input type="text" class="form-control" value="{{ url('/gallery/' . $user->access_token) }}" readonly>
                        </td>
                        <td>{{ $user->wishlist_count }}</td>
                        <td>
                            <a href="{{ route('admin.user.wishlist', $user->id) }}" class="btn btn-primary btn-sm">View Wishlist</a>
                            <a href="{{ route('admin.user.wishlist.download', $user->id) }}" class="btn btn-success btn-sm mt-1">Download ZIP</a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
