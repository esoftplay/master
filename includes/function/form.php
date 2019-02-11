<?php  if ( ! defined('_VALID_BBC')) exit('No direct script access allowed');

function form_value($form_id, $post)
{
  if(!empty($post))
  {
    $setDefaultFormValue = 'defaultForm'.session_id();
    global $$setDefaultFormValue;
    $arr = array();
    foreach($post AS $id => $dt)
    {
       $data = is_array($dt) ? "['".implode("', '", $dt)."']" : $dt;
       $arr[] = "['$id', '".$dt."']";
    }
    ?>
    <script type="text/Javascript">
      <?php
      if(!$$setDefaultFormValue)
      {
        $$setDefaultFormValue = true;
        ?>
        function in_array(needle, haystack, strict) {
          var found = false, key, strict = !!strict;
          for (key in haystack) {
            if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
              found = true;
              break;
            }
          }
          return found;
        }
        function setDefaultFormValue(form_id, post) {
          var Obj = document.getElementById(form_id);
          with(Obj) {
            for(var i=0; i < post.length; i++) {
              field_name= eval(post[i][0]);
              field_val   = post[i][1];
              // radio, select, checkbox, text, hidden, textarea, password
              var thisType = field_name.type;
              if(thisType) thisType = thisType.toLowerCase();
              else thisType = 'text';
              if(thisType == 'radio') {
                field_name.checked = (field_name.value == field_val) ? true : false;
              }else
              if(thisType == 'select') {
                if(field_name instanceof Array) {
                  for(i=0; i < field_name.length; i++)
                    field_name[i].selected = ( in_array(field_name[i].value, field_val) ) ? true : false;
                }else{
                  field_name.selected = (field_name.value == field_val) ? true : false;
                }
              }else
              if(thisType == 'checkbox') {
                if(field_name instanceof Array) {
                  for(i=0; i < field_name.length; i++)
                    field_name[i].checked = (in_array(field_name[i].value, field_val)) ? true : false;
                }else{
                  field_name.checked = (field_name.value == field_val) ? true : false;
                }
              }else{
                field_name.value = field_val;
              }
            }
          }
        }
        <?php
      }
      ?>
      var r_fields = [<?php echo implode(', ', $arr);?>];
      setDefaultFormValue('<?php echo $form_id;?>', r_fields);
    </script>
    <?php
  }
}