<?php

namespace Poncho;

class MarkdownHelper
{

    /**
     * @param $markdown
     * @return string
     */
    public function markdown2HTML($markdown)
    {
        return '<h1>Hello World</h1>';
    }

    /**
     * @param $html
     * @return string
     */
    public function HTML2Markdown($html){
        return '#Hello World';
    }

}