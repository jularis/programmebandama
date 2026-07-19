<style>
    #categories {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td, #categories th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even){background-color: #f2f2f2;}

    #categories tr:hover {background-color: #ddd;}

    #categories th {
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
        <td>Code Enfant</td>
        <td>Producteur</td>
        <td>Localite</td>
        <td>Date Enquete</td>
        <td>Nom Enqueteur</td>
        <td>Action(s) Remediation Menee(s)</td>
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
        <td>Etat Soumission</td>
        <td>Status</td>
        <td>Date enreg</td>
    </tr>
    </thead>
    <?php
    foreach($suivis as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->raisonInterview; ?></td>
            <td><?php echo @$c->enfant->codeEnfant; ?></td>
            <td><?php echo stripslashes(@$c->enfant->menage->producteur->nom . ' ' . @$c->enfant->menage->producteur->prenoms); ?></td>
            <td><?php echo @$c->enfant->menage->localite->nom; ?></td>
            <td><?php echo $c->dateEnquete; ?></td>
            <td><?php echo $c->nomEnqueteur; ?></td>
            <td><?php echo $c->actionsRemediation->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->nom; ?></td>
            <td><?php echo $c->dateNaissance; ?></td>
            <td><?php echo $c->sexe; ?></td>
            <td><?php echo $c->lienParente; ?></td>
            <td><?php echo $c->autreLienParente; ?></td>
            <td><?php echo $c->raisonNeVitPasParents; ?></td>
            <td><?php echo $c->autreRaisonNeVitPasParents; ?></td>
            <td><?php echo $c->situationScolaire; ?></td>
            <td><?php echo $c->niveauScolaire; ?></td>
            <td><?php echo $c->raisonsNonScolarisation->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->autreRaisonNonScolarisation; ?></td>
            <td><?php echo $c->extraitNaissance; ?></td>
            <td><?php echo $c->raisonsPasExtrait->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->situationsPfte->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->raisonsTravailAbus->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->autreRaisonTravailAbus; ?></td>
            <td><?php echo $c->mesuresEnfant->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->mesuresMenage->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->mesuresCommunaute->pluck('valeur')->implode(', '); ?></td>
            <td><?php echo $c->autreMesure; ?></td>
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
            <td><?php echo $c->etatSoumission; ?></td>
            <td><?php echo $c->status == 1 ? 'Actif' : 'Inactif'; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>
