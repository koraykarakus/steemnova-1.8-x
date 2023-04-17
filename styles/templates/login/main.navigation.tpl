<header>
	<!-- <nav>
		<ul id="menu">
			<li><a href="index.php">{$LNG.menu_index}</a></li>
			<li><a href="index.php?page=board" target="board">{$LNG.forum}</a></li>
			<li><a href="index.php?page=news">{$LNG.menu_news}</a></li>
			<li><a href="index.php?page=rules">{$LNG.menu_rules}</a></li>
			<li><a href="index.php?page=battleHall">{$LNG.menu_battlehall}</a></li>
			<li><a href="index.php?page=banList">{$LNG.menu_banlist}</a></li>
			<li><a href="index.php?page=disclamer">{$LNG.menu_disclamer}</a></li>
		</ul>
	</nav> -->
	<nav id="navbar" class="container-fluid py-2 bg-dark d-flex align-items-center justify-content-between">

		<ul class="d-none d-md-flex justify-content-start align-items-center m-0">
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'index'}active{/if}" href="index.php?page=index">{$LNG.siteTitleIndex}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'news'}active{/if}" href="index.php?page=news">{$LNG.siteTitleNews}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'screens'}active{/if}" href="index.php?page=screens">{$LNG.siteTitleScreens}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'banList'}active{/if}" href="index.php?page=banList">{$LNG.siteTitleBanList}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'battleHall'}active{/if}" href="index.php?page=battleHall">{$LNG.siteTitleBattleHall}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'rules'}active{/if}" href="index.php?page=rules">{$LNG.siteTitleRules}</a>
			</li>
			<li class="d-flex align-items-center">
				<a class="hover-color-yellow text-decoration-none border-end px-2 border-light {if $page == 'disclamer'}active{/if}" href="index.php?page=disclamer">{$LNG.siteTitleDisclamer}</a>
			</li>
		</ul>

		{if count($languages) > 1}
		<i class="bi bi-list d-flex d-md-none px-3 text-white fs-2 menu_icon" data-bs-toggle="offcanvas" data-bs-target="#phoneMenu"></i>

		<div class="dropdown">
		  <button style="width:120px;height:24px;" class="btn btn-secondary dropdown-toggle p-1 d-flex align-items-center justify-content-center" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown">
		    {$LNG.registerLanguage}
		  </button>
		  <ul style="width:auto;" class="dropdown-menu flex-column bg-dark p-0" aria-labelledby="dropdownMenuButton1">
				{foreach $languages as $langKey => $langName}
			    <li class="d-flex w-100">
						<a class="text-decoration-none hover-bg-color-grey d-flex align-items-center w-100 px-2" href="?lang={$langKey}" rel="alternate" hreflang="{$langKey}" title="{$langName}">
							<span class="flags {$langKey}">{$langName}</span>
							<span class="">{$langName}</span>
						</a>
					</li>
				{/foreach}
		  </ul>
		</div>
		{/if}

	</nav>


	<div class="offcanvas offcanvas-start" id="phoneMenu">
	  <div class="offcanvas-header bg-dark">
	    <span class="offcanvas-title fs-2">{$gameName}</span>
	    <button type="button" class="btn-close btn-close-white" aria-label="Close" data-bs-dismiss="offcanvas"></button>
	  </div>
	  <div class="offcanvas-body p-0 bg-dark">

	    <ul style="list-style:none;" class="p-0 m-0 bg-dark p-0 m-0">
					<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'index'}active{/if}" href="index.php?page=index">{$LNG.siteTitleIndex}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'news'}active{/if}" href="index.php?page=news">{$LNG.siteTitleNews}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'screens'}active{/if}" href="index.php?page=screens">{$LNG.siteTitleScreens}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'banList'}active{/if}" href="index.php?page=banList">{$LNG.siteTitleBanList}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'battleHall'}active{/if}" href="index.php?page=battleHall">{$LNG.siteTitleBattleHall}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'rules'}active{/if}" href="index.php?page=rules">{$LNG.siteTitleRules}</a>
	  			</li>
	  			<li class="d-flex align-items-center">
	  				<a class="fs-6 w-100 hover-color-yellow text-decoration-none py-2 border-light {if $page == 'disclamer'}active{/if}" href="index.php?page=disclamer">{$LNG.siteTitleDisclamer}</a>
	  			</li>
	      </ul>
	  </div>
	</div>

</header>
