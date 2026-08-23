<style>
    #categories, #enfants {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td, #categories th, #enfants td, #enfants th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even), #enfants tr:nth-child(even) {background-color: #f2f2f2;}

    #categories tr:hover, #enfants tr:hover {background-color: #ddd;}

    #categories th, #enfants th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<table id="categories" width="100%">
    <thead>
    <tr>
        <td>ID</td>
            <td>Agent collecteur</td>
        <td>Pourquoi cet interview</td>
        <td>Type d'enquete</td>
        <td>Section</td>
        <td>Region</td>
        <td>Departement</td>
        <td>Localite</td>
        <td>Producteur</td>
        <td>Sexe Producteur</td>
        <td>Code Producteur</td>
        <td>Date Enquete</td>
        <td>Nom Enqueteur</td>
        <td>Latitude</td>
        <td>Longitude</td>
        <td>Altitude</td>
        <td>Precision GPS</td>
        <td>Repondant = Producteur</td>
        <td>Nom Repondant</td>
        <td>Titre Repondant</td>
        <td>Producteur Disponible</td>
        <td>Raison Indisponibilite</td>
        <td>Date Replanification</td>
        <td>Raison(s) Refus</td>
        <td>Autre Raison Refus</td>
        <td>Consentement</td>
        <td>Superficie Plantation</td>
        <td>Manoeuvres Permanents</td>
        <td>Manoeuvres Permanents Moins 18 ans</td>
        <td>Manoeuvres Journaliers</td>
        <td>Manoeuvres Journaliers Moins 18 ans</td>
        <td>Nombre Enfants 0-4</td>
        <td>Nombre Enfants 5-17</td>
        <td>Personnes Trouvees</td>
        <td>Enfants Trouves</td>
        <td>Statut Fin</td>
        <td>Etat Soumission</td>
        <td>Status</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <?php
    foreach($visitePlantations as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo export_collecting_agent($c); ?></td>
            <td><?php echo $c->raisonInterview; ?></td>
            <td><?php echo $c->typeEnquete; ?></td>
            <td><?php echo @$c->section->libelle; ?></td>
            <td><?php echo @$c->section->region; ?></td>
            <td><?php echo @$c->section->departement; ?></td>
            <td><?php echo @$c->localite->nom; ?></td>
            <td><?php echo stripslashes(@$c->producteur->nom . ' ' . @$c->producteur->prenoms); ?></td>
            <td><?php echo $c->sexeProducteur; ?></td>
            <td><?php echo $c->codeProducteur; ?></td>
            <td><?php echo $c->dateEnquete; ?></td>
            <td><?php echo $c->nomEnqueteur; ?></td>
            <td><?php echo $c->latitude; ?></td>
            <td><?php echo $c->longitude; ?></td>
            <td><?php echo $c->altitude; ?></td>
            <td><?php echo $c->precisionGps; ?></td>
            <td><?php echo $c->estProducteurRepondant; ?></td>
            <td><?php echo $c->nomRepondant; ?></td>
            <td><?php echo $c->titreRepondant; ?></td>
            <td><?php echo $c->producteurDisponible; ?></td>
            <td><?php echo $c->raisonIndisponibilite; ?></td>
            <td><?php echo $c->datePlanification; ?></td>
            <td><?php echo $c->raisonsRefus->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->autreRaisonRefus; ?></td>
            <td><?php echo $c->consentement; ?></td>
            <td><?php echo $c->superficiePlantation; ?></td>
            <td><?php echo $c->nombreManoeuvresPermanents; ?></td>
            <td><?php echo $c->manoeuvresPermanentsMoins18; ?></td>
            <td><?php echo $c->nombreManoeuvresJournaliers; ?></td>
            <td><?php echo $c->manoeuvresJournaliersMoins18; ?></td>
            <td><?php echo $c->nombreEnfants0a4; ?></td>
            <td><?php echo $c->nombreEnfants5a17; ?></td>
            <td><?php echo $c->nombrePersonnesTrouvees; ?></td>
            <td><?php echo $c->nombreEnfantsTrouves; ?></td>
            <td><?php echo $c->statutFin; ?></td>
            <td><?php echo $c->etatSoumission; ?></td>
            <td><?php echo $c->status == 1 ? 'Actif' : 'Inactif'; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>

<br><br>

<table id="enfants" width="100%">
    <thead>
    <tr>
        <td>ID Visite Plantation</td>
        <td>Producteur</td>
        <td>Localite</td>
        <td>Code Enfant</td>
        <td>Nom Enfant</td>
        <td>Date Naissance</td>
        <td>Sexe</td>
        <td>Lien Parente</td>
        <td>Autre Lien Parente</td>
        <td>Raison Ne Vit Pas Avec Parents</td>
        <td>Autre Raison Ne Vit Pas Avec Parents</td>
        <td>Situation Scolaire</td>
        <td>Niveau Scolaire</td>
        <td>Raison(s) Non Scolarisation</td>
        <td>Autre Raison Non Scolarisation</td>
        <td>Extrait Naissance</td>
        <td>Raison(s) Pas Extrait</td>
        <td>Situation(s) PFTE</td>
        <td>Raison(s) Travail/Abus</td>
        <td>Autre Raison Travail/Abus</td>
        <td>Mesures Enfant</td>
        <td>Mesures Menage</td>
        <td>Mesures Communaute</td>
        <td>Autre Mesure</td>
        <td>Status</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <?php
    foreach($visitePlantations as $visite)
    {
        foreach($visite->enfants as $enfant)
        {
    ?>
        <tbody>
        <tr>
            <td><?php echo $visite->id; ?></td>
            <td><?php echo stripslashes(@$visite->producteur->nom . ' ' . @$visite->producteur->prenoms); ?></td>
            <td><?php echo @$visite->localite->nom; ?></td>
            <td><?php echo $enfant->codeEnfant; ?></td>
            <td><?php echo $enfant->nom; ?></td>
            <td><?php echo $enfant->dateNaissance; ?></td>
            <td><?php echo $enfant->sexe; ?></td>
            <td><?php echo $enfant->lienParente; ?></td>
            <td><?php echo $enfant->autreLienParente; ?></td>
            <td><?php echo $enfant->raisonNeVitPasParents; ?></td>
            <td><?php echo $enfant->autreRaisonNeVitPasParents; ?></td>
            <td><?php echo $enfant->situationScolaire; ?></td>
            <td><?php echo $enfant->niveauScolaire; ?></td>
            <td><?php echo $enfant->raisonsNonScolarisation->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->autreRaisonNonScolarisation; ?></td>
            <td><?php echo $enfant->extraitNaissance; ?></td>
            <td><?php echo $enfant->raisonsPasExtrait->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->situationsPfte->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->raisonsTravailAbus->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->autreRaisonTravailAbus; ?></td>
            <td><?php echo $enfant->mesuresEnfant->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->mesuresMenage->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->mesuresCommunaute->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $enfant->autreMesure; ?></td>
            <td><?php echo $enfant->status == 1 ? 'Actif' : 'Inactif'; ?></td>
            <td><?php echo date('d-m-Y', strtotime($enfant->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
        }
    }
    ?>

</table>
