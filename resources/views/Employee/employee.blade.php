@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Employee</h1>
@stop

@section('content')

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <div class="card-tools">
                    <a href="{{ route('employee.index') }}">
                        <button type="button" class="btn btn-block btn-primary">Add Employee</button>
                    </a>
                </div>
            </div>
            @csrf
            <div class="card-body p-0">
                <div class="tb_search">
                    <input type="text" id="search_input_all" placeholder="Search.." class="form-control">
                </div>
                <table id="employee_table" class="table table-striped projects">
                    <thead>
                        <tr>
                            <th style="width: 1%">
                                #
                            </th>
                            <th style="width: 20%">
                                Employee Name
                            </th>
                            <th>
                                Employee Email
                            </th>
                            <th>
                                Employee Designation
                            </th>
                            <th style="width: 20%">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
@stop
@section('css')
    <style>
        .img_wrap {
            width: 82px;
            height: 82px;
            margin: 0 auto 10px;
            border-radius: 50%;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .img_wrap .dp {
            background: #ebc4e0;
            border-radius: 50%;
            width: 100%;
            height: auto;
        }

        .img_wrap .dp_name {
            background: #ebc4e0;
            border-radius: 50%;
            color: #fff;
            font-weight: 600;
            font-size: 36px;
            text-align: center;
            width: 82px;
            height: 82px;
            padding-top: 14px;
        }

        .contact_list.img_wrap {
            width: 40px !important;
            height: 40px !important;
            margin: 0 auto !important;
            float: right;
            overflow: hidden;
        }

        .contact_list.img_wrap .dp {
            background: #92dce3 !important;
            border-radius: 50% !important;
            width: 100% !important;
            height: auto !important;
        }

        .contact_list.img_wrap .dp_name {
            background: #92dce3 !important;
            border-radius: 50% !important;
            color: #fff !important;
            font-weight: 600 !important;
            font-size: 18px !important;
            text-align: center !important;
            width: 40px !important;
            height: 40px !important;
            padding-top: 0;
            text-transform: uppercase;
        }

        .contact_list.img_wrap .dp_name.no_img {
            /*padding-top: 6px;*/
            line-height: 40px;
        }

    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {

            oTable = $('#employee_table').DataTable({
                processing: false,
                serverSide: true,
                autoWidth: false,
                searchable: false,
                "bFilter": false,
                "dom": '<"top"f>rt<"bottom"lpi><"clear">',
                "bSort": true,
                ajax: {
                    url: '{!! route('employee.datatable') !!}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: function(d) {
                        d.search = $('#search_input_all').val();
                    }
                },
                columns: [{
                        data: 'dp',
                        name: 'dp',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false
                    },
                    {
                        data: 'designation',
                        name: 'designation',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ]
            });
        });

        $("#search_input_all").keydown(function() {
            oTable.draw();
        });
        $("#search_input_all").keyup(function() {
            oTable.draw();
        });
        $('body').on('click','.delete_employee',function() {
            const url = $(this).attr('data-href');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    }
                });
                    $.ajax({
                url: url,
                type: 'DELETE',
                success: function(res) {
                    if (res.status == 1000) {
                        oTable.draw();
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

                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })

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
    </script>
@stop
