<?php
class UtilisateurListeHTML {
	
	private $droitEdition;
	
	
	public function addDroitEdition(){
		$this->droitEdition = true;
	}
	
	public function display(array $liste_utilisateur,$id_e){
		
		?>
		
		<h2>Liste des utilisateurs
		<?php if ($this->droitEdition) : ?>
		<a href="utilisateur/edition.php?id_e=<?php echo $id_e?>" class='btn_add'>
				Nouveau
			</a>
		<?php endif;?>
		</h2>
		
		<table class='tab_02'>
		<tr>
			<th>Prénom Nom</th>
			<th>login</th>
			<th>email</th>
			<th>Role</th>
			
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
				<td><?php echo $user['role']?></td>
			</tr>
		<?php endforeach; ?>
		
		</table>
		
		<?php
	}
	
}