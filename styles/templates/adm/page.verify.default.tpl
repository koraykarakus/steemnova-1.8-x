{block name="content"}
	<div class="bg-black w-75  p-3 my-3 mx-auto fs-12">
		<div class="form-group">
			<span>{$LNG.vt_head}</span>
		</div>
		<div class="form-group">
			<span>{$LNG.vt_info}</span>
		</div>
		<div class="form-group">
			<a class="" href="admin.php?page=verify&amp;mode=getFileList&amp;ext=php">{$LNG.vt_filephp}</a>
			<a class="" href="admin.php?page=verify&amp;mode=getFileList&amp;ext=tpl">{$LNG.vt_filetpl}</a>
			<a class="" href="admin.php?page=verify&amp;mode=getFileList&amp;ext=css">{$LNG.vt_filecss}</a>
			<a class="" href="admin.php?page=verify&amp;mode=getFileList&amp;ext=js">{$LNG.vt_filejs}</a>
			<a class=""
				href="admin.php?page=verify&amp;mode=getFileList&amp;ext=png|jpg|gif">{$LNG.vt_fileimg}</a>
			<a class=""
				href="admin.php?page=verify&amp;mode=getFileList&amp;ext=htaccess">{$LNG.vt_filehtaccess}</a>
		</div>
		<div class="form-group">
			<a class=""
				href="admin.php?page=verify&amp;mode=getFileList&amp;ext=php|tpl|js|css|png|jpg|gif|htaccess">{$LNG.vt_all}</a>
		</div>
	</div>

{/block}