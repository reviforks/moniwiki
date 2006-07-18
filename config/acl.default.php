# acl.default.php
# <?php exit()?>
# Please don't modify the lines above
#
# A sample Access Control Lists file for Moniwiki
#
# $Id$
@Guest	Anonymous
*	@ALL		deny	*
*	@ALL		allow	edit,ticket,savepage
*	@User		allow	*
# some pages are allowed to edit
WikiSandBox	@Guest	allow	edit,info,diff
#*	Anonymous	deny	*
# some POST actions support protected mode using admin password
*	@ALL		protect	deletefile,deletepage,rename,rcspurge,rcs,chmod,backup,restore
# some actions allowed to @ALL
*	@ALL		allow	read,userform,rss_rc,aclinfo,fortune,deletepage,fixmoin,ticket
# some pages have restrict permission
MoniWiki	@ALL	deny	edit,uploadfile,diff
HelpOn*		@User	deny	edit,uploadfile,diff
# special pages
ACL	@ALL		deny    edit,diff,info # a shared ACL file (not supported yet XXX)
# set group members
#@KLDP	Hello,foobar,moniwiki
#@Kiwirian	foobar
#*	@Kiwirian	deny	*
#*	@Kiwirian	allow	read
