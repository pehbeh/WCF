{if $recaptchaLegacyMode|empty}
	{include file='shared_captcha'}
{else}
	{if RECAPTCHA_PUBLICKEY_V3 && RECAPTCHA_PRIVATEKEY_V3}
		{assign var="recaptchaType" value="v3"}
		{assign var="recaptchaPublicKey" value=RECAPTCHA_PUBLICKEY_V3}
	{elseif RECAPTCHA_PUBLICKEY && RECAPTCHA_PRIVATEKEY}
		{if RECAPTCHA_PUBLICKEY_INVISIBLE && RECAPTCHA_PRIVATEKEY_INVISIBLE}
			{assign var="recaptchaType" value="invisible"}
			{assign var="recaptchaPublicKey" value=RECAPTCHA_PUBLICKEY_INVISIBLE}
		{else}
			{assign var="recaptchaType" value="v2"}
			{assign var="recaptchaPublicKey" value=RECAPTCHA_PUBLICKEY}
		{/if}
	{/if}
	{if !$ajaxCaptcha|isset}
		{assign var="ajaxCaptcha" value=false}
	{/if}

	{if $recaptchaType|isset && $recaptchaPublicKey|isset}
		{assign var="recaptchaBucketID" value=true|microtime|sha1}
		<dl class="{if $errorField|isset && $errorField == 'recaptchaString'}formError{/if}">
			<dt>{if $recaptchaType !== "v3"}<label>{lang}wcf.recaptcha.title{/lang}</label>{/if}</dt>
			<dd>
				<input type="hidden" name="recaptcha-type" value="{$recaptchaType}">
				<div id="recaptchaBucket{$recaptchaBucketID}"></div>
				{if (($errorType|isset && $errorType|is_array && $errorType[recaptchaString]|isset) || ($errorField|isset && $errorField == 'recaptchaString'))}
					{if $errorType|is_array && $errorType[recaptchaString]|isset}
						{assign var='__errorType' value=$errorType[recaptchaString]}
					{else}
						{assign var='__errorType' value=$errorType}
					{/if}
					<small class="innerError">
						{if $__errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.captcha.recaptcha{$recaptchaType|ucfirst}.error.recaptchaString.{$__errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>
		<script data-relocate="true">
			require(['WoltLabSuite/Core/Component/Captcha/Recaptcha'], ({ Recaptcha }) => {
				new Recaptcha('{$recaptchaType}', '{unsafe:$recaptchaPublicKey|encodeJS}', 'recaptchaBucket{$recaptchaBucketID}'{if $ajaxCaptcha}, '{unsafe:$captchaID|encodeJS}'{/if});
			});
		</script>
	{/if}
{/if}
