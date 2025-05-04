<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Selection</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .photo {
            position: relative;
        }
        .selected {
            border: 5px solid green;
        }
        .heart {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: red;
            cursor: pointer;
        }
        /* Modal styling */
        .modal-content img {
            width: 100%;
            height: auto;
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

    <!-- Modal Template for each photo -->
    @foreach ($photos as $photo)
    <div class="modal fade" id="photoModal{{ $photo->id }}" tabindex="-1" aria-labelledby="photoModalLabel{{ $photo->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel{{ $photo->id }}">Wedding Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('storage/photos/' . $photo->filename) }}" alt="Wedding Photo">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Open modal when photo (not heart) is clicked
        $('.photo').click(function (e) {
            // Ensure the heart click doesn't trigger the modal
            if ($(e.target).hasClass('heart')) {
                return;
            }
            let photoId = $(this).data('id');
            $('#photoModal' + photoId).modal('show');
        });

        // Prevent modal opening when clicking on the heart
        $('.heart').click(function (e) {
            e.stopPropagation(); // Stop the event from propagating to the photo div
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
