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
        <td>Producteur</td>
        <td>Code Producteur</td>
        <td>Date Enquete</td>
        <td>Nom Enqueteur</td>
        <td>Producteur Disponible</td>
        <td>Consentement</td>
        <td>Situation Matrimoniale</td>
        <td>Nombre Adultes</td>
        <td>Nombre Enfants 0-4</td>
        <td>Nombre Enfants 5-17</td>
        <td>Total Personnes Menage</td>
        <td>Enfant A Charge</td>
        <td>Nombre Enfants A Charge</td>
        <td>Statut Fin</td>
        <td>Etat Soumission</td>
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
            <td><?php echo @$c->localite->nom; ?></td>
            <td><?php echo stripslashes(@$c->producteur->nom . ' ' . @$c->producteur->prenoms); ?></td>
            <td><?php echo $c->codeProducteur; ?></td>
            <td><?php echo $c->dateEnquete; ?></td>
            <td><?php echo $c->nomEnqueteur; ?></td>
            <td><?php echo $c->producteurDisponible; ?></td>
            <td><?php echo $c->consentement; ?></td>
            <td><?php echo $c->situationMatrimoniale; ?></td>
            <td><?php echo $c->nombreAdultes; ?></td>
            <td><?php echo $c->nombreEnfants0a4; ?></td>
            <td><?php echo $c->nombreEnfants5a17; ?></td>
            <td><?php echo $c->totalPersonnesMenage; ?></td>
            <td><?php echo $c->aEnfantACharge; ?></td>
            <td><?php echo $c->nombreEnfantsACharge; ?></td>
            <td><?php echo $c->statutFin; ?></td>
            <td><?php echo $c->etatSoumission; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>
