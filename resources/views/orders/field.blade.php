<div class="row justify-content-center">

    <div class="card col-md-10">
        <div class="card-header">
            <h3 class="card-title">Order & Account Information </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">

                <div class="col-6">
                    <div class="row">
                        <div class="form-group col-12">
                            <h3 class="card-title">Order Information </h3>
                        </div>
                        <div class="form-group col-12">
                            <label>Order Date</label>
                            <input type="text" class="form-control" value="{{\Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i:s')}}" readonly />
                        </div>
                        <div class="form-group col-12">
                            <label>Order Status</label>
                            @if($mode=='show')
                            <input type="text" class="form-control" value="{{$order->status}}" readonly>
                            @else
                            <select id="status" class="form-control action-btn">
                                @foreach($status_options as $key => $option)
                                    @if($key === Order::PENDING && $order->status !== $key ? false : true)
                                        <option value="{{ $key }}" @selected($order->status == $key) @disabled($key === Order::PENDING)>{{ $key }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @endif
                        </div>
                        @if($mode!= 'show')
                        <div class="form-group col-12">
                            <label>Tracking Code</label>
                            <button type="button" class="action-btn btn btn-info btn-sm form-control" data-toggle="modal" data-target="#modal"> <i class="fas fa-fw fa-edit"></i></button>
                        </div>
                        @endif
                        <div class="form-group col-12">
                            <label>Order Type</label>
                            <input type="text" class="form-control" value="{{$order->pickup_type_display}}" readonly />
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="row">
                        <div class="form-group col-12">
                            <h3 class="card-title">Account Information </h3>
                        </div>
                        <div class="form-group col-12">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" value="{{$order->customer_name}}" readonly />
                        </div>
                        <div class="form-group col-12">
                            <label>Customer Email</label>
                            <input type="text" class="form-control" value="{{$order->customer_email}}" readonly />
                        </div>
                        <div class="form-group col-12">
                            <label>Customer Phone</label>
                            <input type="text" class="form-control" value="{{$order->customer_phone_code ?? ""}}{{$order->customer_phone ?? ""}}" readonly />
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <div class="card col-md-10">
        <div class="card-header">
            <h3 class="card-title">Address Information</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>

        <div class="card-body">
            <div class="row form-group">

                {{Form::model($order,['route'=>['admin.orders.update',$order->id]] )}}
                @csrf
                @method('PUT')

                <div class="col-6">
                    <div class="row">
                        <div class="form-group col-12">
                            <h3 class="card-title">Billing Address</h3>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing Name</label>
                            @error('billing_name') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_name" class="form-control" value="{{$order->billing_name}}"  {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label for="contact">Billing Contact</label>

                            <div class="row col-12">
                                {{Form::select('billing_phone_code', $countries, null, ['class'=>'form-control col-4' ,'disabled' => $mode == 'show'])}}
                                @error('billing_phone_code') <small class="text-danger">{{$message}} </small> @enderror
                                {{Form::text('billing_phone',null,['class'=>'form-control col-8','placeholder'=>'Contact','disabled' => $mode == 'show'])}}
                                @error('billing_phone') <small class="text-danger">{{$message}} </small> @enderror

                            </div>

                        </div>
                        <div class="form-group col-12">
                            <label>Billing Street Address 1</label>
                            @error('billing_street_address1') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_street_address1" class="form-control" value="{{$order->billing_street_address1}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing Street Address 2</label>
                            @error('billing_street_address2') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_street_address2" class="form-control" value="{{$order->billing_street_address2}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing City</label>
                            @error('billing_city') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_city" class="form-control" value="{{$order->billing_city}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing Postcode</label>
                            @error('billing_postcode') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="number" name="billing_postcode" class="form-control" value="{{$order->billing_postcode}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing State</label>
                            @error('billing_state') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_state" class="form-control" value="{{$order->billing_state}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing Country</label>
                            @error('billing_country') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_country" class="form-control" value="{{$order->billing_country}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Billing Email</label>
                            @error('billing_email') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="billing_email" class="form-control" value="{{$order->billing_email}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="row">
                        <div class="form-group col-12">
                            <h3 class="card-title">Shipping Address</h3>
                        </div>
                        @if($order->pickup_type == "self_pickup")
                        <div class="form-group col-12">
                            <label>Shipping Method</label>
                            <input type="text" name="shipping_method" class="form-control" value="Self Collect" readonly {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                        <div class="form-group col-12">
                            <label>Collector Name</label>
                            @error('collector_name') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="collector_name" class="form-control" value="{{$order->collector_name}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                        <div class="form-group col-12">
                            <label>Collector Contact</label>
                            @error('collector_phone') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="collector_phone" class="form-control" value="{{$order->collector_phone}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                         <div class="form-group col-12">
                            <label>Collector Email</label>
                            @error('collector_email') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="email" name="collector_email" class="form-control" value="{{$order->collector_email}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                        @else
                        <div class="form-group col-12">
                            <label>Shipping Method</label>
                            <input type="text" name="shipping_method" class="form-control" value="Standard Shipping" readonly {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                        <div class="form-group col-12">
                            <label>Shipping Name</label>
                            @error('shipping_name') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_name" class="form-control" value="{{$order->shipping_name}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>

                        <div class="form-group col-12">
                            <label for="contact">Shipping Contact</label>
                            <div class="row col-12">
                                {{Form::select('shipping_phone_code', $countries, null, ['class'=>'form-control col-4' ,'disabled' => $mode == 'show'])}}
                                @error('shipping_phone_code') <small class="text-danger">{{$message}} </small> @enderror
                                {{Form::text('shipping_phone',null,['class'=>'form-control col-8','placeholder'=>'Contact' ,'disabled' => $mode == 'show'])}}
                                @error('shipping_phone') <small class="text-danger">{{$message}} </small> @enderror
                            </div>
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping Street Address 1</label>
                            @error('shipping_street_address1') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_street_address1" class="form-control" value="{{$order->shipping_street_address1}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping Street Address 2</label>
                            @error('shipping_street_address2') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_street_address2" class="form-control" value="{{$order->shipping_street_address2}}" {{($mode == 'show') ?'disabled' : ''}} />
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping City</label>
                            @error('shipping_city') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_city" class="form-control" value="{{$order->shipping_city}}" {{($mode == 'show') ?'disabled' : ''}} />
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping Postcode</label>
                            @error('shipping_postcode') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="number" name="shipping_postcode" class="form-control" value="{{$order->shipping_postcode}}" {{($mode == 'show') ?'disabled' : ''}} />
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping State</label>
                            @error('shipping_state') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_state" class="form-control" value="{{$order->shipping_state}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping Country</label>
                            @error('shipping_country') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_country" class="form-control" value="{{$order->shipping_country}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        <div class="form-group col-12">
                            <label>Shipping Email</label>
                            @error('shipping_email') <small class="text-danger">{{$message}} </small> @enderror
                            <input type="text" name="shipping_email" class="form-control" value="{{$order->shipping_email}}" {{($mode == 'show') ?'disabled' : ''}}/>
                        </div>
                        @endif

                    </div>
                </div>
                @if($mode !== 'show')
                <div class="col-12">
                    <button type="submit" class="btn btn-success">Update Address</button>
                </div>
                @endif
                {{Form::close()}}

            </div>
        </div>
    </div>


    <div class="card col-md-10">
        <div class="card-header">
            <h3 class="card-title">Items Ordered </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row form-group">

                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table order-details-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Line Total </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                        @if ($item->sku_details)
                                        <ul class="list-inline product-options">
                                            <label>{{ $item->sku_details}}</label>
                                        </ul>
                                        @endif

                                    </td>

                                    <td>
                                        RM {{ $item->unit_price }}
                                    </td>

                                    <td>
                                        {{ $item->quantity }}
                                    </td>

                                    <td>
                                        RM {{ $item->line_total }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="3">Sub Total</td>
                                    <td colspan="1">RM {{$order->sub_total}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Shipping Fee</td>
                                    <td colspan="1">RM {{$order->shipping_cost}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Discount</td>
                                    <td colspan="1">- RM {{$order->discount}} {{$order->discount_code ? "[". $order->discount_code ."]" : ""}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td colspan="1">RM {{$order->total}}</td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
