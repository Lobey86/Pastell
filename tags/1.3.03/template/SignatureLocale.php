
<a class='btn btn-mini' href='document/edition.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'><i class='icon-circle-arrow-left'></i><?php echo $info['titre']? $info['titre']:$info['id_d']?></a>
<div class="box">


<div class='action'>
<applet codebase = "<?php echo $libersign_url ?>"
		code = "org/adullact/parapheur/applets/splittedsign/Main.class"
		archive = "SplittedSignatureApplet.jar, lib/bcmail-jdk16-138.jar, lib/bcprov-jdk16-138.jar, lib/xom-1.1.jar"
		name = "appletsignature"
		width = "500"
		height = "257" >
		<param name="hash_count" value="<?php echo count($tab_included_files)?>" />
		<?php foreach($tab_included_files as $i => $included_file) : ?>
			<param name="iddoc_<?php echo $i +1?>" value="<?php echo $included_file['id']?>" />
			<param name="hash_<?php echo $i +1?>" value="<?php echo $included_file['sha1'] ?>" /> 
			<param name="format_<?php echo $i +1?>" value="CMS" />
		<?php endforeach;?> 
		<param name="id_user" value="id=<?php echo $id_d?>" />
		<param name="return_mode" value="form" />
	 </applet>
	 </div>
<script type="text/javascript" src="/javascript/jfu/js/jquery.min.js"></script> 

<form action='document/external-data-controler.php' method='post' id='form_sign'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<input type='hidden' name='id' id='form_sign_id' value='<?php echo $id_d?>'/>
	<input type='hidden' name='nb_signature'  value='<?php echo count($tab_included_files)?>'/>
	<?php foreach($tab_included_files as $i => $included_file) : ?>
		<input type='hidden' name='signature_id_<?php echo $i +1?>' value='<?php echo $included_file['id']?>' />
		<input type='hidden' name='signature_<?php echo $i +1?>' id='signature_<?php echo $i +1?>' value=''/>
	<?php endforeach;?>
</form>

<script>
function injectSignature() {
    signature = null;
    <?php foreach($tab_included_files as $i => $included_file) : ?> 
    	signature = document.applets[0].returnSignature("<?php echo $included_file['id'] ?>");
		$("#signature_<?php echo $i + 1?>").val(signature);
	<?php endforeach;?>
	$("#form_sign").submit();
}
</script>
	 
	 </div>