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
        <td>Cooperative</td>
        <td>Section</td>
        <td>Localite</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Code Producteur</td>
        <td>Code Parcelle</td>
        <td>Type declaration</td>
        <td>Annee Creation</td>
        <td>Culture</td> 
        <td>Superficie</td>
        <td>Latitude</td>
        <td>Longitude</td> 
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
    foreach($parcelles as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->producteur->localite->section->cooperative->name; ?></td>
            <td><?php echo $c->producteur->localite->section->libelle; ?></td>
            <td><?php echo $c->producteur->localite->nom; ?></td>
            <td><?php echo stripslashes($c->producteur->nom); ?></td> 
            <td><?php echo stripslashes($c->producteur->prenoms); ?></td> 
            <td><?php echo $c->producteur->codeProd; ?></td> 
            <td><?php echo $c->codeParc; ?></td>
            <td><?php echo $c->typedeclaration; ?></td>
            <td><?php echo $c->anneeCreation; ?></td>
            <td><?php echo $c->culture; ?></td>
            <td><?php echo $c->superficie; ?></td>
            <td><?php echo $c->latitude; ?></td>
            <td><?php echo $c->longitude; ?></td> 
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>