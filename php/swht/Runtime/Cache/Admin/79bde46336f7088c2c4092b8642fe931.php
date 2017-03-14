<?php if (!defined('THINK_PATH')) exit();?> <div class="container-span top-columns cf">
	<dl class="show-num-mod">
		<dt><i class="count-icon user-count-icon"></i></dt>
		<dd>
			<strong><?php echo ($info["user"]); ?></strong>
			<span>用户数</span>
		</dd>
	</dl>
	<dl class="show-num-mod">
		<dt><i class="count-icon user-action-icon"></i></dt>
		<dd>
			<strong><?php echo ($info["action"]); ?></strong>
			<span>用户行为</span>
		</dd>
	</dl>
	<dl class="show-num-mod">
		<dt><i class="count-icon doc-count-icon"></i></dt>
		<dd>
			<strong><?php echo ($info["document"]); ?></strong>
			<span>文档数</span>
		</dd>
	</dl>
	<dl class="show-num-mod">
		<dt><i class="count-icon doc-modal-icon"></i></dt>
		<dd>
			<strong><?php echo ($info["model"]); ?></strong>
			<span>文档模型</span>
		</dd>
	</dl>
	<dl class="show-num-mod">
		<dt><i class="count-icon category-count-icon"></i></dt>
		<dd>
			<strong><?php echo ($info["category"]); ?></strong>
			<span>分类数</span>
		</dd>
	</dl>
</div>