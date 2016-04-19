<?php

namespace Poncho;

class BlogPost
{

    /**
     * @var Image
     */
    protected $featuredImage;

    /**
     * @var string
     */
    protected $content;

    /**
     * @param Image $image
     * @return $this
     */
    public function setFeaturedImage(Image $image)
    {
        $this->featuredImage = $image;

        return $this;
    }

    /**
     * @return Image
     */
    public function getFeaturedImage()
    {
        return $this->featuredImage;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}