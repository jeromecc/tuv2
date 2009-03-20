
{literal}<style>
.lk {
display: table;
}

.lk_header{
display: table-header-group;
}

.lk_case{

background:yellow;
border: 1px solid blue;
display: table-cell;
}

.lk_titreCols {
display: table-cell;
}

.lk_list_ligne_pair {
display: table-row;
background:grey;
}
.lk_list_ligne_impair {
display: table-row;
background:white;
}



</style>
{/literal}
<div id="{$id}" class="lk" >
	<div class="lk_header" >
	{section name=titre loop=$colonnes}
		<div class="lk_case" class="lk_titreCols" >
		<a href="{$urlTri}&amp;sort={$colonnes[titre].id}">{$colonnes[titre].libelle}</a>
		</div>
	{/section}
	</div>
{section name=objet loop=$data}
	<div class="lk_list_ligne{cycle values="_pair,_impair"}" >
	{foreach name=col key=key item=item from=$data[objet]}
		<div  class="lk_case" >
		{$item}
		</div>
	{/foreach}
	</div>
{/section}
</div>