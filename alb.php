<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
</style>
<title>Erika Carrillo Photography</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="galleriffic/jquery.galleriffic.js"></script>
<script src="galleriffic/jquery.history.js"></script>
<script src="galleriffic/jquery.opacityrollover.js"></script>
<link rel="stylesheet" type="text/css" href="galleriffic/basic.css"></link>
<link rel="stylesheet" type="text/css" href="galleriffic/galleriffic-5.css"></link>
<link rel="stylesheet" type="text/css" href="galleriffic/black.css"></link>

<script type="text/javascript">
			$(document).ready(function($) {
				$('div.content').css('display', 'block');

				var onMouseOutOpacity = 0.67;
				$('#thumbs ul.thumbs li, div.navigation a.pageLink').opacityrollover({
					mouseOutOpacity:   onMouseOutOpacity,
					mouseOverOpacity:  1.0,
					fadeSpeed:         'fast',
					exemptionSelector: '.selected'
				});
				
				// Initialize Advanced Galleriffic Gallery
				var gallery = $('#thumbs').galleriffic({
					delay:                     2500,
					numThumbs:                 8,
					preloadAhead:              8,
					enableTopPager:            false,
					enableBottomPager:         false,
					imageContainerSel:         '#slideshow',
					controlsContainerSel:      '#controls',
					captionContainerSel:       '#caption',
					loadingContainerSel:       '#loading',
					renderSSControls:          true,
					renderNavControls:         true,
					playLinkText:              'Play Slideshow',
					pauseLinkText:             'Pause Slideshow',
					prevLinkText:              '&lsaquo; Previous Photo',
					nextLinkText:              'Next Photo &rsaquo;',
					nextPageLinkText:          'Next &rsaquo;',
					prevPageLinkText:          '&lsaquo; Prev',
					enableHistory:             true,
					autoStart:                 false,
					syncTransitions:           true,
					defaultTransitionDuration: 900,
					onSlideChange:             function(prevIndex, nextIndex) {
						// 'this' refers to the gallery, which is an extension of $('#thumbs')
						this.find('ul.thumbs').children()
							.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
							.eq(nextIndex).fadeTo('fast', 1.0);

						// Update the photo index display
						this.$captionContainer.find('div.photo-index')
							.html('Photo '+ (nextIndex+1) +' of '+ this.data.length);
					},
					onPageTransitionOut:       function(callback) {
						this.fadeTo('fast', 0.0, callback);
					},
					onPageTransitionIn:        function() {
						var prevPageLink = this.find('a.prev').css('visibility', 'hidden');
						var nextPageLink = this.find('a.next').css('visibility', 'hidden');
						
						// Show appropriate next / prev page links
						if (this.displayedPage > 0)
							prevPageLink.css('visibility', 'visible');

						var lastPage = this.getNumPages() - 1;
						if (this.displayedPage < lastPage)
							nextPageLink.css('visibility', 'visible');

						this.fadeTo('fast', 1.0);
					}
				});

				gallery.find('a.prev').click(function(e) {
					gallery.previousPage();
					e.preventDefault();
				});

				gallery.find('a.next').click(function(e) {
					gallery.nextPage();
					e.preventDefault();
				});

				function pageload(hash) {
					if(hash) {
						$.galleriffic.gotoImage(hash);
					} else {
						gallery.gotoIndex(0);
					}
				}

				$.historyInit(pageload, "advanced.html");
				$("a[rel='history']").live('click', function(e) {
					if (e.button != 0) return true;
					var hash = this.href;
					hash = hash.replace(/^.*#/, '');
					$.historyLoad(hash);
					return false;
				});

			});
		</script>

</head>
<?php

$screen = intval($_GET['nm']);

$url = "http://picasaweb.google.com/data/feed/api/user/erika.carrillo/album/{$_GET['ref']}?kind=photo&access=all";
require_once("inc/xml2array.inc.php");
require_once("inc/rsaprivkey.inc.php");
$method = "GET"; $time = @mktime(); $rndm = strval(9000000000000000000-$time);
$pkeyid = openssl_get_privatekey($priv_key);
$data = "{$method} {$url} {$time} {$rndm}"; $sig = ""; openssl_sign($data, $sig, $pkeyid); openssl_free_key($pkeyid);
$curl_opt = array( CURLOPT_URL=>$url, CURLOPT_TIMEOUT=>10, CURLOPT_FAILONERROR=>1, CURLOPT_NOPROGRESS=>true, CURLOPT_NOPROGRESS=>true, CURLOPT_RETURNTRANSFER=>true
		, CURLOPT_HTTPHEADER=>array('auth' => "Authorization: AuthSub token=\"{$token}\" data=\"{$data}\" sig=\"".base64_encode($sig)."\" sigalg=\"rsa-sha1\"") );
$curl = curl_init(); curl_setopt_array($curl, $curl_opt);
$xml = curl_exec($curl);
$out = xml2array($xml, $get_attributes=1);

file_put_contents("asdf.xml", $xml);

$thmb_wd = 260;

for ($i = 0; $i < count($out['feed']['entry']); $i++)
{
	$pho['orig'][$i] = $out['feed']['entry'][$i]['link'][4]['attr']['href'];
	$pho['thmb'][$i] = $out['feed']['entry'][$i]['media:group']['media:thumbnail'][1]['attr']['url'];
	$pho['view'][$i] = $out['feed']['entry'][$i]['media:group']['media:content']['attr']['url'];
	$pho['view_w'][$i] = $out['feed']['entry'][$i]['media:group']['media:content']['attr']['width'];
	$pho['view_h'][$i] = $out['feed']['entry'][$i]['media:group']['media:content']['attr']['height'];
	$pho['cptn'][$i] = $out['feed']['entry'][$i]['summary']['value'];
	$pho['id'][$i] = $out['feed']['entry'][$i]['gphoto:id']['value'];
	$pho['dim_x'][$i] = $out['feed']['entry'][$i]['gphoto:width']['value'];
	$pho['dim_y'][$i] = $out['feed']['entry'][$i]['gphoto:height']['value'];
		
	if ($pho['dim_x'][$i] >= $pho['dim_y'][$i])
	{	$thmb_ht = 390;
		$top[$i] = "-40";
		$pho['thmb_y'][$i] = $thmb_ht;
		$pho['thmb_x'][$i] = round($thmb_ht*($pho['dim_x'][$i]/$pho['dim_y'][$i]));
	}
	else
	{	$thmb_ht = 400;
		$top[$i] = "-50";
		$pho['thmb_y'][$i] = $thmb_ht;
		$pho['thmb_x'][$i] = round($thmb_ht*($pho['dim_x'][$i]/$pho['dim_y'][$i]));
	}	
}

?>

<body style="background-color:green;" onLoad="">

<div style="position:relative;width:989px;height:101px;margin-left:auto;margin-right:auto;border:none;background-image:url('img/title.png');"></div>


<div style="width:980px;height:800px;position:relative;margin-top:45px;margin-left:auto;margin-right:auto;border:none;">
<?php

$group = 3*ceil(intval($_GET['nm'])/3)-2;

echo "<img id=\"bttn_menu\" src=\"img/txt.php?str=18_100_ffffff_000000_<*menu\" style=\"position:absolute;top:-50px;left:0px;width:100px;height:50px;cursor:pointer;\""
		." onMouseOver=\"bulge(0,'menu','up');\""
		." onMouseOut=\"bulge(0,'menu','dn');\""
		." onClick=\"location='http://www.erikacarrillophotography.com/';\""
	." />";

echo "<img src=\"img/txt.php?str=28_160_ffffff_000000_". strtolower(substr(strval($_GET['ref']),5)) ."\" style=\"position:absolute;top:-60px;left:509px;\" />";


?>


<div id="container">

				<div class="navigation-container">
					<div id="thumbs" class="navigation">
						<a class="pageLink prev" style="visibility: hidden;" href="#" title="Previous Page"></a>
						<ul class="thumbs noscript">

					<?php

						$thmb_size = 100;

							for ($i = 0; $i < count($pho['thmb']); $i++) {	
								if (!empty($pho['thmb'][$i])) {	

									$cptn = $pho['cptn'][$i];
									$url = "photo.php?url=".urlencode(substr($pho['view'][$i],7))."&d=";

									echo 
										"<li>"
										."<a class=\"thumb\" name=\"{$pho['id'][$i]}\" href=\"{$url}".dechex($pho['thmb_x'][$i])."z".dechex($pho['thmb_y'][$i])."z".dechex($pho['view_w'][$i])."z".dechex($pho['view_h'][$i])."\" title=\"".str_replace("\"","",$pho['cptn'][$i])."\">"
											."<img src=\"{$url}".dechex($pho['thmb_x'][$i])."z".dechex($pho['thmb_y'][$i])."z".dechex($thmb_size)."z".dechex($thmb_size)."\" alt=\"".str_replace("\"","",$pho['cptn'][$i])."\" />"
										."</a>"
										."<div class=\"caption\">"
											."<div class=\"image-title\">{$cptn}</div>"
											."<div class=\"image-desc\">{$cptn}</div>"
											."<div class=\"download\">"
												."<a href=\"javascript:alert('Purchase {$pho['id'][$i]}')\">Order Print</a>"
											."</div>"
										."</div>"
										."</li>";

								}
							}
					?>

						</ul>
						<a class="pageLink next" style="visibility: hidden;" href="#" title="Next Page"></a>
					</div>
				</div>
				<div class="content">
					<div class="slideshow-container">
						<div id="controls" class="controls"></div>
						<div id="loading" class="loader"></div>
						<div id="slideshow" class="slideshow"></div>
					</div>
					<div id="caption" class="caption-container">
						<div class="photo-index"></div>
					</div>
				</div>

				<div style="clear: both;"></div>
			</div>




</div>









<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-6075198-1");
pageTracker._trackPageview();
</script>
</body>
</html>
