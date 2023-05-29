{block name="content"}

<form class="bg-black w-75 text-white p-3 my-3 mx-auto fs-12" action="?page=create&mode=createUser" method="post">
  <div class="form-group">
    <span class="fw-bold text-yellow fs-12">{$LNG.new_title}</span>
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="name">{$LNG.user_reg}</label>
    <input id="name" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="name">
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="password">{$LNG.pass_reg}</label>
    <input id="password" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="password" name="password" autocomplete="new-password">
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="password2">{$LNG.pass2_reg}</label>
    <input id="password2" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="password" name="password2" autocomplete="new-password">
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="email">{$LNG.email_reg}</label>
    <input id="email" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="email">
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="email2">{$LNG.email2_reg}</label>
    <input id="email2" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="email2">
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="galaxy">{$LNG.new_coord}</label>
    <div class="d-flex align-items-center">
      <input id="galaxy" style="width:60px;" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="galaxy" size="1" maxlength="1">
      <span>:</span>
      <input style="width:60px;" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="system" size="3" maxlength="3">
      <span>:</span>
      <input style="width:60px;" class="form-control py-1 bg-dark text-white my-1 border border-secondary" type="text" name="planet" size="2" maxlength="2">
    </div>
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="authlevel">{$LNG.new_range}</label>
    <select id="authlevel" class="form-select py-1 bg-dark text-white my-1 border border-secondary" name="authlevel">
      {foreach $Selector.auth as $key => $currentAuth}
      <option value="{$key}">{$currentAuth}</option>
      {/foreach}
    </select>
  </div>
  <div class="form-group">
    <label class="text-start my-1 cursor-pointer hover-underline d-flex w-100" for="lang">{$LNG.lang_reg}</label>
    <select id="lang" class="form-select py-1 bg-dark text-white my-1 border border-secondary" name="lang">
      {foreach $Selector.lang as $key => $currentLang}
        <option value="{$key}">{$currentLang}</option>
      {/foreach}
    </select>
  </div>
  <div class="form-group">
    <input class="btn btn-primary text-white my-2 w-100" type="submit" value="{$LNG.new_add_user}">
  </div>
  <div class="form-group d-flex justify-content-start">
    <a class="text-white" href="?page=create">{$LNG.new_creator_go_back}</a>&nbsp;<a class="text-white" href="?page=create&amp;mode=user">{$LNG.new_creator_refresh}</a>
  </div>
</form>

{/block}
