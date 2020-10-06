	<div class="row-fluid">
								
	<div class="list-filter border-top-0 mb-5" id="filter-list-filter">
		
	    <form id="filter-form" class="form-inline" accept-charset="utf-8" method="GET" action="<?= admin_url('thoughtco/reports/dashboard'); ?>" role="form">
		    	
			<div class="d-sm-flex flex-sm-wrap w-100 no-gutters">
		        
				@if (sizeof($this->getLocations()) > 1)
				<div class="col col-md-6 col-lg-4">
					
					<div class="filter-scope form-group pr-5 mb-2">
						
						<label for="fld-location">Location</label>
						<select name="location" class="form-control select2-hidden-accessible" id="fld-location">
							@foreach ($this->getLocations() as $key=>$location)
							<option value="{{ $key }}" @if ($key == $locationParam) selected @endif>{{ $location }}</option>'; 
							@endforeach
            			</select>
            			
            		</div>
            		
		        </div>	
				@endif  
		        
				<div class="col col-md-6 col-lg-3">
					
					<div class="filter-scope date form-group pr-5 mb-2">
						
						<label for="datepicker-formfixeddate-date-fixed-startdate">Start Date</label>
						<div id="datepicker-formfixeddate-fixed-startdate" class="control-datepicker">
                    
							<div class="input-group">
								<input type="text" id="datepicker-formfixeddate-date-fixed-startdate" class="form-control" autocomplete="off" value="{{ $startDate->format('d-m-Y') }}" data-control="datepicker" data-format="dd-mm-yyyy">
							    <span class="input-group-prepend" data-original-title="" title="">
							        <span class="input-group-icon" data-original-title="" title=""><i class="fa fa-calendar-o"></i></span> 
							    </span>
							    <input type="hidden" name="start_date" value="<?= $startDate->format('Y-m-d'); ?>" data-datepicker-value="{{ $startDate->format('Y-m-d') }}">
							</div>
            			
            			</div>						
            			
            		</div>
            		
		        </div>      
		        
				<div class="col col-md-6 col-lg-3">
					
					<div class="filter-scope date form-group pr-5 mb-2">
						
						<label for="datepicker-formfixeddate-date-fixed-enddate">End Date</label>
						<div id="datepicker-formfixeddate-fixed-enddate" class="control-datepicker">
                    
							<div class="input-group">
								<input type="text" id="datepicker-formfixeddate-date-fixed-enddate" class="form-control" autocomplete="off" value="{{ $endDate->format('d-m-Y') }}" data-control="datepicker" data-format="dd-mm-yyyy">
							    <span class="input-group-prepend" data-original-title="" title="">
							        <span class="input-group-icon" data-original-title="" title=""><i class="fa fa-calendar-o"></i></span> 
							    </span>
							    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}" data-datepicker-value="{{ $endDate->format('Y-m-d') }}">
							</div>
            			
            			</div>						
            			
            		</div>
            		
		        </div> 		        
		        		        
				<div class="col col-md-1 col-lg-1">
					
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-primary">@lang('thoughtco.kitchendisplay::default.btn_view')</button>
					
				</div>
        
	    	</div>
	    	
		</form>
		
	</div>
        
	<div class="row mx-1">
			
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-green text-white fa fa-money-bill"></i>
					<span class="stat-number">{{ $results->total_sales }}</span>
					<span class="stat-text">Total Sales</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-shopping-basket"></i>
					<span class="stat-number">{{ $results->total_orders }}</span>
					<span class="stat-text">Total Orders</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-warning text-white fa fa-line-chart"></i>
					<span class="stat-number">{{ $results->quantity_of_items }}</span>
					<span class="stat-text">Total Items</span>
				</div>
			</div>
		</div>
								 
	</div>
	
	<div class="row mx-1 mt-2">
					
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-store"></i>
					<span class="stat-number">{{ currency_format($results->pickup_orders->value) }}</span>
					<span class="stat-text">Pick-up Orders</span>
				</div>
			</div>
		</div>

		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-blue text-white fa fa-shipping-fast"></i>
					<span class="stat-number">{{ currency_format($results->delivery_orders->value) }}</span>
					<span class="stat-text">Delivery Orders</span>
				</div>
			</div>
		</div>
		
		<div class="col col-sm-4">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-counter sale">
					<i class="stat-icon  bg-danger text-white fa fa-exclamation-circle"></i>
					<span class="stat-number">{{ currency_format($results->cancelled_orders->value) }}</span>
					<span class="stat-text">Cancelled Orders</span>
				</div>
			</div>
		</div>		
								 
	</div>
	
	<div class="row mx-1 mt-4">
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-users"></i> Top Customers</h1>
				</div>				
				<div class="list-group list-group-flush">
				@foreach ($results->top_customers as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b> <em class="pull-right">{{ currency_format($item->value) }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-users"></i> Bottom Customers</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->bottom_customers as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b> <em class="pull-right">{{ currency_format($item->value) }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="row mx-1 mt-4">

		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
				    <h1 class="h4"><i class="stat-icon fa fa-shopping-bag"></i> Best Selling Items</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->top_items as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b>  <em class="pull-right">{{ $item->quantity }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
		<div class="col col-sm-6">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-shopping-bag"></i> Worst Selling Items</h1>
				</div>
				<div class="list-group list-group-flush">
				@foreach ($results->bottom_items as $item)
					<div class="list-group-item bg-transparent">
						<b>{{ $item->name }}</b>  <em class="pull-right">{{ $item->quantity }}</em>
					</div>
				@endforeach
				</div>
			</div>
		</div>
		
	</div>
	
	<div class="row mx-1 mt-4">
				
		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
			<div class="card-title">
				<h1 class="h4"><i class="stat-icon fa fa-calendar"></i> Orders by Day</h1>
			</div>
			<div class="list-group list-group-flush">
            @foreach ($results->orders_by_day as $key=>$item)
				<div class="list-group-item bg-transparent">
					<b>{{ date('l', strtotime("Sunday + $key Days")) }}</b> <em class="pull-right">{{ currency_format($item->value) }} / {{ $item->count }}</em>
				</div>
			@endforeach
				</div>
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
            <div class="card-title">
                <h1 class="h4"><i class="stat-icon fa fa-clock"></i> Orders by Hour</h1>
            </div>
			<div class="list-group list-group-flush">
            @foreach ($results->orders_by_hour as $key=>$item)
                <div class="list-group-item bg-transparent">
					<b>{{ str_pad($key, 2, '0', STR_PAD_LEFT).':00 - '.str_pad($key+1, 2, '0', STR_PAD_LEFT).':00' }}</b> <em class="pull-right">{{ currency_format($item->value) }} / {{ $item->count }}</em>
				</div>
            @endforeach
				</div>
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-stream"></i> Orders by Category</h1>
				</div>
			<div class="list-group list-group-flush">
            @foreach ($results->orders_by_category as $key=>$item)
				<div class="list-group-item bg-transparent">
					<b>{{ $item->name }}</b> <em class="pull-right">{{ currency_format($item->value) }} / {{ $item->count }}</em>
				</div>
			@endforeach
				</div>
			</div>
		</div>

		<div class="col col-sm-3">
			<div class="card bg-light p-3 shadow-sm">
				<div class="card-title">
					<h1 class="h4"><i class="stat-icon fa fa-money-check-alt"></i> Orders by Payment Type</h1>
				</div>
            <div class="list-group list-group-flush">
            @foreach ($results->orders_by_payment_method as $key=>$item)
				<div class="list-group-item bg-transparent">
					<b>{{ $item->name }}</b> <em class="pull-right"> {{ currency_format($item->value) }} / {{ $item->count }}</em>
				</div>
			@endforeach
				</div>
			</div>
		</div>
				
	</div>
		    	    
</div>
