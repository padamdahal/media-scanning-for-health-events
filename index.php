<?php
	include('scanner.php');
	init();
?>

<!doctype html>
<html lang="en-US" class="has-lab-nav-bottom lab-theme-light">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" href="masonary.css">
	<title>Media Monitoring</title>
	<style>
		body {
			font: 1em/1.67 Arial, Sans-serif;
			margin: 0;
			background: #e9e9e9;
		}

		img, iframe {
		max-width: 100%;
		height: auto;
		display: block;
		}

		.wrapper {
			width: 95%;
			/*margin: 1.5em auto;*/
		}

		.masonry {
			margin: 1.5em 0;
			padding: 0;
			-moz-column-gap: 1.5em;
			-webkit-column-gap: 1.5em;
			column-gap: 1.5em;
			font-size: .85em;
		}

		.item {
			display: inline-block;
			background: #fff;
			padding: 1.5em;
			margin: 0 0 1.5em;
			width: 100%;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.18);
			border-radius: 3px;
			-moz-border-radius: 3px;
			-webkit-border-radius: 3px;
		}

		.title, .footer {
		text-align: center;
		}

		.title {
		font-size: 1.75em;
		margin: .25em 0;
		}

		.title a {
		display: inline-block;
		padding: .75em 1.25em;
		color: #888;
		border: 2px solid #aaa;
		margin: .25em 1em 1em;
		text-decoration: none;
		border-radius: 3px;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		-ms-border-radius: 3px;
		-o-border-radius: 3px;
		}

		.title {
		color: #666;
		}

		.title a:hover {
		color: #666;
		border-color: #888;
		}


		.share-link,
		.article-link {
		color: #888;
		}

		@media only screen and (min-width: 700px) {
			.masonry {
				-moz-column-count: 2;
				-webkit-column-count: 2;
				column-count: 2;
			}
		}

		@media only screen and (min-width: 900px) {
			.masonry {
				-moz-column-count: 3;
				-webkit-column-count: 3;
				column-count: 3;
			}
		}

		@media only screen and (min-width: 1100px) {
			.masonry {
				-moz-column-count: 4;
				-webkit-column-count: 4;
				column-count: 4;
			}
		}

		@media only screen and (min-width: 1280px) {
			.wrapper {
				width: 1260px;
			}
		}
	</style>
</head>
<body class="single single-demo">
<div class="container">
	<div class="wrapper">
			<div class="title" style="font-size:100%">
				<h2>Media Monitoring Dashboard</h2>
				<div class="keywords">
					<?php
						foreach($keywords as $word){
					?>
					<span>#<?php echo $word;?></span>&nbsp;
					<?php
						}
					?>
				</div>
			</div>
			<div class="masonry">
				<?php 
					foreach($array as $key => $item){
				?>
				<div class="item">
					<header class="entry-header">
						<h2 class="entry-title"><a href="<?php echo $item['link'];?>" rel="bookmark"><?php echo $item['title'];?></a></h2>
						<span class="meta-date">
							<time class="entry-date published updated"><?php echo $item['pubDate'];?></time>
						</span>
						<!--span class="meta-author">
							<span class="author vcard">
								<a class="url fn n" href="<?php echo $item['link'];?>" rel="author"><strong><?php echo $item['source'];?></strong></a>
							</span>
						</span-->
					</header>
					<div class="entry-content">
						<p><?php echo substr($item['description'],0,200).'...';?></p>
						<a href="<?php echo $item['link'];?>" target="_blank" class="more-link" style="font-weight:normal;">More on <strong><?php echo $item['source'];?></strong></a>
					</div>
				</div>
				<?php } ?>
			</div>
	</div>
</div>
</body>
</html>