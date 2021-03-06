<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 16/02/2019
 * Time: 17:08
 */

namespace App;

use App\Models\Posts;
use Core\Model;
use App;

class Paginator
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * get maximum number of pages
     * @return int
     */
    public function getPageNumber($id = null){
        $pageNumber = $this->model->countPosts($id);
        if($pageNumber->number < 1){
            return 1;
        }
        return ceil($pageNumber->number / $this->model->returnLimitPages());
    }

    /**
     * get offset for sql query
     * @param int
     * @return int
     */
    public function getOffset($page = 1){
        if($page < 1){
            $page = 1;
        }else if($page > $this->model->returnLimitPages()){
            $page = $this->model->returnLimitPages();
        }
        $offset = ($page * $this->model->returnLimitPages()) - $this->model->returnLimitPages();
        return $offset;
    }

    public function getModelClassName(){
        return get_class($this->model);
    }
}