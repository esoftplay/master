/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	config.language = 'en';
	if (typeof config.path=='undefined') {
		config.path = 'images/uploads/';
	};
	if (typeof(_URL) == 'undefined')
	{
		_URL = document.location.toString();
	}
	config.baseHref       = _URL;
	config.contentsCss    = _URL+'user/editor_css';
	config.bodyClass      = 'page';
	config.enterMode      = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	// FILES
	config.filebrowserBrowseUrl    = config.baseHref + 'user/files/ajaxfilemanager.php?editor=ckeditor';
	config.filebrowserUploadUrl    = config.baseHref + 'user/files/ajax_file_upload_quick.php';
	config.filebrowserWindowHeight = 620;
	// FLASH
	config.filebrowserFlashBrowseUrl = config.filebrowserBrowseUrl;
	config.filebrowserFlashUploadUrl = config.filebrowserUploadUrl;
	// IMAGE
	config.filebrowserImageBrowseUrl = config.filebrowserBrowseUrl;
	config.filebrowserImageUploadUrl = config.filebrowserUploadUrl;
	// LINK
	config.filebrowserImageBrowseLinkUrl = config.filebrowserBrowseUrl;
	config.toolbar = [
    { name: 'document',    items : [ 'Source','-','DocProps','Preview','Templates' ] },
    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
    { name: 'editing',     items : [ 'Find','Replace' ] },
    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
    { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
    { name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
    { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
    { name: 'colors',      items : [ 'TextColor','BGColor' ] },
    { name: 'tools',       items : [ 'Maximize', 'ShowBlocks' ] }
	];
	config.toolbar_Basic =[['Source','Bold','Italic','Underline','Strike','-','NumberedList','BulletedList','Blockquote','CreateDiv','-','Format','Link','Unlink','Smiley']];
	config.smiley_descriptions = ['smiley', 'sad', 'wink', 'big smile', 'batting', 'huggs', 'question', 'love', 'blush', 'tongue', 'kiss', 'broken heart', 'ooooh', 'angry', 'mean', 'sun glas', 'worried', 'sweating', 'devil', 'cry', 'laugh loud', 'neutral', 'eye brow', 'rotfl', 'angel', 'glasses', 'bye', 'sleep', 'eye roll', 'loser', 'sick', 'shhhh', 'silent', 'clown', 'silly', 'party', 'tired', 'drool', 'think', 'doh', 'clap', 'nail biting', 'hypnotized', 'liar', 'waiting', 'sighing', 'mad tongue', 'cowboy', 'on phone', 'call me', 'wit send', 'wave', 'time out', 'day dream'];
	for (var i = 0; i < config.smiley_descriptions.length; i++) {
		config.smiley_images[i] = config.smiley_descriptions[i].replace(' ', '_')+'.gif';
	};
	config.smiley_path = 'includes/smiley/';
	config.smiley_columns = 6;

	config.allowedContent = true;
	CKEDITOR.dtd.$removeEmpty.i = 0;
};
