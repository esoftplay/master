<?php
$r = explode(' ', $config['submenu']);
$y = @$r[0]=='top' ? 'top' : '';
$x = @$r[1]=='left' ? 'left' : '';
echo menu_horizontal($menus, $y, $x);
