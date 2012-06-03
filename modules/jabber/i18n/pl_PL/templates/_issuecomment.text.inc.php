Witaj, <?PHP echo $user_buddyname;?>!
Został dodany komentarz do sprawy: <?php echo TBGContext::getI18n()->__($issue->getIssueType()->getName()); ?> <?php echo $issue->getFormattedIssueNo(true); ?> - <?php echo $issue->getTitle(); ?> by <?php echo $comment->getPostedBy()->getName(); ?>:

<?php echo tbg_parse_text($comment->getContent()); ?>

---
Otwórz sprawę: <?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>

Pokaż kokpit projektu <?php echo $issue->getProject()->getName(); ?>: <?php echo make_url('project_dashboard', array('project_key' => $issue->getProject()->getKey()), false); ?>