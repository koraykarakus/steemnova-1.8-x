<?php
/* Smarty version 4.3.0, created on 2023-05-20 17:49:34
  from 'C:\xampp\htdocs\steemnova-1.8-x\styles\templates\adm\error.default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.0',
  'unifunc' => 'content_6468ec0e4f2f58_28467585',
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
  'cache_lifetime' => 604800,
),true)) {
function content_6468ec0e4f2f58_28467585 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
" class="no-js"> <!--<![endif]-->
<head>
	<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
	
	<?php $_smarty_tpl->_assignInScope('REV', "1.0.0.28" ,true);?>

	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/admin/main.css?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.css?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/jquery.fancybox.css?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
">
	<link rel="stylesheet" type="text/css" href="./styles/resource/css/base/validationEngine.jquery.css?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
">
	<link rel="stylesheet" type="text/css" href="styles/resource/css/login/icon-font/style.css?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" type="text/css">
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">

	<!-- Bootstrap 5 - No IE support -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

	<!-- -->

	<script type="text/javascript" src="./scripts/base/jquery.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<script type="text/javascript" src="./scripts/base/jquery.ui.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<script type="text/javascript" src="./scripts/base/jquery.cookie.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<script type="text/javascript" src="./scripts/base/jquery.fancybox.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<script type="text/javascript" src="./scripts/base/jquery.validationEngine.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<script type="text/javascript" src="./scripts/l18n/validationEngine/jquery.validationEngine-<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>

	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['scripts']->value, 'scriptname');
$_smarty_tpl->tpl_vars['scriptname']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['scriptname']->value) {
$_smarty_tpl->tpl_vars['scriptname']->do_else = false;
?>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['scriptname']->value;?>
.js?v=<?php echo $_smarty_tpl->tpl_vars['REV']->value;?>
"></script>
	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

	<script type="text/javascript">
	$(function() {
		<?php echo $_smarty_tpl->tpl_vars['execscript']->value;?>

	});
	</script>

<script type="text/javascript">
var xsize 	= screen.width;
var ysize 	= screen.height;
function openEdit(id, type) {
	var editlist = window.open("?page=quickEditor&mode="+type+"&id="+id, "edit", "scrollbars=yes,statusbar=no,toolbar=no,location=no,directories=no,resizable=no,menubar=no,width=850,height=600,screenX="+((xsize-600)/2)+",screenY="+((ysize-850)/2)+",top="+((ysize-600)/2)+",left="+((xsize-850)/2));
	editlist.focus();
}
</script>

</head>
<body id="clearCache" class="<?php echo $_smarty_tpl->tpl_vars['bodyclass']->value;?>
">
	<div id="tooltip" class="tip"></div>

<div class="container-fluid">

	<div class="row">
		<div style="width:220px;">
			
<script>
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
</script>

<div id="leftmenu">
	<div style="background-image: url('./styles/theme/gow/img/menu-top.png');height:100px;"></div>
	<input class="bg-dark text-white py-0 my-1 form-control" style="height:38px;width:100%;" id="searchInput" type="text"  placeholder="search...">
	<ul class="bg-dark d-flex flex-column p-0 m-0" id="menu">
					<li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'infos') {?>menu-active<?php }?>">
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=infos">Bilgi</a>
			</li>
							<li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'server') {?>menu-active<?php }?>">
				<a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=server" >Server Ayarlari</a>
			</li>
							<li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'universe') {?>menu-active<?php }?>">
				<a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=universe" >Evren Ayarlari</a>
			</li>
				    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'chat') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=chat" >Chat Ayarlari</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'teamspeak') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=teamspeak" >Teamspeak Ayarlari</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'facebook') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=facebook" >Facebook Kayit</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'module') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=module" >Moduller</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'disclamer') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=disclamer" >Iletisim</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'stats') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=stats" >Istatistik Ayarlari</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'vertify') {?>menu-active<?php }?>">
      <a class="d-flexw-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=vertify" >Oyun Indexini Kontrol et</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'cronjob') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=cronjob" >Zamanlanmis Gorevler</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'dump') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=dump" >Veritabani Yedekle</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'create') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1 text-decoration-none text-white fs-6" href="?page=create" >Olustur</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'accounts') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accounts" >Hesaplari Duzenle</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'banned') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=banned" >Ceza/Ban Sistemi</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'giveaway') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=giveaway" >Genel Hediye/Evrene Dagit</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=online&amp;minimize=on" >Online</a>
    </li>
    		      <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'support') {?>menu-active<?php }?>">
        <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=support" >Destek Bileti<?php if ((isset($_smarty_tpl->tpl_vars['supportticks']->value))) {?> (<?php echo $_smarty_tpl->tpl_vars['supportticks']->value;?>
)<?php }?></a>
      </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'active') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=active" >Oyuncu aktiviteleri</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=p_connect&amp;minimize=on" >Aktif Gezegenler</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'fleets') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=fleets" >Filo Hareketleri</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'news') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=news" >Haberler</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=users&amp;minimize=on" >Oyuncu Listesi</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=planet&amp;minimize=on" >Gezegen Listesi</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search&amp;search=moon&amp;minimize=on" >Ay Listesi</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'messagelist') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=messagelist" >Mesaj Listesi</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'accountData') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=accountData" >Hesap Bilgileri (Yonetici/Moderator)</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'search') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=search" >Gelismis Arama</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'multi') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=multi" >Multi IPs</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'log') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=log" >Admin Log</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'sendMessages') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=sendMessages" >Genel Mesaj</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'passEncripter') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=passEncripter" >MD5 Kodlama</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'statsUpdate') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=statsUpdate"  onClick=" return confirm('Mevcur degeleri guncelliyorsunuz, bunu yaparak su an serverdaki guncel aktiviteler hakkinda bilgi sahibi yapar (Bellek kullanimi, SQL, vb.)');">Manual Puan</a>
    </li>
    		    <li class="d-flex <?php if ($_smarty_tpl->tpl_vars['currentPage']->value == 'clearCache') {?>menu-active<?php }?>">
      <a class="d-flex w-100 h-100 p-1  text-decoration-none text-white fs-6" href="?page=clearCache" >Bellegi Temizle</a>
    </li>
    		<li style="background-image: url('./styles/theme/gow/img/menu-foot.png');height:30px;"></li>
	</ul>
</div>
		</div>
		<div style="width:calc(100% - 250px);">
			<div class="row bg-dark py-3">
				<?php $_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\steemnova-1.8-x\\includes\\libs\\Smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
?><div style="height:50px;" class="d-flex align-items-center justify-content-center text-white fw-bold">Administration Panel</div>
<div class="d-flex justify-content-center align-items-center">
<?php if ($_smarty_tpl->tpl_vars['authlevel']->value == (defined('AUTH_ADM') ? constant('AUTH_ADM') : null)) {?>
<select style="width:auto;" class="form-select bg-dark text-white mx-1" id="universe">
<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['AvailableUnis']->value,'selected'=>$_smarty_tpl->tpl_vars['UNI']->value),$_smarty_tpl);?>

</select>
<?php }?>
<a href="admin.php?page=overview" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;Admin Ana Sayfa&nbsp;</a>
<?php if ($_smarty_tpl->tpl_vars['authlevel']->value == (defined('AUTH_ADM') ? constant('AUTH_ADM') : null)) {?>
<a href="?page=universe&amp;sid=<?php echo $_smarty_tpl->tpl_vars['sid']->value;?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_universe'];?>
&nbsp;</a>
<a href="?page=rights" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['LNG']->value['mu_moderation_page'];?>
&nbsp;</a>
<a href="?page=rights&amp;mode=users&amp;sid=<?php echo $_smarty_tpl->tpl_vars['sid']->value;?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['LNG']->value['ad_authlevel_title'];?>
&nbsp;</a>
<?php }
if ($_smarty_tpl->tpl_vars['id']->value == 1) {?>
<a href="?page=reset&amp;sid=<?php echo $_smarty_tpl->tpl_vars['sid']->value;?>
" target="Hauptframe" class="border mx-1 border-white rounded text-decoration-none text-white fs-6">&nbsp;<?php echo $_smarty_tpl->tpl_vars['re_reset_universe']->value;?>
&nbsp;</a>
<?php }?>
<a href="javascript:top.location.href='game.php';" target="_top" class="border mx-1 border-danger rounded text-decoration-none text-danger fs-6">&nbsp;Oyuna Geri Don&nbsp;</a>
</div>
<script>
$(function() {
	$('#universe').on('change', function(e) {
		parent.frames['Hauptframe'].location.href = parent.frames['Hauptframe'].location.href+'&uni='+$(this).val();
		parent.frames['rightFrame'].location.reload();
	});
});
</script>
			</div>
			<div class="content">
				
<table class="table table-dark fs-12 w-100 my-5">
	<tr>
		<th>Bilgiler</th>
	</tr>
	<tr>
		<td>
			<p><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</p>
			<?php if (!empty($_smarty_tpl->tpl_vars['redirectButtons']->value)) {?>
			<p>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['redirectButtons']->value, 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
				<?php if ((isset($_smarty_tpl->tpl_vars['button']->value['url'])) && $_smarty_tpl->tpl_vars['button']->value['label']) {?>
					<a href="<?php echo $_smarty_tpl->tpl_vars['button']->value['url'];?>
">
						<button class="btn bg-secondary text-white py-0 px-2 fs-11"><?php echo $_smarty_tpl->tpl_vars['button']->value['label'];?>
</button>
					</a>
				<?php }?>
				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			</p>
			<?php }?>
		</td>
	</tr>
</table>

			</div>
		</div>
	</div>
	<div class="row">
			</div>
</div>





</body>
</html>
<?php }
}
