<?php
class UtilisateurListeHTML {
	
	private $droitEdition;
	private $allDroit;

	public function addDroitEdition(){
		$this->droitEdition = true;
	}
	
	public function addDroit($allDroit){
		$this->allDroit = $allDroit;
	}
	
	public function displayAll(array $liste_utilisateur){
		?>
	
		
		<table class='tab_02'>
		<tr>
			<th>Prénom Nom</th>
			<th>login</th>
			<th>email</th>
			<th>entité de base</th>					
		</tr>
		
		<?php foreach($liste_utilisateur as $user) : ?>
			<tr>
				<td>
					<a href='utilisateur/detail.php?id_u=<?php echo $user['id_u'] ?>'>
						<?php if (empty($user['prenom']) && empty($user['nom'])) : ?>
							[pas d'information]
						<?php else : ?>
						<?php echo $user['prenom']?> <?php echo $user['nom']?>
						<?php endif; ?>
					</a>
				</td>
				<td><?php echo $user['login']?></td>
				<td><?php echo $user['email']?></td>
				<td><a href='entite/detail.php?id_e=<?php echo $user['id_e']?>'><?php echo $user['denomination']?></a></td>
			</tr>
		<?php endforeach; ?>
		
		</table>
		
		<?php
	}
	
	public function display(array $liste_utilisateur,$id_e,$droit_selected='',$descendance=''){ ?>
		
		<h2>Liste des utilisateurs
		<?php if ($this->droitEdition) : ?>
		<a href="utilisateur/edition.php?id_e=<?php echo $id_e?>" class='btn_add'>
				Nouveau
			</a>
		<?php endif;?>
		<?php echo $droit_selected?" : $droit_selected":""?>
		</h2>
		
	
		<?php if ($this->allDroit) : ?>
			<div>
			<form action="entite/detail.php" method='get'>
				<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
				<input type='hidden' name='page' value='1'/>
				<input type='checkbox' name='descendance' <?php echo $descendance?"checked='checked'":""?>/>Afficher les utilisateurs des entités filles<br/>
				<select name='droit'>
					<option value=''>Filtrer les droits</option>
					<?php foreach($this->allDroit as $droit):?>
						<option value='<?php echo $droit?>' <?php echo $droit_selected==$droit?'selected="selected"':''?>><?php echo $droit?></option>
					<?php endforeach;?>
				</select>
				<input type='submit' value='Afficher'/>
			</form>
			</div>
		<br/>
		
		<?php endif;?>
		
		
		<table class='tab_02'>
		<tr>
			<th>Prénom Nom</th>
			<th>login</th>
			<th>email</th>
			<th>Role</th>
			<?php if ($descendance) : ?>
				<th>Collectivité de base</th>
			<?php endif;?>
		</tr>
		
		<?php foreach($liste_utilisateur as $user) : ?>
			<tr>
				<td>
					<a href='utilisateur/detail.php?id_u=<?php echo $user['id_u'] ?>'>
						<?php echo $user['prenom']?> <?php echo $user['nom']?>
					</a>
				</td>
				<td><?php echo $user['login']?></td>
				<td><?php echo $user['email']?></td>
				<td><?php echo implode(", ",$user['all_role'])?></td>
				<?php if ($descendance) : ?>
					<td><a href='entite/detail.php?id_e=<?php echo $user['id_e']?>'><?php echo $user['denomination']?></a></td>
				<?php endif;?>
			</tr>
		<?php endforeach; ?>
		
		</table>
		
		<?php
	}
	
}