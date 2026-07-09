{extends file="helpers/list/list_footer.tpl"}

{block name="endForm"}
    {if !isset($disable_list_form) || !$disable_list_form}
        {$smarty.block.parent}
    {/if}
{/block}
