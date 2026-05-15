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
        <td>nomMembre</td>
<td>prenomMembre</td>
<td>sexeMembre</td>
<td>datenaissMembre</td>
<td>codeMembre</td>
<td>lienParente</td>
<td>autreLienParente</td>
<td>frequente</td>
<td>niveauEtude</td>
<td>classe</td>
<td>ecoleVillage</td>
<td>distanceEcole</td>
<td>nomEcole</td>
<td>moyenTransport</td>
<td>avoirFrequente</td>
<td>niveauEtudeAtteint</td>
<td>date_enquete</td> 
    </tr>
    </thead> 
    <?php
    foreach($ssrteclmrs as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->producteur->localite->nom; ?></td>
            <td><?php echo stripslashes($c->producteur->nom); ?></td>
            <td><?php echo stripslashes($c->producteur->prenoms); ?></td>
            <td><?php echo $c->nomMembre; ?></td>
<td><?php echo $c->prenomMembre; ?></td>
<td><?php echo $c->sexeMembre; ?></td>
<td><?php echo date('d-m-Y', strtotime($c->datenaissMembre)); ?></td>
<td><?php echo $c->codeMembre; ?></td>
<td><?php echo $c->lienParente; ?></td>
<td><?php echo $c->autreLienParente; ?></td>
<td><?php echo $c->frequente; ?></td>
<td><?php echo $c->niveauEtude; ?></td>
<td><?php echo $c->classe; ?></td>
<td><?php echo $c->ecoleVillage; ?></td>
<td><?php echo $c->distanceEcole; ?></td>
<td><?php echo $c->nomEcole; ?></td>
<td><?php echo $c->moyenTransport; ?></td>
<td><?php echo $c->avoirFrequente; ?></td>
<td><?php echo $c->niveauEtudeAtteint; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->date_enquete)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>