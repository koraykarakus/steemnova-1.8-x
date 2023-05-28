{block name="content"}
<script>
$(document).ready(function(){
  $("#searchInModules").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#modules .name").filter(function() {
			if ($(this).text().toLowerCase().indexOf(value) > -1) {
				$(this).parent().removeClass('d-none');
			}else {
				$(this).parent().addClass('d-none');
			}
    });
  });
});
</script>

<div class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12">
  <div class="form-group d-flex justify-content-between">
    <span class="text-yellow fs-5">{$LNG.mod_module}&nbsp;-&nbsp;{$LNG.mod_info}</span>
    <input style="max-width:250px;" id="searchInModules" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="" placeholder="search..">
  </div>
  {foreach key=ID item=Info from=$Modules}
  <div id="modules" class="form-group d-flex justify-content-center row my-1 border border-1 border-secondary">
  	<span class="col fs-5 name">{$Info.name}</span>
  	{if $Info.state == 1}
  		<span class="col fs-5" style="color:green">{$LNG.mod_active}</span>
  		<a class="col fs-5 text-white" href="?page=module&mode=change&type=deaktivate&id={$ID}">{$LNG.mod_change_deactive}</a>
  	{else}
  		<span class="col fs-5" style="color:red">{$LNG.mod_deactive}</span>
      <a class="col fs-5 text-white" href="?page=module&mode=change&type=activate&id={$ID}">{$LNG.mod_change_active}</a>
  	{/if}
  </div>
  {/foreach}
</div>

{/block}
