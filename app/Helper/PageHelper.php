<?php
/**
 * User: thomas
 * Date: 2021/6/4
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Helper;

use Illuminate\Database\Eloquent\Builder;

class PageHelper
{
    public $page = 1;

    public $page_size = 10;

    public $total;

    public $totalPage;

    public $list;

    /**
     * @param Builder $selector
     * @param $params
     * @return $this
     */
    public function pagination($selector, $params)
    {
        if (isset($params['page']) && (int)$params['page']) {
            $this->page = $params['page'];
        }
        if (isset($params['page_size']) && (int)$params['page_size']) {
            $this->page_size = $params['page_size'];
        }
        $this->total = $selector->count();

        $this->totalPage = (int)ceil($this->total / $this->page_size);
        $this->list = $selector->forPage($this->page, $this->page_size)->get();
        return $this;
    }

    public function pageHtml()
    {
        $pageCount = 8;
        $leftCount = $rightCount = (int)(floor($pageCount / 2));
        //偶数时
        if ($pageCount % 2 == 0) {
            $leftCount = $rightCount - 1;
        }
        if ($this->totalPage <= $pageCount) {
            $startPage = 1;
            $endPage = $this->totalPage;
        } else if ($this->page + $rightCount >= $this->totalPage) {
            $startPage = $this->totalPage - $pageCount + 1;
            $endPage = $this->totalPage;
        } else if ($this->page - $leftCount <= 0) {
            $startPage = 1;
            $endPage = $pageCount;
        } else {
            $startPage = $this->page - $leftCount;
            $endPage = $this->page + $rightCount;
        }
        $params = request()->all();
        $params['page_size'] = $this->page_size;
        $html = '<div class="pagination-list"><div class="page-container">共' . $this->total . '条 <select name="page_size" style="margin-left: 1rem;margin-right: .5rem;" id="page-size">';
        foreach ([10, 20, 50, 100] as $i) {
            $html .= '<option value="' . $i . '" ' . ($i == $this->page_size ? 'selected' : '') . '>' . $i . '</option>';
        }
        $html .= '</select> 条/页</div>';
        if ($endPage > 1) {
            $params['page'] = $this->page == 1 ? 1 : $this->page - 1;
            $html .=
                '<ul class="pagination">';
            if ($this->page > 1) {
                $html .= '<li class="page-item">' .
                    '<a class="page-link" href="' . \App\Helper\UrlHelper::instance()->to(UrlHelper::instance()->getUri(), $params) . '" aria-label="Previous">' .
                    '<span aria-hidden="true">&laquo;</span>' .
                    '<span class="sr-only">Previous</span>' .
                    '</a></li>';
                $params['page'] = 1;
                $html .= '<li class="page-item ' . ($this->page == 1 ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App\Helper\UrlHelper::instance()->to(UrlHelper::instance()->getUri(), $params) . '">首页</a></li>';
            }
            for ($i = $startPage; $i <= $endPage; $i++) {
                $params['page'] = $i;
                $html .= '<li class="page-item ' . ($this->page == $i ? 'active' : '') . '"><a class="page-link" href="' . \App\Helper\UrlHelper::instance()->to(UrlHelper::instance()->getUri(), $params) . '">' . $i . '</a></li>';
            }
            if ($this->page < $this->totalPage) {
                $params['page'] = $this->totalPage;
                $html .= '<li class="page-item ' . ($this->page == $this->totalPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App\Helper\UrlHelper::instance()->to(UrlHelper::instance()->getUri(), $params) . '">末页</a></li>';
                $params['page'] = $this->page == $endPage ? $endPage : $this->page + 1;
                $html .= '<li class="page-item ' . ($this->page == $endPage ? 'disabled' : '') . '">' .
                    '<a class="page-link" href="' . \App\Helper\UrlHelper::instance()->to(UrlHelper::instance()->getUri(), $params) . '" aria-label="Next">' .
                    '<span aria-hidden="true">&raquo;</span>' .
                    '<span class="sr-only">Next</span>' .
                    '</a></li></ul></div>';
            }
        }
        return $html;
    }
}