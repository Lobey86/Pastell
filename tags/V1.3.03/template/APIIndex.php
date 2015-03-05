<div class="box">
	<h2>Généralités</h2>
<p>

L'authentification à l'API se fait soit : </p>

<ul>
	<li>via un certificat</li>
	<li>via le login/mot de passe Pastell. Celui-ci doit être passé via une authentification HTTP en mode BASIC</li>
	<li>via une connexion CAS, pour cela il faut ajouter un paramètre auth='cas' dans chacune des requête de l'API</li>
</ul>
<h3>Paramètres d'entrée</h3>
<p>
Les paramètres peuvent être envoyés en GET ou en POST. Si des fichiers doivent être envoyés, alors 
il faudra utiliser POST.
</p>
<h3>Paramètres de sortie</h3>
<p>Les paramètres <em>en italique</em> indique que le nom de la clé dépend du résultat.</p>
<p>Une étoile* à la fin du paramètre indique que le paramètre peut être multiple</p>

</div>
<?php 

foreach($functions_list as $function_name => $function_properties) : ?>
<div class="box">

<h2><?php hecho($function_properties[APIDefinition::KEY_DESCRIPTION])?> : <?php hecho($function_name)?></h2>
<p>Description: <?php hecho($function_properties[APIDefinition::KEY_COMMENT])?> </p>
<p>URL du script REST : <?php echo SITE_BASE ?>api/<?php hecho($function_name)?>.php<br/></p>
<?php if ($function_properties[APIDefinition::KEY_SOAP]) : ?>
Méthode SOAP : <?php hecho($function_properties[APIDefinition::KEY_SOAP_NAME])?>
<?php endif;?>
<p></p>

<h3>Paramètres d'entrée</h3>
<?php if ($function_properties[APIDefinition::KEY_INPUT] ) : ?>
<table class="table table-striped">
	<tr>
		<th>Nom du paramètre</th>
		<th>Obligatoire ? </th>
		<th>Valeur par défaut</th>
		<th>Commentaire</th>
	</tr>
	<?php foreach($function_properties[APIDefinition::KEY_INPUT] as $name => $value): ?>
	<tr>
		<td><?php echo $name ?></td>
		<td><?php echo $value[APIDefinition::KEY_REQUIRED]?"oui":"non"?></td>
		<td><?php echo $value[APIDefinition::KEY_DEFAULT]?></td>
		<td><?php echo $value[APIDefinition::KEY_COMMENT]?></td>
	</tr>
	<?php endforeach;?>
</table>
<br/><br/>
<?php else: ?>
<p>Cette fonction ne prend pas de paramètre d'entrée</p>
<?php endif;?>

<h3>Paramètres de sortie</h3>
<?php if ($function_properties[APIDefinition::KEY_OUTPUT] ) : ?>
<table class="table table-striped">
	<tr>
		<th>Nom du paramètre</th>
		<th>Commentaire</th>
	</tr>
	<?php foreach($function_properties[APIDefinition::KEY_OUTPUT] as $name => $value): ?>
	<tr>
		<td>
			<?php if ($value[APIDefinition::KEY_IS_VARIABLE]) : ?>
				<em><?php echo $name ?></em>
			<?php else: ?>
			<?php echo $name ?>
			<?php endif;?>
			<?php if ($value[APIDefinition::KEY_IS_MULTIPLE]) : ?>
				*
			<?php endif;?>
			
		</td>
		<td><?php echo $value[APIDefinition::KEY_COMMENT]?>
		<?php if ($value[APIDefinition::KEY_CONTENT]) : ?>
				<br/>Contenu :
				<ul>
					<?php foreach($value[APIDefinition::KEY_CONTENT] as $content_name => $content_properties ): ?>
						<li>
						<?php if ($content_properties[APIDefinition::KEY_IS_VARIABLE]) : ?>
							<em><?php hecho($content_name)?> </em>
						<?php else: ?>
							<?php hecho($content_name)?> 
						<?php endif;?>
						<?php if ($content_properties[APIDefinition::KEY_IS_MULTIPLE]) : ?>*<?php endif;?>			
						: <?php hecho($content_properties['comment'])?></li>
					<?php endforeach;?>
				</ul>
			<?php endif;?>
		</td>
	</tr>
	<?php endforeach;?>
</table>
<br/><br/>
<?php else: ?>
<p>Cette fonction ne renvoie pas de données</p>
<?php endif;?>

</div>

<?php endforeach;?>