<?php

/**
 * Created by PhpStorm.
 * User: Chinmaya
 * Date: 5/22/2016
 * Time: 07:03 AM
 */

error_reporting(0);

class Youtube{
    public static $count = 1;
    public static $videoArray;

    function __construct( )
    {
        $videoArray = array();
    }

    /* Returns the video info */
    public function getVideoInfo($url) {
        $data = json_decode(file_get_contents('http://www.youtube.com/oembed?url=' . $url . '&format=json'), true);
        return $data;
    }

    /* Video Links Download */
    public function getVideoUrl( $url , $format, $quality = "(Max 480p)" ){
        $video_info = $this->getVideoInfo($url);
        $vid_id = explode('=', $url)[1];

        parse_str(file_get_contents('https://www.youtube.com/get_video_info?video_id='.$vid_id), $file);
        $title = $file['title'];
        $streams = explode(',', $file["url_encoded_fmt_stream_map"]);

        echo "<tr>";

        foreach($streams as $item) {
            parse_str($item, $data);
            $data['type'] = explode(';', $data['type'])[0];
            if(stripos($data['type'], $format) !== false) {
                echo "<td style='padding: 10px 20px;'> <img style='height: 100px; overflow: hidden; border-radius: 5px;' src='" . $video_info['thumbnail_url'] . "' class='img-responsive' /></td>";
                echo "<td style='padding: 10px;'> <a href =" . $data['url']. " download>" . $title . "</a></td>";
                echo "<td style='padding: 10px;'>" . $video_info['height'] . "p</td>";
                $this->count += 1;
                break;
            }
        }

        echo "</tr>";
} /* End of getVideoUrl */

    /*  Playlist Links Download */
    public function getPlaylistVideos( $url , $quality ){
        /* Initial Playlist 100 links Loader */
        $doc = new DOMDocument();
        $doc->loadHTML( file_get_contents($url) );
        $flag = true;
        foreach ($doc->getElementsByTagName('a') as $link) {
            if( $flag ){
                if( preg_match( '/index\=/' , $link->getAttribute('href') ) ){
                    $this->getVideoUrl( 'http://youtube.com'.$link->getAttribute('href') , $quality) ;
                    $flag = false;
                }
            }
            else $flag = true;
        }

        /* Check for any Load More button */
        foreach ( $doc->getElementsByTagName('button') as $btn ){
            if( $btn->hasAttribute('data-uix-load-more-href') ){
                $this->getMoreLinks( "http://www.youtube.com".$btn->getAttribute('data-uix-load-more-href') , $quality );
            }
        }
    }

    /* Video Channel Links */
    function vChannel( $url , $quality){
        $info = explode( 'Uploads' , file_get_contents($url) )[1];
        $dom = new DOMDocument;
        $dom->loadHTML($info);
        $links = array();
        $flag = False;
        foreach( $dom->getElementsByTagName('a') as $link )
        {
            if($flag)
            {
                $flag = False;
                continue;
            }
            $flag = True;
            if( preg_match('/watch\?v=/', $link->getAttribute('href')) ) //getting only video links
            {
                array_push( $links , $link->getAttribute('href') );
            }
        }

        //sending links to video()
        foreach ($links as $link) {
            $this->getVideoUrl('http://www.youtube.com'.$link , $quality);
        }
    }


    //provide only AJAX Load More button Links
    public function getMoreLinks( $newLink , $quality ){
        $toggle = true;
        $data = (array)json_decode( file_get_contents($newLink) );
        $dom = new DOMDocument();
        $dom->loadHTML( $data['content_html'] );
        foreach ( $dom->getElementsByTagName('a') as $link  ){
            if( $toggle ) {
                if ( preg_match("/watch\?v\=/", $link->getAttribute('href')) )
                    //echo Youtube::$count++ . " : " . htmlentities($link->getAttribute('href')) . "<br><br>";
                    //$this->getVideoUrl( $link->getAttribute('href') , $quality );
                    Youtube::$videoArray[] = "http://www.youtube.com".$link->getAttribute('href');
                $toggle = false;
            } else $toggle=true;
        }

        if ( !empty($data['load_more_widget_html']) ){
            $loadMore = new DOMDocument();
            $loadMore->loadHTML( $data['load_more_widget_html'] );

            foreach ( $loadMore->getElementsByTagName('button') as $btn ){
                $this->getMoreLinks( "http://www.youtube.com".$btn->getAttribute('data-uix-load-more-href') , $quality );
            }
        } else{
            /* After complete List is Generated */
            foreach (Youtube::$videoArray as $key => $value)
                $this->getVideoUrl($value , $quality);
        }
    }

}

?>