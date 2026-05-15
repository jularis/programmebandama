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
        <td>Localite</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Quartier</td>
        <td>Sources energies</td> 
        <td>Bois Chauffe</td>
        <td>Ordures Menageres</td>
        <td>Separation Menage</td>
        <td>Eaux Toillette</td>
        <td>Eaux Vaisselle</td>
        <td>WC</td>
        <td>Sources Eaux</td>
        <td>Machine</td>
        <td>Type Machines</td>
        <td>Garde Machines</td>
        <td>Equipements</td>
        <td>Traitement Champs</td>
        <td>Nom Personne Traitant</td>
        <td>Numero Personne Traitant</td>
        <td>Emprunt Machine</td>
        <td>Garde Emprunt Machine</td>
        <td>Activite Femme</td>
        <td>Nom Activite Femme</td>
        <td>Superficie Cacao Femme</td>
        <td>Champ Femme</td>
        <td>Nombre Hectare Femme</td> 
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
    foreach($menages as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->producteur->localite->nom; ?></td>
            <td><?php echo stripslashes($c->producteur->nom); ?></td>
            <td><?php echo stripslashes($c->producteur->prenoms); ?></td>
            <td><?php echo $c->quartier; ?></td>
            <td><?php echo $c->sources_energies; ?></td>
            <td><?php echo $c->boisChauffe; ?></td>
            <td><?php echo $c->ordures_menageres; ?></td>
            <td><?php echo $c->separationMenage; ?></td>
            <td><?php echo $c->eauxToillette; ?></td>
            <td><?php echo $c->eauxVaisselle; ?></td>
            <td><?php echo $c->wc; ?></td>
            <td><?php echo $c->sources_eaux; ?></td>
            <td><?php echo $c->machine; ?></td>
            <td><?php echo $c->type_machines; ?></td>
            <td><?php echo $c->garde_machines; ?></td>
            <td><?php echo $c->equipements; ?></td>
            <td><?php echo $c->traitementChamps; ?></td>
            <td><?php echo $c->nomPersonneTraitant; ?></td>
            <td><?php echo $c->numeroPersonneTraitant; ?></td>
            <td><?php echo $c->empruntMachine; ?></td>
            <td><?php echo $c->gardeEmpruntMachine; ?></td>
            <td><?php echo $c->activiteFemme; ?></td>
            <td><?php echo $c->nomActiviteFemme; ?></td>
            <td><?php echo $c->superficieCacaoFemme; ?></td>
            <td><?php echo $c->champFemme; ?></td>
            <td><?php echo $c->nombreHectareFemme; ?></td> 
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>