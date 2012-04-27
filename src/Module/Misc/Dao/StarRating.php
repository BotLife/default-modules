<?php

namespace Botlife\Module\Misc\Dao;

class StarRating
{
    
    public function getRating($rating, \Botlife\Application\Colors $color = null)
    {
        if (!$color) {
            $color = new \Botlife\Application\Colors;
        }
        $rating = round($rating, 0);
        $str = $color(3, str_repeat('★', $rating));
        $str .= $color(12, str_repeat('☆', 5 - $rating));
        return $str;
    }
    
}