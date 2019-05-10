<?php

namespace Janisbiz\LightOrm;

class Paginator
{
    protected $page;
    protected $itemsPerPage;
    protected $object;
    protected $options;
    protected $totalPages;
    protected $items = [];
    protected $totalItemsCount;
    protected $noCount;
    protected $haveToPaginate;
    protected $fakePaginate;

    /**
     * Paginator constructor.
     *
     * @param int $page
     * @param int $itemsPerPage
     * @param BaseModel $object
     * @param array $options
     */
    public function __construct($page, $itemsPerPage, BaseModel $object, array $options = [])
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
        $this->object = $object;
        $this->options = $options;
        $this->fakePaginate = false;
    }

    /**
     * @return $this
     */
    public function fakePaginate()
    {
        $this->fakePaginate = true;
        $this->haveToPaginate(true);

        return $this;
    }

    /**
     * @param bool $noCount
     *
     * @return bool
     */
    public function haveToPaginate($noCount = false)
    {
        $this->noCount = $noCount;
        if ($this->noCount === true) {
            if ($this->haveToPaginate === true || $this->haveToPaginate === false) {
                return $this->haveToPaginate;
            }

            $this->totalPages = null;

            if ($this->page < 1 || !$this->page) {
                $this->page = 1;
            }

            if (isset($this->options['dataQuery'])) {
                $this->object = $this->options['dataQuery'];
            }

            $this
                ->object
                ->limit($this->itemsPerPage)
                ->offset($this->page * $this->itemsPerPage - $this->itemsPerPage)
            ;

            $this->items = $this->object->find();

            if ($this->items) {
                $this->haveToPaginate = true;
            } else {
                $this->haveToPaginate = false;
            }
        } else {
            if ($this->haveToPaginate === true || $this->haveToPaginate === false) {
                return $this->haveToPaginate;
            }

            if (!$this->totalItemsCount) {
                $this->totalItemsCount = $this->object->count();
            }

            if ($this->totalItemsCount > 0) {
                $this->totalPages = (int) ceil($this->totalItemsCount / $this->itemsPerPage);
                if ($this->page > $this->totalPages) {
                    $this->page = $this->totalPages;
                }

                if ($this->page < 1 || !$this->page) {
                    $this->page = 1;
                }

                if (isset($this->options['dataQuery'])) {
                    $this->object = $this->options['dataQuery'];
                }

                $this
                    ->object
                    ->limit($this->itemsPerPage)
                    ->offset($this->page * $this->itemsPerPage - $this->itemsPerPage)
                ;

                $this->items = $this->object->find();

                if ($this->items) {
                    $this->haveToPaginate = true;
                } else {
                    $this->haveToPaginate = false;
                }
            } else {
                $this->haveToPaginate = false;
            }
        }

        return $this->haveToPaginate;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        if ($this->fakePaginate) {
            if (empty($this->items)) {
                $this->page--;
                $this->haveToPaginate = null;
                $this->haveToPaginate(true);
                $this->totalPages = $this->page;
            } else {
                $this->totalPages = $this->page + 1; // imitating paginator
            }
        }

        $pages = [];
        if (!isset($this->options['showPagesBeforeAfterCurPage'])) {
            $this->options['showPagesBeforeAfterCurPage'] = 2;
        }

        if ($this->options['showPagesBeforeAfterCurPage'] == 'unlimited') {
            for ($i = 1; $i <= $this->totalPages; $i++) {
                $pages[] = $i;
            }
        } else {
            for ($i = $this->options['showPagesBeforeAfterCurPage']; $i > 0; $i--) {
                if ($this->page - $i >= 1) {
                    $pages[$this->page - $i] = ($this->page - $i);
                }
            }

            $pages[$this->page] = $this->page;

            for ($i = 1; $i <= $this->options['showPagesBeforeAfterCurPage']; $i++) {
                if ($this->page + $i <= $this->totalPages) {
                    $pages[$this->page + $i] = ($this->page + $i);
                }
            }
        }

        return $pages;
    }

    /**
     * @return int
     */
    public function getCurPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        if ($this->fakePaginate) {
            if (empty($this->items)) {
                return $this->page; // if current items could not be resolved, don't add more pages.
            }

            return $this->page + 1; // add some pages to total.
        }

        return $this->totalPages;
    }

    /**
     * @return int|null
     */
    public function getNextPage()
    {
        if ($this->page + 1 <= $this->totalPages || ($this->noCount === true && !$this->fakePaginate)) {
            return $this->page + 1;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getPrevPage()
    {
        if ($this->page - 1 >= 1) {
            return $this->page - 1;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function &getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getTotalItemsCount()
    {
        return $this->totalItemsCount;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return BaseModel
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return bool
     */
    public function isFakePaginate()
    {
        return $this->fakePaginate;
    }
}
