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
        <td>Nombre Enfants Enquetes</td>
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
        <td>Situation Matrimoniale</td>
        <td>Nombre Adultes</td>
        <td>Nombre Enfants 0-4</td>
        <td>Nombre Enfants 5-17</td>
        <td>Total Personnes Menage</td>
        <td>Enfant A Charge</td>
        <td>Nombre Enfants A Charge</td>
        <td>Theme(s) Sensibilisation</td>
        <td>Autre Theme Sensibilisation</td>
        <td>Outils Sensibilisation</td>
        <td>Hommes Sensibilises</td>
        <td>Femmes Sensibilisees</td>
        <td>Garcons Sensibilises</td>
        <td>Filles Sensibilisees</td>
        <td>Total Personnes Sensibilisees</td>
        <td>Telephone Sensibilisation</td>
        <td>Photo Sensibilisation</td>
        <td>Statut Fin</td>
        <td>Etat Soumission</td>
        <td>Status</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <?php
    foreach($enqueteMenages as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
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
            <td><?php echo $c->nombreEnfantsEnquetes; ?></td>
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
            <td><?php echo $c->situationMatrimoniale; ?></td>
            <td><?php echo $c->nombreAdultes; ?></td>
            <td><?php echo $c->nombreEnfants0a4; ?></td>
            <td><?php echo $c->nombreEnfants5a17; ?></td>
            <td><?php echo $c->totalPersonnesMenage; ?></td>
            <td><?php echo $c->aEnfantACharge; ?></td>
            <td><?php echo $c->nombreEnfantsACharge; ?></td>
            <td><?php echo $c->themes->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->autreThemeSensibilisation; ?></td>
            <td><?php echo $c->outils->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->nombreHommesSensibilises; ?></td>
            <td><?php echo $c->nombreFemmesSensibilisees; ?></td>
            <td><?php echo $c->nombreGarconsSensibilises; ?></td>
            <td><?php echo $c->nombreFillesSensibilisees; ?></td>
            <td><?php echo $c->totalPersonnesSensibilisees; ?></td>
            <td><?php echo $c->telephoneProducteurSensibilisation; ?></td>
            <td><?php echo $c->photoSensibilisation ? asset(str_replace('public/', 'storage/', $c->photoSensibilisation)) : ''; ?></td>
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
        <td>ID Enquete Menage</td>
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
    foreach($enqueteMenages as $menage)
    {
        foreach($menage->enfants as $enfant)
        {
    ?>
        <tbody>
        <tr>
            <td><?php echo $menage->id; ?></td>
            <td><?php echo stripslashes(@$menage->producteur->nom . ' ' . @$menage->producteur->prenoms); ?></td>
            <td><?php echo @$menage->localite->nom; ?></td>
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
