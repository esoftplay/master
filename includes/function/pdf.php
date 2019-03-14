<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');


/*
$config based on: http://pdfmake.org/playground.html
*/
function pdf_export($config, $title = 'new-file')
{
	global $sys;
	$title = menu_save($title).'.pdf';
	if (empty($config['content']))
	{
		$msg = lang('invalid configuration for pdf maker');
	}else{
		$msg = lang('the file has been downloaded, you can close this window now');
		link_js(_FUNC.'js/pdf/pdfmake.min.js');
		link_js(_FUNC.'js/pdf/vfs_fonts.js');
		?>
		<script type="text/javascript">
				var PDFjs = <?php echo json_encode($config); ?>;
				pdfMake.createPdf(PDFjs).download('<?php echo $title; ?>');
			_Bbc(function($){
				window.setTimeout(function(){
					window.close();
				}, 2000)
			});
		</script>
		<?php
	}
	$sys->set_layout('blank');
	?>
	<div class="container">
		<div class="jumbotron">
		  <h1><?php echo $title; ?></h1>
		  <p><?php echo $msg; ?></p>
		  <p><a class="btn btn-danger btn-lg" href="#" onclick="window.close();" role="button"><?php echo lang('Close'); ?></a></p>
		</div>
	</div>
	<?php
}
/*========================================================
 *	$param = array(
 			'title',
 			'author',
 			'created',
 			'image', // URL
 			'content',
 			'modified',
 			'category'
 		);
 *======================================================*/
function pdf_write($param, $paper = 'a4', $layout = 'portrait')
{
	global $sys;
	$text = $sys->curl($param['image']);
	if (!empty($text))
	{
		$param['image'] = 'data:image/png;base64,'.base64_encode($text);
	}else{
		$param['image'] = '';
	}
	$page = array(
		'content' => array(
			array(
				'text'  => $param['title'],
				'style' => 'title'
				),
			array(
				'columns' => array(
					array(
						'text'  => $param['author'],
						'style' => 'author'
						),
					array(
						'text'  => $param['created'],
						'style' => 'created'
						)
					),
				),
			array(
				'image' => $param['image'],
				'style' => 'image',
				'width' => 520
				),
			array(
				'text'  => pdf_cleaner($param['content']),
				'style' => 'content'
				),
			array(
				'text'  => $param['modified'],
				'style' => 'modified'
				),
			array(
				'text'  => $param['category'],
				'style' => 'category'
				),
			),
		'styles' => array(
			'title'    => array(
				'fontSize' => 18,
				'bold'     => true,
				'margin'   => [0,0,0,20]
				),
			'author'   => array(
				'italic' => true
				),
			'created'  => array(
				'italic'    => true,
				'alignment' => 'right'
				),
			'image'    => array(
				'alignment' => 'center',
				'margin'    => [0,0,0,10]
				),
			'content'  => array(
				),
			'modified' => array(
				'italic'    => true,
				'alignment' => 'right',
				'margin'    => [0,10,0,0]
				),
			'category' => array(
				'italic'    => true,
				'margin'    => [0,20,0,0]
				),
			),
		'pageSize'        => $paper,
		'pageOrientation' => $layout
		);
	if (empty($param['image']))
	{
		unset($page['content'][1]);
	}
	pdf_export($page, $param['title']);
	/*
	echo json_encode($page);
	pr($page, $paper, $layout, __FILE__.':'.__LINE__);die();
	$config = array('paper' => $paper, 'layout' => $layout);
	$pdf = _lib('pdf', $config);
	$pdf -> ezSetCmMargins( 2, 1.5, 1, 1);
	$pdf->selectFont( './fonts/Helvetica' ); //choose font

	$all = $pdf->openObject();
	$pdf->saveState();
	$pdf->setStrokeColor( 0, 0, 0, 1 );

	// footer
	$pdf->addText( 250, 822, 6, config('site','title') );
	$pdf->line( 10, 40, 578, 40 );
	$pdf->line( 10, 818, 578, 818 );
	$pdf->addText( 30, 34, 6, _URL);
	$pdf->addText( 250, 34, 6, 'Powered by esoftplay.com' );
	$pdf->addText( 450, 34, 6, 'Created '.$param['created'] );

	$pdf->restoreState();
	$pdf->closeObject();
	$pdf->addObject( $all, 'all' );
	$pdf->ezSetDy( 30, 'makeSpace' );

	$pdf->ezText( $param['title'], 16 );
	$pdf->ezText( pdf_date($param), 6 );
	if(!empty($param['image']))
	{
		$pdf->ezImage($param['image'],5,0,'full','left');
	}
	$pdf->ezText( pdf_cleaner($param['content']), 10 );

	$options = array(
		'Content-Disposition' => menu_save($param['title']).'.pdf'
	,	'Accept-Ranges'				=> 0
	);
	$pdf->ezStream($options);
	exit;
	*/
}

function pdf_decode( $string )
{
	$string = strtr( $string, array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace_callback(
        '/&#([0-9]+);/m',
        function ($matches) {
            return chr($matches[1]);
        },
        $string
    );

	return $string;
}

function get_php_setting ($val )
{
	$r = ( ini_get( $val ) == '1' ? 1 : 0 );
	return $r ? 'ON' : 'OFF';
}

function pdf_cleaner( $text )
{
	// Ugly but needed to get rid of all the stuff the PDF class cant handle
	$text = preg_replace("#\s{2,}#s", " ", $text );
	$text = str_replace( '<p>', 			"\n\n", 	$text );
	$text = str_replace( '<P>', 			"\n\n", 	$text );
	$text = str_replace( '<br />', 			"\n", 		$text );
	$text = str_replace( '<br>', 			"\n", 		$text );
	$text = str_replace( '<BR />', 			"\n", 		$text );
	$text = str_replace( '<BR>', 			"\n", 		$text );
	$text = str_replace( '<li>', 			"\n - ", 	$text );
	$text = str_replace( '<LI>', 			"\n - ", 	$text );
#	$text = str_replace( '{mosimage}', 		'', 		$text );
#	$text = str_replace( '{mospagebreak}', 	'',			$text );
	$text = strip_tags( $text, '<u>' );
	$text = pdf_decode( $text );
	return $text;
}