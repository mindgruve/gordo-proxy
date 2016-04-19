<?php

namespace Poncho;

class BlogPostViewModel extends BlogPost
{
    /**
     * @var ImageViewModel
     */
    protected $image;

    /**
     * @var MarkdownHelper
     */
    protected $markdownHelper;

    /**
     * @param MarkdownHelper $markdownHelper
     */
    public function __construct(MarkdownHelper $markdownHelper)
    {
        $this->markdownHelper = $markdownHelper;
    }

    /**
     * @return string
     */
    public function renderHTML()
    {
        return $this->markdownHelper->markdown2HTML($this->content);
    }

}