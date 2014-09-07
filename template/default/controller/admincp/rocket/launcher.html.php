<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */
?>
{$style}
<h2>Requirements check</h2>
<h3>{$messages.inipath}</h3>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading">Mandatory requirements</div>
	<div class="panel-body">
		<ul class="list-group">
			{foreach from=$messages.requirements.mandatory name=mandatory item=requirement}
			{$requirement}
			{/foreach}
		</ul>
	</div>
</div>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading">Optional requirements</div>
	<div class="panel-body">
		<ul class="list-group">
			{foreach from=$messages.requirements.optional name=mandatory item=requirement}
			{$requirement}
			{/foreach}
		</ul>
	</div>
</div>