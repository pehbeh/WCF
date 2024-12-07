{if $__userAuthConfig->canRegister}
	{capture assign='contentDescription'}{lang}wcf.user.login.noAccount{/lang}{/capture}
{/if}

{include file='authFlowHeader'}

{if $forceLoginRedirect}
	<woltlab-core-notice type="info">{lang}wcf.user.login.forceLogin{/lang}</woltlab-core-notice>
{/if}

{unsafe:$form->getHtml()}

{include file='authFlowFooter'}
