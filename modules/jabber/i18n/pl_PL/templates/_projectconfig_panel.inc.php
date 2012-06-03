<script type="text/javascript">
	TBG.Modules.jabber = {};
	TBG.Modules.jabber.checkIncomingAccount = function(url, account_id) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'jabber_account_' + account_id + '_indicator'},
			success: {
				callback: function(json) {
					$('jabber_account_' + account_id + '_time').update(json.time);
					$('jabber_account_' + account_id + '_count').update(json.count);
				}
			}
		});
	};
	
	TBG.Modules.jabber.deleteIncomingAccount = function(url, account_id) {
		TBG.Main.Helpers.ajax(url, {
			loading: {indicator: 'jabber_account_' + account_id + '_indicator'},
			success: {
				remove: 'incoming_email_account_' + account_id,
				callback: TBG.Main.Helpers.Dialog.dismiss
			}
		});
	};
</script>
<div id="tab_jabber_pane"<?php if ($selected_tab != 'jabber'): ?> style="display: none;"<?php endif; ?>>
<h3>Editing email settings</h3>
	<div class="content">
		<?php echo __('The Bug Genie can check email accounts and create issues from incoming emails. Set up a new account here, and check the %online_documentation% for more information.', array('%online_documentation%' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:IncomingEmail', '<b>'.__('online documentation').'</b>'))); ?>
	</div>
	<?php if ($access_level != TBGSettings::ACCESS_FULL): ?>
		<div class="rounded_box red" style="margin-top: 10px;">
			<?php echo __('You do not have the relevant permissions to access email settings'); ?>
		</div>
	<?php else: ?>
		<h4>
			<div class="button button-green" style="float: right;" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'jabber_editincomingemailaccount', 'project_id' => $project->getId())); ?>');"><?php echo __('Add new account'); ?></div>
			<?php echo __('Incoming email accounts'); ?>
		</h4>
		<div id="jabber_incoming_accounts">
			<?php foreach (TBGContext::getModule('jabber')->getIncomingEmailAccountsForProject(TBGContext::getCurrentProject()) as $account): ?>
				<?php include_template('jabber/incomingemailaccount', array('account' => $account)); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>