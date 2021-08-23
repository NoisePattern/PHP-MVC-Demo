<?php

class Pagenav extends HtmlHelper {
	protected $itemCount = 0;				// Total number of items to page.
	protected $pageSize = 0;				// Number of items per page.
	protected $pageCount = 0;				// Total number of pages.
	protected $pageCurrent = 0;				// Current page.

	protected $adjacentCount = 3;			// Number of page-numbered page links shown before and after current page.
	protected $linkStyle = 'linkNames';		// Style of page links, either of 'linkNames' or 'linkIcons'.
	protected $linkIcons = [				// Default strings when linkstyle is 'icons'.
		'first' => '&lt&lt;',
		'previous' => '&lt;',
		'next' => '&gt;',
		'last' => '&gt&gt;'
	];
	protected $linkNames = [				// Default strings when linkstyle is 'names'.
		'first' => 'first',
		'previous' => 'prev',
		'next' => 'next',
		'last' => 'last'
	];
	protected $useDefaults = true;
	protected $elementDefaultClass = [
		'pageNav' => 'pageNav',
		'pageItem' => 'page-item',
		'pageItemActive' => 'page-item active',
		'pageLink' => 'page-link'
	];

	/**
	 * Pagenav constructor.
	 *
	 * @param int $itemCount The total number of items listed across pages.
	 * @param int $pageSize The number of items shown on each page. If set to zero, all items are shown.
	 * @param int $pageCurrent The current page, default is zero for first page.
	 * @param array $options An array of configuration options as key-value pairs. Supported keys:
	 * - adjacentCount: The number of page links displayed before and after current page link. Default is 3.
	 * - linkStyle: The style of links displayed at far ends of navigation. Either 'linkNames' or 'linkIcons'. Default is 'linkNames'.
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
	 * @param string $href Link href string, without parameters.
	 * @param array $params Link parameters as key-value pairs.
	 * @param array $itemOptions Array of attributes for li-element as key-value pairs. Class defaults to 'page-item'.
	 * @param array $itemCurrentOptions Array of attributes for li-element on current page as key-value pairs. Class defaults to 'page-item active'.
	 * @param array $linkOptions Array of attributes for a-elements as key-value pairs. Class defaults to 'page-link'.
	 */
	public function nav($href, $params = [], $itemOptions = [], $itemCurrentOptions = [], $linkOptions = []){
		// If all items fit on one page, do not draw navigation.
		if($this->pageCount <= 0) return;

		$nav = '';

		// Set default classes.
		$itemOptions['class'] = $this->selectAttribute('class', $itemOptions, $this->getDefaultClass('pageItem'));
		$itemCurrentOptions['class'] =  $this->selectAttribute('class', $itemCurrentOptions, $this->getDefaultClass('pageItemActive'));
		$linkOptions['class'] =  $this->selectAttribute('class', $linkOptions, $this->getDefaultClass('pageLink'));

		// First page link.
		$text = $this->{$this->linkStyle}['first'];
		$params['page'] = 0;
		$link = Html::a($href, $params, $text, $linkOptions);
		$nav .= Html::li($link, $itemOptions);

		// Previous page link.
		$text = $this->{$this->linkStyle}['previous'];
		$params['page'] = max(0, $this->pageCurrent - 1);
		$link = Html::a($href, $params, $text, $linkOptions);
		$nav .= Html::li($link, $itemOptions);

		// Adjacent numbered page links before current page.
		$adjacentStart = max(0, $this->pageCurrent - $this->adjacentCount);
		for($i = $adjacentStart; $i <= $this->pageCurrent - 1; $i++){
			$params['page'] = $i;
			$link = Html::a($href, $params, $i, $linkOptions);
			$nav .= Html::li($link, $itemOptions);
		}

		// Current page.
		$params['page'] = $this->pageCurrent;
		$link = Html::a($href, $params, $this->pageCurrent, $linkOptions);
		$nav .= Html::li($link, $itemCurrentOptions);

		// Adjacent numbered page links after current page.
		$adjacentEnd = min($this->pageCount, $this->pageCurrent + $this->adjacentCount);
		for($i = $this->pageCurrent + 1; $i <= $adjacentEnd; $i++){
			$params['page'] = $i;
			$link = Html::a($href, $params, $i, $linkOptions);
			$nav .= Html::li($link, $itemOptions);
		}

		// Next page link.
		$text = $this->{$this->linkStyle}['next'];
		$params['page'] = min($this->pageCount, $this->pageCurrent + 1);
		$link = Html::a($href, $params, $text, $linkOptions);
		$nav .= Html::li($link, $itemOptions);

		// Last page link.
		$text = $this->{$this->linkStyle}['last'];
		$params['page'] = $this->pageCount;
		$link = Html::a($href, $params, $text, $linkOptions);
		$nav .= Html::li($link, $itemOptions);

		$ul = Html::ul($nav, ['class' => 'pagination']);
		$nav = '<nav>' . $ul . '</nav>';
		return $nav;
	}
}

?>