<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

define('_VIDEO_EMBED', '<iframe width="100%" height="315" src="https://www.youtube.com/embed/{code}" frameborder="0" allowfullscreen></iframe>');
define('_VIDEO_IMAGE', 'https://i2.ytimg.com/vi/{code}/0.jpg');
define('_AUDIO_EMBED', '<iframe width="100%" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{code}&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>');
define('_AUDIO_IMAGE', 'https://api.soundcloud.com/tracks/{code}?client_id=217f5cd8d53e2815593575eea044de7f');
define('_AUDIO_TOKEN', 'c318190c6f6246a9e89d8d274eced1fc'); // Client Secret
