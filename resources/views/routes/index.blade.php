<!DOCTYPE html>
<html>
<head>
    <title>Laravel Routes List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Laravel Routes List</h2>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Method</th>
                    <th>URI</th>
                    <th>Name</th>
                    <th>Action</th>
                    <th>Middleware</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routes as $index => $route)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><code>{{ $route['method'] }}</code></td>
                        <td><code>{{ $route['uri'] }}</code></td>
                        <td>{{ $route['name'] }}</td>
                        <td>{{ $route['action'] }}</td>
                        <td>{{ $route['middleware'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
