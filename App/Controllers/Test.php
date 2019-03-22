<?php
/**
 * Created by PhpStorm.
 * User: lukas
 * Date: 08.03.19.
 * Time: 18:52
 */

namespace App\Controllers;


class Test extends  \Core\Controller
{
    public function returner() {
        if(isset($this->routeParams['id'])){
           $file = fopen(__DIR__ . '/log.txt', 'a');
           fwrite($file, $this->routeParams['id'] . PHP_EOL);
           fclose($file);
        }
        //$this->redirect('/');
        header("location: https://www.proprofs.com/quiz-school/story.php?title=prometni-propisi-i-sigurnosna-pravila");
        exit;
    }
}
