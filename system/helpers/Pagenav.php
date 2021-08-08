<?php

class Pagenav extends HtmlHelper {
	protected $itemCount = 0;				// Total number of items to page.
	protected $pageSize = 0;				// Number of items per page.
	protected $pageCount = 0;				// Total number of pages.
	protected $pageCurrent = 0;				// Current page.

	protected $adjacentCount = 3;			// Number of page-numbered page links shown before and after current page.
	protected $endLinks = true;				// Display page-numbered first and last page links.
	protected $stepLinks = false;			// Display navigation icons that step to next and previous page.
	protected $jumpLinks = false;			// Display navigation icons that jump to first and last page.

	protected $useDefaults = true;
	protected $elementDefaultClass = [
		'pageNav' => 'pageNav',
		'pageItem' => 'pageItem',
		'pageItemCurrent' => 'pageItemCurrent'
	];

	/**
	 * Pagenav constructor.
	 *
	 * @param int $itemCount The total number of items listed across pages.
	 * @param int $pageSize The number of items shown on each page.
	 * @param int $pageCurrent The current page, default is zero for first page.
	 * @param array $options An array of configuration options as key-value pairs. Supported keys:
	 * - adjacentCount: the number of page links displayed before and after current page link. Default is 3.
	 * - endLinks: display links to first and last page. Default is true.
	 */
	public function __construct($itemCount, $pageSize, $pageCurrent = 0, $options = []){
		$this->itemCount = $itemCount;
		$this->pageSize = $pageSize;
		$this->pageCurrent = $pageCurrent;
		$this->pageCount = ceil($itemCount / $pageSize) - 1;
		if(isset($options['adjacentCount'])) $this->adjacentCount = $options['adjacentCount'];
		if(isset($options['endLinks'])) $this->adjacentCount = $options['endLinks'];
		if(isset($options['stepLinks'])) $this->adjacentCount = $options['stepLinks'];
		if(isset($options['jumpLinks'])) $this->adjacentCount = $options['jumpLinks'];
	}

	/**
	 * Draws a page navigation.
	 *
	 * @param array $options An array of attributes as key-value pairs to set to link elements.
	 */
	public function nav($href, $params = [], $linkOptions = []){
		// If all items fit on one page, do not draw navigation.
		if($this->itemCount <= $this->pageSize) return;

		$nav = '';

		// If first numbered page is always shown.
		if($this->endLinks && $this->pageCurrent > 0){

			$thisParams = $params;
			$thisParams['page'] = 0;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, '0', $options);
			$adjacentStart = max(1, $this->pageCurrent - $this->adjacentCount);
		}

		// Adjacent numbered page links before current page.
		$adjacentStart = max($this->endLinks ? 1 : 0, $this->pageCurrent - $this->adjacentCount);
		if($adjacentStart > 1) $nav.= '<span> ... </span>';
		if($this->adjacentCount > 0){
			for($i = $adjacentStart; $i <= $this->pageCurrent - 1; $i++){
				$thisParams = $params;
				$thisParams['page'] = $i;
				$options = $linkOptions;
				$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
					$nav .= Html::a($href, $thisParams, $i, $options);
			}
		}
		// Current page.
		$thisParams = $params;
		$thisParams['page'] = $this->pageCurrent;
		$options = $linkOptions;
		$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItemCurrent'));
		$nav .= Html::a($href, $thisParams, $this->pageCurrent, $options);

		// Adjacent numbered page links after current page.
		$adjacentEnd = min($this->endLinks ? $this->pageCount -1 : $this->pageCount, $this->pageCurrent + $this->adjacentCount);
		if($this->adjacentCount > 0){
			for($i = $this->pageCurrent + 1; $i <= $adjacentEnd; $i++){
				$thisParams = $params;
				$thisParams['page'] = $i;
				$options = $linkOptions;
				$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
				$nav .= Html::a($href, $thisParams, $i, $options);
			}
		}

		// If last numbered page is always shown.
		if($adjacentEnd < $this->pageCount - 1) $nav .= '<span> ... </span>';
		if($this->endLinks && $this->pageCurrent < $this->pageCount){
			$adjacentEnd = min($this->pageCount - 1, $this->pageCurrent + $this->adjacentCount);
			$thisParams = $params;
			$thisParams['page'] = $this->pageCount;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, $this->pageCount, $options);
		}

		$nav = Html::div($nav);
		return $nav;
	}
}

?>