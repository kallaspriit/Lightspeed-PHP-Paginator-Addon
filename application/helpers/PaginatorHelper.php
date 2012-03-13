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
 * @id $Id: PaginatorHelper.php 95 2011-03-17 15:26:10Z kallaspriit $
 * @author $Author: kallaspriit $
 * @version $Revision: 95 $
 * @modified $Date: 2011-03-17 17:26:10 +0200 (Thu, 17 Mar 2011) $
 * @package Lightspeed
 * @subpackage Helpers
 */

require_once LIBRARY_PATH.'/paginator/Paginator.php';
require_once APPLICATION_PATH.'/View.php';

/**
 * Paginator helper renders pagination navigation.
 *
 * @id $Id: PaginatorHelper.php 95 2011-03-17 15:26:10Z kallaspriit $
 * @author $Author: kallaspriit $
 * @version $Revision: 95 $
 * @modified $Date: 2011-03-17 17:26:10 +0200 (Thu, 17 Mar 2011) $
 * @package Lightspeed
 * @subpackage Helpers
 */
class PaginatorHelper {

	/**
	 * Filename of the partial to use for pager navigation rendering by default.
	 *
	 * @var string
	 */
	protected static $defaultPartial;

	/**
	 * Default options to use for the rendering partials.
	 *
	 * The options a pager navigation partial supports should be explained in
	 * the partial file and partials provide default for everything.
	 *
	 * @var array
	 */
	protected static $defaultOptions = array();

	/**
	 * Sets the default partial filename to use for rendering.
	 *
	 * @param string $filename Path to default partial
	 */
	public static function setDefaultPartial($filename) {
		self::$defaultPartial = $filename;
	}

	/**
	 * Returns the default partial filename to use for rendering.
	 *
	 * @return string Path to default partial
	 */
	public static function getDefaultPartial() {
		if (self::$defaultPartial === null) {
			self::$defaultPartial = PARTIAL_PATH.'/paginator.php';
		}
		
		return self::$defaultPartial;
	}

	/**
	 * Sets the default partial options.
	 *
	 * The options a pager navigation partial supports should be explained in
	 * the partial file and partials provide default for everything.
	 *
	 * @param array $options Default options to use.
	 */
	public static function setDefaultOptions(array $options) {
		self::$defaultOptions = $options;
	}

	/**
	 * Returns default partial options.
	 *
	 * The options a pager navigation partial supports should be explained in
	 * the partial file and partials provide default for everything.
	 *
	 * @return array
	 */
	public static function getDefaultOptions() {
		return self::$defaultOptions;
	}

	/**
	 * Renders the pagination navigation and returns the results.
	 *
	 * If the partial filename is not set, the default one is used. You can set
	 * the default partial filename using
	 * {@see PaginatorHelper::setDefaultPartial()}. The default partial defaults
	 * to PARTIAL_PATH.'/paginator.php'.
	 *
	 * If the options are not set, default options are used that can be set
	 * statically using {@see PaginatiorHelper::setDefaultOptions()}.
	 *
	 * For the same partial, options, route and paginator settings, the
	 * generated pagination HTML is cached.
	 *
	 * @param View $view View script being rendered
	 * @param Paginator $paginator Paginator to render
	 * @param array $options Optional array of options for partial
	 * @param string $partialFilename Set this to override default partial.
	 * @return string Rendered paginator navigation controller
	 */
	public static function render(
		ViewBase $view,
		Paginator $paginator,
		array $options = null,
		$partialFilename = null,
		$useCache = LS_USE_SYSTEM_CACHE
	) {
		if ($partialFilename === null) {
			$partialFilename = self::getDefaultPartial();
		}

		if ($options === null) {
			$options = self::getDefaultOptions();
		}

		$cacheKey = null;

		if ($useCache) {
			$cacheKey = 'lightspeed.paginator|'.
				$partialFilename.'.'.
				serialize($options).'.'.
				$view->getRoute()->__toString().'.'.
				$paginator->__toString();
		}

		$pagination = $useCache ? Cache::fetchLocal($cacheKey) : false;

		if ($pagination !== false) {
			return $pagination;
		}

		$partial = new View();

		// copy references to all of the classes the top-level view knows about
		$partial->setBootstrapper($view->getBootstapper());
		$partial->setController($view->getController());
		$partial->setDispatchToken($view->getDispatchToken());
		$partial->setDispatcher($view->getDispatcher());
		$partial->setFrontController($view->getFrontController());
		$partial->setRequest($view->getRequest());
		$partial->setRoute($view->getRoute());
		$partial->setRouter($view->getRouter());

		// give view the paginator and options
		$partial->paginator = $paginator;
		$partial->options = $options;

		// return the rendered navigation
		$pagination = $partial->render($partialFilename);

		if ($useCache) {
			Cache::storeLocal($cacheKey, $pagination, LS_TTL_PAGINATION);
		}

		return $pagination;
	}
}