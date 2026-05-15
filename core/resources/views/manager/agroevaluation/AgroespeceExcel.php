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
        <td>Localite</td>
        <td>Code producteur</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Variété</td>
        <td>Strate</td>
        <td>Quantité</td>
        <td>Date enregistrement</td>
    </tr>
    </thead>
    <?php

    foreach($agroespeces as $c)
    {

    ?>
        <tbody>
        <tr>
            <td><?php echo $c->agroevaluation->producteur->localite->nom; ?></td>
            <td><?php echo $c->agroevaluation->producteur->codeProd; ?></td>
            <td><?php echo stripslashes($c->agroevaluation->producteur->nom); ?></td>
            <td><?php echo stripslashes($c->agroevaluation->producteur->prenoms); ?></td>
            <td><?php echo stripslashes($c->agroespecesarbre->nom); ?></td>
            <td><?php echo stripslashes($c->agroespecesarbre->strate); ?></td>
            <td><?php echo $c->total; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>
