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
        <td>Campagne</td>
        <td>Periode</td>
        <td>Section</td>
        <td>Magasin Section</td> 
        <td>Code livraison</td>
        <td>Date livraison</td>
        <td>Quantite livrée</td>
        <td>Quantite sortie</td>
        <td>Quantite restante</td> 
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
    foreach($stockssection as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo $c->livraisonInfo->senderCooperative->name; ?></td>
            <td><?php echo $c->campagne->nom; ?></td>
            <td><?php echo $c->campagnePeriode->nom; ?></td>
            <td><?php echo $c->magasinSection->section->libelle; ?></td>
            <td><?php echo $c->magasinSection->nom; ?></td>
            <td><?php echo $c->livraisonInfo->code; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->livraisonInfo->estimate_date)); ?></td>
            <td><?php echo $c->livraisonInfo->quantity; ?></td>
            <td><?php echo $c->stocks_sortant; ?></td>
            <td><?php echo $c->livraisonInfo->quantity - $c->stocks_sortant; ?></td> 
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>