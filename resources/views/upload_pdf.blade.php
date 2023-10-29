<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CSV App</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <!-- Styles -->
        <style>
            #progressContainer {
                width: 100%;
                text-align: center;
                margin: 20px 0;
                border: 1px solid black;
            }

            #progressBar {
                width: 0;
                height: 30px;
                background-color: #4CAF50; /* Green color */
                text-align: center;
                line-height: 30px;
                color: white;
            }

            #progressStatus {
                margin: 0;
            }
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="container">
        <div class="banner">
            This Is Upload PDF Page
        </div>
        @if (session('status'))
            <div class="alert alert-{{session('status')[0]}}">
                {{ session('status')[1] }}
            </div>
        @endif
        <div id="uploadStatus" class="alert ">

        </div>
        <div id="progressContainer" style="display:none;">
            <div id="progressBar">
                <div id="progressStatus"></div>
            </div>
        </div>
        <div class="outer-form">
            <input type="file" id="file-input" style="display: none;" accept=".csv" name="uploaded_file">
            <div id="drop-area" class=" row border-1">
                <div class="col-9 file-label" id="label">
                    Drop files here or click to select
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary float-right" id="uploadButton">Submit</button>
                </div>
            </div>
        </div>
        <table class="table" id="filetable">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Filename</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
                
        </table>
    </body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#filetable').DataTable();
        $('#uploadButton').click(function() {
            var fileInput = document.getElementById('file-input');
            var file = fileInput.files[0];
            $('#uploadButton').prop('disabled', true);
            $('#uploadButton').html('<i class="fas fa-spinner fa-spin"></i> Uploading');
            if (file) {
                var formData = new FormData();
                formData.append('uploaded_file', file);
                formData.append('_token', '{{ csrf_token() }}');
                $('#progressContainer').show();
                $.ajax({
                    url: '{{ route("upload") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    xhr: function() {
                        var xhr = $.ajaxSettings.xhr();
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                var percentComplete = (event.loaded / event.total) * 100;
                                $('#progressBar').css('width', percentComplete + '%');
                                $('#progressStatus').text(percentComplete.toFixed(2) + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        $('#uploadStatus').removeClass('alert-danger').addClass('alert-success').html(response.message);
                        $('#uploadButton').prop('disabled', false);
                        $('#uploadButton').html('Submit');
                    },
                    error: function(xhr, status, error) {
                        $('#uploadStatus').removeClass('alert-success').addClass('alert-danger').html(xhr.responseJSON.message);
                        $('#uploadButton').prop('disabled', false);
                        $('#uploadButton').html('Submit');
                    }
                });
            } else {
                $('#uploadStatus').removeClass('alert-success').addClass('alert-danger').html('No file selected.');
                $('#uploadButton').prop('disabled', false);
                $('#uploadButton').html('Submit');
            }
        });
        function refreshDataTable() {
            $.ajax({
                url: '{{route("ajxdata")}}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    table.clear();
                    data.forEach(element => {
                        table.row.add([element.col1, element.col2, element.col3]).draw();
                    });
                    setTimeout(refreshDataTable, 10000);
                },
                error: function() {
                    console.log('Failed to fetch data.');
                    setTimeout(refreshDataTable, 10000);
                }
            });
        }

        refreshDataTable();
    });
</script>