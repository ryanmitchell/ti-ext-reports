<?php

namespace Thoughtco\Reports\FormWidgets;

use Event;
use Thoughtco\Reports\Parser\QueryBuilderParser;

class ReportsTable extends \Admin\FormWidgets\DataTable
{
    protected $defaultAlias = 'reportstable';

    public function getDataTableRecords($offset, $limit, $search)
    {
        if (!($model = $this->config['attributes']['model']))
            abort(404);   
        
        $klass = new $model->builderjson['model']();
        
        $parser = new QueryBuilderParser();
        
        $table = $klass->newQuery();
        $query = $parser->parse(json_encode($model->builderjson['rules']), $table);
        
        $this->fireSystemEvent('thoughtco.reports.extendQuery', [$query, $model->builderjson['model']] );
        
        if (strlen($search)) {
            $query->search($search, $this->searchableFields);
        }

        if (is_array($this->defaultSort)) {
            [$sortColumn, $sortBy] = $this->defaultSort;
            $query->orderBy($sortColumn, $sortBy);
        }
        
        $page = ($offset / $limit) + 1;

        return $query->paginate($limit, ['*'], 'page', $page);
    }
}
