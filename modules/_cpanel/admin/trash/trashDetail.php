<?php defined( '_VALID_BBC' ) or die( 'Restricted access' );

$data = $db->getOne("SELECT params FROM bbc_content_trash WHERE id=".intval($_GET['id']));
if($db->Affected_rows())
{
	$arr = config_decode($data);
  ?>
  <table class="table table-hover">
    <caption><b>Trash Detail</b></caption>
    <tbody>
    	<?php
      $i = 0;
    	foreach((array)$arr AS $var => $value)
    	{
      	?>
        <tr>
          <th><?php echo $var;?></th>
          <td><?php echo $value;?></td>
        </tr>
        <?php
      }
    	?>
    </tbody>
  </table>
  <?php
}
echo $sys->button($Bbc->mod['circuit'].'.'.$Bbc->mod['task']);