<script>
// TBG. namespace has been designed without namespaces, so handle accordingly manually
(function($){
$(function(){
  $('input[name=jabber_notification_settings_preset]').change(function(){
	console.log($(this).val());
    if($(this).val()=='custom'){
      $('#jabber_notification_settings_selectors').show();
    }else{
      $('#jabber_notification_settings_selectors').hide();
    }
  });
});
})(jQuery);
</script>

<table style="width: 895px; margin-bottom: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
	<td style="width: 60px; padding: 5px; border-bottom: 1px solid #DDD;"><label for="jabber_jid" style="font-weight: normal;"><?php echo __('Your JID'); ?></label></td>
	<td style="padding: 5px; border-bottom: 1px solid #DDD;" valign="middle">
		<input type="textheckbox" name="jabber_jid" value="<?PHP echo $module->getSetting('jid', $uid); ?>" id="jabber_jid">
	</td>
</table>

<table style="width: 895px; margin-bottom: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
	<tr>
		<td valign="middle">
			<label for="jabber_notification_settings_preset_recommended"><?php echo __('Notification preset'); ?></label>
		</td>
		<td>
			<input type="radio" name="jabber_notification_settings_preset" value="silent"<?php if ($selected_preset == 'silent') echo ' checked'; ?> id="jabber_notification_settings_preset_silent">&nbsp;<label for="jabber_notification_settings_preset_silent"><?php echo __('Silent notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __('We will hardly ever send you any messages. Check back regularly.'); ?></div>
			<input type="radio" name="jabber_notification_settings_preset" value="recommended"<?php if ($selected_preset == 'recommended') echo ' checked'; ?> id="jabber_notification_settings_preset_recommended">&nbsp;<label for="jabber_notification_settings_preset_recommended"><?php echo __('Recommended notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __("We will keep you updated when important stuff happens, but we'll keep quiet about less important stuff."); ?></div>
			<input type="radio" name="jabber_notification_settings_preset" value="verbose"<?php if ($selected_preset == 'verbose') echo ' checked'; ?> id="jabber_notification_settings_preset_verbose">&nbsp;<label for="jabber_notification_settings_preset_verbose"><?php echo __('Verbose notification settings'); ?></label><br>
			<div class="faded_out"><?php echo __("If anything happens, you'll know.."); ?></div>
			<input type="radio" name="jabber_notification_settings_preset" value="custom"<?php if ($selected_preset == 'custom') echo ' checked'; ?> id="jabber_notification_settings_preset_custom">&nbsp;<label for="jabber_notification_settings_preset_custom"><?php echo __('Advanced settings'); ?></label><br>
			<div class="faded_out"><?php echo __("Pick and choose, mix or match - it's like an all-you-can-eat notification feast."); ?></div>
		</td>
	</tr>
</table>
<div id="jabber_notification_settings_selectors" <?php if ($selected_preset != 'custom'): ?>style="display: none;"<?php endif; ?>>
	<table style="width: 895px; margin-bottom: 15px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td style="border-bottom: 1px solid #CCC; font-size: 12px; font-weight: bold;"><?php echo __('Issues'); ?></td>
			<td style="width: 50px; text-align: center; border-bottom: 1px solid #CCC;">&nbsp;</td>
		</tr>
		<?php foreach ($issues_settings as $setting => $description): ?>
			<tr>
				<td style="width: auto; padding: 5px; border-bottom: 1px solid #DDD;"><label for="jabber_<?php echo $setting; ?>_yes" style="font-weight: normal;"><?php echo $description; ?></label></td>
				<td style="width: 50px; padding: 5px; text-align: center; border-bottom: 1px solid #DDD;" valign="middle">
					<input type="checkbox" name="jabber_<?php echo $setting; ?>" value="1" id="jabber_<?php echo $setting; ?>_yes"<?php if ($module->getSetting($setting, $uid) == 1): ?> checked<?php endif; ?>>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>