<?php

namespace Poncho;

class ImageViewModel extends Image
{

    /**
     * @var ThumbnailHelper
     */
    protected $thumbnailHelper;

    /**
     * @param ThumbnailHelper $thumbnailHelper
     */
    public function __construct(ThumbnailHelper $thumbnailHelper)
    {
        $this->thumbnailHelper = $thumbnailHelper;
    }

}