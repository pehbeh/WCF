<dl class="plain dataList">
	{event name='stats'}
	
	<dt>{lang}wcf.user.members{/lang}</dt>
	<dd>{#$statistics->members}</dd>
	
	{if USERS_ONLINE_RECORD}
		<dt>{lang}wcf.user.mostOnlineUsers{/lang}</dt>
		<dd title="{@USERS_ONLINE_RECORD_TIME|plainTime}" class="jsTooltip">{#USERS_ONLINE_RECORD}</dd>
	{/if}

	{if $statistics->newestMember !== null}
		<dt>{lang}wcf.user.newestMember{/lang}</dt>
		<dd>{user object=$statistics->newestMember}</dd>
	{/if}
</dl>
