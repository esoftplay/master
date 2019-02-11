<?php
asd;

class rtfFile
{
	var $salida;

	function __construct()
	{
		$this->salida="{\\rtf\\ansi ";
		$this->tblColor();
		$this->salida.="{";
	}

	//funciones de tablas
	function tblColor()
	{
		$this->salida.="{\\colortbl;\\red0\\green0\\blue0;\\red0\\green0\\blue255;\\red0\\green255\\blue255;\\red0\\green255\\blue0;\\red255\\green0\\blue255;\\red255\green0\\blue0;\\red255\\green255\\blue0;\\red255\\green255\\blue255;\\red0\green0\\blue128;\\red0\\green128\\blue128;\\red0\\green128\\blue0;\red128\\green0\\blue128;\\red128\\green0\\blue0;\\red128\\green128\\blue0;\red128\\green128\\blue128;\\red192\\green192\\blue192;}";
	}

	//mostrar un mensaje..
	function say($msg)
	{
		$this->salida.=$msg;
	}

	//formato del texto...
	function bold($switch=1)			{	if($switch==0)	$this->salida.="\\b0 ";	else	$this->salida.="\\b ";	}
	function italic($switch=1)		{	if($switch==0)	$this->salida.="\\i0 ";	else	$this->salida.="\\i ";	}
	function underline($switch=1)	{	if($switch==0)	$this->salida.="\\ulnone ";	else	$this->salida.="\\ul ";	}
	function caps($switch=1)			{	if($switch==0)	$this->salida.="\\caps0 ";	else	$this->salida.="\\caps ";	}
	function emboss($switch=1)		{	if($switch==0)	$this->salida.="\\embo0 ";	else	$this->salida.="\\embo ";	}
	function engrave($switch=1)		{	if($switch==0)	$this->salida.="\\impr0 ";	else	$this->salida.="\\impr ";	}
	function outline($switch=1)		{	if($switch==0)	$this->salida.="\\outl0 ";	else	$this->salida.="\\outl ";	}
	function shadow($switch=1)		{	if($switch==0)	$this->salida.="\\shad0 ";	else	$this->salida.="\\shad ";	}
	function sub($switch=1)				{	if($switch==0)	$this->salida.="\\nosupersub ";	else	$this->salida.="\\sub ";	}
	function super($switch=1)			{	if($switch==0)	$this->salida.="\\nosupersub ";	else	$this->salida.="\\super ";	}

	//armado de parrafos..
	function parrafo()		{	$this->salida.="\\par ";	}

	function color($n=0)	{	$this->salida.="\\cf$n ";	}

	//paragraph alignement
	function center()			{	$this->salida.="\\qc ";	}
	function left()				{	$this->salida.="\\ql ";	}
	function right()			{	$this->salida.="\\qr ";	}
	function justify()		{	$this->salida.="\\qj ";	}

	//bordes lugar y luego tipos
	function bordert()		{	$this->salida.="\\brdrt ";	}
	function borderb()		{	$this->salida.="\\brdrb ";	}
	function borderl()		{	$this->salida.="\\brdrl ";	}
	function borderr()		{	$this->salida.="\\brdrr ";	}

	function boxSingle()	{	$this->salida.="\\brdrhair ";	}
	function boxDouble()	{	$this->salida.="\\brdrdb ";	}
	function boxTriple()	{	$this->salida.="\\brdrtriple ";	}
	function boxDotted()	{	$this->salida.="\\brdrdot ";	}
	function boxDashed()	{	$this->salida.="\\brdrdashsm ";	}

	//functiones de ayuda
	function showColorTable()
	{
		$this->parrafo();
		$this->bold();
		$this->say("Tabla de Colores:");
		$this->bold(0);
		$this->parrafo();

		$this->bordert();
		$this->boxSingle();
		for($a=0;$a<16;$a++)
		{
			$this->color($a);
			$this->say("[$a] ABC abc 123 Este es un texto en el color $a.");$this->parrafo();
		}
	}

	function showMethodTable()
	{
		$this->bold();$this->say("Tabla de Colores:");$this->bold(0);$this->parrafo();
		$this->bordert();$this->boxSingle();
		$this->underline();$this->say("FONT:");$this->underline(0);$this->parrafo();
		$this->say("bold(): [1 on | 0 off]"); $this->parrafo();
		$this->say("italic(): [1 on | 0 off]"); $this->parrafo();
		$this->say("underline(): [1 on | 0 off]"); $this->parrafo();
		$this->say("caps(): [1 on | 0 off]"); $this->parrafo();
		$this->say("emboss(): [1 on | 0 off]"); $this->parrafo();
		$this->say("engrave(): [1 on | 0 off]"); $this->parrafo();
		$this->say("outline(): [1 on | 0 off]"); $this->parrafo();
		$this->say("shadow(): [1 on | 0 off]"); $this->parrafo();
		$this->say("sub(): [1 on | 0 off]"); $this->parrafo();
		$this->say("super(): [1 on | 0 off]"); $this->parrafo();
		$this->say("color(): [n index number] user showColorTable for more info."); $this->parrafo();
		$this->underline();$this->say("PARAGRAPH:");$this->underline(0);$this->parrafo();
		$this->say("parrafo()"); $this->parrafo();
		$this->say("center()"); $this->parrafo();
		$this->say("left()"); $this->parrafo();
		$this->say("right()"); $this->parrafo();
		$this->say("justify()"); $this->parrafo();
		$this->underline();$this->say("BORDER:");$this->underline(0);$this->parrafo();
		$this->say("bordert()"); $this->parrafo();
		$this->say("borderb()"); $this->parrafo();
		$this->say("borderl()"); $this->parrafo();
		$this->say("borderr()"); $this->parrafo();
		$this->say("boxSingle"); $this->parrafo();
		$this->say("boxDouble"); $this->parrafo();
		$this->say("boxTriple"); $this->parrafo();
		$this->say("boxDotted"); $this->parrafo();
		$this->say("boxDashed"); $this->parrafo();
		$this->underline();$this->say("MAIN:");$this->underline(0);$this->parrafo();
		$this->say("say()"); $this->parrafo();
		$this->say("getRTF()"); $this->parrafo();
	}


	//sacar el documento
	function getRTF()
	{
		return($this->salida."}}");
	}

}

	$rtf=new rtfFile();
	$rtf->showMethodTable();
	$rtf->showColorTable();
	header("Content-type: application/msword ");
	//header("Content-Length: $largo");
	//header("Content-Disposition: inline; filename=pepe.rtf");
	print $rtf->getRTF();


/*
		$arr=Array(
							aliceblue=>Array(1,"#F0F8FF",240,248,255),
							antiquewhite=>Array(2,"#FAEBD7",249,234,214),
							aqua=>Array(3,"#00FFFF",0,255,255),
							aquamarine=>Array(4,"#7FFFD4",127,255,212),
							azure=>Array(5,"#F0FFFF",240,255,255),
							beige=>Array(6,"#F5F5DC",244,244,220),
							bisque=>Array(7,"#FFE4C4",255,228,196),
							black=>Array(8,"#000000",0,0,0),
							blanchedalmond=>Array(9,"#FFEBCD",255,235,205),
							blue=>Array(10,"#0000FF",0,0,255),
							blueviolet=>Array(11,"#8A2BE2",138,44,225),
							brown=>Array(12,"#A52A2A",164,41,41),
							burlywood=>Array(13,"#DEB887",221,184,135),
							cadetblue=>Array(14,"#5F9EA0",94,157,159),
							chartreuse=>Array(15,"#7FFF00",127,255,0),
							chocolate=>Array(16,"#D2691E",209,104,30),
							coral=>Array(17,"#FF7F50",255,127,80),
							cornflowerblue=>Array(18,"#6495ED",100,148,235),
							cornsilk=>Array(19,"#FFF8DC",255,248,220),
							crimson=>Array(20,"#DC143C",219,20,60),
							cyan=>Array(21,"#00FFFF",0,255,255),
							darkblue=>Array(22,"#00008B",0,0,138),
							darkcyan=>Array(23,"#008B8B",0,138,138),
							darkgoldenrod=>Array(24,"#B8860B",183,133,11),
							darkgray=>Array(25,"#A9A9A9",168,168,168),
							darkgreen=>Array(26,"#006400",0,99,0),
							darkkhaki=>Array(27,"#BDB76B",188,182,107),
							darkmagenta=>Array(28,"#8B008B",138,0,138),
							darkolivegreen=>Array(29,"#556B2F",84,105,46),
							darkorange=>Array(30,"#FF8C00",255,139,0),
							darkorchid=>Array(31,"#9932CC",153,51,204),
							darkred=>Array(32,"#8B0000",138,0,0),
							darksalmon=>Array(33,"#E9967A",232,149,121),
							darkseagreen=>Array(34,"#8FBC8B",142,61,138),
							darkslateblue=>Array(35,"#483D8B",72,61,138),
							darkslategray=>Array(36,"#2F4F4F",46,77,77),
							darkturquoise=>Array(37,"#00CED1",0,204,207),
							darkviolet=>Array(38,"#9400D3",147,0,210),
							deeppink=>Array(39,"#FF1493",255,20,147));
deepskyblue(#00BFFF)
dimgray(#696969)
dodgerblue(#1E90FF)
firebrick(#B22222)
floralwhite(#FFFAF0)
forestgreen(#228B22)
fuchsia(#FF00FF)
gainsboro(#DCDCDC)
ghostwhite(#F8F8FF)
gold(#FFD700)
goldenrod(#DAA520)
gray(#808080)
green(#008000)
greenyellow(#ADFF2F)
honeydew(#F0FFF0)
hotpink(#FF69B4)
indianred(#CD5C5C)
indigo(#4B0082)
ivory(#FFFFF0)
khaki(#F0E68C)
lavender(#E6E6FA)
lavenderblush(#FFF0F5)
lawngreen(#7CFC00)
lemonchiffon(#FFFACD)
lightblue(#ADD8E6)
lightcoral(#F08080)
lightcyan(#E0FFFF)
lightgoldenrodyellow(#FAFAD2)
lightgreen(#90EE90)
lightgrey(#D3D3D3)
lightpink(#FFB6C1)
lightsalmon(#FFA07A)
lightseagreen(#20B2AA)
lightskyblue(#87CEFA)
lightslategray(#778899)
lightsteelblue(#B0C4DE)
lightyellow(#FFFFE0)
lime(#00FF00)
limegreen(#32CD32)
linen(#FAF0E6)
magenta(#FF00FF)
maroon(#800000)
mediumaquamarine(#66CDAA)
mediumblue(#0000CD)
mediumorchid(#BA55D3)
mediumpurple(#9370DB)
mediumseagreen(#3CB371)
mediumslateblue(#7B68EE)
mediumspringgreen(#00FA9A)
mediumturquoise(#48D1CC)
mediumvioletred(#C71585)
midnightblue(#191970)
mintcream(#F5FFFA)
mistyrose(#FFE4E1)
moccasin(#FFE4B5)
navajowhite(#FFDEAD)
navy(#000080)
oldlace(#FDF5E6)
olive(#808000)
olivedrab(#6B8E23)
orange(#FFA500)
orangered(#FF4500)
orchid(#DA70D6)
palegoldenrod(#EEE8AA)
palegreen(#98FB98)
paleturquoise(#AFEEEE)
palevioletred(#DB7093)
papayawhip(#FFEFD5)
peachpuff(#FFDAB9)
peru(#CD853F)
pink(#FFC0CB)
plum(#DDA0DD)
powderblue(#B0E0E6)
purple(#800080)
red(#FF0000)
rosybrown(#BC8F8F)
royalblue(#4169E1)
saddlebrown(#8B4513)
salmon(#FA8072)
sandybrown(#F4A460)
seagreen(#2E8B57)
seashell(#FFF5EE)
sienna(#A0522D)
silver(#C0C0C0)
skyblue(#87CEEB)
slateblue(#6A5ACD)
slategray(#708090)
snow(#FFFAFA)
springgreen(#00FF7F)
steelblue(#4682B4)
tan(#D2B48C)
teal(#008080)
thistle(#D8BFD8)
tomato(#FF6347)
turquoise(#40E0D0)
violet(#EE82EE)
wheat(#F5DEB3)
white(#FFFFFF)
whitesmoke(#F5F5F5)
yellow(#FFFF00)
yellowgreen(#9ACD32)
*/

?>



