<?php
/* Smarty version 4.3.0, created on 2023-05-20 17:49:34
  from 'C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\error.default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6468ec0e4e4602_44236901',
  'has_nocache_code' => true,
  'file_dependency' => 
  array (
    '5c0717b67188819925351a71dd579d618c37c399' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\error.default.tpl',
      1 => 1684020828,
      2 => 'extends',
    ),
    'f4ce704e86addb4260bb5ff1d93bebfef7fab1dd' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\error.default.tpl',
      1 => 1684020828,
      2 => 'file',
    ),
    'dd86ce32d9f12b60bb8d56f71be2a6905475b68a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\layout.full.tpl',
      1 => 1684591840,
      2 => 'file',
    ),
    'e848e711ca05bd7a25eafb1048061856b7c330fd' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\main.header.tpl',
      1 => 1684597551,
      2 => 'file',
    ),
    'd3772abaa62951a204682c42a4173f7a601a6ae4' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\main.navigation.tpl',
      1 => 1684597627,
      2 => 'file',
    ),
    'a0d66353770ae2f0a8cf11be59b3495a1da010fc' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\main.topnav.tpl',
      1 => 1684591052,
      2 => 'file',
    ),
    '174c8f0409d60094ecd35ebc40b1b4cbaac141d9' => 
    array (
      0 => 'C:\\xampp\\htdocs\\steemnova-1.8-x\\styles\\templates\\adm\\overall_footer.tpl',
      1 => 1684590690,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:error.default.tpl' => 1,
    'file:layout.full.tpl' => 1,
    'file:main.header.tpl' => 1,
    'file:main.navigation.tpl' => 1,
    'file:main.topnav.tpl' => 1,
    'file:overall_footer.tpl' => 1,
  ),
),false)) {
function content_6468ec0e4e4602_44236901 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
$_smarty_tpl->_subTemplateRender('file:error.default.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 2, false, 'f4ce704e86addb4260bb5ff1d93bebfef7fab1dd', 'content_6468ec0e454aa9_09232855');
$_smarty_tpl->inheritance->endChild($_smarty_tpl);
$_smarty_tpl->_subTemplateRender('file:layout.full.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array(), 2, false, 'dd86ce32d9f12b60bb8d56f71be2a6905475b68a', 'content_6468ec0e470355_47848460');
}
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\error.default.tpl" =============================*/
function content_6468ec0e454aa9_09232855 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_13098058956468ec0e4594a5_67474178', "title");
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_13669378856468ec0e45e045_04394828', "content");
?>

<?php
}
/* {block "title"} */
class Block_13098058956468ec0e4594a5_67474178 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'title' => 
  array (
    0 => 'Block_13098058956468ec0e4594a5_67474178',
  ),
);
public $prepend = 'true';
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['LNG']->value['fcm_info'];
}
}
/* {/block "title"} */
/* {block "content"} */
class Block_13669378856468ec0e45e045_04394828 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_13669378856468ec0e45e045_04394828',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->cached->hashes['2338274006468ec0e430724_03753271'] = true;
?>

<table class="table table-dark fs-12 w-100 my-5">
	<tr>
		<th><?php echo $_smarty_tpl->tpl_vars['LNG']->value['fcm_info'];?>
</th>
	</tr>
	<tr>
		<td>
			<p><?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'message\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
</p>
			<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if (!empty($_smarty_tpl->tpl_vars[\'redirectButtons\']->value)) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

			<p>
				<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars[\'redirectButtons\']->value, \'button\');
$_smarty_tpl->tpl_vars[\'button\']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars[\'button\']->value) {
$_smarty_tpl->tpl_vars[\'button\']->do_else = false;
?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

				<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ((isset($_smarty_tpl->tpl_vars[\'button\']->value[\'url\'])) && $_smarty_tpl->tpl_vars[\'button\']->value[\'label\']) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

					<a href="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'button\']->value[\'url\'];?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
						<button class="btn bg-secondary text-white py-0 px-2 fs-11"><?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'button\']->value[\'label\'];?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
</button>
					</a>
				<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

				<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

			</p>
			<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

		</td>
	</tr>
</table>
<?php
}
}
/* {/block "content"} */
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\error.default.tpl" =============================*/
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.header.tpl" =============================*/
function content_6468ec0e4728d8_14805111 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\steemnova-1.8-x\\includes\\libs\\Smarty\\libs\\plugins\\modifier.htmlspecialchars.php','function'=>'smarty_modifier_htmlspecialchars',),));
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" class="no-js"> <!--<![endif]-->
<head>
	<title><?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'title\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
</title>
	<?php if (!empty($_smarty_tpl->tpl_vars['goto']->value)) {?>
	<meta http-equiv="refresh" content="<?php echo $_smarty_tpl->tpl_vars['gotoinsec']->value;?>
;URL=<?php echo $_smarty_tpl->tpl_vars['goto']->value;?>
">
	<?php }?>

	<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php $_smarty_tpl->_assignInScope(\'REV\', "1.0.0.28" ,true);?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>


	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/admin/main.css?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.css?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.fancybox.css?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/validationEngine.jquery.css?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<link rel="stylesheet" type="text/css" href="styles/resource/css/login/icon-font/style.css?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" type="text/css">
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">

	<!-- Bootstrap 5 - No IE support -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"><?php echo '</script'; ?>
>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

	<!-- -->

	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/base/jquery.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/base/jquery.ui.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/base/jquery.cookie.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/base/jquery.fancybox.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/base/jquery.validationEngine.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="./scripts/l18n/validationEngine/jquery.validationEngine-<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'lang\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>

	<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars[\'scripts\']->value, \'scriptname\');
$_smarty_tpl->tpl_vars[\'scriptname\']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars[\'scriptname\']->value) {
$_smarty_tpl->tpl_vars[\'scriptname\']->do_else = false;
?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'scriptname\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
.js?v=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'REV\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
"><?php echo '</script'; ?>
>
	<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>


	<?php echo '<script'; ?>
 type="text/javascript">
	$(function() {
		<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'execscript\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

	});
	<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
var xsize 	= screen.width;
var ysize 	= screen.height;
function openEdit(id, type) {
	var editlist = window.open("?page=quickEditor&mode="+type+"&id="+id, "edit", "scrollbars=yes,statusbar=no,toolbar=no,location=no,directories=no,resizable=no,menubar=no,width=850,height=600,screenX="+((xsize-600)/2)+",screenY="+((ysize-850)/2)+",top="+((ysize-600)/2)+",left="+((xsize-850)/2));
	editlist.focus();
}
<?php echo '</script'; ?>
>

</head>
<body id="<?php if ((isset($_GET['page']))) {
echo (($tmp = smarty_modifier_htmlspecialchars($_GET['page']) ?? null)===null||$tmp==='' ? 'overview' ?? null : $tmp);
}?>" class="<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'bodyclass\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
	<div id="tooltip" class="tip"></div>
<?php
}
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.header.tpl" =============================*/
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.navigation.tpl" =============================*/
function content_6468ec0e492425_75284635 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
?>

<?php echo '<script'; ?>
>
$(document).ready(function(){
  $("#searchInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#menu li").filter(function() {

      if ($(this).text().toLowerCase().indexOf(value) == -1) {

        if ($(this).hasClass('d-flex')) {
          $(this).removeClass('d-flex').addClass('d-none');
        }

      }else {
        if ($(this).hasClass('d-none')) {
          $(this).removeClass('d-none').addClass('d-flex');
        }
      }


    });
  });
});
<?php echo '</script'; ?>
>

<div id="leftmenu">
	<div style="background-image: url('./styles/theme/gow/img/menu-top.png');height:100px;"></div>
	<input class="bg-dark text-white py-0 my-1 form-control" style="height:38px;width:100%;" id="searchInput" type="text"  placeholder="search...">
	<ul class="bg-dark d-flex flex-column p-0 m-0" id="menu">
		<?php if (allowedTo('ShowInformationPage')) {?>
			<li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'infos\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=infos"><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_game_info'];?>
</a>
			</li>
		<?php }?>
		<?php if (allowedTo('ShowConfigBasicPage')) {?>
			<li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'server\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
				<a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=server" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_settings'];?>
</a>
			</li>
		<?php }?>
		<?php if (allowedTo('ShowConfigUniPage')) {?>
			<li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'universe\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=universe" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_unisettings'];?>
</a>
			</li>
		<?php }?>
		<?php if (allowedTo('ShowChatConfigPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'chat\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=chat" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_chat'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowTeamspeakPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'teamspeak\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=teamspeak" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_ts_options'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowFacebookPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'facebook\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=facebook" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_fb_options'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowModulePage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'module\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=module" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_module'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowDisclamerPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'disclamer\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=disclamer" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_disclaimer'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowStatsPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'stats\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=stats" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_stats_options'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowVertifyPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'vertify\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flexw-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=vertify" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_vertify'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowCronjobPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'cronjob\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=cronjob" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_cronjob'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowDumpPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'dump\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=dump" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_dump'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowCreatorPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'create\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=create" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['new_creator_title'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowAccountEditorPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'accounts\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accounts" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_add_delete_resources'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowBanPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'banned\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=banned" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_ban_options'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowGiveawayPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'giveaway\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=giveaway" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_giveaway'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=online&amp;minimize=on" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_connected'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSupportPage')) {?>
      <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'support\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=support" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_support'];
echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ((isset($_smarty_tpl->tpl_vars[\'supportticks\']->value))) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
 (<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'supportticks\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
)<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
</a>
      </li>
    <?php }?>
		<?php if (allowedTo('ShowActivePage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'active\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=active" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_vaild_users'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=p_connect&amp;minimize=on" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_active_planets'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowFlyingFleetPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'fleets\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=fleets" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_flying_fleets'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowNewsPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'news\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=news" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_news'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=users&amp;minimize=on" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_user_list'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=planet&amp;minimize=on" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_planet_list'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=moon&amp;minimize=on" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_moon_list'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowMessageListPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'messagelist\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=messagelist" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_mess_list'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowAccountDataPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'accountData\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accountData" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_info_account_page'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSearchPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'search\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_search_page'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowMultiIPPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'multi\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=multi" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_multiip_page'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowLogPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'log\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=log" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_logs'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowSendMessagesPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'sendMessages\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=sendMessages" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_global_message'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowPassEncripterPage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'passEncripter\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=passEncripter" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_md5_encripter'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowStatUpdatePage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'statsUpdate\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=statsUpdate"  onClick=" return confirm('<?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_mpu_confirmation'];?>
');"><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_manual_points_update'];?>
</a>
    </li>
    <?php }?>
		<?php if (allowedTo('ShowClearCachePage')) {?>
    <li class="d-flex <?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'currentPage\']->value == \'clearCache\') {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
menu-active<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=clearCache" ><?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_clear_cache'];?>
</a>
    </li>
    <?php }?>
		<li style="background-image: url('./styles/theme/gow/img/menu-foot.png');height:30px;"></li>
	</ul>
</div>
<?php
}
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.navigation.tpl" =============================*/
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.topnav.tpl" =============================*/
function content_6468ec0e4d2cd8_98289844 (Smarty_Internal_Template $_smarty_tpl) {
echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php $_smarty_tpl->_checkPlugins(array(0=>array(\'file\'=>\'C:\\\\xampp\\\\htdocs\\\\steemnova-1.8-x\\\\includes\\\\libs\\\\Smarty\\\\libs\\\\plugins\\\\function.html_options.php\',\'function\'=>\'smarty_function_html_options\',),));
?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
?>
<div style="height:50px;" class="d-flex align-items-center justify-content-center text-white fw-bold"><?php echo $_smarty_tpl->tpl_vars['LNG']->value['adm_cp_title'];?>
</div>
<div class="d-flex justify-content-center align-items-center">
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'authlevel\']->value == (defined(\'AUTH_ADM\') ? constant(\'AUTH_ADM\') : null)) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<select style="width:auto;" class="form-select bg-dark text-white mx-1" id="universe">
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo smarty_function_html_options(array(\'options\'=>$_smarty_tpl->tpl_vars[\'AvailableUnis\']->value,\'selected\'=>$_smarty_tpl->tpl_vars[\'UNI\']->value),$_smarty_tpl);?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

</select>
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<a href="admin.php?page=overview" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['LNG']->value['adm_cp_index'];?>
&nbsp;</a>
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'authlevel\']->value == (defined(\'AUTH_ADM\') ? constant(\'AUTH_ADM\') : null)) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<a href="?page=universe&amp;sid=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'sid\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'LNG\']->value[\'mu_universe\'];?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
&nbsp;</a>
<a href="?page=rights" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'LNG\']->value[\'mu_moderation_page\'];?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
&nbsp;</a>
<a href="?page=rights&amp;mode=users&amp;sid=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'sid\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'LNG\']->value[\'ad_authlevel_title\'];?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
&nbsp;</a>
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php if ($_smarty_tpl->tpl_vars[\'id\']->value == 1) {?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<a href="?page=reset&amp;sid=<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'sid\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php echo $_smarty_tpl->tpl_vars[\'re_reset_universe\']->value;?>
/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>
&nbsp;</a>
<?php echo '/*%%SmartyNocache:2338274006468ec0e430724_03753271%%*/<?php }?>/*/%%SmartyNocache:2338274006468ec0e430724_03753271%%*/';?>

<a href="javascript:top.location.href='game.php';" target="_top" class="border mx-1 border-danger rounded text-decoration-none text-danger fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['LNG']->value['adm_cp_logout'];?>
&nbsp;</a>
</div>
<?php echo '<script'; ?>
>
$(function() {
	$('#universe').on('change', function(e) {
		parent.frames['Hauptframe'].location.href = parent.frames['Hauptframe'].location.href+'&uni='+$(this).val();
		parent.frames['rightFrame'].location.reload();
	});
});
<?php echo '</script'; ?>
>
<?php
}
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\main.topnav.tpl" =============================*/
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\overall_footer.tpl" =============================*/
function content_6468ec0e4df9d9_31988221 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
if ((isset($_GET['reload']))) {
if ($_GET['reload'] == 't') {
echo '<script'; ?>
 type="text/javascript">
parent.topFrame.document.location.reload();
<?php echo '</script'; ?>
>
<?php } elseif ($_GET['reload'] == 'l') {
echo '<script'; ?>
 type="text/javascript">
parent.rightFrame.document.location.reload();
<?php echo '</script'; ?>
>
<?php } elseif ($_GET['reload'] == 'r') {
echo '<script'; ?>
 type="text/javascript">
top.document.location.reload();
<?php echo '</script'; ?>
>
<?php }
}
}
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\overall_footer.tpl" =============================*/
/* Start inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\layout.full.tpl" =============================*/
function content_6468ec0e470355_47848460 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->compiled->nocache_hash = '2338274006468ec0e430724_03753271';
foreach (array('bodyclass'=>"full") as $ik => $iv) {
$_smarty_tpl->tpl_vars[$ik] =  new Smarty_Variable($iv);
}
$_smarty_tpl->_subTemplateRender("file:main.header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('bodyclass'=>"full"), 0, false, 'e848e711ca05bd7a25eafb1048061856b7c330fd', 'content_6468ec0e4728d8_14805111');
?>

<div class="container-fluid">

	<div class="row">
		<div style="width:220px;">
			<?php
foreach (array('bodyclass'=>"full") as $ik => $iv) {
$_smarty_tpl->tpl_vars[$ik] =  new Smarty_Variable($iv);
}
$_smarty_tpl->_subTemplateRender("file:main.navigation.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('bodyclass'=>"full"), 0, false, 'd3772abaa62951a204682c42a4173f7a601a6ae4', 'content_6468ec0e492425_75284635');
?>
		</div>
		<div style="width:calc(100% - 250px);">
			<div class="row bg-dark py-3">
				<?php
foreach (array('bodyclass'=>"full") as $ik => $iv) {
$_smarty_tpl->tpl_vars[$ik] =  new Smarty_Variable($iv);
}
$_smarty_tpl->_subTemplateRender("file:main.topnav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('bodyclass'=>"full"), 0, false, 'a0d66353770ae2f0a8cf11be59b3495a1da010fc', 'content_6468ec0e4d2cd8_98289844');
?>
			</div>
			<div class="content">
				<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_5930937246468ec0e4de119_97537944', "content");
?>

			</div>
		</div>
	</div>
	<div class="row">
		<?php
foreach (array('bodyclass'=>"full") as $ik => $iv) {
$_smarty_tpl->tpl_vars[$ik] =  new Smarty_Variable($iv);
}
$_smarty_tpl->_subTemplateRender("file:overall_footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('bodyclass'=>"full"), 0, false, '174c8f0409d60094ecd35ebc40b1b4cbaac141d9', 'content_6468ec0e4df9d9_31988221');
?>
	</div>
</div>





</body>
</html>
<?php
}
/* {block "content"} */
class Block_5930937246468ec0e4de119_97537944 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_5930937246468ec0e4de119_97537944',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "content"} */
/* End inline template "C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\layout.full.tpl" =============================*/
}
