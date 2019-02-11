<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

class uploader
{
    private $allowedExtensions = array();
    private $sizeLimit = 2097152;
    private $file;
    private $fileresult;
    private $inputField;

    function __construct($allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'bmp'), $sizeLimit = 0)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit ? $sizeLimit : min($this->toBytes(ini_get('upload_max_filesize')), $this->toBytes(ini_get('post_max_size')));
        $this->checkServerSettings();
        if (isset($_GET['qqfile'])) {
            $this->file = new uploaderXML();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new uploaderFORM();
        } else {
            $this->file = false;
        }
    }
    public function build($path, $tmp, $current = array(), $inputName = 'files', $text = 'Add Images', $node_id = 'file-uploader')
    {
        if(isset($_GET['qqfile']))
        {
            if(!file_exists(_ROOT.$tmp))
            {
                _func('path');
                path_create(_ROOT.$tmp);
            }
            $result = $this->handleUpload(_ROOT.$tmp);
            if (!empty($result)) {
                output_json($result);
            }
        }
        global $sys;
        ob_start();
        ?>
<div id="<?php echo $node_id;?>">
    <noscript> <p>Please enable JavaScript to use file uploader.</p> </noscript>
</div>
       <?php
       if (!empty($current)) {
            echo '<div id="'.$node_id.'-current">';
           foreach ($current as $image) {
               echo image($path.$image);
           }
           echo '</div>';
       }
       ?>
<script type="text/javascript">
var b = new qq.FileUploader({
    element: document.getElementById('<?php echo $node_id;?>'),
    action: document.location.href,
    path: "<?php echo _URL.$tmp;?>",
    text: "<?php echo $text;?>",
    name: "<?php echo $inputName;?>",
    append: document.getElementById('<?php echo $node_id;?>-current'),
    debug: false
});
</script>
       <?php
       $sys->link_js(_URL.'includes/lib/uploader/uploader.js');
       $sys->link_css(_URL.'includes/lib/uploader/uploader.css');
       $this->inputField = ob_get_contents();
       ob_end_clean();
    }
    public function input()
    {
        return $this->inputField;
    }
    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true, 'result'=>'filename.jpg') or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        $this->file->path = $uploadDirectory;
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        $this->fileresult = $uploadDirectory . $filename . '.' . $ext;
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            return array('success'=>true, 'result' => $filename.'.'.$ext);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }

    }
    function name()
    {
        $out = $this->result();
        return $out['basename'];
    }
    function path()
    {
        $out = $this->result();
        return $out['dirname'].'/';
    }
    private function result()
    {
        if(is_array($this->fileresult))
        {
            return $this->fileresult;
        }else{
            $this->fileresult = pathinfo($this->fileresult);
            return $this->fileresult;
        }
    }
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class uploaderXML {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class uploaderFORM {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

/*
// Simple Example :
$uploader = _lib('uploader', array('jpg', 'jpeg', 'gif', 'png', 'bmp'), (2*1024*1024));
$uploader->build(
  $path      = 'images/modules/test/',
  $tmp       = 'images/cache/0/',
  $current   = array('img1.jpg', 'img2.jpg'),
  $inputName = 'files',
  $text      = 'Add Images',
  $node_id   = 'file-uploader'
);
echo '<form>'.$uploader->input().'</form>';
#*/