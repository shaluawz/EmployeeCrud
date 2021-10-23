@extends('adminlte::page')

@section('title', 'Add Employee')

@section('content_header')
    <h1>Add Employee</h1>
@stop

@section('content')
    <section class="content">
        <form method="post" id="saveEmployee" action="{{ route('employee.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputName">Employee Name</label>
                                <input type="text" id="inputName" name="name" class="form-control validate">
                            </div>
                            <div class="form-group">
                                <label for="inputEmail">Email</label>
                                <input type="email" id="inputEmail" name="email" class="form-control validate">
                            </div>
                            <div class="form-group">
                                <label for="inputDesignation">Designation</label>
                                <select id="inputDesignation" name="designation"
                                    class="form-control validate custom-select">
                                    <option selected disabled>Select one</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <img id="blah" src="#" alt="your image" />
                                <label for="inputImage">Image</label>
                                <input type="file" id="inputImage" name="image" class="form-control ">
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ url('/') }}" class="btn btn-secondary">Cancel</a>
                    <input type="submit" value="Create Employee" class="btn btn-success save_employee float-right">
                </div>
            </div>
        </form>
    </section>
@stop
@section('css')
    <style>
        .error_custom {
            border: 1px solid #da8885 !important;
        }

    </style>
@stop

@section('js')
    <script>
        $('body').on('click', '.save_employee', function(e) {
            e.preventDefault();
            var flag = '0';
            var msg = '';

            $('#saveEmployee *').filter(':input, textarea, select').each(function() {

                if ($(this).hasClass('validate')) {

                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('error_custom');
                        $(this).parent().find('.select2-container .select2-selection').addClass(
                            'error_custom');
                        flag = '1';
                        msg = 'Mandatory fields required';
                    } else {
                        $(this).removeClass('error_custom');
                        $(this).parent().find('.select2-container .select2-selection').removeClass(
                            'error_custom');
                    }
                } else {
                    $(this).removeClass('error_custom');
                    $(this).parent().find('.select2-container .select2-selection').removeClass(
                        'error_custom');
                }

                /*FOR EMAIL VALIDATION*/
                if (($(this).is('#inputEmail')) && ($(this).val() != '')) {
                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    if (testEmail.test($(this).val())) {
                        $(this).removeClass('error_custom');
                    } else {
                        $(this).addClass('error_custom');
                        flag = '1';
                        msg = "invalid email address"
                    }
                }
                /*FOR EMAIL VALIDATION*/

            });


            if (flag == '1') {
                alert_error(msg);
                return false;
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });

                $('#busyloading').show();
                var url = $('#saveEmployee').attr('action');
                var formData = new FormData(document.getElementById("saveEmployee"));
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false, // tell jQuery not to process the data
                    contentType: false, // tell jQuery not to set contentType
                    success: function(res) {
                        if (res.status == 1000) {
                            alert_success(res.message)
                        } else if (res.status == 2000) {
                            alert_error(res.message)
                            return false;
                        } else if (res.status == 3000) {
                            alert_error(res.message)
                            return false;
                        }

                    },
                    error: function(res) {
                        var errors = jQuery.parseJSON(res.responseText);
                        $.each(errors, function(key, val) {
                            alert_error(errors.val)
                        });

                    }
                });
            }
        });

        function alert_error(msg) {
            Swal.fire({
                title: 'Oops...',
                text: msg,
            })
        }

        function alert_success(msg) {
            Swal.fire(
                'Good job!',
                msg,
                'success'
            )
        }
        inputImage.onchange = evt => {
            const [file] = inputImage.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>
@stop
