<?php
	/**
	 * processing the uploaded files
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */	
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$error_code = 0;
	$error = "";
	
	include_once(CLASS_UPLOAD);
	$upload = new Upload();

	$upload->setInvalidFileExt(explode(",", CONFIG_UPLOAD_INVALID_EXTS));
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_UPLOAD)
	{
		$error_code = 500;
		$error = SYS_DISABLED;
	}else	
	if(!$upload->isFileUploaded('upload'))
	{
		$error_code = 101;
		$error = ERR_FILE_NOT_UPLOADED;
	}else 
	if(!$upload->moveUploadedFile(CONFIG_SYS_DEFAULT_PATH))
	{
		$error_code = 201;
		$error = ERR_FILE_MOVE_FAILED;
	}	
	else
	if(!$upload->isPermittedFileExt(explode(",", CONFIG_UPLOAD_VALID_EXTS)))
	{
		$error_code = 202;
		$error = ERR_FILE_TYPE_NOT_ALLOWED;
	}else
	if(defined('CONFIG_UPLOAD_MAXSIZE') && CONFIG_UPLOAD_MAXSIZE && $upload->isSizeTooBig(CONFIG_UPLOAD_MAXSIZE))
	{
		$error_code = 101;
		$error = sprintf(ERROR_FILE_TOO_BID, transformFileSize(CONFIG_UPLOAD_MAXSIZE));
	}else{
		$error_code = 0;
	}
// Required: anonymous function reference number as explained above.
$funcNum = @$_GET['CKEditorFuncNum'] ;
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = @$_GET['CKEditor'] ;
// Optional: might be used to provide localized messages.
$langCode = @$_GET['langCode'] ;
$path = preg_replace('~^'.preg_quote(_URL, '~').'~s', '', getFileUrl(CONFIG_SYS_DEFAULT_PATH.'/'.$upload->getFileName()));
?>
<script type='text/javascript'>
	var a = window.parent ? window.parent : (window.opener ? window.opener : null);
	if (a) {
		if (a.CKEDITOR) {
			a.CKEDITOR.tools.callFunction(<?php echo $funcNum; ?>, '<?php echo $path; ?>', '<?php echo $error; ?>');
		};
	};
</script>
