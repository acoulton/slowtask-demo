<html>
    <head>
	<title>SlowTask demo</title>
    </head>
    <body>
	<h1>Welcome to the SlowTask demo!</h1>
	<ul>
	    <li><?=HTML::anchor(Route::get('default')->uri(
					   array(
						'controller'=>'welcome',
						'action'=>'slow_redirect')),
		   "Get slowly redirected");?></li>
	    <li><?=HTML::anchor(Route::get('default')->uri(
					   array(
					   'controller'=>'welcome',
					   'action'=>'slow_html')),
		   "Slowly load a page");?></li>
	    <li><?=HTML::anchor(Route::get('default')->uri(
					   array(
					   'controller'=>'welcome',
					   'action'=>'slow_file')),
		   "Slowly download a file");?></li>
	</ul>
    </body>
</html>
