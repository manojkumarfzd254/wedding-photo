<!DOCTYPE html>
<html>
<head>
    <title>Upload Wedding Photos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

    <div class="container">
        <h2>Upload Wedding Photos (With Progress Bar)</h2>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <input type="file" name="photos[]" multiple required class="form-control mb-2">
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <div class="progress mt-3" style="height: 25px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                role="progressbar" style="width: 0%">0%</div>
        </div>

        <div id="message" class="mt-3"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            let progressBar = $('.progress-bar');
            let xhr = new XMLHttpRequest();

            xhr.open('POST', '/admin/upload', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    let percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.css('width', percent + '%').text(percent + '%');
                }
            });

            xhr.onload = function () {
                if (xhr.status === 200) {
                    $('#message').html('<div class="alert alert-success">Upload complete!</div>');
                } else {
                    $('#message').html('<div class="alert alert-danger">Upload failed.</div>');
                }
                progressBar.removeClass('progress-bar-animated');
            };

            xhr.onerror = function () {
                $('#message').html('<div class="alert alert-danger">Network Error.</div>');
                progressBar.removeClass('progress-bar-animated');
            };

            xhr.send(formData);
        });
    </script>
</body>
</html>
