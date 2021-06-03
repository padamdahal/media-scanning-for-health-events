<?php
	define("APPBASE", true);
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
					<span>#<?php echo $keyword = rtrim(ltrim($word));?></span>&nbsp;
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