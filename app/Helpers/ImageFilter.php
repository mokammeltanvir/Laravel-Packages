<?php

namespace App\Helpers;

use Intervention\Image\Filters\FilterInterface;

class ImageFilter implements FilterInterface
{
    const BLUR_VALUE = 15;
    private $blur;

    public function __construct($blur = null)
    {
        $this->blur = $blur ?? self::BLUR_VALUE; // 15
    }

    /**
     * Applies filter effects to given image
     *
     * @param  Intervention\Image\Image $image
     * @return Intervention\Image\Image
     */
    public function applyFilter(\Intervention\Image\Image $image)
    {
        return $image->fit(300, 300)
        ->blur($this->blur)->greyscale();
    }
}