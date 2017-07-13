<?php
	error_reporting(0);

	if ( isset($_GET['link']) ) {

		require_once 'Youtube.php';

		$youtube = new Youtube();

		//processing the link
		$url = htmlentities($_GET['link']);
		$quality = htmlentities($_GET['format']);

		if( preg_match('/watch/', $url) ){
				try {
                    echo "<table border='0'>";
                    echo "<tr><td style='padding: 20px;'><b>Thumbnails</b></td><td style='padding: 20px;'><b>Title</b></td><td><b>Max Quality</b></td></tr>";
					$youtube->getVideoUrl($url , $quality);
                    echo "</table>";
				} catch(Exception $e){
					echo $e->getMessage();
				}

		} else if( preg_match('/videos/', $url) ){

				try {
                    echo "<table border='0'>";
                    echo "<tr><td style='padding: 20px;'><b>Thumbnails</b></td><td style='padding: 20px;'><b>Title</b></td><td><b>Max Quality</b></td></tr>";
                    $youtube->vChannel($url , $quality);
                    echo "</table>";
				} catch(Exception $e){
					echo $e->getMessage();
				}

		} else if( preg_match('/playlist\?list/', $url) ){

				try {
                    echo "<table border='0'>";
                    echo "<tr><td style='padding: 20px;'><b>Thumbnails</b></td><td style='padding: 20px;'><b>Title</b></td><td>Max Quality</b></td></b></tr>";
                    $youtube->getPlaylistVideos($url , $quality);
                    echo "</table>";
				} catch(Exception $e){
					echo $e->getMessage();
				}

		} else {
				die("We'll add the support for this link sooner!<br>Thanks for visiting :)");
		}
		die();
	}
?>

<!doctype html>
<head>
	<title>Youtube Multi Download</title>
</head>
<body>
<h2>Youtube Multi Downloader (BETA2.0 - Rev A)</h2>
<h3>
	Enter any video/video channel/playlist link<br />
	<h4>
		E.g.<br />
		video : https://www.youtube.com/watch?v=tLgjU-CDDfM<br />
		video channel : https://www.youtube.com/user/allindiabakchod/videos<br />
		playlist : https://www.youtube.com/playlist?list=PLF05eR2VJLBl8g0Tll1Ufk0l4pw5ELR4h
	</h4>
</h3>

<form name="links" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="GET">
	Enter your link here : <input type="text" name="link" style="width: 400px">

	<div>
		Select Quality :&nbsp;&nbsp;&nbsp;
<!--        <select name="quality">-->
<!--            <option value="(Max 480p)">480p</option>-->
<!--            <option value="1080p">1080p</button>-->
<!--            <option value="720p">720p</option>-->
<!--            <option value="360p">360p</option>-->
<!--            <option value="240p">240p</option>-->
<!--            <option value="144p">144p</option>-->
<!--        </select>-->
        <select name="format">
            <option value="mp4">mp4</option>
            <option value="3gp">3gp</option>
        </select>
	</div>

	<p>NOTE: Only videos with the quality available will be displayed</p>
	<input type="submit">
</form>
</body>
</html>
