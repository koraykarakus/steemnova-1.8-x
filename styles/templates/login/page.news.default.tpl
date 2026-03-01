{block name="title" prepend}{$LNG.siteTitleNews}{/block}
{block name="content"}
    {foreach $news_list as $c_news}
        {if !$c_news@first}
        <hr>{/if}
        <h2>{$c_news.title}</h2><br>
        <div class="info">{$c_news.from}</div>
        <br>
        <div>
            <p>{$c_news.text}</p>
        </div>
    {foreachelse}
        <h1>{$LNG.news_does_not_exist}</h1>
    {/foreach}
{/block}