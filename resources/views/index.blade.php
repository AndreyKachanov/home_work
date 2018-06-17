@extends('layout')
@section('content')

    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                Home work
            </div>

            <div class="links" style="padding-left: 25px">
                <h3>Select a file with currencies (JSON format)</h3>
                <div class="form-group form-inline">
                    <input class="form-control input-file" type="file" id="file">
                    <button style="margin-left: 50px;" id="btn-file" class="btn btn-dark">Upload</button>
                </div>
                <div id="file-data-block"></div>

                <div hidden id="error-block" class="alert-warning form-control"></div>
            </div>
        </div>
    </div>

    <script>
        $('#btn-file').on('click', function (e) {
            e.preventDefault();

            var file = $("#file").val();
            if (file) {
                var url = '{!! route('upload_file') !!}';
                var fd = new FormData();
                fd.append('file', $('#file')[0].files[0]);
                fd.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $.ajax({
                    type: 'POST',
                    url: url,
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: fd,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(result) {
                        $('#btn-file').attr('disabled', 'disabled');
                    },
                    success: function(result) {
                        $('#file').val('');
                        $('#file-data-block').html(result);
                        console.log(result.html);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    complete: function(result) {
                        $('#btn-file').prop('disabled', false);
                    }

                });
            }

        });

        var filesExt = ['json'];
        $('input[type=file]').change(function(){
            var parts = $(this).val().split('.');
            if(!(filesExt.join().search(parts[parts.length - 1]) != -1)){
                alert('Select only json file!');
            }

        });

    </script>

@endsection