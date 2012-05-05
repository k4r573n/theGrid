<?php
/* this script should create an Grid in OSM-Tags to use an
 * default renderer to render a nice grid overlay
 */

//	if (!((isset($_GET['left'])) && (isset($_GET['right'])) &&
//			(isset($_GET['top'])) && (isset($_GET['bottom'])))) 
//	{
//			print "Error: no area specified!";
//			exit 0;
//	}

 //differenz between two lines e.g. equivalent to 500m
 static $delta_x = 0.007328667;
 static $delta_y = 0.00449325;

 static $boarder_high = 0.000449325;
 static $boarder_width = 0.0007328667;

 static $time = "2009-08-29T13:05:43Z";
 static $user = "none";

// $left = $_GET['left'];
// $right = $_GET['right'];
// $top = $_GET['top'];
// $bottom = $_GET['bottom'];

 static $left = 10.5321550369263;
 static $right = 10.5363178253174;
 static $top = 52.2712562711988;
 static $bottom = 52.2697068495611;

	//the start position of the grid (e.g. top left corner)
 //$xpos = $_GET['xpos'];
 //$ypos = $_GET['ypos'];

 $xpos = $left;
 $ypos = $top;

/**
 * param: $id e.g. 0, 1, 2, or any int >= 0 - used to calc object ids (1=>-1,-2,-3)
 * param: $x_pos a horizontal position in degree
 * param: $tags a xml string with osm object tags
 */
function vline($id, $x_pos, $tags){
	global $time, $user, $top, $bottom;
	print "\t<node id='".(-3*$id-1)."' timestamp='$time' user='$user' visible='true' version='1' lat='$top' lon='$x_pos' />\n";
	print "\t<node id='".(-3*$id-2)."' timestamp='$time' user='$user' visible='true' version='1' lat='$bottom' lon='$x_pos' />\n";
  print "\t<way id='".(-3*$id-3)."' timestamp='$time' user='$user' visible='true' version='1'>\n".
    "\t\t<nd ref='".(-3*$id-1)."' />\n".
    "\t\t<nd ref='".(-3*$id-2)."' />\n".
    "\t\t<tag k='highway' v='primary' />\n".
		"\t\t$tags\n".
  "\t</way>\n";
}
/**
 * param: $id e.g. 0, 1, 2, or any int >= 0 - used to calc object ids (1=>-1,-2,-3)
 * param: $y_pos a vertical position in degree
 * param: $tags a xml string with osm object tags
 */
function hline($id, $y_pos, $tags){
	global $time, $user, $left, $right;
	print "\t<node id='".((-3*$id)-1)."' timestamp='$time' user='$user' visible='true' version='1' lat='$y_pos' lon='$left' />\n";
	print "\t<node id='".(-3*$id-2)."' timestamp='$time' user='$user' visible='true' version='1' lat='$y_pos' lon='$right' />\n";
  print "\t<way id='".(-3*$id-3)."' timestamp='$time' user='$user' visible='true' version='1'>\n".
    "\t\t<nd ref='".(-3*$id-1)."' />\n".
    "\t\t<nd ref='".(-3*$id-2)."' />\n".
    "\t\t<tag k='highway' v='primary' />\n".
		"\t\t$tags\n".
  "\t</way>\n";
}

function letter($id, $x, $y, $position) {
	print "\t<node id='$id' timestamp='$time' user='$user' visible='true' version='1' lat='$y' lon='$x'>\n".
		"\t\t<tag />\n".
		"\t</node>\n";
}

	print "<?xml version='1.0' encoding='UTF-8'?>\n";
	print "<osm version='0.6' generator='php-grid-generator'>\n";
  print "	<bounds minlat='$bottom' minlon='$left' maxlat='$top' maxlon='$right' origin='generated' />\n";

	//generate the boarder
	//top
	hline(0, $top, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='outertop' />\n");
	hline(1, ($top-$boarder_high), "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='innertop' />\n");
	//bottom
	hline(2, $bottom, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='outerbottom' />\n");
	hline(3, ($bottom+$boarder_high), "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='innerbottom' />\n");

	//right
	vline(4, $right, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='outerright' />\n");
	vline(5, $right-$boarder_width, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='innerright' />\n");
	//left
	vline(6, $left, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='outerleft' />\n");
	vline(7, $left+$boarder_width, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder' v='innerleft' />\n");


	//where begin to Count
	$xstart = ($left - $xpos) / $delta_x;
	$xend	  = ($right - $xpos) / $delta_x;
	$ystart = ($ypos - $top) / $delta_y;
	$yend   = ($ypos - $bottom) / $delta_y;


	//where is the first line
	//$xoffset = $delta_x - (($left - $xpos) % $delta_x);
	//$yoffset = $delta_y - (($ypos - $top) % $delta_y);

//	for ($i=0; $i < 
//  <node id='-1' timestamp='2009-08-29T13:05:43Z' user='none' visible='true' version='1' lat='52.2697068495611' lon='10.5322' />
//  <node id='-2' timestamp='2009-08-29T13:05:43Z' user='none' visible='true' version='1' lat='52.2712562711988' lon='10.5322' />
//
//  <way id='-3' timestamp='2009-09-08T08:07:17Z' user='none' visible='true' version='3'>
//    <nd ref='-1' />
//    <nd ref='-2' />
//    <tag k='highway' v='primary' />
//  </way>
	print "</osm>\n";
?>
