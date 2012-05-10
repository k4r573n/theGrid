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

/**
 *
 *  BUGS:
 *  - crasy things happens if xpos ypos is in the bbox
 *	- ?
 *
 *
 *
 *
 */

//differenz between two lines e.g. equivalent to 500m
//$delta_x = 0.007328667;
//$delta_y = 0.00449325;
$delta_x = 0.0007328667;
$delta_y = 0.000449325;

if (isset($_GET['delta_x']))
	$delta_x = $_GET['delta_x'];
if (isset($_GET['delta_y']))
	$delta_y = $_GET['delta_y'];

$boarder_width = $delta_x * 0.3;//0.0007328667;
$boarder_high = $delta_y * 0.3;// 0.000449325;
//static $boarder_width = 0.0007328667;
//static $boarder_high = 0.000449325;

if (isset($_GET['boarder_width']))
	$boarder_width = $_GET['boarder_width'];
if (isset($_GET['boarder_high']))
	$boarder_high = $_GET['boarder_high'];


static $time = "2009-08-29T13:05:43Z";//Todo
static $user = "theGrid";


$left = 10.5321550369263;
$right = 10.5363178253174;
$top = 52.2712562711988;
$bottom = 52.2697068495611;

if (isset($_GET['left']))
	$left = $_GET['left'];
if (isset($_GET['right']))
	$right = $_GET['right'];
if (isset($_GET['top']))
	$top = $_GET['top'];
if (isset($_GET['bottom']))
	$bottom = $_GET['bottom'];


 //$xpos = $left;
 //$ypos = $top;
 //$xpos = $left+$boarder_width;
 //$ypos = $top-$boarder_high;

//the start position of the grid (e.g. top left corner)
$xpos = $left-1*$delta_x;
$ypos = $top+1*$delta_y;
 
if (isset($_GET['xpos']))
 $xpos = $_GET['xpos'];
if (isset($_GET['ypos']))
 $ypos = $_GET['ypos'];

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
		"\t\t$tags\n".
  "\t</way>\n";
}

function letter($id, $x, $y, $position, $tag) {
	global $time, $user;
	print "\t<node id='$id' timestamp='$time' user='$user' visible='true' version='1' lat='$y' lon='$x'>\n".
		"\t\t<tag k='grid' v='letter' />\n".
		"\t\t<tag k='letter:num' v='$position' />\n".
		"\t\t<tag k='letter:alph' v='".num2alph($position)."' />\n".
		"\t\t$tag\n".
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
	$xstart = floor(($left - $xpos) / $delta_x);//idx of first column intersecting bbox
	$xend	  = ceil(($right - $xpos) / $delta_x);//idx of last column intersecting bbox
	$ystart = floor(($ypos - $top) / $delta_y);//idx of first row intersecting bbox
	$yend   = ceil(($ypos - $bottom) / $delta_y);//idx of last row intersecting bbox
	
	//where is the first line - % didn't work
	$xoffset = $delta_x - mod(floor(($left - $xpos)*1000000)/1000000, $delta_x);
	$yoffset = $delta_y - mod(floor(($ypos - $top)*1000000)/1000000, $delta_y);


	//generate Text
	//left/right
	for ($i=0; $i<$yend-$ystart; $i++) {
		$tag = "";
		if ($ypos < $top)
			//top left corner of the grid is in the map area
			$tmp_ypos = $ypos+$delta_y/2-$i*$delta_y;
		else
			$tmp_ypos = $top-$yoffset+$delta_y/2-$i*$delta_y;
			//top left corner of the grid is not in the map area

		if (($tmp_ypos > $top)||($tmp_ypos < $bottom)) continue;
		if ($tmp_ypos > $top-$boarder_high)
		 	$tag = "\n\t\t<tag k='conflict' v='top' />";
		elseif ($tmp_ypos < $bottom+$boarder_high)
		 	$tag = "\n\t\t<tag k='conflict' v='bottom' />";
		letter($nid+$i*2, $left+($boarder_width/2), $tmp_ypos, $ystart+$i, "<tag k='letter:pos' v='left' />$tag");
		letter($nid+$i*2+1, $right-($boarder_width/2), $tmp_ypos, $ystart+$i, "<tag k='letter:pos' v='right' />$tag");
	}
	$nid = $nid+$i*2;//next ID

	//top/bottom
	for ($i=0; $i<$xend-$xstart; $i++) {
		$tag = "";
		if ($xpos > $left)
			//top left corner of the grid is in the map area
			$tmp_xpos = $xpos-$delta_x/2+$i*$delta_x;
		else
			//top left corner of the grid is not in the map area
			$tmp_xpos = $left+$xoffset-$delta_x/2+$i*$delta_x;

		if (($tmp_xpos > $right)||($tmp_xpos < $left)) continue;
		if ($tmp_xpos > $right-$boarder_width)
		 	$tag = "\n\t\t<tag k='conflict' v='right' />";
		elseif ($tmp_xpos < $left+$boarder_width)
		 	$tag = "\n\t\t<tag k='conflict' v='left' />";
		letter($nid+$i*2, $tmp_xpos, $top-($boarder_high/2), $xstart+$i, "<tag k='letter:pos' v='top' />$tag");
		letter($nid+$i*2+1, $tmp_xpos, $bottom+($boarder_high/2), $xstart+$i, "<tag k='letter:pos' v='bottom' />$tag");
	}
	$nid = $nid+$i*2;//next ID

	//debug point (top left corner of the grid)
	letter($nid, $xpos, $ypos, 0, "<tag k='debug' v='origin' />");
	$nid = $nid + 1;



	//horizontal lines
	for ($i=0; $i<$yend-$ystart-1; $i++) {
		$tag = "";
		if ($ypos < $top)
			//top left corner of the grid is in the map area
			$tmp_ypos = $ypos-$i*$delta_y;
		else
			//top left corner of the grid is not in the map area
			$tmp_ypos = $top-$yoffset-$i*$delta_y;

		if ($tmp_ypos >= $top-$boarder_high)
			//line is in the area of the top boarder
			$tag ="\t\t<tag k='conflict' v='top' />\n";
		else if ($tmp_ypos <= $bottom+$boarder_high)
			//line is in the area of the bottom boarder
			$tag ="\t\t<tag k='conflict' v='bottom' />\n";
		hline($nid+$i, $tmp_ypos, "<tag k='grid' v='line' />\n\t\t<tag k='line' v='horizontal' />\n".$tag);
	}
	$nid = $nid+$i;//next ID

	//vertical lines
	for ($i=0; $i<$xend-$xstart-1; $i++) {
		$tag = "";
		if ($xpos > $left)
			//top left corner of the grid is in the map area
			$tmp_xpos = $xpos+$i*$delta_x;
		else
			//top left corner of the grid is not in the map area
			$tmp_xpos = $left+$xoffset+$i*$delta_x;

		if ($tmp_xpos <= $left+$boarder_width)
			//line is in the area of the left boarder
			$tag ="\t\t<tag k='conflict' v='left' />\n";
		else if ($tmp_xpos >= $right-$boarder_width)
			//line is in the area of the right boarder
			$tag ="\t\t<tag k='conflict' v='right' />\n";
		vline($nid+$i, $tmp_xpos, "<tag k='grid' v='line' />\n\t\t<tag k='line' v='vertical' />\n".$tag);
	}
	$nid = $nid+$i;//next ID

	print "</osm>\n";
?>
