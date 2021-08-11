## Reports

Add reporting and data interrogation to your TastyIgniter install.

This is free and doesn't require a license, but you can [donate to Ryan](https://github.com/sponsors/ryanmitchell), the developer behind it, to show your appreciation.

### Installation

1. Add to extensions/thoughtco/reports inside Tasty Igniter install.
2. Enable extension in Systems -> Extensions

## Usage

After installation a new Reports menu will be added to the admin sidebar, giving links to the Reports Dashboard and Query Builder.

### Dashboard

The Dashboard is a widget based view giving some high level overviews of reports in a configurable date range. Widgets can be changed, moved, and deleted in a similar way to the main admin dashboard. 

### Query Builder
The query builder allows you to generate and save custom queries that create an exportable, searchable table of data. Once set up this queries can be accessed at will with data auto-updated.

## Extending

This extension has been designed to allow extending of the Querybuilder queries and fields.

### Adding rule options

Through your extension add rules by listening for the core `admin.form.extendFieldsBefore` event. For example:

```php
Event::listen('admin.form.extendFieldsBefore', function (Form $form) {
    
    if ($form->model instanceof \Thoughtco\Reports\Models\QueryBuilder) {
        
        $form->fields['builderjson']['filters']['\Admin\Models\Customers_model']['filters'][] = [
            'id' => 'customers.loyaltypoints',
            'label' => 'Loyalty Points',
            'type' => 'integer',
            'operators' => [
                'equal', 'not_equal',
                'less', 'less_or_equal',
                'greater', 'greater_or_equal',
            ],
        ];
        
    }

});
```

### Extending where fields

Through your extension add rules by listening for the core `thoughtco.reports.fieldToQuery` event, and apply logic on the basis of the $field and $controller . For example:

```php
Event::listen('thoughtco.reports.fieldToQuery', function ($controller, $query, $field, $operator, $value, $condition) {

	if (!in_array(get_class($query->getModel()), ['Admin\Models\Customers_model']))
		return;

	if ($field == 'customers.loyaltypoints') {
		$query->where('loyalty_points', $operator, $value);	
	}

});
```

### Extending overall query

Through your extension add rules by listening for the core `extendQuery` event, and apply logic on the basis of the $query, $modelName and $controller . For example:

```php
Event::listen('thoughtco.reports.extendQuery', function($controller, $query, $modelName) {
                        
	// we only care about certain models by default - this allows others to extend
	if (!in_array($modelName, ['\Admin\Models\Orders_model', '\Admin\Models\Customers_model']))
		return;
                
	$query->selectRaw('*, CONCAT(first_name, " ", last_name) as customer_name');

});
```
