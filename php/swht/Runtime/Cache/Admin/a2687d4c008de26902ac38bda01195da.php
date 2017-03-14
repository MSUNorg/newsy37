<?php if (!defined('THINK_PATH')) exit();?><div class="span<?php echo ($addons_config["width"]); ?>">
	<div class="columns-mod">
		<div class="hd cf">
			<h5><?php echo ($addons_config["title"]); ?></h5>
			<div class="title-opt">
			</div>
		</div>
		<div class="bd">
			<div class="sys-info">
				<table>
					<tr>
						<th>OneThink版本</th>
						<td><?php echo (ONETHINK_VERSION); ?>&nbsp;&nbsp;&nbsp;
							<?php if(!empty($addons_config["new_version"])): ?><a href="http://www.onethink.cn" target="_blank">
									发现新版本[<?php echo ($addons_config["new_version"]); ?>]
								</a>&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="<?php echo U('/update/index');?>" target="_blank">
                                    在线更新
                                </a><?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>服务器操作系统</th>
						<td><?php echo (PHP_OS); ?></td>
					</tr>
					<tr>
						<th>ThinkPHP版本</th>
						<td><?php echo (THINK_VERSION); ?></td>
					</tr>
					<tr>
						<th>运行环境</th>
						<td><?php echo ($_SERVER['SERVER_SOFTWARE']); ?></td>
					</tr>
					<tr>
						<th>MYSQL版本</th>
						<?php $system_info_mysql = M()->query("select version() as v;"); ?>
						<td><?php echo ($system_info_mysql["0"]["v"]); ?></td>
					</tr>
					<tr>
						<th>上传限制</th>
						<td><?php echo ini_get('upload_max_filesize');?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>