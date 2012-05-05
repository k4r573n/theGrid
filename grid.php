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
 //static $delta_x = 0.007328667;
 //static $delta_y = 0.00449325;

 //static $boarder_high = 0.000449325;
 //static $boarder_width = 0.0007328667;
 static $delta_x = 0.0007328667;
 static $delta_y = 0.000449325;

 static $boarder_width = 0.0007328667;
 static $boarder_high = 0.000449325;

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

 $xpos = $left+$boarder_width;
 $ypos = $top-$boarder_high;

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
    //"\t\t<tag k='highway' v='primary' />\n".
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
    //"\t\t<tag k='highway' v='primary' />\n".
		"\t\t$tags\n".
  "\t</way>\n";
}

function letter($id, $x, $y, $position) {
	global $time, $user;
	print "\t<node id='$id' timestamp='$time' user='$user' visible='true' version='1' lat='$y' lon='$x'>\n".
		"\t\t<tag k='grid' v='letter' />\n".
		"\t\t<tag k='letter:no' v='$position' />\n".
		"\t\t<tag k='letter:alph' v='".num2alph($position)."' />\n".
		"\t\t<tag k='name' v='".num2alph($position)."' />\n".
		"\t</node>\n";
}

function n2a($num) {
	$ret = "Ö";
	if($num>=0 && $num<=25) {
		 $ret = (chr(65+$num));
	}
	return $ret;
}

/* TODO
 * sign 26 is BA insted of AA
 * all signs with A* are missing
 * the same thing with BAA (all A** are missing)
 */
function num2alph($number) {
	$ret = "";
	if ($number <0) return "Ä";

	while($number>=0) {
		$ret = n2a($number%26).$ret;
		$number = floor($number/26);
		if ($number==0) break;
		print "[$number]";
	}

	return $ret;
}

function mod($big, $div) {
	return ($big - ((floor($big/$div) *$div)));
}

	print "<?xml version='1.0' encoding='UTF-8'?>\n";
	print "<osm version='0.6' generator='php-grid-generator'>\n";
  print "	<bounds minlat='$bottom' minlon='$left' maxlat='$top' maxlon='$right' origin='generated' />\n";

	//generate the boarder
	//top
	hline(0, $top, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='top' />\n\t\t<tag k='boarder:kind' v='outer' />\n");
	hline(1, ($top-$boarder_high), "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='top' />\n\t\t<tag k='boarder:kind' v='inner' />\n");
	//bottom
	hline(2, $bottom, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='bottom' />\n\t\t<tag k='boarder:kind' v='outer' />\n");
	hline(3, ($bottom+$boarder_high), "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='bottom' />\n\t\t<tag k='boarder:kind' v='inner' />\n");


	//right
	vline(4, $right, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='right' />\n\t\t<tag k='boarder:kind' v='outer' />\n");
	vline(5, $right-$boarder_width, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='right' />\n\t\t<tag k='boarder:kind' v='inner' />\n");
	//left
	vline(6, $left, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='left' />\n\t\t<tag k='boarder:kind' v='outer' />\n");
	vline(7, $left+$boarder_width, "<tag k='grid' v='boarder' />\n\t\t<tag k='boarder:pos' v='left' />\n\t\t<tag k='boarder:kind' v='inner' />\n");
	$nid= 8;//next ID


	//where begin/end to Count
	$xstart = ($left - $xpos) / $delta_x;
	$xend	  = ($right - $xpos) / $delta_x;
	$ystart = ($ypos - $top) / $delta_y;
	$yend   = ($ypos - $bottom) / $delta_y;
	//
	//where is the first line
	$xoffset = $delta_x - mod(floor(($left - $xpos)*10000)/10000, $delta_x);
	$yoffset = $delta_y - mod(($ypos - $top), $delta_y);


	//generate Text
	//left/right
	for ($i=0; $i<$yend-$ystart; $i++) {
		letter($nid+$i*2, $left+($boarder_width/2), $top-$yoffset+$delta_y/2-$i*$delta_y, $ystart+$i);
		letter($nid+$i*2+1, $right-($boarder_width/2), $top-$yoffset+$delta_y/2-$i*$delta_y, $ystart+$i);
	}
	$nid = $nid+$i*2;//next ID

	//top/bottom
	for ($i=0; $i<$xend-$xstart; $i++) {
		letter($nid+$i*2, $left+$xoffset+$delta_x/2+$i*$delta_x, $top-($boarder_high/2), $xstart+$i);
		letter($nid+$i*2+1, $left+$xoffset+$delta_x/2+$i*$delta_x, $bottom+($boarder_high/2), $xstart+$i);
	}
	$nid = $nid+$i*2;//next ID



	for ($i=0; $i<$yend-$ystart-1; $i++) {
		$tag = "";
		if ($top-$yoffset-$i*$delta_y >= $top-$boarder_high)
			//line is in the area of the top boarder
			$tag ="\t\t<tag k='conflict' v='top' />\n";
		else if ($top-$yoffset-$i*$delta_y <= $bottom+$boarder_high)
			//line is in the area of the bottom boarder
			$tag ="\t\t<tag k='conflict' v='bottom' />\n";
		hline($nid+$i, $top-$yoffset-$i*$delta_y, "<tag k='grid' v='line' />\n\t\t<tag k='line' v='horizontal' />\n".$tag);
	}
	$nid = $nid+$i;//next ID

	for ($i=0; $i<$xend-$xstart-1; $i++) {
		$tag = "";
		if ($left+$xoffset+$i*$delta_x <= $left+$boarder_width)
			//line is in the area of the left boarder
			$tag ="\t\t<tag k='conflict' v='left' />\n";
		else if ($left+$xoffset+$i*$delta_x >= $right-$boarder_width)
			//line is in the area of the right boarder
			$tag ="\t\t<tag k='conflict' v='right' />\n";
		vline($nid+$i, $left+$xoffset+$i*$delta_x, "<tag k='grid' v='line' />\n\t\t<tag k='line' v='vertical' />\n".$tag);
	}
	$nid = $nid+$i;//next ID

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
