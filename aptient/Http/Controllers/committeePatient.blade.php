@extends('main')
@section('title', 'اللجنة الطبية')
@section('page_link')
<a href="{{url('patient')}}">اللجنة الطبية</a>
@endsection
@section('content')
<style>
    td > .btn-group {

        display: inline-block;
    }

</style>
<div class="modal container  fadeIn" id="excelModal" data-width="500" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN SAMPLE FORM PORTLET-->

                <div class="portlet-title">
                    <div class="caption font-green-haze">
                        <i class="icon-settings font-green-haze"></i>
                        <span class="caption-subject bold uppercase"></span>إضافة الملف
                    </div>
                </div>


                <div class="portlet-body form">

                    <form method="POST" action="{{url('committeePatient_excel')}}" data-toggle="validator" id="excel_form" accept-charset="UTF-8" class="form-horizontal form" role="form" enctype="multipart/form-data">

                        <input name="id" id="pk_id_personal" type="hidden" >
                        {{ csrf_field() }}
                        <div class="form-body">

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-2 control-label" for="name">الملف</label>
                                        <div class="col-md-10">
                                            <input type="file" required="" class="form-control" required name="excel" id="name" placeholder="الإسم رباعي">
                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>



                        </div>

                    </form>
                </div>

                <!-- END SAMPLE FORM PORTLET-->
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="excel_submit" class="btn green ok">حفظ التغييرات</button>
        <button type="button" data-dismiss="modal" class="btn btn-default">اغلاق</button>
    </div>
</div>
<div class="modal container  fadeIn" id="committeePatientModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN SAMPLE FORM PORTLET-->

                <div class="portlet-title">
                    <div class="caption font-green-haze">
                        <i class="icon-settings font-green-haze"></i>
                        <span class="caption-subject bold uppercase"></span>اللجنة الطبية
                    </div>
                </div>


                <div class="portlet-body form">
                    <form method="POST" action="" data-toggle="validator" id="medical_form" accept-charset="UTF-8" class="form-horizontal form" role="form" enctype="multipart/form-data">

                        <input name="id" type="hidden" id="pk_id_medical">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div  class="row">

                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-5 control-label" for="personal_id">إسم الجريح</label>

                                        <div class="col-md-7">
                                            <select required="" style=" text-align: center; " name="personal_id" data-placeholder="رقم الهوية-إسم الجريح" class=" select2me select2 form-control" >
                                                <option class="empty1" value=""></option>
                                                @foreach ($patient as $value)
                                                <option value="{{$value->id}}">{{$value->identity}}-{{$value->name}}</option>

                                                @endforeach
                                            </select>           
                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-5 control-label" for="committee_id">إسم اللجنة</label>

                                        <div class="col-md-7">
                                            <select  name="committee_id" data-placeholder="إسم اللجنة" class=" select2me select2 form-control" >

                                                @foreach ($committee as $value)
                                                <option value="{{$value->id}}">{{$value->comit_name}}</option>

                                                @endforeach
                                            </select>            
                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-5 control-label" for="committee_class_id">تصنيفات اللجان</label>

                                        <div class="col-md-7">
                                            <select  name="committee_class_id"  data-placeholder="تصنيفات اللجان" class=" select2me select2 form-control" >

                                                @foreach ($committeeClass as $value)
                                                <option value="{{$value->id}}">{{$value->committee_class_name}}</option>

                                                @endforeach
                                            </select>            
                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-5 control-label" for="need_id">الإحتياج الطبي</label>

                                        <div class="col-md-7">
                                            <select  name="need_id"  data-placeholder="الإحتياج الطبي" class=" select2me select2 form-control" >

                                                @foreach ($needs as $value)
                                                <option value="{{$value->id}}">{{$value->need_name}}</option>

                                                @endforeach
                                            </select>            
                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group form-md-line-input ">
                                        <label class="col-md-5 control-label" for="treatment_duration">مدة العلاج</label>

                                        <div class="col-md-7">
                                            <input id="treatment_duration" name="treatment_duration" placeholder="مدة العلاج" type="number"   class="form-control">

                                            <div class="form-control-focus">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>

                <!-- END SAMPLE FORM PORTLET-->
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="medical_submit" class="btn green ok">حفظ التغييرات</button>
        <button type="button" data-dismiss="modal" class="btn btn-default">اغلاق</button>
    </div>
</div>



<div class="row">

    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption green ">
                    <i class="icon-layers green font-red"></i>
                    <span class="caption-subject font-red bold uppercase">اللجنة الطبية
                    </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a >
                                    <button  onclick="committeePatientModal()" id="sample_editable_1_new" class="btn sbold green">  إضافة جديد
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-striped  table-hover" id="committeePatient_table">
                        <thead>
                            <tr>
                                <th> إسم الجريح</th>
                                <th> اللجنة</th>
                                <th> التصنيف</th>
                                <th> وقت العلاج</th>
                                <th> إحتياج طبي</th>
                                <th> إجراء</th>

                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats  -->
    </div>

</div>


@endsection

@section('level_scr_scr')

<script src="{{url('assets/pages/scripts/form-wizard.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/pages/scripts/components-date-time-pickers.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/scripts/datatable.js')}}" type="text/javascript"></script>

@endsection
@section('level_plug_scr')

<script src="{{url('assets/global/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/bootstrapValidator.js')}}"></script>
<script src="{{url('assets/jquery.validate.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>
@endsection



@section('css')

<link href="{{url('assets/validation.css')}}" rel="stylesheet" type="text/css" />
<link href="{{url('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />

<link href="{{url('assets/global/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap-rtl.css')}}" rel="stylesheet" type="text/css" />
@endsection


@section('script')



<script>
                                        $('#main_filter_submit').on('click', function (e) {
                                            dataTableCommitteePatient.draw();
                                            e.preventDefault();
                                        });

                                        dataTableCommitteePatient = $("#committeePatient_table").DataTable({
                                            processing: true,
                                            serverSide: true,
                                            ajax: {
                                                url: "{{url('getCommitteePatient')}}",
                                                data: function (d) {

                                                    d.status_class = $("#filter").find("[name='status_class']").val();
                                                    d.identity = $("#filter").find("[name='identity']").val();
                                                    d.city = $("#filter").find("[name='city']").val();
                                                    d.area = $("#filter").find("[name='area']").val();
                                                    d.economic_situt = $("#filter").find("[name='economic_situt']").val();
                                                    d.status_desc_id = $("#filter").find("[name='status_desc_id']").val();

                                                }
                                            },
                                            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                                            buttons: [
                                                {
                                                    text: 'تحديث',
                                                    className: 'btn green reload',
                                                    action: function (e, dt, node, config) {
                                                        dt.ajax.reload();
                                                    }},
                                                {
                                                    exportOptions: {
                                                        columns: [0, 1, 2, 3, 4, 5]
                                                    },
                                                    text: 'إستيراد إكسل',
                                                    className: 'btn green importExcel',
                                                    action: function (e, dt, node, config) {
                                                        $('#excelModal').modal('show', {backdrop: 'static'});
                                                    }},
                                            ],
                                            columns: [
                                                {className: 'text-center', data: 'p_name', name: 'p_name', orderable: true, searchable: true},
                                                {className: 'text-center', data: 'comit_name', name: 'comit_name', orderable: false, searchable: false},
                                                {className: 'text-center', data: 'committee_class_name', name: 'committee_class_name', orderable: false, searchable: false},
                                                {className: 'text-center', data: 'treatment_duration', name: 'treatment_duration', orderable: false, searchable: false},
                                                {className: 'text-center', data: 'need_name', name: 'need_name', orderable: false, searchable: false},
                                                {className: '', data: 'action', name: 'acrion', orderable: false, searchable: false},
                                            ]
                                        });

                                        $('#medical_form').bootstrapValidator({
                                            framework: 'bootstrap',
                                            message: '',
                                            live: true,
                                            feedbackIcons: {
                                                valid: 'fa fa-check',
                                                invalid: 'fa fa-times',
                                                validating: 'fa fa-refresh'
                                            },
                                            fields: {
                                                disability_percentage: {
                                                    validators: {
                                                    }
                                                },
                                                personal_id: {
                                                    validators: {
                                                    }
                                                },
                                                status_class: {
                                                    validators: {
                                                    }
                                                },
                                                status_desc_id: {
                                                    validators: {
                                                    }
                                                },
                                                need_id: {
                                                    validators: {
                                                    }
                                                },
                                                place_id: {
                                                    validators: {
                                                    }
                                                },
                                                notes: {
                                                    validators: {
                                                    }
                                                },
                                                date: {
                                                    validators: {
                                                    }
                                                },
                                            }
                                        });

                                        function committeePatientModal(id) {
                                            $('#medical_form').find('select').val(null).trigger('change');

                                            if (id == null) {
                                                $('#committeePatientModal').modal('show', {backdrop: 'static'});
                                                $('#medical_form').attr('action', "{{url('add_CommitteePatient')}}");
                                                $('#medical_form').find('[name="id"]').val(0);
                                                $('#medical_form').bootstrapValidator('resetForm', true);

                                            } else {
                                                $.post("{{url('showCommitteePatient')}}", {
                                                    '_token': token, 'pk_id': id,
                                                }, function (data) {
                                                    if (data.success) {
                                                        $('#medical_form').attr('action', "{{url('edit_CommitteePatient')}}");
                                                        $('#committeePatientModal').modal('show', {backdrop: 'static'});

                                                        $('#medical_form').find('[name="id"]').val(data.data.id);
                                                        $('#medical_form').find('[name="personal_id"]').val(data.data.personal_id).trigger('change');
                                                        $('#medical_form').find('[name="committee_class_id"]').val(data.data.committee_class_id).trigger('change');
                                                        $('#medical_form').find('[name="need_id"]').val(data.data.need_id).trigger('change');
                                                        $('#medical_form').find('[name="committee_id"]').val(data.data.committee_id).trigger('change');
                                                        $('#medical_form').find('[name="treatment_duration"]').val(data.data.treatment_duration);


                                                    } else {


                                                        showAlertMessage('alert-danger', 'اللجنة الطبية/ ', ' حدث خطا ! حاول مجددا');
                                                    }
                                                }, 'json').error(function (error) {
                                                    showAlertMessage('alert-danger', 'Fatal error !', 'An unknown error occured !');
                                                });
                                            }

                                        }

                                        $('#medical_submit').click(function (e) {

                                            $('#medical_form').data('bootstrapValidator').validate();
                                            if (!$('#medical_form').data('bootstrapValidator').isValid()) {
                                                return true;
                                            } else {
                                                var $form = $('#medical_form'),
                                                        formData = new FormData(),
                                                        params = $form.serializeArray();


                                                $.each(params, function (i, obj) {
                                                    formData.append(obj.name, obj.value);
                                                });
                                                $.ajax({
                                                    url: $form.attr('action'),
                                                    data: formData,
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    type: 'POST',
                                                    success: function (data) {

                                                        if (data.success) {
                                                            $('#medical_form').bootstrapValidator('resetForm', true);
                                                            $('#committeePatientModal').modal('hide');
                                                            dataTableCommitteePatient.ajax.reload(null, false);
                                                            showAlertMessage('alert-success', ' اللجنة الطبية / ', data.message);
                                                        } else {

                                                            showAlertMessage('alert-danger', 'اللجنة الطبية / ', data.message);
                                                        }
                                                    },
                                                    error: function (data) {
                                                        console.log(data);
                                                        showAlertMessage('alert-danger', 'Fatal error !', 'An unknown error occured !');
                                                    },
                                                    statusCode: {
                                                        500: function (data) {
                                                            console.log(data);
                                                        }
                                                    }
                                                });
                                            }


                                        });

                                        function deleteCommitteePatient(id) {
                                            if (!confirm('هل انت متاكد؟')) {
                                                return false;
                                            } else {
                                                $.ajax({
                                                    url: '{{url("deleteCommitteePatient")}}',
                                                    data: {id: id, _token: '{{csrf_token()}}'},
                                                    type: "POST",
                                                    success: function (data, textStatus, jqXHR) {
                                                        if (data.is_delete == false)
                                                        {

                                                            showAlertMessage('alert-danger', 'اللجنة الطبية / ', 'لا يمكنك الحذف');
                                                        }
                                                        if (data.success == true) {

                                                            dataTableCommitteePatient.ajax.reload(null, false);
                                                            showAlertMessage('alert-success', 'اللجنة الطبية / ', 'تم حذف الحقل بنجاح');

                                                        } else {
                                                            showAlertMessage('alert-danger', 'اللجنة الطبية / ', 'حدث خطأ أثناء الحذف');
                                                        }
                                                    },
                                                    error: function (data, textStatus, jqXHR) {
                                                        showAlertMessage('alert-danger', 'اللجنة الطبية / ', 'حدث خطأ أثناء الحذف');
                                                        console.log(data);
                                                    },
                                                    statusCode: {
                                                        500: function (data) {
                                                            console.log(data);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                        $('#excel_submit').click(function (e) {


                                            var $form = $('#excel_form'),
                                                    formData = new FormData(),
                                                    params = $form.serializeArray();
                                            $.each($("input[name='excel']")[0].files, function (i, file) {
                                                formData.append('name', file);
                                            });

                                            $.each(params, function (i, obj) {
                                                formData.append(obj.name, obj.value);
                                            });
                                            $.ajax({
                                                url: $form.attr('action'),
                                                data: formData,
                                                cache: false,
                                                contentType: false,
                                                processData: false,
                                                type: 'POST',
                                                success: function (data) {

                                                    if (data.success) {
                                                        dataTableCommitteePatient.ajax.reload(null, false);

                                                        $('#medical_form').bootstrapValidator('resetForm', true);
                                                        $('#excelModal').modal('hide');
                                                        showAlertMessage('alert-success', ' ملف الجريح / ', data.message);
                                                    } else {

                                                        showAlertMessage('alert-danger', 'ملف الجريح / ', ' حدث خطا ! حاول مجددا');
                                                    }
                                                },
                                                error: function (data) {
                                                    console.log(data);
                                                    showAlertMessage('alert-danger', 'Fatal error !', 'An unknown error occured !');
                                                },
                                                statusCode: {
                                                    500: function (data) {
                                                        console.log(data);
                                                    }
                                                }
                                            });



                                        });


</script>
@endsection
