<!DOCTYPE html>
<html lang="en">    
    <head>
        <meta charset="utf-8">
        <link href="/static/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
        <link href="/static/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="/static/styles.css" />
        <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" href="/static/jquery.ui.theme.css" />
        <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
        <title><?php echo $title; ?> - PoE Armory</title>
</head>
<body>
<div id="menu">
	<ul>
		<li><a href="/">Home</a></li>
		<li><a href="/about">About</a></li>
		<li><a href="/armory/index">My Armory</a></li>
	</ul>
</div>
	<h1><?php echo isset($contentTitle) ? $contentTitle: $title; ?></h1>