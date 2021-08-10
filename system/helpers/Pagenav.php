<?php

class Pagenav extends HtmlHelper {
	protected $itemCount = 0;				// Total number of items to page.
	protected $pageSize = 0;				// Number of items per page.
	protected $pageCount = 0;				// Total number of pages.
	protected $pageCurrent = 0;				// Current page.

	protected $adjacentCount = 3;			// Number of page-numbered page links shown before and after current page.
	protected $linkStyle = 'numbered';		// Style of page links, either of 'numbers' or 'icons'.
	protected $linkStrings = [				// Default strings when linkstyle is 'icons'.
		'first' => '<<',
		'previous' => '<',
		'next' => '>',
		'last' => '>>'
	];
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
	 * @param int $pageSize The number of items shown on each page. If set to zero, all items are shown.
	 * @param int $pageCurrent The current page, default is zero for first page.
	 * @param array $options An array of configuration options as key-value pairs. Supported keys:
	 * - adjacentCount: The number of page links displayed before and after current page link. Default is 3.
	 * - linkStyle: The style of links displayed at far ends of navigation. Either 'numbered' or 'icons'. Default is 'numbered'.
	 * - linkStrings: An array of strings to use as custom icons. Defaults are: ['first' => '<<', 'previous' => '<', 'next' => '>', 'last' => '>>']

	 */
	public function __construct($itemCount, $pageSize, $pageCurrent = 0, $options = []){
		$this->itemCount = $itemCount;
		$this->pageSize = $pageSize;
		$this->pageCurrent = $pageCurrent;
		if($pageSize > 0){
			$this->pageCount = ceil($itemCount / $pageSize) - 1;
		} else {
			$this->pageCount = 0;
		}
		if(isset($options['adjacentCount'])) $this->adjacentCount = $options['adjacentCount'];
		if(isset($options['linkStyle'])) $this->linkStyle = $options['linkStyle'];
	}

	/**
	 * Draws a page navigation.
	 *
	 * @param array $options An array of attributes as key-value pairs to set to link elements.
	 */
	public function nav($href, $params = [], $linkOptions = []){
		// If all items fit on one page, do not draw navigation.
		if($this->pageCount == 0) return;

		$nav = '';

		// If icon navigation is used.
		if($this->linkStyle === 'icons' && $this->pageCurrent > 0){
			// First page link.
			$thisParams = $params;
			$thisParams['page'] = 0;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, $this->linkStrings['first'], $options);
			// Previous page link.
			$thisParams['page'] = $this->pageCurrent - 1;
			$nav .= Html::a($href, $thisParams, $this->linkStrings['previous'], $options);
		}

		// If numbered navigation is used.
		else if($this->linkStyle == 'numbered' && $this->pageCurrent > 0){
			$thisParams = $params;
			$thisParams['page'] = 0;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, '0', $options);
		}

		// Adjacent numbered page links before current page.
		$adjacentStart = max($this->linkStyle === 'numbered' ? 1 : 0, $this->pageCurrent - $this->adjacentCount);
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
		$adjacentEnd = min($this->linkStyle === 'numbered' ? $this->pageCount - 1 : $this->pageCount, $this->pageCurrent + $this->adjacentCount);
		if($this->adjacentCount > 0){
			for($i = $this->pageCurrent + 1; $i <= $adjacentEnd; $i++){
				$thisParams = $params;
				$thisParams['page'] = $i;
				$options = $linkOptions;
				$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
				$nav .= Html::a($href, $thisParams, $i, $options);
			}
		}

		// If numbered navigation is used.
		if($adjacentEnd < $this->pageCount - 1) $nav .= '<span> ... </span>';
		if($this->linkStyle === 'numbered' && $this->pageCurrent < $this->pageCount){
			$thisParams = $params;
			$thisParams['page'] = $this->pageCount;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, $this->pageCount, $options);
		}

		// If icon navigation is used.
		else if(($this->linkStyle === 'icons') && $this->pageCurrent < $this->pageCount){
			// Next page link.
			$thisParams = $params;
			$thisParams['page'] = $this->pageCurrent + 1;
			$options = $linkOptions;
			$options['class'] = $this->selectAttribute('class', $options, $this->getDefaultClass('pageItem'));
			$nav .= Html::a($href, $thisParams, $this->linkStrings['next'], $options);
			// Last page link.
			$thisParams['page'] = $this->pageCount;
			$nav .= Html::a($href, $thisParams, $this->linkStrings['last'], $options);
		}

		$nav = Html::div($nav);
		return $nav;
	}
}

?>