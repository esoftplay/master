'use strict';

module.exports = {
	css: [
		"custom/bootstrap-pre.css",
		"github/bootstrap4/dist/css/bootstrap.css",
		"custom/bootstrap-post.css"
		],
	scss: [],
	font: [],
	js: [
		"custom/bootstrap-pre.js",
		"github/jquery/node_modules/jquery/dist/jquery.min.js",
		"github/bootstrap4/dist/js/bootstrap.bundle.js",
		"custom/bootstrap-post.js",
		],
	source: "/Users/me/Sites/php/bootstrap/",
	dest: {
		path: "/var/www/html/master/templates/admin/bootstrap/",
		css: "bootstrap4.min.css",
		js: "bootstrap4.min.js"
	},
	jscompress : 2, // 1=uglify, 2=packer
	watch : 0 // 1=uglify, 2=packer
}