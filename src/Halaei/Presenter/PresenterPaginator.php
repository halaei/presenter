<?php namespace Halaei\Presenter;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\Presenter;
use Illuminate\Database\Eloquent\Collection;

class PresenterPaginator extends ContainerWrapper implements Paginator, LengthAwarePaginator{

    function __construct(Paginator $paginator)
    {
        $this->container = $paginator;
    }

    /**
     * Get the URL for a given page.
     *
     * @param  int $page
     * @return string
     */
    public function url($page)
    {
        return $this->container->url($page);
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
        return $this->container->appends($key, $value);
    }

    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null $fragment
     * @return $this|string
     */
    public function fragment($fragment = null)
    {
        return $this->container->fragment($fragment);
    }

    /**
     * The the URL for the next page, or null.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        return $this->container->nextPageUrl();
    }

    /**
     * Get the URL for the previous page, or null.
     *
     * @return string|null
     */
    public function previousPageUrl()
    {
        return $this->container->previousPageUrl();
    }

    /**
     * Get all of the items being paginated.
     *
     * @return array
     */
    public function items()
    {
        $result = $this->container->items();

        return $this->toPresenterArray($result);
    }

    /**
     * @return PresenterCollection
     */
    public function getCollection()
    {
        $items = $this->container->items();
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
        return $this->container->firstItem();
    }

    /**
     * Get the "index" of the last item being paginated.
     *
     * @return int
     */
    public function lastItem()
    {
        return $this->container->lastItem();
    }

    /**
     * Determine how many items are being shown per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->container->perPage();
    }

    /**
     * Determine the current page being paginated.
     *
     * @return int
     */
    public function currentPage()
    {
        return $this->container->currentPage();
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages()
    {
        return $this->container->hasPages();
    }

    /**
     * Determine if there is more items in the data store.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->container->hasMorePages();
    }

    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->container->isEmpty();
    }

    /**
     * Render the paginator using a given Presenter.
     *
     * @param  \Illuminate\Contracts\Pagination\Presenter|null $presenter
     * @return string
     */
    public function render(Presenter $presenter = null)
    {
        return $this->container->render($presenter);
    }

    /**
     * Determine the total number of items in the data store.
     *
     * @return int
     */
    public function total()
    {
        return $this->container->total();
    }

    /**
     * Get the page number of the last available page.
     *
     * @return int
     */
    public function lastPage()
    {
        return $this->container->lastPage();
    }

    function __call($name, $arguments)
    {
        return call_user_func_array([$this->container, $name], $arguments);
    }

}