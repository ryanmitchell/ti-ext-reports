<?php

namespace Thoughtco\Reports\Models;

use Igniter\Flame\Database\Traits\Validation;
use Model;

class QueryBuilder extends Model
{
    use Validation;

    /**
     * @var string The database table name
     */
    protected $table = 'thoughtco_reportbuilder';

    public $timestamps = TRUE;

    public $casts = [
        'builderjson' => 'array',
        'list_columns' => 'array',
        'csv_columns' => 'array',
    ];
    
    protected $fillable = ['title', 'builderjson'];
    
    public $rules = [
        ['title', 'lang:thoughtco.reports::default.label_title', 'required|string'],
        ['builderjson.*', 'lang:thoughtco.reports::default.label_rules', 'required'],
    ];
}
