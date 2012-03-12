<?php
/**
 * Lightspeed high-performance hiphop-php optimized PHP framework
 *
 * Copyright (C) <2011> by <Priit Kallas>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Paginator
 */

/**
 * Paginator can be used to paginate a set of data.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Paginator
 */
class Paginator implements IteratorAggregate, Countable {

	/**
	 * The original data as given in constructor or setter.
	 * 
	 * @var mixed
	 */
	protected $data;

	/**
	 * Datasource to paginate.
	 *
	 * @var DataSource
	 */
	protected $source;

	/**
	 * The number of items on each page
	 *
	 * @var integer
	 */
	protected $itemsPerPage = 10;

	/**
	 * Current page number.
	 *
	 * This is kept in the range from one to the number of pages.
	 *
	 * @var integer
	 */
	protected $page = 1;

	/**
	 * The maximum number of item in which case the user is allowed to view
	 * all results at once.
	 *
	 * @var integer
	 */
	protected $showAllMaximumItemCount = 500;

	/**
	 * Should all the results be returned on a single page.
	 *
	 * @var boolean
	 */
	protected $showAll = false;

	/**
	 * Constructs the paginator, optionally sets the data to use.
	 *
	 * The data is encapsulated in a {@see DataSource} of correct type. For
	 * example if you pass in an array, {@see ArrayDataSource} is used. Yoy can
	 * later get the data-source object by {@see Paginator::getSource()}.
	 *
	 * @param mixed $data Data to paginate
	 * @param integer $page Current page number
	 */
	public function __construct($data = null, $page = 1) {
		if ($data !== null) {
			$this->setData($data);
		}

		if ($page !== 1) {
			$this->setPage($page);
		}
	}

	/**
	 * Returns string hash of the paginator.
	 *
	 * This hash is the same for given page and configuration an can be used to
	 * cache information.
	 */
	public function  __toString() {
		return $this->page.'.'.
			$this->itemsPerPage.'.'.
			$this->showAllMaximumItemCount.'.'.
			$this->source->count();
	}

	/**
	 * Sets the number of items to display per page.
	 *
	 * This is forced to be atleast one.
	 *
	 * @param integer $itemsPerPage Number of items to show on a single page
	 * @return Paginator The paginator object for chaining actions.
	 */
	public function setItemsPerPage($itemsPerPage) {
		$this->itemsPerPage = max((int)$itemsPerPage, 1);
		
		return $this;
	}

	/**
	 * Returns the number of items displayed per page.
	 *
	 * @return integer
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}

	/**
	 * Sets the maximum number of item in which case the user is allowed to view
	 * all results at once.
	 *
	 * Set this to -1 to always allow. Set it to 0 to never allow.
	 *
	 * @param integer $itemCount The number of items.
	 * @return Paginator The paginator object for chaining actions.
	 */
	public function setShowAllMaximumItemCount($itemCount) {
		$this->showAllMaximumItemCount = (int)$itemCount;

		return $this;
	}

	/**
	 * Returns the maximum number of item in which case the user is allowed to
	 * view all results at once.
	 *
	 * @return integer The item count
	 */
	public function getShowAllMaximumItemCount() {
		return $this->showAllMaximumItemCount;
	}

	/**
	 * Returns whether it is currently allowed to show all results.
	 *
	 * It is allowed then {@see Paginator::setShowAllMaximumItemCount()} has
	 * been set to -1 or there are fewer or same number of items as set by it.
	 * 
	 * @return boolean Is showing all results on a single page allowed.
	 */
	public function isShowAllAllowed() {
		if ($this->showAllMaximumItemCount == -1) {
			return true;
		}

		if ($this->showAllMaximumItemCount == 0) {
			return false;
		}

		return $this->source->count() <= $this->showAllMaximumItemCount;
	}

	/**
	 * Sets the data to paginate.
	 *
	 * The data is encapsulated in a {@see DataSource} of correct type. For
	 * example if you pass in an array, {@see ArrayDataSource} is used. Yoy can
	 * later get the data-source object by {@see Paginator::getSource()}.
	 *
	 * @param mixed $data Data to paginate
	 * @return Paginator The paginator object for chaining actions.
	 */
	public function setData($data) {
		if ($data instanceof DataSource) {
			$this->source = $data;
		} else if (is_array($data)) {
			require_once LIBRARY_PATH.'/data-source/ArrayDataSource.php';

			$this->source = new ArrayDataSource($data);
		} else {
			throw new Exception('Given datatype is not supported');
		}

		$this->data = $data;

		return $this;
	}

	/**
	 * Returns the data in it's original form as given to constructor or setter.
	 *
	 * @return mixed User provided data
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Sets the data-source to use.
	 *
	 * The data is encapsulated in a {@see DataSource} of correct type. For
	 * example if you pass in an array, {@see ArrayDataSource} is used. Yoy can
	 * later get the data-source object by {@see Paginator::getSource()}.
	 *
	 * @param DataSource $source Source to use
	 */
	public function setSource(DataSource $source) {
		$this->source = $source;

		// try to set the page to current value, if new source has fewer data,
		// page number will be set to last.
		$this->setPage($this->getPage());
	}

	/**
	 * Returns the used data-source.
	 *
	 * The data is encapsulated in a {@see DataSource} of correct type. For
	 * example if you pass in an array, {@see ArrayDataSource} is used. Yoy can
	 * set the data-source object by calling {@see Paginator::setSource()}.
	 *
	 * @return DataSource
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * Returns the number of items in the data-set.
	 *
	 * @return integer
	 */
	public function getItemCount() {
		if ($this->source === null) {
			return 0;
		}
		
		return $this->source->count();
	}

	/**
	 * Returns the number of pages it takes to paginate the results to given
	 * items per page.
	 *
	 * @return integer
	 */
	public function getPageCount() {
		return ceil($this->getItemCount() / $this->itemsPerPage);
	}

	/**
	 * Sets the page to provide data from.
	 *
	 * This is forced to the range from one to number of pages.
	 *
	 * @param integer $pageNumber Page number to use.
	 */
	public function setPage($pageNumber) {
		$this->page = (int)$pageNumber;

		if ($this->page == 0 && $this->isShowAllAllowed()) {
			$this->showAll = true;
		} else if ($this->page < 1) {
			$this->page = 1;
		}
	}

	/**
	 * Returns current page.
	 *
	 * @return integer
	 */
	public function getPage() {
		return min($this->page, $this->getPageCount());
	}

	/**
	 *
	 * @return <type>
	 */
	public function isShowingAll() {
		return $this->showAll;
	}

	/**
	 * Returns the key offset from which current page data should be fetched
	 * from.
	 *
	 * @return integer Data offset on current page
	 */
	public function getOffset() {
		return max(($this->getPage() - 1) * $this->itemsPerPage, 0);
	}

	/**
	 * Returns items on a page.
	 *
	 * You may change current page by setting the optional parameter. This also
	 * modifies the internal state.
	 *
	 * @param integer|null $pageNumber Optional, sets new current page.
	 * @return array Array of data on given page
	 */
	public function getItemsOnPage($pageNumber = null) {
		if ($pageNumber !== null) {
			$this->setPage($pageNumber);
		}

		if ($this->isShowingAll()) {
			return $this->source->getItems(0, $this->source->count());
		} else {
			return $this->source->getItems(
				$this->getOffset(),
				$this->itemsPerPage
			);
		}
	}

	/**
	 * Returns current page data as iteratable entries.
	 *
	 * @return array
	 */
	public function getIterator() {
		return new ArrayIterator($this->getItemsOnPage());
	}

	/**
	 * Implements the countable interface, allows the number of results to be
	 * counted with simple php count().
	 *
	 * @return integer The total number of items paginated
	 */
	public function count() {
		if ($this->source === null) {
			throw new Exception(
				'Unable to count rows, source has not been set'
			);
		}

		return $this->source->count();
	}

}