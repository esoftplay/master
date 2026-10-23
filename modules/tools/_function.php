<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function tools_askai($email, $subject, $question)
{
	// return shell_exec(
	// 	'source /Users/me/venv/bin/activate'."\n".
	// 	'python /Users/me/Downloads/openai/email/answer.py "'.$email.'" "'.addslashes($subject).'" "'.addslashes($question).'" '
	// );
	return shell_exec(
		'source /Users/me/venv/bin/activate'."\n".
		'python /Users/me/Downloads/openai/email/answer.py "'.$email.'" "'.addslashes($subject).'" "'.addslashes($question).'" > /dev/null 2>&1 &'
	);
}
/*
source /Users/me/venv/bin/activate
python /Users/me/Downloads/openai/email/answer.py "danang@fisip.net" "pemrograman python" "bagaimana cara memulai belajar python"
python /Users/me/Downloads/openai/email/answer.py "danang@fisip.net" "menyapa" "apakabar"
python /Users/me/Downloads/openai/email/answer.py "danang@fisip.net" "tanya angka" "5+2 berapa?"
*/