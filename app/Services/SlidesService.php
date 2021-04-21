<?php
namespace App\Services;
 
use App\Models\Admin\Slide;
 
class SlidesService { 

    public function slides()  {
        return Slide::all();
    }

    public function direcaoImagem()  {
        return rand(0,2);
    }
    
}