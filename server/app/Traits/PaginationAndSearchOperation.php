<?php

namespace App\Traits;

use App\Helpers\Constants;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait PaginationAndSearchOperation
{
  protected function handleQuery(Request $req, Builder &$query)
  {
    $per_page       = (int) $req->get('per_page', Constants::PER_PAGE);
    $per_page       = $per_page ? $per_page : Constants::PER_PAGE;
    $req->per_page  = $per_page;

    $page           = (int) $req->get('page', Constants::PAGE);
    $page           = $page ? $page : Constants::PAGE;
    $req->page      = $page;

    $sort_dir       = Str::lower($req->get('sort_dir', Constants::SORT_DIR));
    $sort_dir       = in_array($sort_dir, ['asc', 'desc']) ? $sort_dir : Constants::SORT_DIR;
    $req->sort_dir  = $sort_dir;

    $fillable       = Arr::prepend($query->getModel()->getFillable(), $query->getModel()->getKeyName());
    $sort_by        = Str::lower($req->get('sort_by', Constants::SORT_BY));
    $sort_by        = in_array($sort_by, $fillable) ? $sort_by : Constants::SORT_BY;
    $req->sort_by   = $sort_by;

    $search         = Str::lower($req->get('search', Constants::SEARCH));
    $search         = Str::limit($search, 20);
    $req->search    = $search;

    if (!empty($search)) {

      foreach ($this->searchFields as $field) {;
        $query->orWhere($field, 'LIKE', '%' . $search . '%');
      }
    }

    foreach ($this->filterationFields as $key => $field) {

      $field_str = Str::limit($req->get($key), 20);

      if (!empty($field_str)) {
        $query->where($field, $field_str);
      }
    }

    $query->orderBy($sort_by, $sort_dir);
  }
}
