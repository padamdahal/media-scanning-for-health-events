$(function(){
	console.log('Initializing...');
	
	$.get("https://raw.githubusercontent.com/padamdahal/media-scanning-for-health-events/main/keywords.txt", function(keywords){
		$("#keywords").empty();
		keywords = keywords.split(",");
		$.each(keywords, function(index, keyword){
			$("#keywords").append("<span class='keyword' style='color:#fff;padding:5px;'>#"+keyword.trim()+"</span>"); 
		});
		init();
	});
		
	function init(){
		// Get list of urls
		$.getJSON("https://raw.githubusercontent.com/padamdahal/media-scanning-for-health-events/main/feedurls.json", function(data){
			$("#newsItems").empty();
			$.each(data, function(key, detail){
				$.ajax({
					method: "POST",
					url: "scan.php?ref="+detail['title'].replaceAll(" ", "-"),
					data: detail
				}).done(function(news) {
					news = JSON.parse(news);
					$.each(news, function(index, newsItem){
						
						var newsBlock = `<div class="item">
							<header class="entry-header">
								<h2 class="entry-title"><a href="`+newsItem.link+`">`+newsItem.title+`</a></h2>
								<span class="meta-date">
									<time class="entry-date published updated">`+newsItem.pubDate+`</time>
								</span>
							</header>
							<div class="entry-content">
								<p>`+newsItem.description+`</p>
								<a href="`+newsItem.link+`" target="_blank" class="more-link" style="font-weight:normal;">More on <strong>`+newsItem.source+`</strong></a>
							</div>
						</div>`;
					
						$("#newsItems").append(newsBlock);						
					});
				});
			});
		});
	}
	
	// Reload the news every 10 minutes
	setInterval(function(){ init(); }, 600000);
});