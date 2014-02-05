<?php 


class CreatePES extends ActionExecutor {
	
	public function go(){
		$entite_info = $this->getEntite()->getInfo();
		;
		$nom_fic = mt_rand(0,mt_getrandmax());
		$date = date("Y-m-d");
		
		$pes_content = $this->getPES($nom_fic,$entite_info['siren'],$date);
		
		header("Content-type: text/xml");
		header("Content-disposition: attachment; filename=PES_$nom_fic.xml");
		echo $pes_content;
		exit;
	}
	
	private function getPES($nom_fic,$siren,$date){

	$content = <<< PES_ALLER
<?xml version="1.0" encoding="ISO-8859-1"?>
<n:PES_Aller xmlns:n="http://www.minefi.gouv.fr/cp/helios/pes_v2/Rev0/aller" xmlns:acta="http://www.minefi.gouv.fr/cp/helios/pes_v2/etatactif/r0/aller" xmlns:buda="http://www.minefi.gouv.fr/cp/helios/pes_v2/budget/r0/aller" xmlns:cm="http://www.minefi.gouv.fr/cp/helios/pes_v2/commun" xmlns:depa="http://www.minefi.gouv.fr/cp/helios/pes_v2/depense/r0/aller" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:empa="http://www.minefi.gouv.fr/cp/helios/pes_v2/emprunt/r0/aller" xmlns:mara="http://www.minefi.gouv.fr/cp/helios/pes_v2/marche/r0/aller" xmlns:reca="http://www.minefi.gouv.fr/cp/helios/pes_v2/recette/r0/aller" xmlns:rola="http://www.minefi.gouv.fr/cp/helios/pes_v2/role/r0/aller" xmlns:xad="http://uri.etsi.org/01903/v1.1.1#" xmlns:xenc="http://www.w3.org/2001/04/xmlenc#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
 <Enveloppe>
  <Parametres>
   <Version V="1"/>
   <TypFic V="PESALR2"/>
   <NomFic V="$nom_fic"/>
  </Parametres>
  <Emetteur>
   <Sigle V="EMSIGLE"/>

   <Adresse V="EMADRESSE"/>
  </Emetteur>
  <Recepteur>
   <Sigle V="HELIOS"/>
   <Adresse V="READRESSE"/>
  </Recepteur>
 </Enveloppe>
 <EnTetePES>
  <DteStr V="$date"/>

  <IdPost V="034000"/>
  <IdColl V="$siren"/>
  <CodCol V="123"/>
  <CodBud V="12"/>
  <LibelleColBud V="COMMUNE"/>
 </EnTetePES>
 <PES_DepenseAller>
  <EnTeteDepense>
   <IdVer V="1"/>

   <InfoDematerialisee V="0"/>
  </EnTeteDepense>
  <Bordereau>
   <BlocBordereau>
    <Exer V="2009"/>
    <IdBord V="72"/>
    <DteBordEm V="2009-07-16"/>
    <TypBord V="01"/>
    <NbrPce V="1"/>

    <MtCumulAnnuel V="6312190.16"/>
    <MtBordHT V="75724.75"/>
   </BlocBordereau>
   <Piece>
    <BlocPiece>
     <InfoPce>
      <IdPce V="832"/>
      <TypPce V="01"/>
      <NatPce V="01"/>

      <Obj V="TEST HOMOLOGATION"/>
     </InfoPce>
    </BlocPiece>
    <LigneDePiece>
     <BlocLignePiece>
      <InfoLignePce>
       <IdLigne V="1"/>
       <Nature V="6553"/>
       <Fonction V="113"/>

       <LibVir1 V="ECHEANCIER"/>
       <LibVir2 V="LE NUMERO N EST PAS PRECISE"/>
       <ModRegl V="03"/>
       <TVAIntraCom V="0"/>
       <MtHT V="39724.75"/>
      </InfoLignePce>
     </BlocLignePiece>
     <Tiers>
      <InfoTiers>

       <RefTiers V="811"/>
       <CatTiers V="22"/>
       <NatJur V="09"/>
       <Nom V="PAIERIE DEPART. HERAULT"/>
      </InfoTiers>
      <Adresse>
       <TypAdr V="1"/>
       <Adr2 V="1000 RUE ALCO"/>
       <CP V="34000"/>

       <Ville V="MONTPELLIER"/>
       <CodRes V="0"/>
      </Adresse>
      <CpteBancaire>
       <CodeEtab V="30001"/>
       <CodeGuic V="00866"/>
       <IdCpte V="C7850000000"/>
       <CleRib V="67"/>
       <LibBanc V="LA BANQUE DU FUTUR"/>

       <TitCpte V="PAIERIE DEPART. HERAULT"/>
      </CpteBancaire>
     </Tiers>
    </LigneDePiece>
   </Piece>
  </Bordereau>
 </PES_DepenseAller>
 <PES_PJ>
  <EnTetePES_PJ>

   <IdVer V="1"/>
  </EnTetePES_PJ>
 </PES_PJ>
</n:PES_Aller>
PES_ALLER;

		return $content;
	}
	
}