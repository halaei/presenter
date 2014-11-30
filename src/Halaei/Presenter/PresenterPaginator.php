<?php namespace Halaei\Presenter;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\Presenter;
use Illuminate\Database\Eloquent\Collection;

class PresenterPaginator implements Paginator, LengthAwarePaginator{

    /**
     * @var Paginator | LengthAwarePaginator
     */
    private $paginator;

    function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Get the URL for a given page.
     *
     * @param  int $page
     * @return string
     */
    public function url($page)
    {
        return $this->paginator->url($page);
    }

    /**
     * Add a set of query string values to the paginator.
     *
     * @param  array|string $key
     * @param  string|null $value
     * @return $this
     */
    public function appends($key, $value = null)
    {
        return $this->paginator->appends($key, $value);
    }

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null $fragment
     * @return $this|string
     */
    public function fragment($fragment = null)
    {
        return $this->paginator->fragment($fragment);
    }

    /**
     * The the URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        return $this->paginator->nextPageUrl();
    }

    /**
     * Get the URL for the previous page, or null.
     *
     * @return string|null
     */
    public function previousPageUrl()
    {
        return $this->paginator->previousPageUrl();
    }

    /**
     * Get all of the items being paginated.
     *
     * @return array
     */
    public function items()
    {
        $result = $this->paginator->items();
        foreach ($result as &$item) {
            if($item instanceof Model)
                $item = $item->present();
        }
        return $result;
    }

    /**
     * @return PresenterCollection
     */
    public function getCollection()
    {
        $items = $this->paginator->items();
        $collection = new Collection($items);
        return new PresenterCollection($collection);
    }

    /**
     * Get the "index" of the first item being paginated.
     *
     * @return int
     */
    public function firstItem()
    {
        return $this->paginator->firstItem();
    }

    /**
     * Get the "index" of the last item being paginated.
     *
     * @return int
     */
    public function lastItem()
    {
        return $this->paginator->lastItem();
    }

    /**
     * Determine how many items are being shown per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->paginator->perPage();
    }

    /**
     * Determine the current page being paginated.
     *
     * @return int
     */
    public function currentPage()
    {
        return $this->paginator->currentPage();
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->paginator->hasPages();
    }

    /**
     * Determine if there is more items in the data store.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->paginator->hasMorePages();
    }

    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->paginator->isEmpty();
    }

    /**
     * Render the paginator using a given Presenter.
     *
     * @param  \Illuminate\Contracts\Pagination\Presenter|null $presenter
     * @return string
     */
    public function render(Presenter $presenter = null)
    {
        return $this->paginator->render($presenter);
    }

    /**
     * Determine the total number of items in the data store.
     *
     * @return int
     */
    public function total()
    {
        return $this->paginator->total();
    }

    /**
     * Get the page number of the last available page.
     *
     * @return int
     */
    public function lastPage()
    {
        return $this->paginator->lastPage();
    }

}