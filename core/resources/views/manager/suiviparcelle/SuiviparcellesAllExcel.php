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
        <td>Campagne</td>  
        <td>Cooperative</td>
        <td>Section</td>
        <td>Localite</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Code Producteur</td>
        <td>Code Parcelle</td>
        <td>Varietes Cacao</td>
<td>Autre Variete</td>
<td>Existe Cours Eaux</td>
<td>Cours Eaux</td>
<td>Pente</td>
<td>Variete Abres</td>
<td>Nombre Sauvageons</td>
<td>Arbres Agroforestiers</td>
<td>Activite Taille</td>
<td>Activite Egourmandage</td>
<td>Activite Desherbage Manuel</td>
<td>Activite Recolte Sanitaire</td>
<td>Intrant NPK</td>
<td>Nombre Sacs NPK</td>
<td>Intrant Fiente</td>
<td>Nombre Sacs Fiente</td>
<td>Intrant Composte</td>
<td>Nombre Sacs Composte</td>
<td>Presence Pourriture Brune</td>
<td>Presence Bio Agresseur</td>
<td>Presence Insectes Ravageurs</td>
<td>Presence Fourmis Rouge</td>
<td>Presence Araignee</td>
<td>Presence Ver de Terre</td>
<td>Presence Mente Religieuse</td>
<td>Presence Swollen Shoot</td>
<td>Presence Insectes Parasites</td>
<td>Nom Insecticide</td>
<td>Nombre Insecticide</td>
<td>Nom Fongicide</td>
<td>Nombre Fongicide</td>
<td>Nom Herbicide</td>
<td>Nombre Herbicide</td>
<td>Nombre Desherbage</td>
<td>Date Visite</td>
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
    foreach($suiviparcelles as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td> 
            <td><?php echo $c->campagne->nom; ?></td> 
            <td><?php echo $c->parcelle->producteur->localite->section->cooperative->name; ?></td>
            <td><?php echo $c->parcelle->producteur->localite->section->libelle; ?></td>
            <td><?php echo $c->parcelle->producteur->localite->nom; ?></td>
            <td><?php echo $c->parcelle->producteur->nom; ?></td> 
            <td><?php echo $c->parcelle->producteur->prenoms; ?></td> 
            <td><?php echo $c->parcelle->producteur->codeProd; ?></td> 
            <td><?php echo $c->parcelle->codeParc; ?></td>
            <td><?php echo $c->varietes_cacao; ?></td>
<td><?php echo $c->autreVariete; ?></td>
<td><?php echo $c->existeCoursEaux; ?></td>
<td><?php echo $c->cours_eaux; ?></td>
<td><?php echo $c->pente; ?></td>
<td><?php echo $c->varieteAbres; ?></td>
<td><?php echo $c->nombreSauvageons; ?></td>
<td><?php echo $c->arbresagroforestiers; ?></td>
<td><?php echo $c->activiteTaille; ?></td>
<td><?php echo $c->activiteEgourmandage; ?></td>
<td><?php echo $c->activiteDesherbageManuel; ?></td>
<td><?php echo $c->activiteRecolteSanitaire; ?></td>
<td><?php echo $c->intrantNPK; ?></td>
<td><?php echo $c->nombresacsNPK; ?></td>
<td><?php echo $c->intrantFiente; ?></td>
<td><?php echo $c->nombresacsFiente; ?></td>
<td><?php echo $c->intrantComposte; ?></td>
<td><?php echo $c->nombresacsComposte; ?></td>
<td><?php echo $c->presencePourritureBrune; ?></td>
<td><?php echo $c->presenceBioAgresseur; ?></td>
<td><?php echo $c->presenceInsectesRavageurs; ?></td>
<td><?php echo $c->presenceFourmisRouge; ?></td>
<td><?php echo $c->presenceAraignee; ?></td>
<td><?php echo $c->presenceVerTerre; ?></td>
<td><?php echo $c->presenceMenteReligieuse; ?></td>
<td><?php echo $c->presenceSwollenShoot; ?></td>
<td><?php echo $c->presenceInsectesParasites; ?></td>
<td><?php echo $c->nomInsecticide; ?></td>
<td><?php echo $c->nombreInsecticide; ?></td>
<td><?php echo $c->nomFongicide; ?></td>
<td><?php echo $c->nombreFongicide; ?></td>
<td><?php echo $c->nomHerbicide; ?></td>
<td><?php echo $c->nombreHerbicide; ?></td>
<td><?php echo $c->nombreDesherbage; ?></td>
<td><?php echo $c->dateVisite; ?></td>
    <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>