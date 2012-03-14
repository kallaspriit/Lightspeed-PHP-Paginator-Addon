Lightspeed-PHP paginator helper
===============================

![Lightspeed-PHP logo](http://lightspeed-php.com/images/logo.png "Lightspeed-PHP")

**[LIGHTSPEED-PHP](http://lightspeed-php.com) IS A MINIMALISTIC AND FAST PHP FRAMEWORK** aiming to provide basic structure that helps you build your applications faster and more efficiently on solid architecture. It's designed to be small, fast, easy to understand and extend.

Lightspeed-PHP [Github project](https://github.com/kallaspriit/Lightspeed-PHP) | [Homepage](http://lightspeed-php.com)


How to install
--------------
Download the archive and unpack it to the root directory of your project. Creates a "paginator" directory under "library", "PaginatorHelper.php" under application/helpers and "paginator.php" partial in application/partials. It also comes with "example-style.css" file which contains example css styles that you can use to build your own look-and-feel upon.

Just make sure your **application/Autoload.php** contains a rule to autoload the Paginator class when needed.

```
else if ($className == 'Paginator') {
	require_once LIBRARY_PATH.'/paginator/Paginator.php';
}
```

You need to add translations for it, add the following to the end of your **application/translations/main-translations.php** file:

```
// Pager
'pager.label.page' => array(
	LANGUAGE_ENGLISH => 'Page',
),
'pager.label.first' => array(
	LANGUAGE_ENGLISH => 'First',
),
'pager.label.last' => array(
	LANGUAGE_ENGLISH => 'Last',
),
'pager.label.previous' => array(
	LANGUAGE_ENGLISH => 'Previous',
),
'pager.label.next' => array(
	LANGUAGE_ENGLISH => 'Next',
),
'pager.label.count(count)' => array(
	LANGUAGE_ENGLISH => 'Total: %s',
),
'pager.label.showing(items)' => array(
	LANGUAGE_ENGLISH => 'Showing: %s',
),
'pager.label.show-all' => array(
	LANGUAGE_ENGLISH => 'Show all',
),
'pager.label.show-first-page' => array(
	LANGUAGE_ENGLISH => 'Show first page',
),
'pager.label.all' => array(
	LANGUAGE_ENGLISH => 'all',
),
```

Lastly, the pager needs some CSS to make it look pretty. You can base your own rules on the following:

```
.pager {
	margin-top: 20px;
}
.pager .pager-stats {
	color: #666;
	font-style: italic;
	font-size: 90%;
	text-align: right;
}
.pager UL {
	margin: 0;
	padding: 0;
	list-style: none;
	height: 26px;
}
.pager LI {
	float: left;
	height: 24px;
	background-color: #f8f8f8;
	line-height: 24px;
}
.pager LI.active {
	border-top: 1px solid #fff;
	background-color: #fff;
}
.pager LI STRONG,
.pager LI A {
	display: block;
	float: left;
}
.pager LI SPAN {
	display: block;
	float: left;
	padding: 0 8px 0 8px;
}
.pager LI A {
	text-decoration: none;
}
.pager LI {
	border-left: 1px solid #eee;
	border-top: 1px solid #eee;
	border-bottom: 1px solid #eee;
}
.pager LI:first-child {
	border-radius: 4px 0 0 4px;
	-moz-border-radius: 4px 0 0 4px;
	-webkit-border-radius: 4px 0 0 4px;
}
.pager LI:last-child {
	border-right: 1px solid #eee;
	border-radius: 0 4px 4px 0;
	-moz-border-radius: 0 4px 4px 0;
	-webkit-border-radius: 0 4px 4px 0;
}
```

How to use it
-------------
An example of how to use the pager in your project is covered [on this tutorial page](http://lightspeed-php.com/tutorial/pagination).

Up-to-date version of this tutorial is available on the [addons page](http://lightspeed-php.com/add-ons/paginator).