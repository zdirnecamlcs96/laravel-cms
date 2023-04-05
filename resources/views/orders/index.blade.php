@extends('adminlte::page')
@section('plugins.Bootbox', true)
@section('plugins.Datatables', true)


@section('css')
<!-- Custom CSS -->

@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <div class="col-auto p-0">
                <h3 class="card-title">Orders</h3>
            </div>
        </div>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="form-group">
            <div class="row mb-3" id="filter-section">
                <div class="col-12 form-inline">
                    <div class="form-group mr-5">
                        <label class="mr-3">Pickup Type :</label>
                        <select class="form-control" name="pickup_type" id="pickup_type">
                            <option value="">All</option>
                            @foreach (Order::PICKUP_TYPES as $value => $type)
                                <option value="{{ $value }}" @selected(request()->query('pickup_type') === $value)>{{ Str::headline($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-5">
                        <label class="mr-3">Status :</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">All</option>
                            @foreach (Order::ORDER_STATUS as $key => $type)
                                @if ($type !== Order::PENDING)
                                    <option value="{{ $type }}" @selected(request()->query('status') === $type)>{{ Str::headline($type) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="form-group mr-5">
                        <label class="mr-3">Min Total Price :</label>
                        <input type='number' class="form-control" name="min_price" id="min_price" />
                    </div>
                    <div class="form-group">
                        <label class="mr-3">Max Total Price :</label>
                        <input type='number' class="form-control" name="max_price" id="max_price"/>
                    </div> --}}
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-inline">
                    <div class="form-group">
                        <div class="input-group mr-5" data-target-input="nearest">
                            <input type="text" class="form-control" style="padding: 0.375rem 0.75rem !important;" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <div class="input-group mr-5" data-target-input="nearest">
                            <input type="text" class="form-control end_date_picker" style="padding: 0.375rem 0.75rem !important;" id="end_date" name="end_date" placeholder="End Date" autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary mr-3" id="filter-btn">Filter</button>
                            <button class="btn btn-default" id="reset-filter-btn">Reset Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-2 col-sm-6">
                    <div class="info-box bg-info">
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sales ( MYR )</span>
                            <span id="totalsales" class="info-box-number count-decimal"  data-value="0"> - </span>
                        </div>
                    </div>
                </div>
            </div>
            <table class="datatable table">
                <thead>
                    <tr>
                        <th>Order No</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Pickup Type</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Created At</th>
                        <th>Last Modified</th>
                        @foreach ($hiddenFields as $name => $value)
                        <th scope="col"><a class="list-sort text-muted">{{ __($name) }}</a></th>
                        @endforeach
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_no ?? '-' }}</td>
                        <td>{{ $order->customer_name ?? '-' }}</td>
                        <td>{{ $order->customer_email ?? '-' }}</td>
                        <td>{{ $order->pickup_type_display ?? '-' }}</td>
                        <td>{{ $order->status ?? '-'}}</td>
                        <td>{{ $order->total ?? '-'}}</td>
                        <td>{{ $order->created_at ?? '-'}}</td>
                        <td>{{ $order->formatted_billing_address ?? '-'}}</td>
                        <td>{{ $order->formatted_shipping_address ?? '-'}}</td>
                        <td>
                            @can('order_read')
                            <a class="btn btn-sm btn-info" href="{{ route('admin.orders.show', $order) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('order_update')
                            <a class="btn btn-sm btn-warning" href="{{ route('admin.orders.edit', $order) }}">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@section('js')
@php
    $hiddenColumns = [];
    foreach ($hiddenFields as $field){
        array_push($hiddenColumns,[ "targets" => $field, "visible" => false, "sortable" => false ]);
    }
    $exportTitle = "CLM Orders Report ".now()->toDateTimeString();
@endphp
<script>
    $('body').on('click', '.record-delete', function(e) {
        e.preventDefault()
        var csrfField = document.createElement("input");
        csrfField.setAttribute("type", "hidden");
        csrfField.setAttribute("name", "_token");
		csrfField.setAttribute("value", "{{ csrf_token() }}");
		bootbox.confirm({
			message: "<form class='bootbox-form' id='coupon-delete' method='POST'>\
                        <input name='_method' type='hidden' value='DELETE'>\
                        <p>Are you sure to remove this admin?</p>\
                    </form>",
			buttons: {
				confirm: {
					label: 'Yes',
					className: 'btn-success'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger'
				}
			},
			callback: function (result) {
				if (result) {
                    var url = $(e.currentTarget).data('action-url');

                    $('#coupon-delete').attr('action', url);
                    $('#coupon-delete').append(csrfField);
                    $('#coupon-delete').submit();
				}
			}
		});
    })

    $(document).ready(function(){
        const datatable = $('.datatable').DataTable({
            dom:'<"mb-3" r><"row mb-3" <"col">><"row" <"col-sm-12 col-md-6" <"d-flex align-items-center"  <"mr-3" l> <B> >><"col-sm-12 col-md-6" f>> <"table-responsive mb-3" t> <"row" <"col-sm-12 col-md-6" i><"col-sm-12 col-md-6" p>>',
            serverSide: true,
            ordering: true,
            searchDelay: 1000,
            processing: true,
            filter: true,
            paging:true,
            isShowPaging:true,
            // order:[],
            columns:[
                // {
                //     data: null,
                //     orderable:false,
                //     render: (data, type, row, meta) => {
                //         return meta.row + 1 + meta.settings._iDisplayStart
                //     }
                // },
                { data: "order_no" },
                { data: "customer_name" , width:"15%"},
                { data: "customer_email" },
                { data: "pickup_type_display" },
                { data: "status" },
                { data: "total" },
                { data: "created_at" },
                { data: "last_modified", render: (data, type, row, meta) => {
                    return data?.created_at ? `${(new Date(data?.created_at)).toLocaleString()} (${data?.causer?.name || 'System'})` : null
                }},
                // Hidden fields
                @foreach ($hiddenFields as $field)
                    { data: "{{ $field }}", visible: false, sortable: false },
                @endforeach
                {
                    data: null,
                    orderable:false,
                    render: (data, type, row, meta) => {

                        let viewUrl = "{{ route('admin.orders.show', 'placeholder') }}"
                        let editUrl = "{{ route('admin.orders.edit', 'placeholder') }}"
                        return `
                            @can('order_read')
                                <a class="btn btn-sm btn-info" href="${viewUrl.replace('placeholder',data.id)}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan
                            @can('order_update')
                                <a class="btn btn-sm btn-warning" href="${editUrl.replace('placeholder',data.id)}">
                                    <i class="fas fa-pen"></i>
                                </a>
                            @endcan
                        `
                    }
                },
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            ajax: {
                    url: "{{ route('admin.order.datatable') }}",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: function ( d ) {
                        d.filter = {
                            min_price: $('#min_price').val(),
                            max_price: $('#max_price').val(),
                            pickup_type: $("#pickup_type").val(),
                            status: $("#status").val(),
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                        }
                    },
                    dataSrc: (json) => {
                        // console.log('here',json);
                        // if (json.data.length <= 0 && json.recordsFiltered > 0)
                        // {
                        //     ajaxDt.state.clear()
                        //     ajaxDt.draw()
                        // }
                        // $('#totalOrder').text(json.recordsFiltered)
                        return json.data
                    },
                    complete: function (data) {
                        // console.log(data['responseJSON']);
                        let response = data['responseJSON'];
                        // console.log($('#totalsales'));
                        $('#totalsales').data('value',response['totalSales']);
                        // $('#totalsales').trigger('refresh');
                        $('.count-decimal').each(function () {
                            $(this).prop('Counter', 0).animate({
                                    Counter: $(this).data('value')
                                }, {
                                duration: 1000,
                                easing: 'swing',
                                step: function (now) {
                                    $(this).text(this.Counter.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                                },
                                complete: function() {
                                    $(this).text(this.Counter.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                                }
                            });
                        });
                        // console.log(data,'this is data from ajax')
                        // return data

                    },
                    error: (error) => {
                        console.log(error)
                    }
                },
                buttons: [

                    {
                        extend: 'csv',
                        @if(isset($exportTitle))
                            title:"{{$exportTitle}}",
                        @endif
                        exportOptions: {
                            columns: ':not(:last-child)',
                        },
                        action: ajaxExportAction
                    },
                    {
                        extend: 'excel',
                        @if(isset($exportTitle))
                            title: "{{$exportTitle}}",
                        @endif
                        exportOptions: {
                            columns: ':not(:last-child)',
                        },
                        action: ajaxExportAction
                    },
                    //  {
                    //     extend: 'pdfHtml5',
                    //     exportOptions: {
                    //         columns: ':not(:last-child)',
                    //     }
                    // },
                ]
        });

        initDatatableEvents(datatable)

        $('#filter-btn').on('click',function () {
            datatable.ajax.reload()
        });

        $('#reset-filter-btn').on('click',function(){
            $('#start_date').val('');
            $('#end_date').val('');
            $('#filter-section').find('input, select').val('');
            datatable.ajax.reload();
        });

        $( "#start_date" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            endDate: new Date(),
            minDate:0

        }).on("changeDate", function (e) {
                var dt2 = $('#end_date');
                console.log(dt2);
                var startDate = $(this).datepicker('getDate');
                //add 30 days to selected date
                // startDate.setDate(startDate.getDate() + 30);
                var minDate = $(this).datepicker('getDate');
                var dt2Date = dt2.datepicker('getDate');
                //difference in days. 86400 seconds in day, 1000 ms in second
                var dateDiff = (dt2Date - minDate)/(86400 * 1000);

                //dt2 not set or dt1 date is greater than dt2 date
                if (dt2Date == null || dateDiff < 0) {
                        console.log('minDate',minDate);
                        dt2.datepicker('setDate', minDate);
                }
                //dt1 date is 30 days under dt2 date
                else if (dateDiff > 30){
                        dt2.datepicker('setDate', startDate);
                }
                //sets dt2 maxDate to the last day of 30 days window
                dt2.datepicker('setStartDate', startDate);

                //first day which can be selected in dt2 is selected date in dt1
                dt2.datepicker('option', 'minDate', minDate);

        });


        $( "#end_date" ).datepicker({
            format: "dd/mm/yyyy",
            changeMonth: true,
            changeYear: true,
            minDate:0,
            endDate: new Date()
        });

    });

</script>

{{-- @include('shared.components.datatable',
    [
        'columnsVariable' => $hiddenColumns,
        'exportTitle' => $exportTitle
    ]
)
--}}


@endsection
