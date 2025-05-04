<!DOCTYPE html>
<html>
<head>
    <title>Photo Selection</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .photo { position: relative; }
        .selected { border: 5px solid green; }
        .heart {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body class="p-4">
    <h2>Hello {{ $user->name }}! Select your favorite wedding photos 💖</h2>
    <div class="row">
        @foreach ($photos as $photo)
        <div class="col-md-3 mb-3">
            <div class="photo {{ in_array($photo->id, $wishlist) ? 'selected' : '' }}" data-id="{{ $photo->id }}">
                <img src="{{ asset('storage/photos/' . $photo->filename) }}" class="img-fluid">
                <div class="heart">&#10084;</div>
            </div>
        </div>
        @endforeach
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $('.heart').click(function () {
            let photoDiv = $(this).closest('.photo');
            let photoId = photoDiv.data('id');
            photoDiv.toggleClass('selected');

            $.post('/wishlist-toggle', {
                _token: '{{ csrf_token() }}',
                token: '{{ $user->access_token }}',
                photo_id: photoId
            });
        });
    </script>
</body>
</html>
