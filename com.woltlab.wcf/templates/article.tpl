{capture assign='pageTitle'}{if $articleContent->metaTitle}{$articleContent->metaTitle}{else}{$articleContent->title}{/if}{/capture}

{assign var='__mainItemScope' value='itemprop="mainEntity" itemscope itemtype="http://schema.org/Article"'}

{capture assign='contentHeader'}
	<header class="contentHeader articleContentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle" itemprop="name headline">{$articleContent->title}</h1>
			<ul class="inlineList contentHeaderMetaData articleMetaData">
				<li itemprop="author" itemscope itemtype="http://schema.org/Person">
					{icon name='user'}
					{if $article->userID}
						<a href="{$article->getUserProfile()->getLink()}" class="userLink" data-object-id="{$article->userID}" itemprop="url">
							<span itemprop="name">{unsafe:$article->getUserProfile()->getFormattedUsername()}</span>
						</a>
					{else}
						<span itemprop="name">{$article->username}</span>
					{/if}
				</li>
				
				<li>
					{icon name='clock'}
					<a href="{$article->getLink()}">{time time=$article->time}</a>
					<meta itemprop="datePublished" content="{$article->time|date:'c'}">
				</li>

				{if $article->hasLabels()}
					<li>
						{icon name='tags'}
						<ul class="labelList">
							{foreach from=$article->getLabels() item=label}
								<li>{unsafe:$label->render()}</li>
							{/foreach}
						</ul>
					</li>
				{/if}
				
				<li>
					{icon name='eye'}
					{lang}wcf.article.articleViews{/lang}
				</li>
				
				{if $article->getDiscussionProvider()->getDiscussionCountPhrase()}
					<li itemprop="interactionStatistic" itemscope itemtype="http://schema.org/InteractionCounter">
						{icon name='comments'}
						{if $article->getDiscussionProvider()->getDiscussionLink()}<a href="{$article->getDiscussionProvider()->getDiscussionLink()}">{else}<span>{/if}
						{$article->getDiscussionProvider()->getDiscussionCountPhrase()}
						{if $article->getDiscussionProvider()->getDiscussionLink()}</a>{else}</span>{/if}
						<meta itemprop="interactionType" content="http://schema.org/CommentAction">
						<meta itemprop="userInteractionCount" content="{$article->getDiscussionProvider()->getDiscussionCount()}">
					</li>
				{/if}
				
				{hascontent}
					<li>
						{icon name='flag'}
						{content}
							{if $article->isDeleted}
								<span class="badge red">{lang}wcf.message.status.deleted{/lang}</span>
							{/if}
							{if !$article->isPublished()}
								<span class="badge green">{lang}wcf.message.status.disabled{/lang}</span>
							{/if}
							{event name='contentHeaderMetaDataFlag'}
						{/content}
					</li>
				{/hascontent}
				
				{event name='contentHeaderMetaData'}
			</ul>
			
			<div itemprop="publisher" itemscope itemtype="http://schema.org/Organization">
				<meta itemprop="name" content="{PAGE_TITLE|phrase}">
				<div itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
					<meta itemprop="url" content="{$__wcf->getStyleHandler()->getStyle()->getPageLogo()}">
				</div>
			</div>
		</div>
		
		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
					{content}
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{capture assign='headContent'}
	{if $article->isMultilingual}
		{foreach from=$article->getLanguageLinks() item='langArticleContent'}
			{if $langArticleContent->getLanguage()}
				<link rel="alternate" hreflang="{$langArticleContent->getLanguage()->languageCode}" href="{$langArticleContent->getLink()}">
			{/if}
		{/foreach}
	{/if}
{/capture}

{capture assign='contentInteractionButtons'}
	{if $article->canEdit()}
		<a href="{link controller='ArticleEdit' id=$article->articleID}{/link}" class="contentInteractionButton button small">{icon name='pencil'} <span>{lang}wcf.acp.article.edit{/lang}</span></a>
	{/if}

	{if $article->isMultilingual && $__wcf->user->userID}
		<div class="contentInteractionButton dropdown jsOnly">
			<button type="button" class="dropdownToggle boxFlag box24 button small">
				<span><img src="{$articleContent->getLanguage()->getIconPath()}" alt="" class="iconFlag"></span>
				<span>{$articleContent->getLanguage()->languageName}</span>
			</button>
			<ul class="dropdownMenu">
				{foreach from=$article->getLanguageLinks() item='langArticleContent'}
					{if $langArticleContent->getLanguage()}
						<li class="boxFlag">
							<a class="box24" href="{$langArticleContent->getLink()}">
								<span><img src="{$langArticleContent->getLanguage()->getIconPath()}" alt="" class="iconFlag"></span>
								<span>{$langArticleContent->getLanguage()->languageName}</span>
							</a>
						</li>
					{/if}
				{/foreach}
			</ul>
		</div>
	{/if}
{/capture}

{capture assign='contentInteractionShareButton'}
	<button type="button" class="button small wsShareButton jsTooltip" title="{lang}wcf.message.share{/lang}" data-link="{$articleContent->getLink()}" data-link-title="{$articleContent->getTitle()}" data-bbcode="[wsa]{$article->getObjectID()}[/wsa]">
		{icon name='share-nodes'}
	</button>
{/capture}

{include file='header'}

{if !$article->isPublished()}
	<woltlab-core-notice type="info">{lang publicationDate=$article->publicationDate}wcf.article.publicationStatus.{$article->publicationStatus}{/lang}</woltlab-core-notice>
{/if}

<div
	class="section article coreArticle"
	{unsafe:$__wcf->getReactionHandler()->getDataAttributes('com.woltlab.wcf.likeableArticle', $article->articleID)}
>
	{if $articleContent->teaser}
		<div class="article__teaser htmlContent">
			{unsafe:$articleContent->getFormattedTeaser()}
		</div>
	{/if}
	
	{if $articleContent->getImage() && $articleContent->getImage()->hasThumbnail('large')}
		<div class="article__coverPhoto__wrapper" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
			<figure class="article__coverPhoto">
				{unsafe:$articleContent->getImage()->getThumbnailTag('large')}
				{if $articleContent->getImage()->caption}
					<figcaption itemprop="description">
						{if $articleContent->getImage()->captionEnableHtml}
							{unsafe:$articleContent->getImage()->caption}
						{else}
							{$articleContent->getImage()->caption}
						{/if}
					</figcaption>
				{/if}
			</figure>
			<meta itemprop="url" content="{$articleContent->getImage()->getThumbnailLink('large')}">
			<meta itemprop="width" content="{$articleContent->getImage()->getThumbnailWidth('large')}">
			<meta itemprop="height" content="{$articleContent->getImage()->getThumbnailHeight('large')}">
		</div>
	{/if}
	
	{event name='beforeArticleContent'}

	<div class="article__content htmlContent" itemprop="description articleBody">
		{if MODULE_WCF_AD}
			{unsafe:$__wcf->getAdHandler()->getAds('com.woltlab.wcf.article.inArticle')}
		{/if}
		
		{unsafe:$articleContent->getFormattedContent()}
		
		{event name='htmlArticleContent'}
	</div>

	{event name='afterArticleContent'}

	{if !$tags|empty}
		<ul class="tagList">
			{foreach from=$tags item=tag}
				<li><a href="{link controller='Tagged' object=$tag}objectType=com.woltlab.wcf.article{/link}" class="tag">
					{icon name='tag'}
					{$tag->name}
				</a></li>
			{/foreach}
		</ul>
	{/if}

	{include file='articleAttachments' objectID=$article->articleID}

	<footer class="article__footer">
		{if MODULE_LIKE && ARTICLE_ENABLE_LIKE && $__wcf->session->getPermission('user.like.canViewLike')}
			<div class="articleLikesSummery">
				{include file="reactionSummaryList" reactionData=$articleLikeData objectType="com.woltlab.wcf.likeableArticle" objectID=$article->articleID}
			</div>
		{/if}
		
		{hascontent}
			<ul class="article__footerButtons buttonGroup buttonList smallButtons">
				{content}
					{if $__wcf->session->getPermission('user.profile.canReportContent')}
						<li>
							<button
								type="button"
								title="{lang}wcf.moderation.report.reportContent{/lang}"
								class="button jsTooltip"
								data-report-content="com.woltlab.wcf.article"
								data-object-id="{$articleContent->articleID}"
							>
								{icon name='triangle-exclamation'}
								<span class="invisible">{lang}wcf.moderation.report.reportContent{/lang}</span>
							</button>
						</li>
					{/if}
					{if MODULE_LIKE && ARTICLE_ENABLE_LIKE && $__wcf->session->getPermission('user.like.canLike') && $article->userID != $__wcf->user->userID}
						<li>
							<button
								type="button"
								class="button jsTooltip reactButton{if $articleLikeData[$article->articleID]|isset && $articleLikeData[$article->articleID]->reactionTypeID} active{/if}"
								title="{lang}wcf.reactions.react{/lang}"
								data-reaction-type-id="{if $articleLikeData[$article->articleID]|isset && $articleLikeData[$article->articleID]->reactionTypeID}{$articleLikeData[$article->articleID]->reactionTypeID}{else}0{/if}"
							>
								{icon name='face-smile'} <span class="invisible">{lang}wcf.reactions.react{/lang}</span>
							</button>
						</li>
					{/if}
					
					{event name='articleLikeButtons'}{* deprecated: use footerButtons instead *}
					{event name='articleButtons'}{* deprecated: use footerButtons instead *}
					{event name='footerButtons'}
				{/content}
			</ul>
		{/hascontent}
	</footer>
</div>

{if ARTICLE_SHOW_ABOUT_AUTHOR && $article->getUserProfile()->aboutMe}
	<div class="section articleAboutAuthor">
		<h2 class="sectionTitle">{lang}wcf.article.aboutAuthor{/lang}</h2>
		
		<div class="box128">
			<span class="articleAboutAuthorAvatar">{unsafe:$article->getUserProfile()->getAvatar()->getImageTag(128)}</span>
			
			<div>
				{event name='beforeAboutAuthorText'}
				
				<div class="articleAboutAuthorText">{unsafe:$article->getUserProfile()->getFormattedUserOption('aboutMe')}</div>
				
				{event name='afterAboutAuthorText'}
				
				<div class="articleAboutAuthorUsername">
					{user object=$article->getUserProfile() class='username'}
					
					{if MODULE_USER_RANK}
						{if $article->getUserProfile()->getUserTitle()}
							<span class="badge userTitleBadge{if $article->getUserProfile()->getRank() && $article->getUserProfile()->getRank()->cssClassName} {$article->getUserProfile()->getRank()->cssClassName}{/if}">{$article->getUserProfile()->getUserTitle()}</span>
						{/if}
						{if $article->getUserProfile()->getRank() && $article->getUserProfile()->getRank()->rankImage}
							<span class="userRank">{unsafe:$article->getUserProfile()->getRank()->getImage()}</span>
						{/if}
					{/if}
				</div>
			</div>
		</div>
	</div>
{/if}

<footer class="contentFooter">
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{event name='afterFooter'}

{if $previousArticle || $nextArticle}
	<div class="section">
		<nav>
			<ul class="articleNavigation">
				{if $previousArticle}
					<li class="previousArticleButton articleNavigationArticle{if $previousArticle->getTeaserImage()} articleNavigationArticleWithImage{/if}">
						<span class="articleNavigationArticleIcon">
							{icon size=48 name='chevron-left'}
						</span>
						{if $previousArticle->getTeaserImage()}
							<span class="articleNavigationArticleImage">{unsafe:$previousArticle->getTeaserImage()->getElementTag(96)}</span>
						{/if}
						<span class="articleNavigationArticleContent">
							<span class="articleNavigationEntityName">{lang}wcf.article.previousArticle{/lang}</span>
							<span class="articleNavigationArticleTitle">
								<a href="{$previousArticle->getLink()}" rel="prev" class="articleNavigationArticleLink articleLink" data-object-id="{$previousArticle->getObjectID()}">
									{$previousArticle->getTitle()}
								</a>
							</span>
						</span>
					</li>
				{/if}
				
				{if $nextArticle}
					<li class="nextArticleButton articleNavigationArticle{if $nextArticle->getTeaserImage()} articleNavigationArticleWithImage{/if}">
						<span class="articleNavigationArticleIcon">
							{icon size=48 name='chevron-right'}
						</span>
						{if $nextArticle->getTeaserImage()}
							<span class="articleNavigationArticleImage">{unsafe:$nextArticle->getTeaserImage()->getElementTag(96)}</span>
						{/if}
						<span class="articleNavigationArticleContent">
							<span class="articleNavigationEntityName">{lang}wcf.article.nextArticle{/lang}</span>
							<span class="articleNavigationArticleTitle">
								<a href="{$nextArticle->getLink()}" rel="next" class="articleNavigationArticleLink articleLink" data-object-id="{$nextArticle->getObjectID()}">
									{$nextArticle->getTitle()}
								</a>
							</span>
						</span>
					</li>
				{/if}
			</ul>
		</nav>
	</div>
{/if}

{if $relatedArticles !== null && $relatedArticles|count}
	<section class="section relatedArticles">
		<h2 class="sectionTitle">{lang}wcf.article.relatedArticles{/lang}</h2>
		
		{include file='articleListItems' objects=$relatedArticles}
	</section>
{/if}

{event name='beforeComments'}

{unsafe:$article->getDiscussionProvider()->renderDiscussions()}

{if MODULE_LIKE && ARTICLE_ENABLE_LIKE}
	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ui/Reaction/Handler'], function(UiReactionHandler) {
			new UiReactionHandler('com.woltlab.wcf.likeableArticle', {
				// permissions
				canReact: {if $__wcf->getUser()->userID}true{else}false{/if},
				canReactToOwnContent: false,
				canViewReactions: {if LIKE_SHOW_SUMMARY}true{else}false{/if},
				
				// selectors
				containerSelector: '.coreArticle',
				summarySelector: '.articleLikesSummery'
			});
		});
	</script>
{/if}

{include file='footer'}
