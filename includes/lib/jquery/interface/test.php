<?php
class ngetest
{
	function ngetest()
	{
		$dir = dirname(dirname(__FILE__)).'/';
		include_once $dir.'jquery.php';
		$this->jquery = $jquery;
	}
	function accordion()
	{
		$r = array(
			'ini pertanyaan' => 'jawaban yang akana di sampaikan'
		,	'selanjutnya adalah tanggapan' => 'inilah tanggapan yang bakal menjadi menjadi statement'
		);
		$j = $this->jquery->widget('interface');
		$j->accordion($r);
	}
	function carousel()
	{
		$array = array(
			'Moon eclipse'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw1.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw1.jpg')
		,	'Rain drops'	=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw2.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw2.jpg')
		,	'Church'			=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw3.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw3.jpg')
		,	'City lights'	=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights1.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights1.jpg')
		,	'Flash lights'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights2.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights2.jpg')
		,	'Laser lights'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights3.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights3.jpg')
		);
		$j = $this->jquery->widget('interface');
		$j->carousel($array);
	}
	function drag()
	{
		$j = $this->jquery->widget('interface');
		$j->drag('Cuman Mo ngetest drag');
	}
	function fisheye()
	{
		$array = array(
			'Home'			=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/home.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw1.jpg')
		,	'Email'			=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/email.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw2.jpg')
		,	'Display'		=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/display.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw3.jpg')
		,	'Clock'			=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/clock.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights1.jpg')
		,	'Web'				=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/web.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights2.jpg')
		,	'Home 2'		=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/home.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw1.jpg')
		,	'Email 2'		=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/email.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw2.jpg')
		,	'Display 2'	=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/display.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw3.jpg')
		,	'Clock 2'		=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/clock.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights1.jpg')
		,	'Web 2'			=> array('thumb' => 'http://interface.eyecon.ro/demos/images/bar/web.png', 'link'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights2.jpg')
		);
		$j = $this->jquery->widget('interface');
		$j->fisheye($array);
	}
	function imagebox()
	{
		$array = array(
			'Moon eclipse'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw1.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw1.jpg')
		,	'Rain drops'	=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw2.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw2.jpg')
		,	'Church'			=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/bw3.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_bw3.jpg')
		,	'City lights'	=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights1.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights1.jpg')
		,	'Flash lights'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights2.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights2.jpg')
		,	'Laser lights'=> array('image' => 'http://interface.eyecon.ro/demos/images/imagebox/lights3.jpg', 'thumb'=>'http://interface.eyecon.ro/demos/images/carousel/th_lights3.jpg')
		);
		$j = $this->jquery->widget('interface');
		$j->imagebox($array);
	}
	function slideshow()
	{
		$r = array(
			'Panda Emotion'			=> 'http://interface.eyecon.ro/demos/images/panda/Emotion.jpg'
		,	'Panda 4X4 Climbing'=> 'http://interface.eyecon.ro/demos/images/panda/Climbing.jpg'
		,	'Panda Dynamic'			=> 'http://interface.eyecon.ro/demos/images/panda/Dynamic.jpg'
		,	'Panda Actual'			=> 'http://interface.eyecon.ro/demos/images/panda/Actual.jpg'
		,	'Fiat Panda'				=> 'http://interface.eyecon.ro/demos/images/panda/Panda.jpg'
		,	'Panda Active'			=> 'http://interface.eyecon.ro/demos/images/panda/Active.jpg'
		);
		$j = $this->jquery->widget('interface');
		$j->slideshow($r);
	}
	function sortable()
	{
		$array = array(
			'position-1' => array(
												'block-1' => array( 'title' => 'First Block', 'content' => 'this is the content (could be html)')
											,	'block-2' => array( 'title' => 'Second Block', 'content' => 'this is the content (could be html)'))
		,	'position-2' => array(
												'block-3' => array( 'title' => 'Tirth Block', 'content' => 'this is the content (could be html)')
											,	'block-4' => array( 'title' => 'Fourth Block', 'content' => 'this is the content (could be html)'))
		);
		$this->jquery->load('1.1.2'); // OPTION IN OTHER VERSION OF JQUERY
		$j = $this->jquery->widget('interface');
		$j->sortable($array);
	}
	function sorttab()
	{
		$array = array(
			'position-1' => 'this is the content (could be html)'
		,	'position-2' => 'this is the content (could be html)'
		);
//		$this->jquery->load('1.1.2'); // OPTION IN OTHER VERSION OF JQUERY
		$j = $this->jquery->widget('interface');
		$j->sorttab($array);
	}
	function tooltip()
	{
		echo '<a href="http://fisip.net/" title="in adalah title yang jadi tip">test</a>';
		$j = $this->jquery->widget('interface');
		$j->tooltip($tagHTML = 'a', $className = 'tooltip', $css='tooltip', $params = array());
	}
	function ttabs()
	{	?>
		<textarea id="testArea" cols="50" rows="10"></textarea>
		<a href="#" onclick="$('#testArea').EnableTabs()">enable tabs in textarea</a> 
		<a href="#" onclick="$('#testArea').DisableTabs()">disable tabs in textarea</a>
		<?php		$j = $this->jquery->widget('interface');
	}
	function windows()
	{
		$array = array(
			'Open Window'	=> array('title' => 'Window Title', 'link' => 'http://framework-2008.dot.jc/new/admin/')
		);
		$this->jquery->load('1.1.2'); // IT WORK ONLY IN VERSION 1.1.2
		$j = $this->jquery->widget('interface');
		$j->windows($array);
		echo '<a href="http://localhost/" onClick="return winOpen(this);" id="windowOpen" title="Ngetest Login" style="float: right;">Login</a>';
	}
}

$t = new ngetest();
$v = 'test_class';
$r = get_class_methods($t);
$u = seo_uri();
$u.= strstr($u, '?') ? '&' : '?';
$u.= $v.'=';
$out = '';
foreach((array)$r AS $key => $func) {
	if(!preg_match('~^ngetest~', $func)){
		$out .= "<br /><a href=".$u.$func.">$func</a>";
	}
}
$exec = false;

if($_GET[$v]) {
	$c = $_GET[$v];
	if(in_array($c, $r)) {
		$t->$c();
		$exec = true;
	}
}
if(!$exec) echo $out;

?>