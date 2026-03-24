{block name="title" prepend}{$LNG.fcm_info}{/block}
{block name="content"}
    <div class="container">
        <div class="bg-dark border border-secondary mx-auto  p-3 my-3 d-flex flex-column align-items-start">
            <h3 class="text-left my-1 w-100">{$LNG.fcm_info}</h3>
            <span class="my-1 fs-14 w-100">{$msg}</span>
            {if !empty($redirect_btns)}
                <p class="my-2 fs-14">
                    {foreach $redirect_btns as $button}
                        {if isset($button.url) && $button.label}
                            <a href="{$button.url}">
                                <button class="btn btn-primary  py-0 px-2 fs-11">{$button.label}</button>
                            </a>
                        {/if}
                    {/foreach}
                </p>
            {/if}
        </div>
    </div>
{/block}