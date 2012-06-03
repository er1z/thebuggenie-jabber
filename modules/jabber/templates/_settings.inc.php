<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
	<div class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 744px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These are the settings for outgoing communication, such as notification messages.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="mailnotification_settings_table">
			<tr>
				<td style="width: 300px; padding: 5px;"><label for="enable_outgoing_notifications"><?php echo __('Enable outgoing Jabber notifications'); ?></label></td>
				<td style="width: auto;">
					<select name="enable_outgoing_notifications" id="enable_outgoing_notifications" onchange="if ($(this).getValue() == 0) { $('mailnotification_settings_table').select('input').each(function (element, index) { element.disable(); }); } else { $('mailnotification_settings_table').select('input').each(function (element, index) { element.enable(); }); }">
						<option value="1"<?php if ($module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value="0"<?php if (!$module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="from_jid"><?php echo __('"From" JID'); ?></label></td>
				<td><input type="text" name="jid" id="from_jid" value="<?php echo $module->getSetting('jid'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="from_pass"><?php echo __('Password'); ?></label></td>
				<td><input type="password" name="password" id="from_jid" value="" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="from_host"><?php echo __('Host [if differs than JID]'); ?></label></td>
				<td><input type="text" name="host" id="from_host" value="<?php echo $module->getSetting('host'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="from_port"><?php echo __('Port [5222]'); ?></label></td>
				<td><input type="text" name="port" id="from_port" value="<?php echo $module->getSetting('port'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="from_res"><?php echo __('Resource'); ?></label></td>
				<td><input type="text" name="resource" id="from_res" value="<?php echo $module->getSetting('resource'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			
			<tr>
				<td style="padding: 5px;"><label for="use_queue"><?php echo __('Queue messages for batch processing'); ?></label></td>
				<td>
					<input type="radio" name="use_queue" value="0" id="use_queue_no"<?php if (!$module->usesMessageQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_no"><?php echo __('Send notifications instantly'); ?></label><br>
					<input type="radio" name="use_queue" value="1" id="use_queue_yes"<?php if ($module->usesMessageQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_yes"><?php echo __('Use message queueing'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __("If you're using a queue, outgoing messages will not slow down the system."); ?></td>
			</tr>
		</table>
		<table style="width: 680px; margin-top: 10px;<?php if ($module->getSetting('mail_type') != TBGMailer::MAIL_TYPE_B2M): ?> display: none;<?php endif; ?>" class="padded_table" cellpadding=0 cellspacing=0 id="mail_type_b2m_info">
			<tr>
				<td style="width: 300px; padding: 5px;"><label for="smtp_host"><?php echo __('SMTP server address'); ?></label></td>
				<td style="width: auto;"><input type="text" name="smtp_host" id="smtp_host" value="<?php echo $module->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_port"><?php echo __('SMTP address port'); ?></label></td>
				<td><input type="text" name="smtp_port" id="smtp_port" value="<?php echo $module->getSetting('smtp_port'); ?>" style="width: 40px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="timeout"><?php echo __('SMTP server timeout'); ?></label></td>
				<td><input type="text" name="timeout" id="timeout" value="<?php echo $module->getSetting('timeout'); ?>" style="width: 40px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>><?php echo __('%number_of% seconds', array('%number_of%' => '')); ?></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('Connection information for the outgoing email server'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="ehlo_no"><?php echo __('Microsoft Exchange server'); ?></label></td>
				<td>
					<input type="radio" name="ehlo" id="ehlo_yes" value="1" <?php echo ($module->getSetting('ehlo') == 1) ? ' checked' : ''; ?>><label for="ehlo_yes"><?php echo __('No'); ?></label>&nbsp;
					<input type="radio" name="ehlo" id="ehlo_no" value="0" <?php echo ($module->getSetting('ehlo') == 0) ? ' checked' : ''; ?>><label for="ehlo_no"><?php echo __('Yes'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('For compatibility reasons, specify whether the SMTP server is a Microsoft Exchange server'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_user"><?php echo __('SMTP username'); ?></label></td>
				<td><input type="text" name="smtp_user" id="smtp_user" value="<?php echo $module->getSetting('smtp_user'); ?>" style="width: 300px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The username used for sending emails'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_wd"><?php echo __('SMTP password'); ?></label></td>
				<td><input type="password" name="smtp_pwd" id="smtp_pwd" value="<?php echo $module->getSetting('smtp_pwd'); ?>" style="width: 150px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The password used for sending emails'); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
		</table>
	</div>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 740px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save email notification settings', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
	</div>
<?php endif; ?>
</form>
<?php if ($module->isEnabled()): ?>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('jabber_test_message');//*/ // TODO routes fail ?>" method="post">
		<div class="rounded_box borderless mediumgrey" style="margin: 10px 0 0 0; width: 740px; padding: 5px 5px 30px 5px;">
			<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 125px; padding: 5px;"><label for="jid"><?php echo __('Send test email'); ?></label></td>
					<td style="width: auto;"><input type="text" name="jid" id="jid" value="" style="width: 300px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td class="config_explanation" colspan="2" style="font-size: 13px;">
						<span class="faded_out">
							<?php
							echo __('Enter an JID, and click "%send_test_message%" to check if the email module is configured correctly', array('%send_test_message%' => __('Send test message')));
							?>
						</span>
					</td>
				</tr>
			</table>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Send test message'); ?>">
		</div>
	</form>
<?php endif; ?>