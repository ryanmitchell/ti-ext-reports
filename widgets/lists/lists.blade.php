	<div class="card-title">
		<h6 class="widget-title"><i class="stat-icon {{ $listIcon }}"></i> @lang($listLabel)</h6>
	</div>
	<div class="list-group list-group-flush">
	@foreach ($listItems as $item)
		<div class="list-group-item bg-transparent">
			<b>{{ $item->name }}</b> <em class="pull-right">{{ $item->value }}</em>
		</div>
	@endforeach
	</div>
