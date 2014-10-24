
<a class='btn btn-mini' href='document/edition.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'><i class='icon-circle-arrow-left'></i><?php echo $info['titre']? $info['titre']:$info['id_d']?></a>
<div class="box">


<div class='action'>
	<applet codebase = "<?php echo $libersign_url ?>"
			code = "org/adullact/parapheur/applets/splittedsign/Main.class" 
			archive = "SplittedSignatureApplet.jar, lib/bcmail-jdk16-138.jar, lib/bcprov-jdk16-138.jar, lib/xom-1.1.jar" 
			name = "appletsignature"
			width = "500"
			height = "257" >
			
	<param value="all-permissions" name="permissions"></param>
    <param value="false" name="codebase_lookup"></param>
    <param value="true" name="display_cancel"></param>
    <param value="javascript" name="cancel_mode"></param>
    <param value="1" name="hash_count"></param>
    <param value="<?php echo $signatureInfo['bordereau_hash']?>" name="hash_1"></param>
    <param value="<?php echo $signatureInfo['bordereau_id']?>" name="pesid_1"></param>
    <param value="<?php echo $signatureInfo['bordereau_id']?>" name="iddoc_1"></param>
    
    <param value="urn:oid:1.2.250.1.131.1.5.18.21.1.4" name="pespolicyid_1"></param>
    <param value="Politique de signature Helios de la DGFiP" name="pespolicydesc_1"></param>
    <param value="Jkdb+aba0Hz6+ZPKmKNhPByzQ+Q=" name="pespolicyhash_1"></param>
    <param value="https://portail.dgfip.finances.gouv.fr/documents/PS_Helios_DGFiP.pdf" name="pesspuri_1"></param>
    <param value="France" name="pescountryname_1"></param>
    <param value="Ordonnateur" name="pesclaimedrole_1"></param>
    <param value="null" name="p7s_1"></param>
    <param value="iso-8859-1" name="pesencoding_1"></param>
    <param value="XADES-env" name="format_1"></param>
    <param value="form" name="return_mode"></param>
			

    <param value="<?php hecho($libersign_properties->get('libersign_city'))?>" name="pescity_1"></param>
    <param value="<?php hecho($libersign_properties->get('libersign_cp'))?>" name="pespostalcode_1"></param>
    
		
	 </applet>
	 </div>
<script type="text/javascript" src="/javascript/jfu/js/jquery.min.js"></script> 
<form action='document/external-data-controler.php' id='form_sign' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />
	<input type='hidden' name='nb_signature'  value='1'/>
	<input type='hidden' name='signature_id_1' value='<?php echo $signatureInfo['bordereau_id']?>' />
	<input type='hidden' name='signature_1' id='signature_1' value=''/>
</form>
<script>
function injectSignature() {
	signature = document.applets[0].returnSignature("<?php echo $signatureInfo['bordereau_id'] ?>");
	$("#signature_1").val(signature);
	$("#form_sign").submit();
}
</script>
</div>