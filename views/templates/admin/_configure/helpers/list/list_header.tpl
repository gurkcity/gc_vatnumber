{extends file="helpers/list/list_header.tpl"}

{block name="startForm"}
    {if !isset($disable_list_form) || !$disable_list_form}
        {$smarty.block.parent}
    {/if}
{/block}
