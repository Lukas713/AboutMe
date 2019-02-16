<?php
/**
 * Created by PhpStorm.
 * User: Korisnik
 * Date: 16/02/2019
 * Time: 17:08
 */

namespace App;

use App\Models\Posts;

class Paginator
{
    /**
     * get maximum number of pages
     * @return int
     */
    public static function getPageNumber(){
        $pageNumber = Posts::countPosts();
        return ceil($pageNumber->number / Config::LIMIT_PAGES);
    }

    /**
     * get offset for sql query
     * @param int
     * @return int
     */
    public static function getOffset($page = 1){
        $offset = ($page * Config::LIMIT_PAGES) - Config::LIMIT_PAGES;
        return $offset;
    }
}