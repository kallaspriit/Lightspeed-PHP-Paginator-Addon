<?php

/**
 * This renderer displays a limited number of pages where the current page
 * number is in the center and a limited number (pages-left, pages-right) are
 * displayed both on the left and right. It can also display the previous-next
 * and first-last links and stats about the amount of data.
 *
 * This renderer requires that the view has route, router and request set.
 *
 * This paginator renderer supports the following options:
 *
 * > "route-name" - Name of the route to use for urls, default to current
 * > "class" - class to add to the pager container, defaults to "general"
 * > "pages-left" - how many page numbers to show on the left of center
 * > "pages-right" - how many page numbers to show on the right of center
 * > "show-first-last" - should first and last page links be displayed
 * > "show-previous-next" - should previous and next page links be displayed
 * > "show-display-all" - should a link to display all items at once be shown
 * > "show-stats" - should stats about the amount of data be displayed
 * > "instance-id" - token to add to pager id to have several on single page
 *
 * You can see the defaults below
 */

// Get options and set defaults
$routeName			= isset($this->options['route-name'])
						? $this->options['route-name']
						: $this->getRoute()->getName();

$pagerClass			= isset($this->options['class'])
						? $this->options['class']
						: 'general';
$pagesLeft			= isset($this->options['pages-left'])
						? $this->options['pages-left']
						: 3;

$pagesRight			= isset($this->options['pages-right'])
						? $this->options['pages-right']
						: 3;
$showFirstLast		= isset($this->options['show-first-last'])
						? $this->options['show-first-last']
						: true;

$showPreviousNext	= isset($this->options['show-previous-next'])
						? $this->options['show-previous-next']
						: true;

$showDisplayAll		= isset($this->options['show-display-all'])
						? $this->options['show-display-all']
						: true;

$showStats			= isset($this->options['show-stats'])
						? $this->options['show-stats']
						: true;

$instanceId			= isset($this->options['instance-id'])
						? $this->options['instance-id']
						: null;

// Get info from the pager
$currentPage	= $this->paginator->getPage();
$resultsPerPage	= $this->paginator->getItemsPerPage();
$resultsCount	= $this->paginator->getItemCount();
$totalPages		= $this->paginator->getPageCount();
$isShowingAll	= $this->paginator->isShowingAll();

// Get current URL params
$urlParams = $this->getRequest()->getUrlParams();
$routeParams = $this->getRoute()->getParams();
$urlParams = array_merge($urlParams, $routeParams);

/*
foreach ($urlParams as $urlParamKey => $urlParamValue) {
	if (is_string($urlParamValue)) {
		$urlParams[$urlParamKey] = urlencode($urlParamValue);
	}
}
*/

// Start building the HTML
$pagerHtml = '';

if ($resultsCount > 0 && ($isShowingAll || $totalPages > 1)) {
	if (!$isShowingAll) {
		$offsetEnd = $currentPage * $resultsPerPage;

		if ($offsetEnd > $totalPages * $resultsPerPage) {
			$offsetEnd = $resultsCount;
		}

		if ($currentPage == 1) {
			if ($showFirstLast) {
				$pagerHtml .= '<li class="first"><strong><span>'.Translator::get('pager.label.first').'</span></strong></li>';
			}
			
			if ($showPreviousNext) {
				$pagerHtml .= '<li class="prev-page"><strong><span>'.Translator::get('pager.label.previous').'</span></strong></li>';
			}
		} else {
			if ($showFirstLast) {
				$pagerHtml .= '<li class="first"><a id="page-1" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => 1))).'"><span>'.Translator::get('pager.label.first').'</span></a></li>';
			}
			
			if ($showPreviousNext) {
				$pagerHtml .= '<li class="prev-page"><a id="page-'.($currentPage - 1).'" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => $currentPage - 1))).'"><span>'.Translator::get('pager.label.previous').'</span></a></li>';
			}
		}

		$missingLeft = max($pagesLeft - $currentPage + 1, 0);
		$missingRight = max($pagesRight - ($totalPages - $currentPage), 0);

		$startPage = max($currentPage - $pagesLeft - $missingRight, 1);
		$endPage = min($currentPage + $pagesRight + $missingLeft, $totalPages);

		for ($i = $startPage; $i <= $endPage; $i++) {
			if ($i == $currentPage) {
				$pagerHtml .= '<li class="active"><strong><span>'.$i.'</span></strong></li>';
			} else {
				$pagerHtml .= '<li><a id="page-'.$i.'" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => $i))).'"><span>'.$i.'</span></a></li>';
			}
		}

		if ($currentPage == $totalPages) {
			if ($showPreviousNext) {
				$pagerHtml .= '<li class="next-page"><strong><span>'.Translator::get('pager.label.next').'</span></strong></li>';
			}

			if ($showFirstLast) {
				$pagerHtml .= '<li class="last"><strong><span>'.Translator::get('pager.label.last').'</span></strong></li>';
			}
		} else {
			if ($showPreviousNext) {
				$pagerHtml .= '<li class="next-page"><a id="page-'.($currentPage + 1).'" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => $currentPage + 1))).'"><span>'.Translator::get('pager.label.next').'</span></a></li>';
			}

			if ($showFirstLast) {
				$pagerHtml .= '<li class="last"><a id="page-'.$totalPages.'" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => $totalPages))).'"><span>'.Translator::get('pager.label.last').'</span></a></li>';
			}
		}

		if ($showStats) {
			$pagesInfo = '
					<p class="pager-stats">
						'.Translator::get('pager.label.count(count)', $resultsCount).'
						<span>|</span>
						'.Translator::get('pager.label.showing(items)', ($totalPages > 0 ? ($this->paginator->getOffset() + 1).'-'.min($offsetEnd, $resultsCount) : min($offsetEnd, $resultsCount))).' <span>|</span> '.Translator::get('pager.label.page').': '.$currentPage.'/'.$totalPages.'
						'.($showDisplayAll ? '<span>|</span>
						<a id="page-'.Translator::get('pager.label.all').'" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => Translator::get('pager.label.all')))).'">'.Translator::get('pager.label.show-all').'</a>' : '').'
					</p>';
		} else {
			$pagesInfo = '';
		}

		$pagerHtml = '
				<div class="pager '.$pagerClass.'" id="pager-'.$routeName.($instanceId != null ? '-'.$instanceId : '').'">
					'.$pagesInfo.'
					<ul>'.$pagerHtml.'</ul>
				</div>';
	} else {
		$pagerHtml = '
				<div class="pager '.$pagerClass.'" id="pager-'.$routeName.($instanceId != null ? '-'.$instanceId : '').'">
					<p>'.Translator::get('pager.label.count(count)', $resultsCount).' <span>|</span> <a id="page-1" href="'.$this->makeUrl($routeName, array_merge($urlParams, array('page' => 1))).'">'.Translator::get('pager.label.show-first-page').'</a></p>
				</div>';
	}
}

echo $pagerHtml;

?>