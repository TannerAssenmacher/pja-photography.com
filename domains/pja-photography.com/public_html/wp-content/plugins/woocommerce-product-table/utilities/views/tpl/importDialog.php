<style id="wtbpImportWndStyle" type="text/css">
	.wtbpImportReasonShell {
		display: block;
		margin-bottom: 10px;
	}
	#wtbpImportWnd {display: none; clear:both;}
	#wtbpImportWnd input[type="text"],
	#wtbpImportWnd textarea {
		width: 100%;
	}
	#wtbpImportWnd h4 {
		line-height: 1.53em;
	}
	#wtbpImportWnd + .ui-dialog-buttonpane .ui-dialog-buttonset {
		float: none;
	}
</style>
<div id="wtbpImportWnd" title="<?php echo esc_attr(__('Import Tables', 'woo-product-tables')); ?>">
	<p><?php esc_html_e('Upload your export sql file', 'woo-product-tables'); ?></p>
	<form id="wtbpImportForm">
		<label class="wtbpImportReasonShell">
			<?php
			HtmlWtbp::input('import_file', array(
				'type' => 'file',
				'attrs' => ' id="wtbpImportInput" accept=".sql"'
			));
			?>
		</label>
		<?php HtmlWtbp::hidden('mod', array('value' => 'utilities')); ?>
		<?php HtmlWtbp::hidden('action', array('value' => 'importGroup')); ?>
	</form>
</div>
