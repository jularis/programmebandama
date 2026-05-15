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
        <td>Type inspection</td>
        <td>Localite</td>
        <td>Campagne</td>
        <td>Nom</td>
        <td>Prenoms</td>
        <td>Code Prod</td>
        <td>Parcelle</td>
        <td>Inspecteur</td> 
        <td>Note</td>
        <td>Date Inspection</td> 
        <td>Etat approbation</td> 
        <td>Date approbation</td> 
    </tr>
    </thead> 
    <?php
    foreach($inspections as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo implode(',',json_decode($c->certificat)); ?></td>
            <td><?php echo $c->producteur->localite->nom; ?></td>
            <td><?php echo $c->campagne->nom; ?></td>
            <td><?php echo stripslashes($c->producteur->nom); ?></td>
            <td><?php echo stripslashes($c->producteur->prenoms); ?></td>
            <td><?php echo $c->producteur->codeProd; ?></td>
            <td><?php echo $c->parcelle->codeParc ?? null; ?></td>
            <td><?php echo $c->user->lastname; ?> <?php echo $c->user->firstname; ?></td>
            <td><?php echo $c->note; ?></td> 
            <td><?php echo date('d/m/Y', strtotime($c->date_evaluation)); ?></td>
            <td>
                <?php
            if($c->approbation==1){
                echo 'Approuvé';
            } 
            if($c->approbation==2){
                echo 'Non Approuvé';
            }
            if($c->approbation==3){
                echo 'Exclu';
            }
            ?>       
        </td>
        <td><?php echo $c->date_approbation; ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>